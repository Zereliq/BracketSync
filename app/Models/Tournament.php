<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'edition',
        'abbreviation',
        'description',
        'qualifier_results_public',
        'elim_type',
        'bracket_size',
        'auto_bracket_size',
        'format',
        'min_teamsize',
        'max_teamsize',
        'has_qualifiers',
        'seeding_type',
        'win_condition',
        'signup_method',
        'staff_can_play',
        'mode',
        'signup_restriction',
        'rank_min',
        'rank_max',
        'country_restriction_type',
        'country_list',
        'signup_start',
        'signup_end',
        'status',
        'created_by',
    ];

    protected $casts = [
        'qualifier_results_public' => 'boolean',
        'has_qualifiers' => 'boolean',
        'auto_bracket_size' => 'boolean',
        'staff_can_play' => 'boolean',
        'country_list' => 'array',
        'signup_start' => 'datetime',
        'signup_end' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mappools()
    {
        return $this->hasMany(Mappool::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function matches()
    {
        return $this->hasMany(MatchModel::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tournaments_roles_users')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function tournamentRoleLinks()
    {
        return $this->hasMany(TournamentRoleUser::class);
    }

    public function registeredPlayers()
    {
        return $this->hasMany(TournamentPlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(User::class, 'tournament_players')
            ->withTimestamps();
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'tournament_likes')
            ->withTimestamps();
    }

    public function isHost(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        return $this->tournamentRoleLinks()
            ->where('user_id', $user->id)
            ->whereHas('role', fn ($query) => $query->where('name', 'Host'))
            ->exists();
    }

    public function canManageStaff(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        return $this->tournamentRoleLinks()
            ->where('user_id', $user->id)
            ->whereHas('role', fn ($query) => $query->whereIn('name', ['Host', 'Organizer']))
            ->exists();
    }

    public function getFormattedTeamSize(): string
    {
        if ($this->min_teamsize === $this->max_teamsize) {
            return $this->format.'v'.$this->format;
        }

        return $this->min_teamsize.'v'.$this->min_teamsize.' - '.$this->max_teamsize.'v'.$this->max_teamsize;
    }

    public function isTeamTournament(): bool
    {
        return $this->format > 1 || $this->min_teamsize > 1 || $this->max_teamsize > 1;
    }

    public function getRankRangeDisplay(): string
    {
        if (! $this->rank_min && ! $this->rank_max) {
            return 'n/a';
        }

        if ($this->rank_min && $this->rank_max) {
            return '#'.number_format($this->rank_min).' - #'.number_format($this->rank_max);
        }

        if ($this->rank_min) {
            return '#'.number_format($this->rank_min).'+';
        }

        return 'Up to #'.number_format($this->rank_max);
    }

    public function getCurrentStage(): string
    {
        $now = now();

        // Draft stage - not yet published
        if ($this->status === 'draft') {
            return 'draft';
        }

        // Announced but signups haven't started
        if ($this->status === 'announced' && $this->signup_start && $now->isBefore($this->signup_start)) {
            return 'announced';
        }

        // Signups are open
        if ($this->status === 'announced' && $this->signup_start && $this->signup_end) {
            if ($now->isBetween($this->signup_start, $this->signup_end)) {
                return 'registration';
            }
        }

        // Signups closed but not ongoing yet
        if ($this->status === 'announced' && $this->signup_end && $now->isAfter($this->signup_end)) {
            return 'screening';
        }

        // If announced but no signup dates set, just show as announced
        if ($this->status === 'announced') {
            return 'announced';
        }

        // Tournament is ongoing - check if in qualifiers
        if ($this->status === 'ongoing') {
            if ($this->has_qualifiers) {
                // You could add more logic here to determine if qualifiers are done
                return 'qualifiers';
            }

            return 'bracket';
        }

        // Tournament finished
        if ($this->status === 'finished') {
            return 'finished';
        }

        // Tournament archived
        if ($this->status === 'archived') {
            return 'archived';
        }

        return 'draft';
    }

    public function getStageProgress(): int
    {
        $stages = ['draft', 'announced', 'registration', 'screening', 'qualifiers', 'bracket', 'finished', 'archived'];
        $currentStage = $this->getCurrentStage();
        $currentIndex = array_search($currentStage, $stages);

        // If qualifiers don't exist, skip that stage
        if (! $this->has_qualifiers && in_array($currentStage, ['bracket', 'finished', 'archived'])) {
            $stagesWithoutQualifiers = array_diff($stages, ['qualifiers']);
            $currentIndex = array_search($currentStage, array_values($stagesWithoutQualifiers));

            return (int) (($currentIndex / (count($stagesWithoutQualifiers) - 1)) * 100);
        }

        return (int) (($currentIndex / (count($stages) - 1)) * 100);
    }

    public function signupsOpen(): bool
    {
        $now = now();

        if ($this->signup_start && $now->isBefore($this->signup_start)) {
            return false;
        }

        if ($this->signup_end && $now->isAfter($this->signup_end)) {
            return false;
        }

        return true;
    }

    public function canUserSignup($user): bool
    {
        if (! $user) {
            return false;
        }

        if (! $this->signupsOpen()) {
            return false;
        }

        if (! in_array($this->status, ['draft', 'published'])) {
            return false;
        }

        return true;
    }
}
