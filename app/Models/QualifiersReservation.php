<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualifiersReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'qualifiers_slot_id',
        'tournament_id',
        'user_id',
        'team_id',
        'reserved_by_user_id',
        'status',
        'suggested_time',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'suggested_time' => 'datetime',
        ];
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(QualifiersSlot::class, 'qualifiers_slot_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function reservedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by_user_id');
    }

    public function isSuggestion(): bool
    {
        return $this->qualifiers_slot_id === null && $this->suggested_time !== null;
    }

    public function isPending(): bool
    {
        return $this->status === 'reserved' && $this->isSuggestion();
    }
}
