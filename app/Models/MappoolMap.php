<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappoolMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'mappool_id',
        'map_id',
        'slot',
        'mod_type',
        'is_tiebreaker',
    ];

    protected $casts = [
        'is_tiebreaker' => 'boolean',
    ];

    public function mappool()
    {
        return $this->belongsTo(Mappool::class);
    }

    public function map()
    {
        return $this->belongsTo(Map::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
