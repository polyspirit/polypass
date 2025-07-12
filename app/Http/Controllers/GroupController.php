<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\Group;
use App\Enums\GroupTypeEnum;

class GroupController extends Controller
{
    protected $modelClassName = Group::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API
    public function index(Request $request): View
    {
        $groupsBuilder = Group::where('type', '!=', GroupTypeEnum::Root->value);

        if ($request->has('type')) {
            $groupsBuilder->where('type', $request->type)->orderBy('type', 'asc');
        }

        $groups = $groupsBuilder->orderBy('name', 'asc')->get();

        $this->checkItemsPolicy($groups);

        return view(
            'pages.groups.list',
            [
                'groups' => $groups,
                'title' => __('entities.groups')
            ]
        );
    }

    public function create(): View
    {
        return view(
            'pages.groups.create',
            [
                'breadcrumbs' => $this->getBreadcrumbs(),
                'title' => __('groups.create')
            ]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['user_id' => auth()->user()->id]);

        $request->validate([
            'name' => ['required', 'string', 'max:127', 'min:1'],
            'type' => ['required', 'string', 'max:127', 'min:1']
        ]);

        Group::create($request->all());

        return redirect()->route('groups.index');
    }

    public function show(Group $group): View
    {
        $group->load(['credentials', 'notes']);

        return view(
            'pages.groups.detail',
            [
                'group' => $group,
                'breadcrumbs' => $this->getBreadcrumbs(),
                'title' => $group->name
            ]
        );
    }

    public function edit(Group $group): View
    {
        return view(
            'pages.groups.edit',
            [
                'group' => $group,
                'breadcrumbs' => $this->getBreadcrumbs(),
                'title' => __('groups.edit') . ' ' . $group->name
            ]
        );
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $validationRules = [
            'name' => ['string', 'max:127', 'min:1'],
            'type' => ['string', 'max:127', 'min:1']
        ];

        $request->validate($validationRules);

        $group->update($request->all());

        return redirect()->route('groups.edit', ['group' => $group])->with('status', __('groups.message-updated'));
    }

    public function destroy(Group $group): RedirectResponse
    {
        if ($group->credentials) {
            $group->credentials()->delete();
        }

        if ($group->notes) {
            $group->notes()->delete();
        }

        $group->delete();

        return redirect()->route('groups.index');
    }


    // OTHER

    private function getBreadcrumbs(): array
    {
        return ['/groups/' => __('entities.groups')];
    }
}
