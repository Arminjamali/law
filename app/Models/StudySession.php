<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StudySession extends Model
{
    protected $fillable = [
        'subject_id', 'topic_id', 'resource_id',
        'date', 'start_time', 'end_time', 'type', 'repeat_count', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function topic(): BelongsTo   { return $this->belongsTo(Topic::class); }
    public function resource(): BelongsTo { return $this->belongsTo(Resource::class); }

    public function getDurationMinutesAttribute(): int
    {
        return Carbon::parse($this->start_time)->diffInMinutes(Carbon::parse($this->end_time));
    }

    public function getDurationFormattedAttribute(): string
    {
        $mins = $this->duration_minutes;
        $h = intdiv($mins, 60);
        $m = $mins % 60;
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'book'     => 'کتاب',
            'pamphlet' => 'جزوه',
            'teaching' => 'تدریس',
            'video'    => 'ویدیو',
            default    => $type,
        };
    }
}
