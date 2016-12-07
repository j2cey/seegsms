<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Test;

class TestController extends Controller
{
    /**
     * Get all tests.
     *
     * @return JSON
     */
    public function getIndex()
    {
        $tests = Test::all();// User::all();

        return response()->success(compact('tests'));
    }
}
