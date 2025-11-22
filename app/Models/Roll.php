<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roll extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
        'roll',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
