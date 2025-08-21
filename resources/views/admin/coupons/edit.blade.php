<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Coupon:') }} <span class="font-mono bg-gray-100 px-1 rounded">{{ $coupon->code }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8"> {{-- Adjusted max-width for form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}">
                        @method('PUT') {{-- Method spoofing for PUT request --}}
                        @include('admin.coupons._form', ['coupon' => $coupon]) {{-- Include partial, passing the $coupon --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
