<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['name', 'color', 'icon', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('order');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function studySessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }

    public function testSessions(): HasMany
    {
        return $this->hasMany(TestSession::class);
    }

    public function dailyPlanItems(): HasMany
    {
        return $this->hasMany(DailyPlanItem::class);
    }

    public function getTotalStudyMinutesAttribute(): int
    {
        return $this->studySessions->sum(fn($s) =>
            \Carbon\Carbon::parse($s->start_time)->diffInMinutes(\Carbon\Carbon::parse($s->end_time))
        );
    }

    public function getTestAccuracyAttribute(): float
    {
        $total = $this->testSessions->sum('total_questions');
        if ($total === 0) return 0;
        return round($this->testSessions->sum('correct_count') / $total * 100, 1);
    }
}
