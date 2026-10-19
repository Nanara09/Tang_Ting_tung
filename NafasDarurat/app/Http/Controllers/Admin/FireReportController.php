<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FireReport;
use Illuminate\Http\Request;

class FireReportController extends Controller
{
    public function update(Request $request, FireReport $fireReport)
    {
        $data = $request->validate(['state' => ['required', 'in:approved,rejected']]);

        $fireReport->state = $data['state'];
        $fireReport->save();

        return back();
    }

    public function destroy(FireReport $fireReport)
    {
        $fireReport->delete();

        return back();
    }
}
