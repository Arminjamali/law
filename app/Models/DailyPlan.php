<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyPlan extends Model
{
    protected $fillable = ['date', 'goal_hours', 'notes'];

    protected $casts = ['date' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(DailyPlanItem::class)->orderBy('order');
    }

    public function getCompletionPercentageAttribute(): int
    {
        $total = $this->items->count();
        if ($total === 0) return 0;
        return (int) round($this->items->where('is_completed', true)->count() / $total * 100);
    }
}
