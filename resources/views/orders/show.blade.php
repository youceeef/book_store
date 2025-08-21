<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} #{{ $order->id }}
            </h2>
            <a href="{{ route('orders.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                ← Back to My Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b">
                    <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        <div>
                            <p><strong>Total Amount:</strong> <span
                                    class="font-semibold">${{ number_format($order->total_amount, 2) }}</span></p>
                            <p><strong>Status:</strong>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($order->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('paid')
                                        @case('processing') bg-blue-100 text-blue-800 @break
                                        @case('shipped') bg-green-100 text-green-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('failed') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            {{-- Optional: Billing/Shipping Address display if collected --}}
                            {{-- <p><strong>Billing Address:</strong> {{ $order->billing_address ?: 'N/A' }}</p> --}}
                            {{-- <p><strong>Shipping Address:</strong> {{ $order->shipping_address ?: 'N/A' }}</p> --}}
                            <p><strong>Stripe Ref:</strong> <code
                                    class="text-xs">{{ $order->stripe_payment_intent_id ?: 'N/A' }}</code></p>
                        </div>
                    </div>
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
                                        Price</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($order->items as $item)
                                    {{-- Loop through eager loaded items --}}
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-16 w-12 mr-4">
                                                    {{-- Use book relationship loaded within the item --}}
                                                    <img class="h-16 w-auto object-contain rounded"
                                                        src="{{ $item->book?->cover_image ?: asset('images/covers/default.png') }}"
                                                        alt="{{ $item->book?->title }}">
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <a href="{{ $item->book ? route('books.show', $item->book->slug) : '#' }}"
                                                            class="hover:text-blue-600">
                                                            {{ $item->book?->title ?? 'Book not found' }}
                                                        </a>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $item->book?->author ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($item->price, 2) }}</td> {{-- Price at time of purchase --}}
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
                            <tfoot>
                                <tr>
                                    <td colspan="3"
                                        class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">Total
                                    </td>
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
</x-app-layout>
