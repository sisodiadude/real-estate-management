<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Fetch states based on selected country (RESTful way)
     */
    public function index($country_id)
    {
        $states = State::where('country_id', $country_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($states);
    }
}
