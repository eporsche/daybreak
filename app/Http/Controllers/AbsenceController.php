<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('absences.index');
    }
}
