<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use App\Models\TestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\QuestionType;
use App\Models\Subject;
use App\Models\UserAnswer;

class TestController extends Controller
{
    public function generateTest(Request $request)
{
    $request->validate([
        'structure' => 'required|array',
        'structure.*.subject_id' => 'required|exists:subjects,id',
        'structure.*.type_id'    => 'required|exists:question_types,id',
        'structure.*.quantity'   => 'required|integer|min:1',
        'title'       => 'nullable|string|max:255',   // 👈
        'description' => 'nullable|string',           // 👈
    ]);


    $userId = Auth::id();

    $test = Test::create([
        'user_id'     => Auth::id(),
        'start_time'  => null,
        'end_time'    => null,
        'score'       => null,
        'reviewed'    => false,
        'title'       => $request->input('title'),        // 👈 lưu
        'description' => $request->input('description'),  // 👈 lưu
    ]);


    $selectedQuestions = [];
    $warnings = [];

    foreach ($request->structure as $block) {
        $questions = Question::where('subject_id', $block['subject_id'])
            ->where('type_id',    $block['type_id'])
            ->inRandomOrder()
            ->limit($block['quantity'])
            ->get();

        if ($questions->count() < $block['quantity']) {
            $subjectName = optional(Subject::find($block['subject_id']))->name;
            $typeName    = optional(QuestionType::find($block['type_id']))->name;
            $warnings[]  = "Không đủ câu hỏi cho {$subjectName} - {$typeName} (yêu cầu {$block['quantity']}, có {$questions->count()})";
        }

        foreach ($questions as $question) {
            TestDetail::create([
                'test_id'     => $test->id,
                'question_id' => $question->id,
            ]);
            $selectedQuestions[] = $question;
        }
    }

    return response()->json([
        'message'     => 'Tạo bài test thành công',
        'test_id'     => $test->id,
        'title'       => $test->title,
        'description' => $test->description,
        'questions'   => $selectedQuestions,
        'warnings'    => $warnings,
    ]);
    }
     public function getSubjects()
    {
        return response()->json(Subject::select('id', 'name')->get());
    }

    public function getQuestionTypes()
    {
        return response()->json(QuestionType::select('id', 'name')->get());
    }
    public function index()
    {
        $tests = Test::query()
            ->withCount('details')
            ->orderBy('created_at','desc')
            ->get(['id','title','description','created_at','reviewed']);

        return response()->json($tests);
    }
    public function show($id)
    {
        $test = Test::with([
            'subject',
            'details.question.answers'
        ])->findOrFail($id);

        return response()->json([
            'id'          => $test->id,
            'title'       => $test->title,
            'description' => $test->description,
            'duration'    => $test->duration,
            'subject'     => $test->subject,
            'details'     => $test->details,
            'created_at'  => $test->created_at,
        ]);
    }

    public function destroy($id)
    {
        $test = Test::findOrFail($id);
        $test->details()->delete();
        $test->delete();

        return response()->json(['message' => 'Xóa đề thành công']);
    }
    public function storeUserAnswers(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|integer|exists:tests,id',
            'user_id' => 'required|integer|exists:users,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.score'       => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $rows = [];
            $now  = now();
            foreach ($validated['answers'] as $ans) {
                $rows[] = [
                    'test_id'     => $validated['test_id'],
                    'user_id'     => $validated['user_id'],
                    'question_id' => $ans['question_id'],
                    'score'       => $ans['score'],
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ];
            }
            UserAnswer::upsert(
                $rows,
                ['test_id', 'user_id', 'question_id'],
                ['score','updated_at']
            );
        });

        return response()->json(['message' => 'Lưu thành công']);
    }


    public function userSolutions(Request $request, Test $test)
    {
        $includeChoices = (bool) $request->boolean('includeChoices', true);
        $userId = (int) ($request->input('user_id') ?: optional($request->user())->id);
        abort_unless($userId, 403, 'Thiếu user.');

        $userAnswers = UserAnswer::where('test_id', $test->id)
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get(['id','test_id','question_id','user_id','score']);

        if ($userAnswers->isEmpty()) {
            return response()->json([
                'test_id'   => (int) $test->id,
                'title'     => $test->title ?? null,
                'user_id'   => $userId,
                'questions' => [],
            ]);
        }

        $questionIds = $userAnswers->pluck('question_id')->unique()->values();

        $questions = Question::query()
            ->with(['answers' => function ($q) {
                $q->select('id','question_id','content','is_correct');
            }])
            ->whereIn('id', $questionIds)
            ->get([
                'id','subject_id','content','image_url','formula_latex','blanks',
                'choices','type_id','correct_answer','solution','created_by'
            ])
            ->keyBy('id');

        $rows = [];
        foreach ($userAnswers as $ua) {
            $q = $questions->get($ua->question_id);
            if (!$q) continue;

            $choices = [];
            if ($q->relationLoaded('answers') && $q->answers->count()) {
                $choices = $q->answers->map(fn($a) => [
                    'id'         => (int) $a->id,
                    'content'    => $a->content,
                    'is_correct' => (int) $a->is_correct,
                ])->values()->all();
            } elseif ($includeChoices && !empty($q->choices)) {
                $choices = $this->choicesFromJson($q->choices, $q->correct_answer);
            }

            $correctChoices = array_values(array_filter($choices, fn($c) => (int)($c['is_correct'] ?? 0) === 1));
            $correctAnswerParsed = $this->safeJsonDecode($q->correct_answer) ?? $q->correct_answer;

            $isCorrect = null;
            if (!is_null($ua->score)) {
                $isCorrect = ((float)$ua->score) > 0;
            }

            $rows[] = [
                'question_id'      => (int) $q->id,
                'type_id'          => (int) $q->type_id,
                'content'          => $q->content,
                'image_url'        => $q->image_url,
                'formula_latex'    => $q->formula_latex,
                'blanks'           => (int) $q->blanks,
                'choices'          => $includeChoices ? $choices : [],
                'correct_choices'  => $correctChoices,
                'correct_answer'   => $correctAnswerParsed,
                'is_correct'       => $isCorrect,
                'solution'         => $q->solution,
            ];
        }

        return response()->json([
            'test_id'   => (int) $test->id,
            'title'     => $test->title ?? null,
            'user_id'   => $userId,
            'questions' => $rows,
        ]);
    }

    private function safeJsonDecode($value)
    {
        if (is_array($value)) return $value;
        if (!is_string($value)) return null;
        $trim = trim($value);
        if ($trim === '') return null;
        try {
            return json_decode($trim, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function choicesFromJson($choicesJson, $correctRaw): array
    {
        try {
            $arr = is_array($choicesJson) ? $choicesJson : json_decode($choicesJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return [];
        }
        $arr = is_array($arr) ? $arr : [];
        $correctParsed = $this->safeJsonDecode($correctRaw);
        $correctSet = [];

        if (is_array($correctParsed)) {
            $correctSet = array_map('strval', $correctParsed);
        } elseif (is_string($correctRaw) && $correctRaw !== '') {
            $correctSet = [strval($correctRaw)];
        }

        $out = [];
        $i = 1;
        foreach ($arr as $opt) {
            $content = is_string($opt) ? $opt : json_encode($opt, JSON_UNESCAPED_UNICODE);
            $out[] = [
                'id'         => $i++,
                'content'    => $content,
                'is_correct' => in_array(strval($content), $correctSet, true) ? 1 : 0,
            ];
        }
        return $out;
    }
}

