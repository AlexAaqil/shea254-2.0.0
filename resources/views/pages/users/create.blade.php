<x-app-layout>
    <div class="custom_form max-w-2xl mx-auto py-4">
        <div class="header">
            <h2>Create New User</h2>
        </div>

        <form action="{{ route('users.store') }}" method="post">
            @csrf

            <div class="inputs">
                <label for="first_name" class="required">First Name</label>
                <input type="text" name="first_name" id="first_name" autocomplete="given-name" value="{{ old('first_name') }}" autofocus>
                <x-form-input-error field="first_name" />
            </div>

            <div class="inputs">
                <label for="last_name" class="required">Last Name</label>
                <input type="text" name="last_name" id="last_name" autocomplete="family-name" value="{{ old('last_name') }}">
                <x-form-input-error field="last_name" />
            </div>

            <div class="inputs">
                <label for="email" class="required">Email Address</label>
                <input type="email" name="email" id="email" autocomplete="email" value="{{ old('email') }}">
                <x-form-input-error field="email" />
            </div>

            <div class="inputs">
                <label for="phone_number" class="required">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" placeholder="2547xxxxxxxx or 2541xxxxxxxx" autocomplete="phone-number" value="{{ old('phone_number') }}">
                <x-form-input-error field="phone_number" />
            </div>

            @php
                $selectedUserLevel = old('user_level', $user->user_level_value ?? null);
            @endphp

            <div class="inputs">
                <label for="user_level" class="required">User Level</label>
                <select name="user_level" id="user_level" required>
                    <option value="">Select a User Level</option>
                    @foreach(\App\Enums\UserRoles::adminLabels() as $value => $label)
                        <option value="{{ $value }}" {{ (string) $selectedUserLevel === (string) $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <x-form-input-error field="user_level" />
            </div>

            <div class="inputs">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" autocomplete="new-password">
                <x-form-input-error field="password" />
            </div>

            <div class="inputs">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
                <x-form-input-error field="password_confirmation" />
            </div>

            <div class="buttons_group">
                <button type="submit">Save</button>
                <a href="{{ route('users.index') }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
