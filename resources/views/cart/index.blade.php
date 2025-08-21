{{-- resources/views/cart/index.blade.php --}}
<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cart
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Session Messages --}}
                    @include('partials._flash_messages')

                    @if ($cartItems->isEmpty())
                        {{-- Empty cart message --}}
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
                            <p class="mt-2 text-gray-500">Looks like you haven't added anything to your cart yet.</p>
                            <a href="{{ route('books.index') }}"
                                class="mt-6 inline-block bg-indigo-600 px-6 py-3 rounded-md text-white font-medium hover:bg-indigo-700 transition duration-150">
                                Browse Books
                            </a>
                        </div>
                    @else
                        {{-- Cart Items Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Product </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Price </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Quantity </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Total </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($cartItems as $item)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-4">
                                                    <div
                                                        class="flex-shrink-0 h-20 w-16 overflow-hidden rounded-lg shadow-sm">
                                                        <img class="h-20 w-16 object-cover transform hover:scale-105 transition duration-200"
                                                            src="{{ asset($item->attributes->image) }}"
                                                            alt="{{ $item->name }}">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <a href="{{ route('books.show', $item->attributes->slug) }}"
                                                            class="text-base font-medium text-gray-900 hover:text-indigo-600 truncate block">
                                                            {{ $item->name }}
                                                        </a>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            {{ Str::limit($item->attributes->description ?? '', 50) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-base font-medium text-gray-900">
                                                    ${{ number_format($item->price, 2) }}
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">per unit</div>
                                            </td>
                                            <td class="px-6 py-4"> {{-- Update Quantity Form --}}
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST"
                                                    class="flex items-center space-x-3" x-data="{ quantity: {{ $item->quantity }} }">
                                                    @csrf @method('PATCH')
                                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                                        <button type="button" @click="if(quantity > 1) quantity--"
                                                            class="p-2 hover:bg-gray-100 rounded-l-lg">
                                                            <svg class="w-4 h-4 text-gray-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M20 12H4" />
                                                            </svg>
                                                        </button>
                                                        <input type="number" name="quantity" x-model="quantity"
                                                            min="1" max="{{ $item->attributes->stock ?? 99 }}"
                                                            class="w-16 border-0 text-center focus:ring-0 text-base"
                                                            @change="quantity = Math.max(1, Math.min(quantity, {{ $item->attributes->stock ?? 99 }}))">
                                                        <button type="button" @click="quantity++"
                                                            class="p-2 hover:bg-gray-100 rounded-r-lg">
                                                            <svg class="w-4 h-4 text-gray-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v12M6 12h12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <button type="submit"
                                                        class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-full transition duration-150"
                                                        title="Update Quantity">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                @if ($item->attributes->stock && $item->quantity > $item->attributes->stock)
                                                    <p class="text-red-500 text-xs mt-1">Only
                                                        {{ $item->attributes->stock }} available</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-base font-semibold text-gray-900">
                                                    ${{ number_format($item->getPriceSum(), 2) }}
                                                </div>
                                                @if ($item->quantity > 1)
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        (${{ number_format($item->price, 2) }} ×
                                                        {{ $item->quantity }})
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                {{-- Remove Item Form --}}
                                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST"
                                                    class="inline-flex" x-data="{ showConfirm: false }"
                                                    @submit.prevent="if(showConfirm || confirm('Are you sure you want to remove this item?')) $el.submit()">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition duration-150"
                                                        title="Remove Item">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Coupon & Summary Area --}}
                        <div class="mt-8 md:grid md:grid-cols-3 md:gap-8">
                            {{-- Coupon Form --}}
                            <div class="md:col-span-1">
                                <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Have a Coupon?</h3>
                                    @if (!session()->has('coupon'))
                                        <form action="{{ route('coupon.store') }}" method="POST">
                                            @csrf
                                            <div class="flex flex-col space-y-3">
                                                <input type="text" name="coupon_code"
                                                    placeholder="Enter your coupon code" required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder-gray-400">
                                                <button type="submit"
                                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150 text-sm flex items-center justify-center space-x-2">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                    <span>Apply Coupon</span>
                                                </button>
                                            </div>
                                            @error('coupon_code')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </form>
                                    @else
                                        {{-- Display applied coupon & remove button --}}
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-green-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3 w-full">
                                                    <p class="text-sm font-medium text-green-800">
                                                        Coupon Applied: {{ session('coupon')['code'] }}
                                                    </p>
                                                    <form action="{{ route('coupon.destroy') }}" method="POST"
                                                        class="mt-2"
                                                        onsubmit="return confirm('Are you sure you want to remove this coupon?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-sm text-red-600 hover:text-red-800 font-medium">
                                                            Remove Coupon
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Spacer --}}
                                <div class="hidden md:block"></div>

                                {{-- Cart Summary --}}
                                <div class="md:col-span-1 md:ml-auto">
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                                        @php
                                            $subtotal = Cart::getSubTotal(false);
                                            $discount = 0;
                                            $couponCode = null;
                                            if (session()->has('coupon')) {
                                                $coupon = session('coupon');
                                                $couponModel = \App\Models\Coupon::where(
                                                    'code',
                                                    $coupon['code'],
                                                )->first();
                                                if ($couponModel) {
                                                    $discount = $couponModel->calculateDiscount($subtotal);
                                                    $couponCode = $coupon['code'];
                                                } else {
                                                    session()->forget('coupon');
                                                }
                                            }
                                            $newTotal = max(0, $subtotal - $discount);
                                        @endphp

                                        <div class="space-y-4">
                                            <div class="flex justify-between text-base text-gray-600">
                                                <span>Subtotal</span>
                                                <span>${{ number_format($subtotal, 2) }}</span>
                                            </div>

                                            @if ($discount > 0)
                                                <div class="flex justify-between text-base text-green-600">
                                                    <span>Discount ({{ $couponCode }})</span>
                                                    <span>-${{ number_format($discount, 2) }}</span>
                                                </div>
                                            @endif

                                            <div class="border-t border-gray-200 pt-4 mt-4">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-base font-medium text-gray-900">Order
                                                        Total</span>
                                                    <span
                                                        class="text-xl font-semibold text-gray-900">${{ number_format($newTotal, 2) }}</span>
                                                </div>
                                            </div>

                                            <a href="{{ route('checkout.index') }}"
                                                class="mt-6 block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-4 px-6 rounded-lg transition duration-150 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <span>Proceed to Checkout</span>
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                    </svg>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Clear Cart Form --}}
                            <div class="mt-8 border-t pt-6">
                                <form action="{{ route('cart.clear') }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to clear your entire shopping cart?');"
                                    class="flex justify-center">
                                    @csrf
                                    <button type="submit"
                                        class="group flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-600 hover:text-red-600 hover:bg-red-50 transition duration-150">
                                        <svg class="h-5 w-5 transform group-hover:rotate-12 transition-transform duration-150"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="font-medium">Clear Shopping Cart</span>
                                    </button>
                                </form>
                            </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
