<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Fetch states based on selected state (RESTful way)
     */
    public function index($state_id)
    {
        $cities = City::where('state_id', $state_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($cities);
    }
}
