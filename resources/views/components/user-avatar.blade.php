@props(['user'])

@if ($user->avatar)
    <img src="{{ $user->avatar }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="rounded-full w-20 h-20 object-cover">
@else
    <span class="bg-gray-200 text-lg text-gray-700 rounded-full w-20 h-20 flex items-center justify-center font-semibold uppercase">{{ substr($user->first_name, 0, 1) }}</span>
@endif
