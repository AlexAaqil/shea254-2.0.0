<?php

namespace App\Livewire\Pages\ContactMessages;

use Livewire\Component;
use App\Models\Comment;

class Edit extends Component
{
    public $message;
    public $notes = '';

    public $delete_message_id = null;

    public function mount(string $message)
    {
        $this->message = Comment::where('uuid', $message)->firstOrFail();
        $this->message->markAsRead();
        $this->notes = $this->message->notes ?? '';
    }

    public function toggleImportant()
    {
        $this->message->is_important = !$this->message->is_important;
        $this->message->save();

        // Refresh state
        $this->message = $this->message->fresh();

        $this->dispatch('notify', type: 'success', message: 'Important status updated.');
    }

    public function updateMessage()
    {
        $this->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->message->update(['notes' => $this->notes]);

        $this->message = $this->message->fresh();

        $this->dispatch('notify', type: 'success', message: 'Note updated successfully.');
    }

    public function deleteMessage()
    {
        if($this->delete_message_id) {
            $message = Comment::findOrFail($this->delete_message_id);
            if($message) {
                $message->delete();

                $this->delete_message_id = null;
                $this->dispatch('close-modal', 'confirm-message-deletion');
                return redirect()->route('contact-messages.index');
                $this->dispatch('notify', type: 'success', message: 'Message is deleted');
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.contact-messages.edit');
    }
}
