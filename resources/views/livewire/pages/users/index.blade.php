<div class="Users">
    <div class="container">
        <div class="app_header">
            <div class="info">
                <h2>Users</h2>
                <div class="stats">
                    <span>{{ $count_admins }} {{ Str::plural('admin', $count_admins) }}</span>
                    <span>{{ $count_users }} {{ Str::plural('user', $count_users) }}</span>
                    @if(auth()->user()->isSuperAdmin())
                        <span>{{ $count_super_admins }} {{ Str::plural('super admin', $count_super_admins) }}</span>
                    @endif
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by email, first name, last name..."
                        wire:model="search"
                        wire:keydown.enter="performSearch"
                        class="pr-8"
                    >
                    @if($search)
                        <button
                            wire:click="resetSearch"
                            class="absolute right-1 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            X
                        </button>
                    @endif
                </div>
            </div>

            <div class="button">
                <a href="{{ route('users.create') }}" wire:navigate class="btn">Create User</a>
            </div>
        </div>

        <div class="users">
            @forelse ($users as $user)
                <div class="user" wire:key="user-{{ $user->id }}">
                    <div class="details">
                        <div class="image h-20 w-20">
                            <x-user-avatar :user="$user" />
                        </div>

                        <div class="info">
                            <h3>
                                {{ $user->first_name }} {{ $user->last_name }}
                                @if($user->isAdmin())
                                    <x-svgs.shield-with-checkmark class="inline-block w-3 h-3 ml-1 text-green-600" />
                                @endif
                            </h3>
                            <p class="{{ $user->email_verified_at === null ? 'text-red-600' : '' }}">
                                {{ $user->email }}
                            </p>
                            <p>{{ $user->phone_numbers ? $user->phone_numbers : 'N/A' }}</p>
                        </div>
                    </div>

                    @php
                        $currentUser = auth()->user();
                        $isCurrentUser = $currentUser->id === $user->id;
                        $isAdmin = $currentUser->isAdmin();
                    @endphp

                    <div class="actions">
                        <div class="others">
                            @if($isAdmin && !$isCurrentUser)
                                <span wire:click="toggleStatus({{ $user->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleStatus"
                                    class="{{ $user->isActive() ? 'border border-green-500 bg-green-100 text-green-900 text-xs p-1' : 'border border-red-500 bg-red-100 text-red-900 text-xs p-1' }}">
                                    {{ $user->status_label }}
                                </span>
                            @else
                                <span>{{ $user->status_label }}</span>
                            @endif
                        </div>

                        <div class="crud">
                            @if($isAdmin)
                                <a href="{{ route('users.edit', ['id' => $user->id]) }}" wire:navigate class="edit">
                                    <x-svgs.edit />
                                </a>
                            @endif

                            @if($isAdmin && !$isCurrentUser)
                                <button x-data
                                        x-on:click.prevent="$wire.set('delete_user_id', {{ $user->id }}); $dispatch('open-modal', 'confirm-user-deletion')"
                                        class="delete">
                                    <x-svgs.trash />
                                </button>
                            @endif

                            @unless($isAdmin)
                                <span>{{ $user->role->label() }}</span>
                            @endunless
                        </div>
                    </div>
                </div>
            @empty
                <p>No users found.</p>
            @endforelse
        </div>

        {{-- ✅ Pagination --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$delete_user_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteUser" @submit="$dispatch('close-modal', 'confirm-user-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this user?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete User
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
