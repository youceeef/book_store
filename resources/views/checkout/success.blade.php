<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-2xl font-semibold text-green-600 mb-4">Thank You!</h3>
                    <p class="text-lg mb-6">Your payment was successful and your order has been placed.</p>

                    {{-- Display success message from session --}}
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded inline-block">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p class="mb-4">An order confirmation may be sent to your email address.</p>
                    {{-- Optional: Link to order history --}}
                    <a href="{{ route('orders.index') }}" {{-- Assuming orders.index route for user history --}}
                        class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 mr-2">
                        View My Orders
                    </a>
                    <a href="{{ route('books.index') }}"
                        class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
