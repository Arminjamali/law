<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = ['subject_id', 'name', 'type', 'author', 'notes'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function studySessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'book'     => 'کتاب',
            'pamphlet' => 'جزوه',
            'video'    => 'ویدیو',
            default    => 'سایر',
        };
    }
}
