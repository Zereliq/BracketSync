<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'mappool_map_id',
        'order_in_match',
        'winning_team_id',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function mappoolMap()
    {
        return $this->belongsTo(MappoolMap::class);
    }

    public function winningTeam()
    {
        return $this->belongsTo(Team::class, 'winning_team_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
