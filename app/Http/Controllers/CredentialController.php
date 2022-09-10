<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Credential;
use App\Models\Remote;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CredentialController extends Controller
{
    protected $modelClassName = Credential::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API
    public function index(): \Illuminate\Contracts\View\View
    {
        $groupRoot = Group::where('name', 'root')->first();
        $credentials = Credential::where(['group_id' => $groupRoot->id])->get();
        $this->checkItemsPolicy($credentials);

        $groups = Group::where('name', '!=', 'root')->get();
        $this->checkItemsPolicy($groups);

        return view(
            'pages.credentials.list',
            [
                'groups' => $groups,
                'credentials' => $credentials,
                'title' => __('credentials.list')
            ]
        );
    }

    public function create(Request $request): \Illuminate\Contracts\View\View
    {
        $breadcrumbs = ['/credentials/' => __('entities.credentials')];
        if ($request->has('group_id')) {
            $group = Group::find($request->input('group_id'));
            if (!$group->isRoot()) {
                $breadcrumbs['/groups/' . $group->id] = $group->name;
            }
        }

        return view(
            'pages.credentials.create',
            [
                'groups' => $this->getGroupsOptions(),
                'group_id' => $request->input('group_id'),
                'breadcrumbs' => $breadcrumbs,
                'title' => __('credentials.create')
            ]
        );
    }

    public function store(Request $request): \Illuminate\Contracts\View\View
    {
        $request->merge(['user_id' => auth()->user()->id]);
        $request->merge(['favorite' => $request->has('favorite')]);
        
        $validationRules = [
            'group_id' => ['integer'],
            'name' => ['required', 'string', 'max:127', 'min:1'],
            'login' => ['required', 'string', 'max:127', 'min:1'],
            'password' => ['required', 'string', 'max:127', 'min:1'],
            'favorite' => ['boolean']
        ];

        if ($request->has('remote')) {
            $validationRules = array_merge($validationRules, [
                'host' => ['string', 'max:255'],
                'port' => ['numeric'],
                'protocol' => ['string', 'max:63']
            ]);
        }

        $request->validate($validationRules);

        if (!$request->has('group_id')) {
            $groupRoot = Group::where('name', 'root')->first();
            $request->merge(['group_id' => $groupRoot->id]);
        }

        $this->encryptData($request);

        $credential = Credential::create($request->all());

        if ($request->has('remote')) {
            $credential->remote()->create([
                'host' => $request->input('host'),
                'port' => $request->input('port'),
                'protocol' => $request->input('protocol')
            ]);
        }

        return $this->show($credential);
    }

    public function show(Credential $credential): \Illuminate\Contracts\View\View
    {
        return view(
            'pages.credentials.detail',
            [
                'groups' => $this->getGroupsOptions(),
                'credential' => $credential,
                'breadcrumbs' => $this->getBreadcrumbs($credential),
                'title' => $credential->name
            ]
        );
    }

    public function edit(Credential $credential): \Illuminate\Contracts\View\View
    {
        $this->decryptData($credential);

        return view(
            'pages.credentials.edit',
            [
                'groups' => $this->getGroupsOptions(),
                'credential' => $credential,
                'breadcrumbs' => $this->getBreadcrumbs($credential),
                'title' => __('global.edit') . ' ' . $credential->name
            ]
        );
    }

    public function update(Request $request, Credential $credential): \Illuminate\Http\RedirectResponse
    {
        $request->merge(['favorite' => $request->has('favorite')]);

        $validationRules = [
            'group_id' => ['integer'],
            'name' => ['string', 'max:127', 'min:1'],
            'login' => ['string', 'max:127', 'min:1'],
            'password' => ['string', 'max:127', 'min:1'],
            'favorite' => ['boolean']
        ];

        if ($request->has('remote')) {
            $validationRules = array_merge($validationRules, [
                'host' => ['string', 'max:255'],
                'port' => ['numeric'],
                'protocol' => ['string', 'max:63']
            ]);
        }

        $request->validate($validationRules);

        if ($request->has('login') || $request->has('password')) {
            $this->encryptData($request);
        }

        $credential->update($request->all());

        if ($request->has('remote')) {
            $remote = Remote::find($credential->remote->id);
            $remote->update([
                'host' => $request->input('host'),
                'port' => $request->input('port'),
                'protocol' => $request->input('protocol')
            ]);
        }

        return redirect()->route(
            'credentials.edit',
            ['credential' => $credential]
        )->with('status', __('credentials.message-updated'));
    }

    public function destroy(Credential $credential): \Illuminate\Http\RedirectResponse
    {
        if ($credential->remote) {
            $credential->remote->delete();
        }
        $credential->delete();

        return redirect()->route('credentials.index');
    }


    // OTHER

    private function getGroupsOptions(): array
    {
        $groupsOptions = [];
        foreach (Group::all() as $group) {
            $groupsOptions[$group->id] = $group->name;
        }

        return $groupsOptions;
    }

    private function encryptData(Request &$request): Request
    {
        if ($request->has('login')) {
            $request->merge(['login' => Crypt::encryptString($request->login)]);
        }

        if ($request->has('password')) {
            $request->merge(['password' => Crypt::encryptString($request->password)]);
        }

        return $request;
    }

    private function getBreadcrumbs(Credential $credential): array
    {
        $breadcrumbs = ['/credentials/' => __('entities.credentials')];
        if (!$credential->group->isRoot()) {
            $breadcrumbs['/groups/' . $credential->group->id] = $credential->group->name;
        }

        return $breadcrumbs;
    }
}
