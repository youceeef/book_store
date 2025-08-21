<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} #{{ $order->id }} (Admin View)
            </h2>
            <a href="{{ route('admin.orders.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                ← Back to All Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @include('partials._flash_messages')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b">
                    <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        {{-- Column 1: Basic Info --}}
                        <div>
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                            <p><strong>Status:</strong>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($order->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('paid')
                                        @case('processing') bg-blue-100 text-blue-800 @break
                                        @case('shipped') bg-purple-100 text-purple-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('failed') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                        {{-- Column 2: Customer Info --}}
                        <div>
                            <p><strong>Customer:</strong> {{ $order->user?->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $order->user?->email ?? 'N/A' }}</p>
                            <p><strong>Stripe Ref:</strong> <code
                                    class="text-xs">{{ $order->stripe_payment_intent_id ?: 'N/A' }}</code></p>
                        </div>
                        {{-- Column 3: Pricing Info --- << UPDATED >> --- --}}
                        <div>
                            @php
                                // Calculate subtotal by adding total and discount
                                // Assumes total_amount is the final discounted price
                                $discount = $order->discount_amount ?? 0;
                                $subtotal = $order->total_amount + $discount;
                            @endphp
                            <p><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
                            @if ($order->coupon_code)
                                <p class="text-green-600">
                                    <strong>Coupon Applied:</strong> {{ $order->coupon_code }}
                                </p>
                                <p class="text-green-600">
                                    <strong>Discount:</strong> -${{ number_format($discount, 2) }}
                                </p>
                            @else
                                <p><strong>Discount:</strong> $0.00</p>
                            @endif
                            <p class="border-t mt-1 pt-1"><strong>Total Amount:</strong> <span
                                    class="font-semibold">${{ number_format($order->total_amount, 2) }}</span></p>
                        </div>
                        {{-- Optional: Billing/Shipping Address display --}}
                        {{-- <div class="md:col-span-3"> ... Address Info ... </div> --}}
                    </div>
                    {{-- Optional: Add Status Update Form Here Later --}}
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Items Ordered</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price (at purchase)</th> {{-- Clarified header --}}
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item Subtotal</th> {{-- Clarified header --}}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-16 w-12 mr-4">
                                                    <img class="h-16 w-auto object-contain rounded"
                                                        src="{{ $item->book?->cover_image ?: asset('images/covers/default.png') }}"
                                                        alt="{{ $item->book?->title }}">
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->book?->title ?? 'Book not found' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500"> ID: {{ $item->book_id }} |
                                                        {{ $item->book?->author ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($item->price, 2) }}</td> {{-- Price from order_items table --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No items found
                                            for this order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            {{-- Footer: Display Subtotal, Discount, Total --}}
                            <tfoot>
                                {{-- Calculate Subtotal from Items --}}
                                @php
                                    $itemsSubtotal = $order->items->sum(function ($item) {
                                        return $item->price * $item->quantity;
                                    });
                                @endphp
                                <tr>
                                    <td colspan="3"
                                        class="px-6 py-2 text-right text-sm font-medium text-gray-500 uppercase">Item
                                        Subtotal</td>
                                    <td class="px-6 py-2 text-left text-sm font-medium text-gray-900">
                                        ${{ number_format($itemsSubtotal, 2) }}</td>
                                </tr>
                                @if ($order->coupon_code)
                                    <tr class="text-green-600">
                                        <td colspan="3" class="px-6 py-2 text-right text-sm font-medium uppercase">
                                            Discount ({{ $order->coupon_code }})</td>
                                        <td class="px-6 py-2 text-left text-sm font-medium">-
                                            ${{ number_format($order->discount_amount ?? 0, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="font-semibold border-t">
                                    <td colspan="3"
                                        class="px-6 py-3 text-right text-sm font-medium text-gray-900 uppercase">Grand
                                        Total</td>
                                    <td class="px-6 py-3 text-left text-sm font-medium text-gray-900">
                                        ${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
