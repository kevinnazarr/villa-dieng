<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CancelReservationAction;
use App\Actions\CreateReservationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GuestAccessRequest;
use App\Http\Requests\Api\V1\StoreReservationRequest;
use App\Http\Resources\Api\V1\ReservationResource;
use App\Models\Reservation;

class ReservationController extends Controller
{
    /** Guest checkout → 201. Domain throws 409/422 via the exception renderer. */
    public function store(StoreReservationRequest $request, CreateReservationAction $action)
    {
        $data = $request->validated();

        // Header wins when both are provided.
        if ($request->header('Idempotency-Key')) {
            $data['idempotency_key'] = $request->header('Idempotency-Key');
        }

        $reservation = $action->execute($data);
        $reservation->load(['cabin', 'payments', 'events']);

        return (new ReservationResource($reservation))->response()->setStatusCode(201);
    }

    /** Code + email lookup; mismatch → 404. */
    public function show(Reservation $reservation, GuestAccessRequest $request)
    {
        $this->assertGuestOwner($reservation, $request->validated()['guest_email']);
        $reservation->load(['cabin', 'payments', 'events']);

        return new ReservationResource($reservation);
    }

    public function cancel(Reservation $reservation, GuestAccessRequest $request, CancelReservationAction $action)
    {
        $this->assertGuestOwner($reservation, $request->validated()['guest_email']);

        $reservation = $action->execute($reservation);
        $reservation->load(['cabin', 'payments', 'events']);

        return new ReservationResource($reservation);
    }

    private function assertGuestOwner(Reservation $reservation, string $email): void
    {
        if (strtolower(trim($reservation->guest_email)) !== strtolower(trim($email))) {
            abort(404);
        }
    }
}
