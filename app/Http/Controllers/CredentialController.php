<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Remote;
use Illuminate\Http\Request;

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
        return view('pages.credentials.list', ['credentials' => Credential::all(), 'title' => __('credentials.list')]);
    }

    public function create(): \Illuminate\Contracts\View\View
    {
        return view('pages.credentials.create', ['title' => __('credentials.create')]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:127', 'min:1'],
            'login' => ['required', 'string', 'max:127', 'min:1'],
            'password' => ['required', 'string', 'max:127', 'min:1']
        ];

        if ($request->has('remote')) {
            $validationRules = array_merge($validationRules, [
                'host' => ['string', 'max:255'],
                'port' => ['numeric'],
                'protocol' => ['string', 'max:63']
            ]);
        }

        $request->validate($validationRules);

        $credential = Credential::create($request->all());

        if ($request->has('remote')) {
            $remote = $credential->remote()->create([
                'host' => $request->input('host'),
                'port' => $request->input('port'),
                'protocol' => $request->input('protocol')
            ]);
        }

        return redirect()->route('credentials.index');
    }

    public function show(Credential $credential): \Illuminate\Contracts\View\View
    {
        return view('pages.credentials.detail', ['credential' => $credential, 'title' => __('credentials.detail')]);
    }

    public function edit(Credential $credential): \Illuminate\Contracts\View\View
    {
        return view('pages.credentials.edit', ['credential' => $credential, 'title' => __('credentials.edit')]);
    }

    public function update(Request $request, Credential $credential): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => ['string', 'max:127', 'min:1'],
            'login' => ['string', 'max:127', 'min:1'],
            'password' => ['string', 'max:127', 'min:1']
        ]);

        $credential->update($request->all());

        return redirect()->route('credentials.edit', ['credential' => $credential])->with('status', __('credentials.message-updated'));
    }

    public function destroy(Credential $credential): \Illuminate\Http\RedirectResponse
    {
        if ($credential->remote) {
            $credential->remote->delete();
        }
        $credential->delete();
        
        return redirect()->route('credentials.index');
    }
}
