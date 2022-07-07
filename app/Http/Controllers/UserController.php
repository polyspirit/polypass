<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        return view('pages.users.list', ['users' => User::all(), 'title' => __('entities.users')]);
    }

    public function show(User $user): \Illuminate\Contracts\View\View
    {
        return view('pages.users.profile', ['user' => $user, 'title' => __('users.profile')]);
    }

    public function edit(User $user): \Illuminate\Contracts\View\View
    {
        return view('pages.users.edit', ['user' => $user, 'title' => __('users.edit')]);
    }

    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $exceptFields = ['role'];

        $rules = [
            'name' => ['string', 'max:255', 'min:2'],
            'role' => ['string']
        ];

        if ($request->has('email') && ($request->input('email') != $user->email)) {
            $rules['email'] = ['string', 'email', 'max:255', 'unique:users,email'];
        }

        if ($request->has('password') && !empty($request->input('password'))) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        } else {
            $exceptFields[] = 'password';
            $exceptFields[] = 'password_confirmation';
        }

        $request->validate($rules);

        if (isset($rules['password'])) {
            $request->merge(['password' => Hash::make($request->input('password'))]);

            $exceptFields[] = 'password_confirmation';
        }

        $redirectRoute = redirect()->route('users.edit', ['user' => $user]);

        if (\Auth::user()->can('changeRole', $user)) {
            $user->removeRole($user->getRoleNames()->first());
            $user->assignRole($request->input('role'));
        } else {
            return $redirectRoute->withErrors(['role' => 'You can not change role of this user']);
        }

        $user->update($request->except($exceptFields));

        return $redirectRoute->with('status', 'User was successfully updated.');
    }

    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index');
    }

}
