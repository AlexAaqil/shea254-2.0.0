<x-admin>
    <div class="container comments">
        <div class="header">
            <h1>Comments <span>({{ $comments->count() }})</span></h1>
            @include('partials.js_search')
        </div>

        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Names</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($comments->count() != 0)
                        @foreach($comments as $value)
                        <tr class="searchable">
                            <td>{{ $value->full_name }}</td>
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->phone_number }}</td>
                            <td>{{ Illuminate\Support\Str::limit($value->message, 40) }}</td>
                            <td class="actions">
                                    <div class="action">
                                    <a href="{{ route('comments.show', ['comment'=>$value->id]) }}">
                                        <i class="fas fa-eye update"></i>
                                    </a>
                                </div>
                                <div class="action">
                                    <form id="deleteForm_{{ $value->id }}" action="{{ route('comments.destroy', ['comment' => $value->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button" onclick="deleteItem({{ $value->id }}, 'comment');">
                                            <i class="fas fa-trash-alt delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No available comments</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <x-sweetalert></x-sweetalert>
</x-admin>
