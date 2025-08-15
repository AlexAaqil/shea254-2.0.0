<section class="update_password">
    <header>
        <h2>Update Password</h2>
        <p>Ensure your account is using a long, random password to stay secure.</p>
    </header>

   <div class="custom_form">
        <form wire:submit="updatePassword">
            <div class="inputs">
                <label for="update_password_current_password">Current Password</label>
                <input type="password" wire:model="current_password" id="update_password_current_password" autocomplete="current_password">
                <x-form-input-error field="current_password" />
            </div>

            <div class="inputs">
                <label for="update_password_password">New Password</label>
                <input type="password" wire:model="password" id="update_password_password" autocomplete="new-password">
                <x-form-input-error field="password" />
            </div>

            <div class="inputs">
                <label for="update_password_password_confirmation">Confirm Password</label>
                <input type="password" wire:model="password_confirmation" id="update_password_password_confirmation" autocomplete="new-password">
                <x-form-input-error field="password_confirmation" />
            </div>

            <button type="submit">Save</button>
            <x-action-message class="me-3" on="password-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </form>
    </div>
</section>
