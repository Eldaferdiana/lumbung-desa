<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $todo = Auth::user()->get();
        return response()->json(['status' => true,'result' => $todo]);
    }

    //
}
