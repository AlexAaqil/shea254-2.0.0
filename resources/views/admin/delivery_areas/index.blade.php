<x-admin>
    <section class="Delivery_locations">
        <div class="container areas">
            @include('admin.partials.locations_navbar')
    
            <div class="header">
                <h1>Areas <span>({{ $areas->count() }})</span></h1>
                @include('partials.js_search')
                <div class="header_btn">
                    <a href="{{ route('areas.create') }}">New</a>
                </div>
            </div>
    
            <div class="body">
                @foreach($locations as $location)
                    <h2>{{ $location->location_name }}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Delivery Areas</th>
                                <th>Price (Kshs.)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($location->delivery_areas->count() != 0)
                                @foreach($location->delivery_areas as $value)
                                    <tr class="searchable">
                                        <td class="area">{{ $value->area_name }} <span class="area_extra_info">{{ $value->location_name }}</span></td>
                                        <td>{{ $value->price }}</td>
                                        <td class="actions">
                                            <div class="action">
                                                <a href="{{ route('areas.edit', ['area'=>$value->id]) }}">
                                                    <i class="fas fa-pencil-alt update"></i>
                                                </a>
                                            </div>
                                            <div class="action">
                                                <form id="deleteForm_{{ $value->id }}" action="{{ route('areas.destroy', ['area' => $value->id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
            
                                                    <button type="button" onclick="deleteItem({{ $value->id }}, 'delivery area');">
                                                        <i class="fas fa-trash-alt delete"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3">No delivery areas yet</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </section>
    <x-sweetalert></x-sweetalert>
</x-admin>
