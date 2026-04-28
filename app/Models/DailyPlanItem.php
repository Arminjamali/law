<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyPlanItem extends Model
{
    protected $fillable = [
        'daily_plan_id', 'subject_id', 'topic_id', 'resource_id',
        'type', 'study_type', 'start_time', 'end_time',
        'notes', 'is_completed', 'order',
    ];

    protected $casts = ['is_completed' => 'boolean'];

    public function dailyPlan(): BelongsTo  { return $this->belongsTo(DailyPlan::class); }
    public function subject(): BelongsTo    { return $this->belongsTo(Subject::class); }
    public function topic(): BelongsTo      { return $this->belongsTo(Topic::class); }
    public function resource(): BelongsTo   { return $this->belongsTo(Resource::class); }

    public function getTypeLabel(): string
    {
        return $this->type === 'test' ? 'تست‌زنی' : 'مطالعه';
    }

    public function getStudyTypeLabel(): string
    {
        return match($this->study_type) {
            'book'     => 'کتاب',
            'pamphlet' => 'جزوه',
            'teaching' => 'تدریس',
            'video'    => 'ویدیو',
            default    => '',
        };
    }
}
