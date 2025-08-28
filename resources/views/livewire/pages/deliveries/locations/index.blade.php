<div class="Deliveries">
    <div class="container DeliveryLocations">
        <div class="breadcrumbs">
            <a href="{{ Route::has('delivery-areas.index') ? route('delivery-areas.index') : '#' }}" wire:navigate>Areas</a>
            <span>Locations</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Delivery Locations</h2>
                <div class="stats">
                    <span>{{ $count_locations }} {{ Str::plural('location', $count_locations) }}</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by location name..."
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
                <a href="{{ Route::has('delivery-locations.create') ? route('delivery-locations.create') : '#' }}" class="btn">New Delivery Location</a>
            </div>
        </div>

        <div class="delivery_locations_list small_cards">
            @forelse($locations as $location)
                <div class="card">
                    <div class="details">
                        <div class="info">
                            <h3>{{ $location->location_name }}</h3>
                            <span>{{ $location->delivery_areas_count }} {{ Str::plural('area', $location->delivery_areas_count) }}</span>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="crud">
                            <a href="{{ Route::has('delivery-locations.edit') ? route('delivery-locations.edit', $location->id) : '#' }}" class="edit">
                                <x-svgs.edit />
                            </a>
                            <button x-data
                                x-on:click.prevent="$wire.set('delete_location_id', {{ $location->id }}); $dispatch('open-modal', 'confirm-location-deletion')"
                                class="delete">
                                <x-svgs.trash />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No locations found.</p>
            @endforelse
        </div>
    </div>

    <x-modal name="confirm-location-deletion" :show="$delete_location_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteLocation" @submit="$dispatch('close-modal', 'confirm-location-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this location and it's areas?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-location-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Location
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
