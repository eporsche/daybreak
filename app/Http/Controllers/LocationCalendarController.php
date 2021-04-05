<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationCalendarController extends Controller
{
    public function show(Request $request)
    {
        return view('calendars.show', [
            'employee' => $request->user()
        ]);
    }
}
