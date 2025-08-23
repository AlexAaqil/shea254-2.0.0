<form wire:submit="submitMessage">
    <div class="inputs_group">
            <div class="inputs">
            <label>Name</label>
            <input type="text" wire:model.blur="full_name">
            <x-form-input-error field="full_name" />
        </div>

        <div class="inputs">
            <label>Phone Number</label>
            <input type="text" wire:model.blur="phone_number">
            <x-form-input-error field="phone_number" />
        </div>
    </div>

    <div class="inputs">
        <label>Email</label>
        <input type="email" wire:model.blur="email">
        <x-form-input-error field="email" />
    </div>

    <div class="inputs">
        <label>Message</label>
        <textarea rows="4" wire:model.blur="message"></textarea>
        <x-form-input-error field="message" />
    </div>

    <button type="submit" wire:loading.attr="disabled" wire:target="submitMessage">
        <span wire:loading.remove wire:target="submitMessage">Send Message &rArr;</span>
        <span wire:loading wire:target="submitMessage">Sending your Message...</span>
    </button>
</form>
