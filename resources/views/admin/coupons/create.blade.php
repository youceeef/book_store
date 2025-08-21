<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Coupon') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8"> {{-- Adjusted max-width for form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form method="POST" action="{{ route('admin.coupons.store') }}">
                        @include('admin.coupons._form') {{-- Include the form partial --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
