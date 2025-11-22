<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentRoleUser extends Model
{
    use HasFactory;

    protected $table = 'tournaments_roles_users';

    protected $fillable = [
        'role_id',
        'tournament_id',
        'user_id',
    ];

    public function role()
    {
        return $this->belongsTo(TournamentRole::class, 'role_id');
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
