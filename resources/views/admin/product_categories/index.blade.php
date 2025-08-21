<x-admin>
    <div class="container">
        @include('admin.partials.products_navbar')

        <div class="header">
            <h1>Product Categories <span>({{ $product_categories->count() }})</span></h1>
            @include('partials.js_search')
            <div class="header_btn">
                <a href="{{ route('product-categories.create') }}">New</a>
            </div>
        </div>

        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product_categories as $value)
                    <tr class="searchable">
                        <td>{{ $value->title }}</td>
                        <td>{{ $value->slug }}</td>
                        <td class="actions">
                                <div class="action">
                                <a href="{{ route('product-categories.edit', ['product_category'=>$value->id]) }}">
                                    <i class="fas fa-pencil-alt update"></i>
                                </a>
                            </div>
                            <div class="action">
                                <form id="deleteForm_{{ $value->id }}" action="{{ route('product-categories.destroy', ['product_category' => $value->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button" onclick="deleteItem({{ $value->id }}, 'product category');">
                                        <i class="fas fa-trash-alt delete"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <x-sweetalert></x-sweetalert>
</x-admin>
