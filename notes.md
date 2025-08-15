# Notes
Installing Livewire
```
composer require livewire/livewire
```
Publish Livewire configurations
```
php artisan livewire:publish --config
```
Change the Livewire layout in config/livewire.php
```
- 'layout' => 'components.layouts.app',
+ 'layout' => 'layouts.app',
```
Install Tailwind V4
```
npm install tailwindcss @tailwindcss/vite
```
