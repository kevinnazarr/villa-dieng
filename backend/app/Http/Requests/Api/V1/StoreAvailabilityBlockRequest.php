<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/** Shape-only: block range + cabin. Overlap stays in the EXCLUDE constraint. */
class StoreAvailabilityBlockRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cabin_id' => ['required', 'uuid', 'exists:cabins,id'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after:starts_on'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
