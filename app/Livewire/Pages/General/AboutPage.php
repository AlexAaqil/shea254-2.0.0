<?php

namespace App\Livewire\Pages\General;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class AboutPage extends Component
{
    public function render()
    {
        return view('livewire.pages.general.about-page');
    }
}
