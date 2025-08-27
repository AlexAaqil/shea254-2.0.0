<?php

namespace App\Livewire\Pages\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use App\Enums\UserRoles;

class Form extends Component
{
    public $user_id;
    public $first_name, $last_name, $email, $role, $password, $password_confirmation;

    public function mount($id = null)
    {
        if ($id) {
            $user = User::where('id', $id)->firstOrFail();
            $this->user_id = $user->id;
            $this->first_name = $user->first_name;
            $this->last_name = $user->last_name;
            $this->email = $user->email;
            $this->role = $user->user_level;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:70'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $this->user_id],
            'password' => ['nullable', 'string', 'confirmed', Rules\Password::defaults()],
            'user_level' => ['required', new Enum(UserRoles::class)],
        ];

        if (!$this->user_id) {
            $rules['password'][] = 'required';
        }

        return $rules;
    }

    protected $messages = [
        'first_name.required' => 'First name is required.',
        'first_name.max' => 'First name must not exceed 70 characters.',
        'last_name.required' => 'Last name is required.',
        'last_name.max' => 'Last name must not exceed 120 characters.',
        'email.required' => 'Email is required.',
        'email.email' => 'Email must be a valid email address.',
        'email.max' => 'Email must not exceed 255 characters.',
        'email.unique' => 'Email is already taken.',
        'password.required' => 'Password is required.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function saveUser()
    {
        $validated_data = $this->validate();

        if (!empty($validated_data['password'])) {
            $validated_data['password'] = Hash::make($validated_data['password']);
        } else {
            unset($validated_data['password']);
        }

        if ($this->user_id) {
            $user = User::findOrFail($this->user_id);
            $user->update($validated_data);
            $message = 'user has been updated';
        } else {
            $validated_data['id'] = Str::ulid();
            User::create($validated_data);
            $message = 'user has been created';
        }

        session()->flash('notify', [
            'message' => $message,
            'type' => 'success',
        ]);

        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.pages.users.form');
    }
}
