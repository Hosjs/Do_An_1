<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function data(Request $req)
    {
        $rows = DB::table('user_answer as ua')
            ->join('users as u', 'u.id', '=', 'ua.user_id')
            ->join('tests as t', 't.id', '=', 'ua.test_id')
            ->join('questions as q', 'q.id', '=', 'ua.question_id')
            ->select(
                'ua.user_id',
                'u.name as user_name',
                'ua.test_id',
                't.title as test_title',
                'ua.question_id',
                'ua.score'
            )
            ->limit(500)
            ->get();

        // Convert to array for calculations
        $userAnswers = $rows->toArray();
        $totalSubmissions = count($userAnswers);
        $avgScore = $totalSubmissions > 0 ? array_sum(array_map(fn($a) => floatval($a->score), $userAnswers)) / $totalSubmissions : 0;

        // Example: pass if score >= 1
        $passCount = count(array_filter($userAnswers, fn($a) => floatval($a->score) >= 1));
        $passRate = $totalSubmissions > 0 ? $passCount / $totalSubmissions : 0;

        // Dummy histogram (replace with real logic)
        $histogram = [];
        foreach ($userAnswers as $a) {
            $bin = floor(floatval($a->score) / 2) * 2; // bin by 2 points
            if (!isset($histogram[$bin])) $histogram[$bin] = 0;
            $histogram[$bin]++;
        }
        $histogramArr = [];
        foreach ($histogram as $bin => $count) {
            $histogramArr[] = ['bin' => $bin, 'count' => $count];
        }

        return response()->json([
            'user_answers' => $rows,
            'histogram' => $histogramArr,
            'per_question' => [],
            'top_students' => [],
            'results' => [],
            'total_submissions' => $totalSubmissions,
            'avg_score' => round($avgScore, 2),
            'pass_rate' => round($passRate, 2),
            'avg_duration_min' => 0,
        ]);
    }
}
