<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mappool extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'stage',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function maps()
    {
        return $this->hasMany(MappoolMap::class);
    }

    public function matches()
    {
        return $this->hasMany(MatchModel::class);
    }
}
