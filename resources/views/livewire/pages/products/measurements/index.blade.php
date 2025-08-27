<div class="Products">
    <div class="container ProductMeasurements">
        <div class="breadcrumbs">
            <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate>Products</a>
            <a href="{{ Route::has('product-categories.index') ? route('product-categories.index') : '#' }}" wire:navigate>Categories</a>
            <span>Measurements</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Product Measurements</h2>
                <div class="stats">
                    <span>{{ $count_measurements }} {{ Str::plural('measurement', $count_measurements) }}</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by measurement name..."
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
                <a href="{{ Route::has('product-measurements.create') ? route('product-measurements.create') : '#' }}" class="btn">New Measurement</a>
            </div>
        </div>

        <div class="measurements_list small_cards">
            @forelse($measurements as $measurement)
                <div class="card">
                    <div class="details">
                        <div class="info">
                            <h3>{{ $measurement->measurement_name }}</h3>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="others">

                        </div>

                        <div class="crud">
                            <a href="{{ Route::has('product-measurements.edit') ? route('product-measurements.edit', $measurement->id) : '#' }}" class="edit">
                                <x-svgs.edit />
                            </a>
                            <button x-data
                                x-on:click.prevent="$wire.set('delete_measurement_id', {{ $measurement->id }}); $dispatch('open-modal', 'confirm-measurement-deletion')"
                                class="delete">
                                <x-svgs.trash />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No measurements found.</p>
            @endforelse
        </div>
    </div>

    <x-modal name="confirm-measurement-deletion" :show="$delete_measurement_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteMeasurement" @submit="$dispatch('close-modal', 'confirm-measurement-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this measurement?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-measurement-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Measurement
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
