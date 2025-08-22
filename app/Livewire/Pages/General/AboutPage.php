<?php

namespace App\Livewire\Pages\General;

use Livewire\Component;

class AboutPage extends Component
{
    public function render()
    {
        return view('livewire.pages.general.about-page')->layout('layouts.guest');
    }
}
