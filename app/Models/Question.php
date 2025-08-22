<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'type_id',
        'content',
        'image_url',
        'formula_latex',
        'blanks',
        'choices',
        'correct_answer',
        'solution',
        'created_by'
    ];

    protected $casts = [
        'choices' => 'array'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'type_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
