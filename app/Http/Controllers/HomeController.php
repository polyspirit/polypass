<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credential;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $credentials = Credential::where('favorite', true)->get();

        return view('home', [
            'user' => auth()->user(),
            'credentials' => $credentials
        ]);
    }
}