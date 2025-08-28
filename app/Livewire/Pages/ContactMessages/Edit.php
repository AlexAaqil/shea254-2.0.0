<?php

namespace App\Livewire\Pages\ContactMessages;

use Livewire\Component;
use App\Models\Comment;

class Edit extends Component
{
    public $message;

    public $delete_message_id = null;

    public function mount(string $message)
    {
        $this->message = Comment::where('id', $message)->firstOrFail();
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
                $this->dispatch('notify', type: 'success', message: 'message deleted successfully');
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.contact-messages.edit');
    }
}
