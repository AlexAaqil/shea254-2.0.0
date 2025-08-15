<section class="update_profile_information">
    <header>
        <h2>Profile Information</h2>
        <p>Update your account's profile information.</p>
    </header>

    <div class="custom_form">
        <form wire:submit="updateProfileInformation">
            <div class="inputs">
                <label for="first_name">First Name</label>
                <input type="text" wire:model="first_name" name="first_name" id="first_name" autocomplete="first_name" autofocus>
                <x-form-input-error field="first_name" />
            </div>

            <div class="inputs">
                <label for="last_name">Last Name</label>
                <input type="text" wire:model="last_name" name="last_name" id="last_name" autocomplete="last_name">
                <x-form-input-error field="last_name" />
            </div>

            <div class="inputs">
                <label for="email">Email Address</label>
                <input type="email" wire:model="email" name="email" id="email" autocomplete="username">
                <x-form-input-error field="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}

                            <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="inputs">
                <label for="phone_number">Phone Number</label>
                <input type="text" wire:model="phone_number" id="phone_number">
                <x-form-input-error field="phone_number" />
            </div>

            <div class="inputs">
                <label for="secondary_phone_number">Other Phone Number</label>
                <input type="text" wire:model="secondary_phone_number" id="secondary_phone_number">
                <x-form-input-error field="secondary_phone_number" />
            </div>

            <button type="submit">Update</button>
            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </form>
    </div>
</section>
