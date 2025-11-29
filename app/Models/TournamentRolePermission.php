<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentRolePermission extends Model
{
    protected $table = 'tournament_role_permissions';

    protected $fillable = [
        'role_id',
        'resource',
        'permission',
    ];

    public function role()
    {
        return $this->belongsTo(TournamentRole::class, 'role_id');
    }

    public function isNone(): bool
    {
        return $this->permission === 'none';
    }

    public function isView(): bool
    {
        return $this->permission === 'view';
    }

    public function isEdit(): bool
    {
        return $this->permission === 'edit';
    }

    public function canView(): bool
    {
        return in_array($this->permission, ['view', 'edit']);
    }

    public function canEdit(): bool
    {
        return $this->permission === 'edit';
    }
}
