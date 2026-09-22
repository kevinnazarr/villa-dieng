<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\AvailabilityChecker;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AvailabilityCheckRequest;

class AvailabilityController extends Controller
{
    public function show(AvailabilityCheckRequest $request)
    {
        $v = $request->validated();

        return response()->json(['data' => [
            'cabin_id' => $v['cabin_id'],
            'check_in' => $v['check_in'],
            'check_out' => $v['check_out'],
            'available' => AvailabilityChecker::isAvailable($v['cabin_id'], $v['check_in'], $v['check_out']),
        ]]);
    }
}
