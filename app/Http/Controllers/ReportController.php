<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('reports.show', [
            'employee' => $request->user()
        ]);
    }
}
