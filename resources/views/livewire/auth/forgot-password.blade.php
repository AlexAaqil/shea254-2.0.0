<div class="Authentication">
    <div class="container ForgotPassword">
        <div class="custom_form">
            <p>Enter your email address to request the reset link for your password.</p>

            <form wire:submit="sendPasswordResetLink">
                <div class="inputs">
                    <label for="email">Email Address</label>
                    <input type="email" wire:model="email" name="email" id="email" autocomplete="username" autofocus>
                    <x-form-input-error field="email" />
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="sendPasswordResetLink">
                    <span wire:loading.remove wire:target="sendPasswordResetLink">Send Password Reset Link</span>
                    <span wire:loading wire:target="sendPasswordResetLink">Sending password reset link...</span>
                </button>
            </form>
        </div>
    </div>
</div>
