<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'mappool_id',
        'team1_id',
        'team2_id',
        'winner_team_id',
        'round',
        'stage',
        'status',
        'scheduled_at',
        'match_start',
        'match_end',
        'referee_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'match_start' => 'datetime',
        'match_end' => 'datetime',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function mappool()
    {
        return $this->belongsTo(Mappool::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'match_id');
    }

    public function rolls()
    {
        return $this->hasMany(Roll::class, 'match_id');
    }
}
