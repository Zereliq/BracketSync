<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualifiersSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'referee_user_id',
        'start_time',
        'end_time',
        'max_participants',
        'is_public',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(QualifiersReservation::class);
    }

    public function getAvailableSpotsAttribute(): int
    {
        $reservedCount = $this->reservations()
            ->whereIn('status', ['reserved', 'checked_in'])
            ->count();

        return max(0, $this->max_participants - $reservedCount);
    }

    public function isFull(): bool
    {
        return $this->available_spots <= 0;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && ! $this->isFull();
    }
}
