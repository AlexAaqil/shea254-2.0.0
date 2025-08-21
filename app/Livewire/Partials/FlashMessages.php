<?php

/*
|--------------------------------------------------------------------------
| Flash Messages Component
|--------------------------------------------------------------------------
- For livewire components without redirects:
  $this->dispatch('notify', $message, $type);
- For livewire components with redirects or normal controllers:
  session()->flash('notify', ['message' => $message, 'type' => $type]);
*/

namespace App\Livewire\Partials;

use Livewire\Component;

class FlashMessages extends Component
{
    public $message = '';
    public $type = 'success';
    public $show = false;

    public function mount()
    {
        if (session()->has('notify')) {
            $notify = session()->pull('notify');
            $this->message = $notify['message'];
            $this->type = $notify['type'] ?? 'success';
            $this->show = true;
            session()->forget('notify');
        }
    }

    protected $listeners = ['notify'];

    public function notify($message, $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.partials.flash-messages');
    }
}
