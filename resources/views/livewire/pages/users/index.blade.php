<div class="Users">
    <div class="container">
        <div class="app_header">
            <div class="info">
                <h2>Users</h2>
                <div class="stats">
                    @if(auth()->user()->isSuperAdmin())
                        <span>{{ $count_super_admins }} {{ Str::plural('super admin', $count_super_admins) }}</span>
                    @endif
                    <span>{{ $count_admins }} {{ Str::plural('admin', $count_admins) }}</span>
                    <span>{{ $count_users }} {{ Str::plural('user', $count_users) }}</span>
                    <span>{{ $count_unverified_users }} unverified</span>
                    <span>{{ $count_inactive_users }} inactive</span>
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
                <a href="{{ route('users.create') }}" class="btn">Create User</a>
            </div>
        </div>

        <div class="users_list">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="action">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                <td class="user_name">
                                    <span>{{ $user->full_name }}</span>
                                    @if ($user->isSuperAdmin())
                                        <x-svgs.shield-with-checkmark class="stroke-red-500" />
                                    @elseif($user->isAdmin())
                                        <x-svgs.shield-with-checkmark class="stroke-green-500" />
                                    @elseif($user->isCashier())
                                        <x-svgs.shield-halved class="fill-blue-500" />
                                    @endif
                                </td>
                                <td>{{ $user->phone_number }}</td>
                                <td class="{{ $user->email_verified_at ? '' : 'text-red-500' }}">{{ $user->email }}</td>
                                <td>{{ $user->status_label }}</td>
                                <td class="actions">
                                    <div class="action">
                                        <a href="{{ Route::has('users.edit') ? route('users.edit', $user->id) : '#' }}" wire:navigate>
                                            <x-svgs.edit class="text-green-600" />
                                        </a>
                                    </div>

                                    @auth
                                        @if(auth()->user()->isSuperAdmin())
                                            <div class="action">
                                                <button x-data
                                                    x-on:click.prevent="$wire.set('delete_user_id', {{ $user->id }}); $dispatch('open-modal', 'confirm-user-deletion')"
                                                    class="delete">
                                                    <x-svgs.trash />
                                                </button>
                                            </div>
                                        @endif
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination mt-4">
                {{ $users->links() }}
            </div>
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
