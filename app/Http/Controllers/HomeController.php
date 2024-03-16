<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Credential;
use App\Models\Group;

use App\Enums\GroupTypeEnum;

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
        $groupRoot = Group::where('type', GroupTypeEnum::Root->value)->first();
        $credentials = Credential::where(['group_id' => $groupRoot->id])->orderBy('name', 'asc')->get();
        $this->checkItemsPolicy($credentials);

        $groups = Group::where('type',  GroupTypeEnum::Credential->value)->get();
        $this->checkItemsPolicy($groups);

        $favorites = Credential::where('favorite', true)->orderBy('name', 'asc')->get();
        $this->checkItemsPolicy($favorites);

        return view('home', [
            'user' => auth()->user(),
            'groups' => $groups,
            'credentials' => $credentials,
            'favorites' => $favorites
        ]);
    }

    public function theme(string $theme)
    {
        return back()->withCookie(cookie()->forever('theme', $theme));
    }
}
