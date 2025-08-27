<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Enums\UserRoles;
use Illuminate\Support\Facades\Auth;

trait UserRoleScopes
{
    /**
     * Scope to return users visible based on the role of the authenticated user.
     */

    public function scopeVisibleToRole(Builder $query, UserRoles $role): Builder
    {
        return match($role) {
            UserRoles::SUPER_ADMIN => $query, // See everyone
            UserRoles::ADMIN => $query->whereIn('user_level', [
                UserRoles::ADMIN,
                UserRoles::USER,
            ]),
            UserRoles::USER => $query->where('id', Auth::user()->id), // See self only
        };
    }
}
