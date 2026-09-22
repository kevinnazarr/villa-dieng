<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/** Shape-only: guest checkout input. Capacity/dates/overlap stay in the action. */
class StoreReservationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cabin_id' => ['required', 'uuid', 'exists:cabins,id'],
            'check_in' => ['required', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['sometimes', 'integer', 'min:0'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email:rfc', 'max:255'],
            'guest_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'special_request' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'idempotency_key' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
