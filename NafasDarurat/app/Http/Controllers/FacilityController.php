<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\JsonResponse;

class FacilityController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Facility::orderBy('city')->orderBy('name')->get(['type', 'name', 'city', 'address', 'hours'])
        );
    }
}
