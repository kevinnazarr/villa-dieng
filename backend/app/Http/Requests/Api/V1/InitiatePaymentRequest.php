<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/** Shape-only: optional idempotency keys. Reuse logic stays in InitiatePaymentAction. */
class InitiatePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'guest_email' => ['required', 'email:rfc', 'max:255'],
            'idempotency_key' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
