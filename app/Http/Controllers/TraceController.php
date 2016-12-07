<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Trace;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Input;
use Validator;
use App\Tracestep;

class TraceController extends Controller
{
    public function getIndex()
    {
        $traces = Trace::all();

        return response()->success(compact('traces'));
    }

    public function getShow($id)
    {
        $trace = Trace::find($id);

        //return response()->success($steps);
        return response()->success(compact('trace'));
    }

    public function getSteps()
    {
        $steps = Tracestep::all();

        return response()->success(compact('steps'));
    }

    public function getTracestepsShow($id)
    {
        $trace = Trace::find($id);
        $steps = Tracestep::where('trace_id', '=', $id)
            ->orderBy('id', 'asc')
            ->get();

        return response()->success(compact('trace', 'steps'));
    }
}
