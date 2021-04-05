<?php

namespace App\Http\Controllers;

use App\Daybreak;
use Illuminate\Http\Request;

class CurrentLocationController extends Controller
{
    /**
     * Update the authenticated user's current location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $location = Daybreak::newLocationModel()->findOrFail($request->location_id);

        if (! $request->user()->switchLocation($location)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }
}
