<div class="custom_form max-w-2xl mx-auto py-4">
    <div class="header">
        <h2>{{ $user_id ? 'Edit User' : 'Create New User' }}</h2>
    </div>

    <form wire:submit="saveUser">
        <div class="inputs">
            <label for="first_name" class="required">First Name</label>
            <input type="text" wire:model="first_name" id="first_name" autocomplete="given-name" autofocus>
            <x-form-input-error field="first_name" />
        </div>

        <div class="inputs">
            <label for="last_name" class="required">Last Name</label>
            <input type="text" wire:model="last_name" id="last_name" autocomplete="family-name">
            <x-form-input-error field="last_name" />
        </div>

        <div class="inputs">
            <label for="email" class="required">Email Address</label>
            <input type="email" wire:model="email" id="email" autocomplete="email">
            <x-form-input-error field="email" />
        </div>

        <div class="inputs">
            <label for="role" class="required">Role</label>
            <select wire:model="role" id="role">
                <option value="">Select a role</option>
                @foreach(\App\Enums\UserRoles::adminLabels() as $value => $label)
                    <option value="{{ $value }}" {{ $value == \App\Enums\UserRoles::USER->value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <x-form-input-error field="role" />
        </div>

        <div class="inputs">
            <label for="password">{{ $user_id ? 'New Password' : 'Password' }}</label>
            <input type="password" wire:model="password" id="password" autocomplete="new-password">
            @if($user_id)
                <small class="text-gray-500 text-sm">Leave blank to keep current password</small>
            @endif
            <x-form-input-error field="password" />
        </div>

        <div class="inputs">
            <label for="password_confirmation">{{ $user_id ? 'Confirm New Password' : 'Confirm Password' }}</label>
            <input type="password" wire:model="password_confirmation" id="password_confirmation" autocomplete="new-password">
            <x-form-input-error field="password_confirmation" />
        </div>

        <div class="buttons_group">
            <button type="submit" class="btn btn_primary">Save</button>
            <a href="{{ route('users.index') }}" wire:navigate class="btn btn_danger">Cancel</a>
        </div>
    </form>
</div>
