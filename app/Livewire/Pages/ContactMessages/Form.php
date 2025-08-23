<?php

namespace App\Livewire\Pages\ContactMessages;

use Livewire\Component;
use App\Models\Comment;
use Exception;

class Form extends Component
{
    public $full_name = '';
    public $email = '';
    public $phone_number = '';
    public $message = '';

    protected $rules = [
        'full_name' => 'required|string|min:3|max:120',
        'email' => 'required|email|max:80',
        'phone_number' => 'required|min:10|max:20',
        'message' => 'required|string|min:3|max:500',
    ];

    protected $messages = [
        'full_name.required' => 'Please enter your name',
        'full_name.min' => 'Name must be at least 3 characters',
        'email.required' => 'Please enter your email address',
        'email.email' => 'Please enter a valid email address',
        'phone_number.required' => 'Please enter your phone number',
        'phone_number.min' => 'Please enter a valid phone number',
        'message.required' => 'Please enter your message',
        'message.min' => 'Message must be at least 3 characters'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submitMessage()
    {
        $validated_data = $this->validate();

        try {
            Comment::create($validated_data);
            $this->reset();
            $this->dispatch('notify', message: 'Thank you for your message. We will get back to you soon.', type: 'success');
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Sorry, an error occured when sending your message. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.pages.contact-messages.form');
    }
}
