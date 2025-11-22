<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'user_id',
        'role_id',
        'invited_by',
        'status',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(TournamentRole::class, 'role_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function accept()
    {
        $this->update(['status' => 'accepted']);

        // Add user to tournament staff
        TournamentRoleUser::create([
            'tournament_id' => $this->tournament_id,
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
        ]);
    }

    public function decline()
    {
        $this->update(['status' => 'declined']);
    }
}
