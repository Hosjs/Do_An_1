<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $table = 'user_answer';
    protected $fillable = [
        'id',
        'test_id',
        'user_id',
        'question_id',
        'score',
        'created_at',
        'updated_at'
    ];
}
