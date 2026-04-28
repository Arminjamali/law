<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestSession extends Model
{
    protected $fillable = [
        'subject_id', 'topic_id',
        'date', 'start_time', 'end_time',
        'total_questions', 'correct_count', 'wrong_count', 'unanswered_count',
        'source', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function topic(): BelongsTo   { return $this->belongsTo(Topic::class); }

    public function getAccuracyAttribute(): float
    {
        if ($this->total_questions === 0) return 0;
        return round($this->correct_count / $this->total_questions * 100, 1);
    }

    public function getScoreAttribute(): float
    {
        if ($this->total_questions === 0) return 0;
        $score = ($this->correct_count - ($this->wrong_count / 3)) / $this->total_questions * 100;
        return round(max(0, $score), 1);
    }
}
