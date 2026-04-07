@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
            {{-- Pass the product data as a prop --}}
            <product-card :product="{{ json_encode($product) }}"></product-card>
        @endforeach
    </div>
@endsection