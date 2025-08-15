<div class="Authentication">
    <div class="container VerifyEmail">
        <div class="custom_form">
            <p>Thank you for signing up.</p>
            <p>Please verify your email address by clicking the link we just sent you.</p>
            <p>If you didn't receive the email, we will gladly send you another.</p>
            <div class="buttons_group">
                <button wire:click="sendVerification" type="submit">Resend Verification Email</button>
                <button wire:click="logout" type="submit" class="btn_danger">Logout</button>
            </div>
        </div>
    </div>
</div>
