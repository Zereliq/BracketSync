<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'osu_id',
        'name',
        'osu_username',
        'avatar_url',
        'osu_rank',
        'osu_pp',
        'osu_hit_accuracy',
        'taiko_rank',
        'taiko_pp',
        'taiko_hit_accuracy',
        'fruits_rank',
        'fruits_pp',
        'fruits_hit_accuracy',
        'mania_rank',
        'mania_pp',
        'mania_hit_accuracy',
        'gamemode_stats_updated_at',
        'country_code',
        'mode',
        'elo',
        'siterole_id',
        'email',
        'discord_username',
        'osu_access_token',
        'osu_refresh_token',
        'osu_token_expires_at',
    ];

    protected $casts = [
        'osu_token_expires_at' => 'datetime',
        'gamemode_stats_updated_at' => 'datetime',
    ];

    protected $hidden = [
        'osu_access_token',
        'osu_refresh_token',
    ];

    // Relationships
    public function siteRole()
    {
        return $this->belongsTo(SiteRole::class, 'siterole_id');
    }

    public function createdTournaments()
    {
        return $this->hasMany(Tournament::class, 'created_by');
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournaments_roles_users')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function tournamentRoles()
    {
        return $this->belongsToMany(TournamentRole::class, 'tournaments_roles_users')
            ->withPivot('tournament_id')
            ->withTimestamps();
    }

    public function tournamentRoleLinks()
    {
        return $this->hasMany(TournamentRoleUser::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'teams_users')
            ->withPivot('is_captain')
            ->withTimestamps();
    }

    public function teamLinks()
    {
        return $this->hasMany(TeamUser::class);
    }

    public function refereedMatches()
    {
        return $this->hasMany(MatchModel::class, 'referee_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function likedTournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_likes')
            ->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->siteRole?->name === 'admin';
    }

    public function isMod(): bool
    {
        return $this->siteRole?->name === 'mod';
    }

    public function isPlayer(): bool
    {
        return $this->siteRole?->name === 'player';
    }

    public function hasSiteRole(string $roleName): bool
    {
        return $this->siteRole?->name === $roleName;
    }

    // Gamemode statistics accessors
    public function getRankAttribute(): ?int
    {
        return match ($this->mode) {
            'taiko' => $this->taiko_rank,
            'fruits' => $this->fruits_rank,
            'mania' => $this->mania_rank,
            default => $this->osu_rank,
        };
    }

    public function getPpAttribute(): ?float
    {
        return match ($this->mode) {
            'taiko' => $this->taiko_pp,
            'fruits' => $this->fruits_pp,
            'mania' => $this->mania_pp,
            default => $this->osu_pp,
        };
    }

    public function getHitAccuracyAttribute(): ?float
    {
        return match ($this->mode) {
            'taiko' => $this->taiko_hit_accuracy,
            'fruits' => $this->fruits_hit_accuracy,
            'mania' => $this->mania_hit_accuracy,
            default => $this->osu_hit_accuracy,
        };
    }
}
