<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentRole extends Model
{
    use HasFactory;

    protected $table = 'tournamentroles';

    protected $fillable = [
        'name',
        'tournament_id',
        'is_protected',
        'description',
    ];

    protected $casts = [
        'is_protected' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tournaments_roles_users')
            ->withPivot('tournament_id')
            ->withTimestamps();
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournaments_roles_users')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function links()
    {
        return $this->hasMany(TournamentRoleUser::class, 'role_id');
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function permissions()
    {
        return $this->hasMany(TournamentRolePermission::class, 'role_id');
    }

    public function hasPermission(string $resource, string $level = 'view'): bool
    {
        $permission = $this->permissions()->where('resource', $resource)->first();

        if (! $permission) {
            return false;
        }

        if ($level === 'view') {
            return in_array($permission->permission, ['view', 'edit']);
        }

        if ($level === 'edit') {
            return $permission->permission === 'edit';
        }

        return false;
    }

    public function getPermission(string $resource): ?string
    {
        $permission = $this->permissions()->where('resource', $resource)->first();

        return $permission?->permission;
    }

    public function scopeForTournament($query, int $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('tournament_id');
    }
}
