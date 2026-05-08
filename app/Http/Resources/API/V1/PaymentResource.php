<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_method_id' => $this->payment_method_id,
            'payment_number' => $this->payment_number,
            'gateway' => $this->gateway,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'amount_base' => $this->amount_base,
            'amount_display' => $this->amount_display,
            'currency_code' => $this->currency_code,
            'paid_at' => $this->paid_at,
        ];
    }
}
