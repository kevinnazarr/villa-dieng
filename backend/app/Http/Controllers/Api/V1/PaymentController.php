<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\InitiatePaymentAction;
use App\Actions\ProcessPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InitiatePaymentRequest;
use App\Http\Requests\Api\V1\ProcessPaymentRequest;
use App\Http\Resources\Api\V1\PaymentResource;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\SandboxPaymentGateway;

class PaymentController extends Controller
{
    /** Reuses the open attempt when one exists (idempotent). */
    public function initiate(Reservation $reservation, InitiatePaymentRequest $request, InitiatePaymentAction $action)
    {
        $v = $request->validated();
        $this->assertGuestOwner($reservation, $v['guest_email']);

        $meta = [];

        if ($request->header('Idempotency-Key')) {
            $meta['idempotency_key'] = $request->header('Idempotency-Key');
        } elseif (! empty($v['idempotency_key'])) {
            $meta['idempotency_key'] = $v['idempotency_key'];
        }

        $payment = $action->execute($reservation, $meta);

        return new PaymentResource($payment);
    }

    /** Guest-owned charge. Scenario runs only outside production (request rule). */
    public function process(Payment $payment, ProcessPaymentRequest $request, ProcessPaymentAction $action)
    {
        $v = $request->validated();
        $reservation = $payment->reservation;
        $this->assertGuestOwner($reservation, $v['guest_email']);

        $payment = $action->execute($payment, $v['scenario'] ?? SandboxPaymentGateway::SCENARIO_SUCCESS);

        return new PaymentResource($payment);
    }

    private function assertGuestOwner(Reservation $reservation, string $email): void
    {
        if (strtolower(trim($reservation->guest_email)) !== strtolower(trim($email))) {
            abort(404);
        }
    }
}
