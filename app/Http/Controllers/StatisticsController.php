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
                'ua.score',
                'ua.answered_at'
            )
            ->orderByDesc('ua.answered_at')
            ->limit(500)
            ->get();

        return response()->json(['user_answer' => $rows]);
    }
}
