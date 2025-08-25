<?php

namespace App\Livewire\Auth\Profile;

use Livewire\Component;
use App\Livewire\Actions\Auth\Logout;
use Illuminate\Support\Facades\Auth;

class DeleteUser extends Component
{
    public string $password = '';

    /**
    * Delete the currently authenticated user.
    */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.profile.delete-user');
    }
}
