<x-admin>
    <section class="container admins">
        <div class="header">
            <h1>Users <span>({{ count($users) }})</span></h1>
            @include('partials.js_search')
        </div>

        <div class="body">
            <table>
                <thead>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @if(count($users) > 0)
                        @php $id = 1 @endphp
                        @foreach($users as $user)
                        <tr class="searchable">
                            <td>
                                <a href="{{ route('user.edit', ['user' => $user->id]) }}" class="update_link">
                                    {{ $id++ }}
                                </a>
                            </td>
                            <td>
                                {{ $user->first_name .' '. $user->last_name }} 
                                {!! $user->user_level == 1 ? '<span class="td_span">admin</span>' : '' !!}
                            </td>
                            <td class="{{ $user->email_verified_at != Null ? 'verified' : 'unverified'  }}">{{ $user->email }}</td>
                            <td>{{ format_phone_number($user->phone_number) }}</td>
                            <td class="{{ $user->user_status == 1 ? 'active' : 'inactive'  }}">{{ $user->user_status == 1 ? 'Active' : 'Inactive'  }}</td>
                            <td class="actions">
                                <div class="action">
                                    <form id="deleteForm_{{ $user->id }}" action="{{ route('user.destroy', ['user' => $user->id]) }}" method="post">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button" onclick="deleteItem({{ $user->id }}, 'user');">
                                            <i class="fas fa-trash-alt delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No users yet</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>

    <x-sweetalert />
</x-admin>