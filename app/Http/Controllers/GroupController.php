<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $modelClassName = Group::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API
    public function index(): \Illuminate\Contracts\View\View
    {
        return view(
            'pages.groups.list', 
            [
                'groups' => Group::where('name', '!=', 'root')->get(),
                'title' => __('entities.groups')
            ]
        );
    }

    public function create(): \Illuminate\Contracts\View\View
    {
        return view('pages.groups.create', ['title' => __('groups.create')]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:127', 'min:1']
        ];

        $request->validate($validationRules);

        Group::create($request->all());

        return redirect()->route('groups.index');
    }

    public function show(Group $group): \Illuminate\Contracts\View\View
    {
        return view('pages.groups.detail', ['group' => $group, 'title' => __('entities.group')]);
    }

    public function edit(Group $group): \Illuminate\Contracts\View\View
    {
        return view(
            'pages.groups.edit',
            [
                'group' => $group,
                'title' => __('groups.edit')
            ]
        );
    }

    public function update(Request $request, Group $group): \Illuminate\Http\RedirectResponse
    {
        $validationRules = [
            'name' => ['string', 'max:127', 'min:1']
        ];

        $request->validate($validationRules);

        $group->update($request->all());

        return redirect()->route('groups.edit', ['group' => $group])->with('status', __('groups.message-updated'));
    }

    public function destroy(Group $group): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('groups.index');
    }
}
