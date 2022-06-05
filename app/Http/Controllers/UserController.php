<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $modelClassName = User::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }

    
    // API

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('pages.users.list', ['users' => User::all(), 'title' => 'Users List']);
    }

    public function show(User $user): \Illuminate\Contracts\View\View
    {
        return view('pages.users.profile', ['user' => $user, 'title' => 'User Profile']);
    }

    public function edit(User $user): \Illuminate\Contracts\View\View
    {
        return view('pages.users.edit', ['user' => $user, 'title' => 'User Edit']);
    }

    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => ['string', 'max:255', 'min:2'],
            'email' => ['string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['string', 'min:8'],
            'role' => ['string']
        ]);

        $redirectRoute = redirect()->route('users.edit', ['user' => $user]);

        if (\Auth::user()->can('changeRole', $user)) {
            $user->removeRole($user->getRoleNames()->first());
            $user->assignRole($request->input('role'));
        } else {
            return $redirectRoute->withErrors(['role' => 'You can not change role of this user']);
        }

        $user->update($request->except(['role']));

        return $redirectRoute->with('status', 'User was successfully updated.');
    }

    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index');
    }

}
