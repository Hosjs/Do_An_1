<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function data(Request $req)
    {
        // Base query for filtering
        $baseQuery = DB::table('user_answer as ua')
            ->join('users as u', 'u.id', '=', 'ua.user_id')
            ->join('tests as t', 't.id', '=', 'ua.test_id')
            ->join('questions as q', 'q.id', '=', 'ua.question_id');

        // Apply filters from the request
        if ($req->filled('search')) {
            $searchTerm = $req->input('search');
            $baseQuery->where(function($q) use ($searchTerm) {
                $q->where('u.name', 'like', "%{$searchTerm}%")
                  ->orWhere('t.title', 'like', "%{$searchTerm}%")
                  ->orWhere('q.id', 'like', "%{$searchTerm}%");
            });
        }
        if ($req->filled('subject_id')) {
            $baseQuery->where('q.subject_id', $req->input('subject_id'));
        }
        if ($req->filled('test_type')) {
            // Assuming the column in the 'tests' table is 'type_id'
            $baseQuery->where('q.type_id', $req->input('test_type'));
        }
        if ($req->filled('from_date')) {
            // Assuming the date column in 'user_answer' is 'created_at'
            $baseQuery->whereDate('ua.created_at', '>=', $req->input('from_date'));
        }
        if ($req->filled('to_date')) {
            // Assuming the date column in 'user_answer' is 'created_at'
            $baseQuery->whereDate('ua.created_at', '<=', $req->input('to_date'));
        }

        // --- Calculate statistics using a single, efficient database query ---
        $passMark = $req->input('pass_mark', 50) / 100.0;

        $statsQuery = clone $baseQuery; // Clone the base query for stats calculation
        
        $statsData = $statsQuery->selectRaw(
            'COUNT(*) as total_submissions, ' .
            'AVG(ua.score) as avg_score, ' .
            'SUM(CASE WHEN ua.score >= ? THEN 1 ELSE 0 END) as pass_count, ' .
            'SUM(CASE WHEN ua.score * 100 BETWEEN 0 AND 20 THEN 1 ELSE 0 END) as bin1_count, ' .
            'SUM(CASE WHEN ua.score * 100 BETWEEN 21 AND 40 THEN 1 ELSE 0 END) as bin2_count, ' .
            'SUM(CASE WHEN ua.score * 100 BETWEEN 41 AND 60 THEN 1 ELSE 0 END) as bin3_count, ' .
            'SUM(CASE WHEN ua.score * 100 BETWEEN 61 AND 80 THEN 1 ELSE 0 END) as bin4_count, ' .
            'SUM(CASE WHEN ua.score * 100 BETWEEN 81 AND 100 THEN 1 ELSE 0 END) as bin5_count',
            [$passMark]
        )->first();

        $totalSubmissions = (int)($statsData->total_submissions ?? 0);
        $passRate = $totalSubmissions > 0 ? (($statsData->pass_count ?? 0) / $totalSubmissions) : 0;

        $histogramArr = [
            ['bin' => '0-20',  'count' => (int)($statsData->bin1_count ?? 0)],
            ['bin' => '21-40', 'count' => (int)($statsData->bin2_count ?? 0)],
            ['bin' => '41-60', 'count' => (int)($statsData->bin3_count ?? 0)],
            ['bin' => '61-80', 'count' => (int)($statsData->bin4_count ?? 0)],
            ['bin' => '81-100','count' => (int)($statsData->bin5_count ?? 0)],
        ];

        // --- Fetch detailed answer rows for the table (with pagination in mind) ---
        $detailsQuery = clone $baseQuery; // Clone again for the details table
        $rows = $detailsQuery->select(
            'ua.user_id',
            'u.name as user_name',
            'ua.test_id',
            't.title as test_title',
            'ua.question_id',
            'ua.score'
        )
        ->limit(500) // Keep limit for the details table to avoid huge payloads
        ->get();

        return response()->json([
            'user_answers' => $rows,
            'histogram' => $histogramArr,
            'total_submissions' => $totalSubmissions,
            'avg_score' => round($statsData->avg_score ?? 0, 2),
            'pass_rate' => round($passRate, 2),
            'avg_duration_min' => 0, // This was hardcoded before, keeping it
        ]);
    }
}
