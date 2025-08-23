<div class="ContactMessages max-w-2xl mx-auto py-4">
    <div class="contact_message">
        <div class="header">
            <div class="info">
                <h2>{{ $message->name }}</h2>
                <div class="extras">
                    <p>
                        <span>{{ $message->email }}</span>
                        <span>{{ $message->phone_number }}</span>
                    </p>
                </div>
            </div>

            <div class="actions flex gap-2 justify-start lg:justify-end">
                <button
                    wire:click="toggleImportant"
                    class="btn_transparent {{ $message->is_important ? 'bg-green-500 active_important_btn' : '' }}"
                >
                    <x-svgs.star class="w-4 h-4" />
                </button>

                @if(auth()->user()->isAdmin())
                    <button
                        x-data=""
                        x-on:click.prevent="$wire.set('delete_message_id', {{ $message->id }}); $dispatch('open-modal', 'confirm-message-deletion')"
                        class="btn_transparent"
                    >
                        <x-svgs.trash class="w-4 h-4 text-red-600 font-bold" />
                    </button>
                @endif
            </div>
        </div>

        <div class="message_details">
            <div class="user_message">
                <p class="message">
                    <span>{{ $message->message }}</span>
                    <span>{{ $message->created_at->format('H:i') }}<span />
                </p>
                <p class="date">{{ $message->created_at->format('M d, Y') }}</p>
            </div>

            @if($message->notes != null)
                <div class="admin_note">
                    <p class="message">
                        <span>{{ $message->notes }}</span>
                        <span>{{ $message->updated_at->format('H:i') }}<span />
                    </p>
                    <p class="date">{{ $message->updated_at->format('M d, Y') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="custom_form">
        <form wire:submit="updateMessage">
            <div class="inputs">
                <label for="notes">Notes</label>
                <textarea wire:model.defer="notes" name="notes" id="notes" placeholder="Enter a note for this message"></textarea>
            </div>

            <div class="buttons_group">
                <button type="submit" class="btn btn_primary">Update Note</button>
                <a href="{{ Route::has('contact-messages.index') ? route('contact-messages.index') : '#' }}" wire:navigate class="btn btn_danger">Back to Messages</a>
            </div>
        </form>
    </div>

    <x-modal name="confirm-message-deletion" :show="$delete_message_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteMessage" @submit="$dispatch('close-modal', 'confirm-message-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Confirm Deletion
                </h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">
                    Are you sure you want to permanently delete this message?
                </p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-message-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Message
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
