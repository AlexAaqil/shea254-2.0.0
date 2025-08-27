<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Signup extends Component
{
    public string $uuid = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function signup(): void
    {
        $validated = $this->validate(
            [
                'first_name' => ['required', 'string', 'max:70'],
                'last_name' => ['required','string','max:120'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'phone_number' => [
                    'required',
                    'string',
                    'regex:/^(2547|2541)[0-9]{8}$/',
                ],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            ],
            [
                'phone_number.regex' => 'The phone number must start with 2547 or 2541 and have exactly 12 digits. (254746055xxx or 254116055xxx)',
            ]
        );

        $validated['uuid'] = Str::ulid();
        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.signup')->layout('layouts.guest');
    }
}
