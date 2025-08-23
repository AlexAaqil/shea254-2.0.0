<?php

namespace App\Livewire\Pages\General;

use Livewire\Component;

class ContactPage extends Component
{
    public function render()
    {
        return view('livewire.pages.general.contact-page')->layout('layouts.guest');
    }
}
