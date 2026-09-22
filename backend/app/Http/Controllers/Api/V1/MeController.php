<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ReservationResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Reservation;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    public function reservations(Request $request)
    {
        $v = $request->validate([
            'status' => ['sometimes', 'string', 'in:pending_payment,paid,confirmed,expired,cancelled'],
        ]);

        $query = Reservation::where('user_id', $request->user()->id)->with(['cabin', 'payments'])
            ->orderByDesc('created_at');

        if (! empty($v['status'])) {
            $query->where('status', $v['status']);
        }

        return ReservationResource::collection($query->paginate(15));
    }
}
