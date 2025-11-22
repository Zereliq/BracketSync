<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentRole extends Model
{
    use HasFactory;

    protected $table = 'tournamentroles';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tournaments_roles_users')
            ->withPivot('tournament_id')
            ->withTimestamps();
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournaments_roles_users')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function links()
    {
        return $this->hasMany(TournamentRoleUser::class, 'role_id');
    }
}
