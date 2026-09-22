<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/** Shape-only: cabin UUID + [) date range. Overlap stays in AvailabilityChecker. */
class AvailabilityCheckRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cabin_id' => ['required', 'uuid', 'exists:cabins,id'],
            'check_in' => ['required', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
        ];
    }
}
