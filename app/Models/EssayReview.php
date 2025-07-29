<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssayReview extends Model
{
    protected $fillable = [
        'test_detail_id',
        'reviewed_by',
        'score',
        'work_time',
        'reviewed_at',
        'comment'
    ];

    public function testDetail()
    {
        return $this->belongsTo(TestDetail::class, 'test_detail_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
