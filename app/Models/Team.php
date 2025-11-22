<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'teamname',
        'logo_url',
        'banner_url',
    ];

    protected $appends = ['name'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'teams_users')
            ->withPivot('is_captain')
            ->withTimestamps();
    }

    public function members()
    {
        return $this->users();
    }

    public function getNameAttribute(): ?string
    {
        return $this->teamname;
    }

    public function teamLinks()
    {
        return $this->hasMany(TeamUser::class);
    }

    public function matchesAsTeam1()
    {
        return $this->hasMany(MatchModel::class, 'team1_id');
    }

    public function matchesAsTeam2()
    {
        return $this->hasMany(MatchModel::class, 'team2_id');
    }

    public function rolls()
    {
        return $this->hasMany(Roll::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
