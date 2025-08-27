<div class="Authentication">
    <div class="container Profile">
        <livewire:auth.profile.update-profile-information />

        <livewire:auth.profile.update-password />

        @if(auth()->check() && !auth()->user()->isAdmin())
            <livewire:auth.profile.delete-user />
        @endif
    </div>
</div>
