<?php

namespace App\Livewire\Auth\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class UpdateProfileInformation extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $secondary_phone_number = '';

    protected function rules(): array {
        return [
            'first_name' => ['required','string','max:70'],
            'last_name' => ['required','string','max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore(Auth::user()->id)],
            'phone_number' => [
                'nullable',
                'string',
                'regex:/^(2547|2541)[0-9]{8}$/',
            ],
            'secondary_phone_number' => [
                'nullable',
                'string',
                'regex:/^(2547|2541)[0-9]{8}$/',
            ],
        ];
    }

    protected $messages = [
        'phone_number.regex' => 'The phone number must start with 2547 or 2541 and have exactly 12 digits. (254746055xxx or 254146055xxx)',
        'secondary_phone_number.regex' => 'The phone number must start with 2547 or 2541 and have exactly 12 digits. (254746055xxx or 254146055xxx)',
    ];

    /**
    * Mount the component.
    */
    public function mount(): void
    {
        $this->first_name = Auth::user()->first_name;
        $this->last_name = Auth::user()->last_name;
        $this->email = Auth::user()->email;
        $this->phone_number = Auth::user()->phone_number ?? '';
        $this->secondary_phone_number = Auth::user()->secondary_phone_number ?? '';
    }

    /**
    * Update the profile information for the currently authenticated user.
    */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate();

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->full_name);
    }


    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.auth.profile.update-profile-information');
    }
}
