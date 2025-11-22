<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'invited_by',
        'status',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

        // Add user to team
        TeamUser::create([
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'is_captain' => false,
        ]);
    }

    public function decline()
    {
        $this->update(['status' => 'declined']);
    }
}
