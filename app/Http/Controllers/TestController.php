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
            'structure.*.type_id' => 'required|exists:question_types,id',
            'structure.*.quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();

        $test = Test::create([
            'user_id' => $userId,
            'start_time' => null,
            'end_time' => null,
            'score' => null,
            'reviewed' => false,
        ]);

        $selectedQuestions = [];
        $warnings = [];

        foreach ($request->structure as $block) {
            $availableCount = Question::where('subject_id', $block['subject_id'])
                ->where('type_id', $block['type_id'])
                ->count();

            $questions = Question::where('subject_id', $block['subject_id'])
                ->where('type_id', $block['type_id'])
                ->inRandomOrder()
                ->limit($block['quantity'])
                ->get();

            if ($questions->count() < $block['quantity']) {
                $subjectName = optional(\App\Models\Subject::find($block['subject_id']))->name;
                $typeName = optional(\App\Models\QuestionType::find($block['type_id']))->name;

                $warnings[] = "Không đủ câu hỏi cho {$subjectName} - {$typeName} (yêu cầu {$block['quantity']}, có {$questions->count()})";
            }

            foreach ($questions as $question) {
                TestDetail::create([
                    'test_id' => $test->id,
                    'question_id' => $question->id,
                ]);
                $selectedQuestions[] = $question;
            }
        }

        return response()->json([
            'message' => 'Tạo bài test thành công',
            'test_id' => $test->id,
            'questions' => $selectedQuestions,
            'warnings' => $warnings,
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
        $tests = Test::with('subject')->orderBy('created_at', 'desc')->get();
        return response()->json($tests);
    }
    public function show($id)
    {
        $test = Test::with([
            'subject',
            'details.question.answers'
        ])->findOrFail($id);

        return response()->json($test);
    }
    public function destroy($id)
    {
        $test = Test::findOrFail($id);
        $test->details()->delete(); // nếu có quan hệ
        $test->delete();

        return response()->json(['message' => 'Xóa đề thành công']);
    }
    public function storeUserAnswers(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|integer',
            'user_id' => 'required|integer',
            'answers' => 'required|array',
        ]);

        foreach ($validated['answers'] as $ans) {
            UserAnswer::create([
                'test_id' => $validated['test_id'],
                'user_id' => $validated['user_id'],
                'question_id' => $ans['question_id'],
                'score' => $ans['score'],
            ]);
        }

        return response()->json(['message' => 'Lưu thành công']);
    }

}

