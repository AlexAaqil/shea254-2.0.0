<div class="ContactMessages">
    <div class="container">
        <div class="header">
            <div class="info">
                <h2>Messages</h2>
                <div class="stats">
                    <span>{{ $count_messages }} {{ Str::plural('message', $count_messages) }}</span>
                    <span>{{ $count_unread }} unread</span>
                    <span>{{ $count_important }} important</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by email, name..."
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

            </div>
        </div>

        <div class="messages_list">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th class="numbering">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th class="action">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($messages as $message)
                            <tr class="{{ $message->is_not_read ? 'unread' : 'read' }}">
                                <td class="numbering">
                                    <span class="important_message">
                                        <x-svgs.star @class([ $message->is_important ? 'text-orange-400' : '' ])
                                        />
                                    </span>
                                </td>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ $message->phone_number }}</td>
                                <td>{{ Str::words($message->message, 8, '...') }}</td>
                                <td>{{ $message->created_at->format('d/m/Y') }}</td>
                                <td class="actions">
                                    <div class="action">
                                        <a href="{{ Route::has('contact-messages.edit') ? route('contact-messages.edit', $message->uuid) : '#' }}" wire:navigate>
                                            <x-svgs.edit class="text-green-600" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No messages found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
