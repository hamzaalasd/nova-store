<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'shipping_method_id' => ['nullable', 'integer', 'exists:shipping_methods,id'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'coupon_code' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.district' => ['nullable', 'string', 'max:100'],
            'shipping_address.street' => ['nullable', 'string', 'max:255'],
            'shipping_address.building_number' => ['nullable', 'string', 'max:50'],
            'shipping_address.postal_code' => ['nullable', 'string', 'max:50'],
            'shipping_address.notes' => ['nullable', 'string', 'max:1000'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
