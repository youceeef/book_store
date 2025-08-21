<x-app-layout>
    <x-slot name="header">
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
            {{-- Checkout Form --}}
            <div class="lg:col-span-7 xl:col-span-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 md:p-8 text-gray-900">
                        {{-- Session Messages --}}<h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Checkout') }}
                        </h2>
    </x-slot>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
            {{ session('error') }}
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Progress Bar --}}
            <div class="mb-8">
                <div class="max-w-3xl mx-auto">
                    <div class="relative">
                        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200"></div>
                        <div class="relative flex justify-between">
                            <div class="step-item">
                                <div
                                    class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-medium relative z-10">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div class="mt-2 text-sm font-medium text-indigo-600">Cart</div>
                            </div>
                            <div class="step-item">
                                <div
                                    class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-medium relative z-10">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div class="mt-2 text-sm font-medium text-indigo-600">Checkout</div>
                            </div>
                            <div class="step-item">
                                <div
                                    class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-medium relative z-10">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="mt-2 text-sm font-medium text-gray-600">Confirmation</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                {{-- Checkout Form --}}
                <div class="w-full">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="p-6 md:p-8 text-gray-900">
                            {{-- Session Messages --}}
                            @include('partials._flash_messages')

                            {{-- Payment Form --}}
                            <form id="payment-form" action="{{ route('checkout.process') }}" method="POST"
                                class="space-y-8">
                                @csrf

                                {{-- Billing Information --}}
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-6">Billing Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="col-span-2 md:col-span-1">
                                            <label for="card-name" class="block text-sm font-medium text-gray-700 mb-1">
                                                Full Name
                                            </label>
                                            <input type="text" name="card-name" id="card-name" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                placeholder="John Doe">
                                        </div>
                                        <div class="col-span-2 md:col-span-1">
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                                Email Address
                                            </label>
                                            <input type="email" name="email" id="email" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                placeholder="john@example.com">
                                        </div>
                                        <div class="col-span-2">
                                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                                Billing Address
                                            </label>
                                            <input type="text" name="address" id="address" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                placeholder="1234 Main St">
                                        </div>
                                        <div class="col-span-2 md:col-span-1">
                                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                                City
                                            </label>
                                            <input type="text" name="city" id="city" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="col-span-2 md:col-span-1">
                                            <label for="postal-code"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                ZIP / Postal Code
                                            </label>
                                            <input type="text" name="postal-code" id="postal-code" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Method --}}
                                <div class="mt-10">
                                    <h3 class="text-lg font-medium text-gray-900 mb-6">Payment Method</h3>
                                    <div class="mt-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                                        <div id="card-element" class="min-h-[40px]">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>
                                        <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                                    </div>
                                </div>

                                {{-- Payment Form --}}
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900 mb-6">Payment Information</h3>

                                    <form id="payment-form" action="{{ route('checkout.process') }}" method="POST"
                                        class="space-y-6">
                                        @csrf

                                        {{-- Card Details --}}
                                        <div class="space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="col-span-2">
                                                    <label for="card-name"
                                                        class="block text-sm font-medium text-gray-700">Cardholder
                                                        Name</label>
                                                    <input type="text" name="card-name" id="card-name"
                                                        value="{{ auth()->user()->name }}"
                                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm"
                                                        placeholder="Name on card">
                                                </div>

                                                <div class="col-span-2">
                                                    <label for="card-element"
                                                        class="block text-sm font-medium text-gray-700">Card
                                                        Details</label>
                                                    <div id="card-element"
                                                        class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                                        <!-- Stripe Elements will be inserted here -->
                                                    </div>
                                                    <div id="card-errors" class="mt-2 text-sm text-red-600"
                                                        role="alert"></div>
                                                </div>

                                                <div class="col-span-2">
                                                    <label for="email"
                                                        class="block text-sm font-medium text-gray-700">Email for
                                                        Receipt</label>
                                                    <input type="email" name="email" id="email"
                                                        value="{{ auth()->user()->email }}"
                                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 cursor-not-allowed sm:text-sm"
                                                        readonly>
                                                </div>

                                                <div class="col-span-2">
                                                    <div class="relative flex items-start">
                                                        <div class="flex items-center h-5">
                                                            <input id="save_card" name="save_card" type="checkbox"
                                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                        </div>
                                                        <div class="ml-3 text-sm">
                                                            <label for="save_card"
                                                                class="font-medium text-gray-700">Save card for future
                                                                purchases</label>
                                                            <p class="text-gray-500">Your payment information will be
                                                                stored securely</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        Add City, Country, Postcode etc. if needed
                                        Remember to add these fields to the Order model/migration and controller if you
                                        collect them.
                                        --}}

                                        {{-- Stripe Card Element --}}
                                        <div class="mb-4">
                                            <label for="card-element" class="block text-sm font-medium text-gray-700">
                                                Credit or debit card
                                            </label>
                                            <div id="card-element"
                                                class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm">
                                                <!-- A Stripe Element will be inserted here. -->
                                            </div>
                                            <!-- Used to display form errors. -->
                                            <div id="card-errors" role="alert" class="mt-2 text-sm text-red-600">
                                            </div>
                                        </div>

                                        {{-- Hidden input to store the Payment Method ID --}}
                                        <input type="hidden" name="payment_method" id="payment_method">

                                        {{-- Submit Button --}}
                                        <div class="mt-6">
                                            <button id="submit-button" type="submit"
                                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                                <span id="button-text">Pay ${{ number_format($cartTotal, 2) }}</span>
                                                <svg id="spinner"
                                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Include Stripe.js --}}
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            const stripeKey = "{{ $stripeKey }}"; // Get key from controller
            const clientSecret = "{{ $intentClientSecret }}"; // Get intent secret from controller
            const paymentForm = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');
            const cardErrors = document.getElementById('card-errors');
            const paymentMethodInput = document.getElementById('payment_method');

            const stripe = Stripe(stripeKey);

            // Style the Card Element (Optional)
            const style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            // Create an instance of the card Element.
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: style,
                hidePostalCode: true
            }); // hidePostalCode is optional

            // Add an instance of the card Element into the `card-element` <div>.
            cardElement.mount('#card-element');

            // Handle real-time validation errors from the card Element.
            cardElement.on('change', function(event) {
                if (event.error) {
                    cardErrors.textContent = event.error.message;
                } else {
                    cardErrors.textContent = '';
                }
                // Optionally enable/disable submit button based on completeness
                submitButton.disabled = event.empty || !event.complete;
            });

            // Handle form submission.
            paymentForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                setLoading(true);

                // Create PaymentMethod using the Card Element and the SetupIntent's client secret
                const {
                    setupIntent,
                    error
                } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: cardElement,
                            // billing_details: { name: document.getElementById('name').value } // Add billing details if needed
                        }
                    }
                );

                if (error) {
                    // Inform the user if there was an error.
                    cardErrors.textContent = error.message;
                    console.error("Stripe Error:", error);
                    setLoading(false);
                } else {
                    // Check SetupIntent status
                    if (setupIntent.status === 'succeeded') {
                        // Card setup succeeded. The PaymentMethod ID is setupIntent.payment_method
                        console.log("SetupIntent Succeeded. PaymentMethod ID:", setupIntent.payment_method);
                        cardErrors.textContent = '';
                        // Add the payment method ID to the form and submit
                        paymentMethodInput.value = setupIntent.payment_method;
                        paymentForm.submit(); // Submit the form to your backend (/checkout route)
                    } else {
                        cardErrors.textContent = 'Card setup failed. Status: ' + setupIntent.status;
                        console.error("SetupIntent Status:", setupIntent.status);
                        setLoading(false);
                    }
                }
            });

            // Helper function to show/hide loading state
            function setLoading(isLoading) {
                if (isLoading) {
                    // Disable button and show spinner
                    submitButton.disabled = true;
                    spinner.classList.remove('hidden');
                    buttonText.classList.add('hidden');
                } else {
                    // Enable button and hide spinner
                    submitButton.disabled = false;
                    spinner.classList.add('hidden');
                    buttonText.classList.remove('hidden');
                }
            }
        </script>
</x-app-layout>
