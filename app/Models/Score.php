<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'map_number',
        'mappool_map_id',
        'user_id',
        'team_id',
        'winning_team_id',
        'score',
        'accuracy',
        'combo',
        'passed',
    ];

    protected $casts = [
        'accuracy' => 'float',
        'passed' => 'boolean',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function mappoolMap()
    {
        return $this->belongsTo(MappoolMap::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function winningTeam()
    {
        return $this->belongsTo(Team::class, 'winning_team_id');
    }
}
