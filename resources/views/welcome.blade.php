@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            {{-- Pass both product data AND the login status --}}
            <product-card :product="{{ json_encode($product) }}"
                :is-logged-in="{{ json_encode(auth()->check()) }}"></product-card>
        @empty
            {{-- Empty State - Spans full width --}}
            <div class="col-span-full flex min-h-[400px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-12 text-center">
                <div class="mb-4 rounded-full bg-slate-100 p-4 text-slate-400">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">No Listings Found</h3>
                <p class="mt-1 text-sm text-slate-500">Looks like there are no active gaming accounts in this category yet.</p>
                
                <a href="{{ url('/') }}" class="mt-6 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Clear all filters
                </a>
            </div>
        @endforelse
    </div>
@endsection