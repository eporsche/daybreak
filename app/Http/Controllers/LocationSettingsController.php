<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocationSettingsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (Gate::denies('view', $request->user()->currentLocation)) {
            abort(403);
        }

        return view('locations.show', [
            'user' => $request->user()
        ]);
    }
}
