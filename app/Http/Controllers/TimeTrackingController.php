<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('time_trackings.index');
    }
}
