<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/** Guest identity for code-scoped endpoints: email validated separately, mismatch → 404. */
class GuestAccessRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'guest_email' => ['required', 'email:rfc', 'max:255'],
        ];
    }
}
