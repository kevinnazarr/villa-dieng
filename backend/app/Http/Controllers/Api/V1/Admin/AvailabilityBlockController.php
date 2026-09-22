<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAvailabilityBlockRequest;
use App\Http\Resources\Api\V1\AvailabilityBlockResource;
use App\Models\AvailabilityBlock;
use Illuminate\Http\Request;

class AvailabilityBlockController extends Controller
{
    public function index(Request $request)
    {
        $v = $request->validate([
            'cabin_id' => ['sometimes', 'uuid', 'exists:cabins,id'],
        ]);

        $query = AvailabilityBlock::orderBy('starts_on');

        if (! empty($v['cabin_id'])) {
            $query->where('cabin_id', $v['cabin_id']);
        }

        return AvailabilityBlockResource::collection($query->paginate(15));
    }

    public function store(StoreAvailabilityBlockRequest $request)
    {
        $v = $request->validated();

        $block = AvailabilityBlock::create([
            ...$v,
            'created_by' => $request->user()->id,
        ]);

        return (new AvailabilityBlockResource($block))->response()->setStatusCode(201);
    }

    public function destroy(AvailabilityBlock $block)
    {
        $block->delete();

        return response()->noContent();
    }
}
