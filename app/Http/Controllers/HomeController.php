<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credential;
use App\Models\Group;

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
        $groupRoot = Group::where('name', 'root')->first();
        $credentials = Credential::where(['group_id' => $groupRoot->id])->get();
        $this->checkItemsPolicy($credentials);

        $groups = Group::where('name', '!=', 'root')->get();
        $this->checkItemsPolicy($groups);

        $favorites = Credential::where('favorite', true)->get();
        $this->checkItemsPolicy($favorites);

        return view('home', [
            'user' => auth()->user(),
            'groups' => $groups,
            'credentials' => $credentials,
            'favorites' => $favorites
        ]);
    }
}