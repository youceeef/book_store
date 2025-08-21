{{-- resources/views/admin/coupons/_form.blade.php --}}

@csrf {{-- CSRF token --}}

{{-- Code --}}
<div class="mb-4">
    <label for="code" class="block text-sm font-medium text-gray-700">Coupon Code <span
            class="text-red-600">*</span></label>
    <input type="text" name="code" id="code" value="{{ old('code', $coupon->code ?? '') }}" required
        maxlength="50"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('code') border-red-500 @enderror"
        placeholder="e.g., SUMMER20, 10OFFNOW">
    @error('code')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Type --}}
<div class="mb-4">
    <label for="type" class="block text-sm font-medium text-gray-700">Discount Type <span
            class="text-red-600">*</span></label>
    <select name="type" id="type" required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('type') border-red-500 @enderror">
        <option value="">Select Type...</option>
        <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)
        </option>
        <option value="percent" {{ old('type', $coupon->type ?? '') == 'percent' ? 'selected' : '' }}>Percentage (%)
        </option>
    </select>
    @error('type')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Value --}}
<div class="mb-4">
    <label for="value" class="block text-sm font-medium text-gray-700">Discount Value <span
            class="text-red-600">*</span></label>
    <input type="number" name="value" id="value" value="{{ old('value', $coupon->value ?? '') }}" required
        min="0" step="0.01"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('value') border-red-500 @enderror"
        placeholder="e.g., 10.00 (for Fixed) or 15 (for Percent)">
    <p class="mt-1 text-xs text-gray-500">Enter dollar amount for 'Fixed' (e.g., 5.50) or percentage for 'Percent'
        (e.g., 10 for 10%).</p>
    @error('value')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Minimum Amount --}}
<div class="mb-4">
    <label for="min_amount" class="block text-sm font-medium text-gray-700">Minimum Cart Amount ($)</label>
    <input type="number" name="min_amount" id="min_amount" value="{{ old('min_amount', $coupon->min_amount ?? '') }}"
        min="0" step="0.01"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('min_amount') border-red-500 @enderror"
        placeholder="Optional: e.g., 50.00">
    <p class="mt-1 text-xs text-gray-500">Leave blank if no minimum purchase amount is required.</p>
    @error('min_amount')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Expires At --}}
<div class="mb-4">
    <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiration Date</label>
    <input type="date" name="expires_at" id="expires_at"
        value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d') : '') }}"
        min="{{ now()->format('Y-m-d') }}" {{-- Prevent selecting past dates --}}
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('expires_at') border-red-500 @enderror">
    <p class="mt-1 text-xs text-gray-500">Leave blank if the coupon should never expire.</p>
    @error('expires_at')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Usage Limit --}}
<div class="mb-4">
    <label for="usage_limit" class="block text-sm font-medium text-gray-700">Usage Limit (Total)</label>
    <input type="number" name="usage_limit" id="usage_limit"
        value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" min="0" step="1"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('usage_limit') border-red-500 @enderror"
        placeholder="Optional: e.g., 100">
    <p class="mt-1 text-xs text-gray-500">Leave blank for unlimited uses.</p>
    @error('usage_limit')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Submit Button Area --}}
<div class="flex justify-end mt-6 pt-4 border-t">
    <a href="{{ route('admin.coupons.index') }}"
        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
        Cancel
    </a>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
        {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
    </button>
</div>
