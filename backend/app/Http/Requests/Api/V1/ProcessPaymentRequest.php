<?php

namespace App\Http\Requests\Api\V1;

use App\Services\SandboxPaymentGateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Shape-only: guest email + sandbox scenario. Production rejects scenario (422). */
class ProcessPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'guest_email' => ['required', 'email:rfc', 'max:255'],
            'scenario' => app()->isProduction()
                ? ['prohibited']
                : ['sometimes', Rule::in([
                    SandboxPaymentGateway::SCENARIO_SUCCESS,
                    SandboxPaymentGateway::SCENARIO_FAIL,
                    SandboxPaymentGateway::SCENARIO_TIMEOUT,
                ])],
        ];
    }
}
