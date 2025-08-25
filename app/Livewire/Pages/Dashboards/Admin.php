<?php

namespace App\Livewire\Pages\Dashboards;

use Livewire\Component;
use App\Enums\UserRoles;
use App\Models\User;
use App\Models\Comment;

class Admin extends Component
{
    public function render()
    {
        $count_super_admins = User::where('user_level', UserRoles::SUPER_ADMIN)->count();
        $count_admins = User::where('user_level', UserRoles::ADMIN)->count();
        $count_users = User::whereNotIn('user_level', [UserRoles::SUPER_ADMIN, UserRoles::ADMIN])->count();

        $count_messages = Comment::count();


        return view('livewire.pages.dashboards.admin', compact(
            'count_super_admins',
            'count_admins',
            'count_users',

            'count_messages',
        ));
    }
}
