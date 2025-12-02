<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    use HasFactory;

    protected $table = 'maps';

    protected $fillable = [
        'osu_beatmap_id',
        'osu_beatmapset_id',
        'artist',
        'title',
        'version',
        'mode',
        'star_rating',
        'length_seconds',
        'mapper',
        'beatmap_url',
        'cover_url',
        'bpm',
        'cs',
        'ar',
        'od',
        'hp',
    ];

    public function mappoolMaps()
    {
        return $this->hasMany(MappoolMap::class);
    }
}
