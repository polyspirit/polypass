<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\GroupTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CredentialResource;
use App\Http\Resources\CredentialSummaryResource;
use App\Models\Credential;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CredentialController extends Controller
{
    protected $modelClassName = Credential::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API

    /**
     * List without secrets. Filters: group_id, favorite, search.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'group_id' => ['integer'],
            'favorite' => ['boolean'],
            'search' => ['string', 'max:127'],
        ]);

        $credentials = Credential::where('user_id', $request->user()->id)
            ->when($request->has('group_id'), fn ($q) => $q->where('group_id', $request->input('group_id')))
            ->when($request->has('favorite'), fn ($q) => $q->where('favorite', $request->boolean('favorite')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->orderBy('name')
            ->get();

        return CredentialSummaryResource::collection($credentials);
    }

    public function store(Request $request): CredentialResource
    {
        $data = $request->validate(array_merge($this->validationRules($request), [
            'name' => ['required', 'string', 'max:127', 'min:1'],
            'login' => ['required', 'string', 'max:127', 'min:1'],
            'password' => ['required', 'string', 'max:127', 'min:1'],
        ]));

        $data['user_id'] = $request->user()->id;
        $data['group_id'] ??= Group::where('type', GroupTypeEnum::Root->value)->value('id');
        $remote = $this->pullRemote($data);

        $credential = DB::transaction(function () use ($data, $remote) {
            $credential = Credential::create(Credential::encryptAttributes($data));

            if ($remote) {
                $credential->remote()->create($remote);
            }

            return $credential;
        });

        return new CredentialResource($credential->refresh());
    }

    public function show(Credential $credential): CredentialResource
    {
        return new CredentialResource($credential);
    }

    /**
     * Partial update. Pass "remote": null to remove remote access info.
     */
    public function update(Request $request, Credential $credential): CredentialResource
    {
        $data = $request->validate($this->validationRules($request));

        $hasRemote = array_key_exists('remote', $data);
        $remote = $this->pullRemote($data);

        DB::transaction(function () use ($credential, $data, $hasRemote, $remote) {
            $credential->update(Credential::encryptAttributes($data));

            if (!$hasRemote) {
                return;
            }

            if ($remote) {
                $credential->remote()->updateOrCreate([], $remote);
            } else {
                $credential->remote()->delete();
            }
        });

        return new CredentialResource($credential->refresh());
    }

    public function destroy(Credential $credential): Response
    {
        DB::transaction(function () use ($credential) {
            $credential->remote()->delete();
            $credential->delete();
        });

        return response()->noContent();
    }


    // OTHER

    private function validationRules(Request $request): array
    {
        return [
            'group_id' => [
                'integer',
                Rule::exists('groups', 'id')->where(fn ($q) => $q
                    ->where('type', GroupTypeEnum::Root->value)
                    ->orWhere(fn ($q) => $q
                        ->where('type', GroupTypeEnum::Credential->value)
                        ->where('user_id', $request->user()->id))),
            ],
            'name' => ['string', 'max:127', 'min:1'],
            'login' => ['string', 'max:127', 'min:1'],
            'password' => ['string', 'max:127', 'min:1'],
            'url' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'favorite' => ['boolean'],
            'remote' => ['nullable', 'array'],
            'remote.host' => ['required_with:remote', 'string', 'max:255'],
            'remote.port' => ['required_with:remote', 'integer', 'between:1,65535'],
            'remote.protocol' => ['required_with:remote', 'string', 'max:63'],
        ];
    }

    /**
     * Remove remote info from credential data and return it.
     */
    private function pullRemote(array &$data): ?array
    {
        $remote = $data['remote'] ?? null;
        unset($data['remote']);

        return $remote;
    }
}
