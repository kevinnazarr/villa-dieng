<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\CancelReservationAction;
use App\Actions\ConfirmReservationAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $v = $request->validate([
            'status' => ['sometimes', 'string', 'in:pending_payment,paid,confirmed,expired,cancelled'],
            'cabin_id' => ['sometimes', 'uuid', 'exists:cabins,id'],
        ]);

        $query = Reservation::with(['cabin', 'payments'])->orderByDesc('created_at');

        if (! empty($v['status'])) {
            $query->where('status', $v['status']);
        }

        if (! empty($v['cabin_id'])) {
            $query->where('cabin_id', $v['cabin_id']);
        }

        return ReservationResource::collection($query->paginate(15));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['cabin', 'payments', 'events']);

        return new ReservationResource($reservation);
    }

    public function confirm(Reservation $reservation, ConfirmReservationAction $action)
    {
        $reservation = $action->execute($reservation, request()->user());
        $reservation->load(['cabin', 'payments', 'events']);

        return new ReservationResource($reservation);
    }

    public function cancel(Reservation $reservation, CancelReservationAction $action)
    {
        $reservation = $action->execute($reservation, request()->user());
        $reservation->load(['cabin', 'payments', 'events']);

        return new ReservationResource($reservation);
    }
}
