<?php

namespace App\Livewire\Pages\General;

use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        return view('livewire.pages.general.home-page')->layout('layouts.guest');
    }
}
