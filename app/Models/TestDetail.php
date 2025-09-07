<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestDetail extends Model
{
    protected $fillable = [
        'test_id',
        'question_id',
        'user_answer',
        'score'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
