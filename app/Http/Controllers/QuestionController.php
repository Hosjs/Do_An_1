<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\TestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Test;

class QuestionController extends Controller
{
    public function index($testId)
    {
        $test = \App\Models\Test::findOrFail($testId);
        $questionIds = $test->details()->pluck('question_id');
        $questions = \App\Models\Question::with(['subject', 'type', 'answers'])
            ->whereIn('id', $questionIds)
            ->get();

        return response()->json($questions);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'test_id'        => 'required|integer|exists:tests,id',
            'subject_id'     => 'required|integer|exists:subjects,id',
            'type_id'        => 'required|integer|exists:question_types,id',
            'content'        => 'required|string',
            'image_url'      => 'nullable|string',
            'formula_latex'  => 'nullable|string',
            'blanks'         => 'nullable|integer',
            'choices'        => 'nullable|array',
            'correct_answer' => 'nullable', // string or array
            'solution'       => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $q = Question::create([
                'subject_id'     => $data['subject_id'],
                'type_id'        => $data['type_id'],
                'content'        => $data['content'],
                'image_url'      => $data['image_url'] ?? null,
                'formula_latex'  => $data['formula_latex'] ?? null,
                'blanks'         => $data['blanks'] ?? 0,
                'choices'        => $data['choices'] ?? null,
                'correct_answer' => is_array($data['correct_answer'] ?? null)
                    ? json_encode($data['correct_answer'])
                    : ($data['correct_answer'] ?? null),
                'solution'       => $data['solution'] ?? null,
                'created_by'     => auth()->id(), // <-- add this line
            ]);

            // Sync answers if choices provided
            if (!empty($data['choices']) && is_array($data['choices'])) {
                Answer::where('question_id', $q->id)->delete();
                $correctSet = [];
                $rawCorrect = $data['correct_answer'] ?? null;
                if (is_array($rawCorrect)) {
                    $correctSet = array_map('strval', $rawCorrect);
                } elseif (is_string($rawCorrect) && $rawCorrect !== '') {
                    $correctSet = [$rawCorrect];
                }
                foreach ($data['choices'] as $opt) {
                    $content = is_string($opt) ? $opt : json_encode($opt, JSON_UNESCAPED_UNICODE);
                    Answer::create([
                        'question_id' => $q->id,
                        'content'     => $content,
                        'is_correct'  => in_array(strval($content), $correctSet, true) ? 1 : 0,
                    ]);
                }
            }

            // Attach to test
            TestDetail::create([
                'test_id'     => (int) $data['test_id'],
                'question_id' => (int) $q->id,
            ]);

            return response()->json($q->load('answers'), 201);
        });
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'subject_id'     => 'sometimes|required|integer|exists:subjects,id',
            'type_id'        => 'sometimes|required|integer|exists:question_types,id',
            'content'        => 'sometimes|required|string',
            'image_url'      => 'nullable|string',
            'formula_latex'  => 'nullable|string',
            'blanks'         => 'nullable|integer',
            'choices'        => 'nullable|array',
            'correct_answer' => 'nullable',
            'solution'       => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $question) {
            $question->update([
                'subject_id'     => $data['subject_id']     ?? $question->subject_id,
                'type_id'        => $data['type_id']        ?? $question->type_id,
                'content'        => $data['content']        ?? $question->content,
                'image_url'      => $data['image_url']      ?? $question->image_url,
                'formula_latex'  => $data['formula_latex']  ?? $question->formula_latex,
                'blanks'         => $data['blanks']         ?? $question->blanks,
                'choices'        => $data['choices']        ?? $question->choices,
                'correct_answer' => array_key_exists('correct_answer', $data)
                    ? (is_array($data['correct_answer']) ? json_encode($data['correct_answer']) : $data['correct_answer'])
                    : $question->correct_answer,
                'solution'       => $data['solution']       ?? $question->solution,
            ]);

            if (array_key_exists('choices', $data)) {
                Answer::where('question_id', $question->id)->delete();
                $choices = $data['choices'] ?? [];
                $rawCorrect = $data['correct_answer'] ?? $question->correct_answer;
                $correctSet = [];
                if (is_array($rawCorrect)) {
                    $correctSet = array_map('strval', $rawCorrect);
                } elseif (is_string($rawCorrect) && $rawCorrect !== '') {
                    // If stored JSON
                    $decoded = json_decode($rawCorrect, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $correctSet = array_map('strval', $decoded);
                    } else {
                        $correctSet = [$rawCorrect];
                    }
                }
                foreach ($choices as $opt) {
                    $content = is_string($opt) ? $opt : json_encode($opt, JSON_UNESCAPED_UNICODE);
                    Answer::create([
                        'question_id' => $question->id,
                        'content'     => $content,
                        'is_correct'  => in_array(strval($content), $correctSet, true) ? 1 : 0,
                    ]);
                }
            }

            return response()->json($question->load('answers'));
        });
    }

    public function destroy(Question $question)
    {
        return DB::transaction(function () use ($question) {
            Answer::where('question_id', $question->id)->delete();
            TestDetail::where('question_id', $question->id)->delete();
            $question->delete();
            return response()->json(['message' => 'Deleted']);
        });
    }
}

