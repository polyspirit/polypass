<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\GroupTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\Remote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    protected $modelClassName = Group::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API

    /**
     * Own groups. Filter: type (credential|note).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate(['type' => [Rule::in($this->allowedTypes())]]);

        $groups = Group::where('user_id', $request->user()->id)
            ->where('type', '!=', GroupTypeEnum::Root->value)
            ->when($request->has('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return GroupResource::collection($groups);
    }

    /**
     * Shared root group: items without a group belong to it.
     */
    public function root(): GroupResource
    {
        return new GroupResource(Group::where('type', GroupTypeEnum::Root->value)->firstOrFail());
    }

    public function store(Request $request): GroupResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:127', 'min:1'],
            'type' => ['required', Rule::in($this->allowedTypes())],
        ]);

        $data['user_id'] = $request->user()->id;

        return new GroupResource(Group::create($data));
    }

    /**
     * Group with its items (without secrets).
     */
    public function show(Group $group): GroupResource
    {
        return new GroupResource($group->load([
            'credentials' => fn ($q) => $q->with('remote')->orderBy('name'),
            'notes' => fn ($q) => $q->orderBy('name'),
        ]));
    }

    public function update(Request $request, Group $group): GroupResource
    {
        $group->update($request->validate([
            'name' => ['string', 'max:127', 'min:1'],
            'type' => [Rule::in($this->allowedTypes())],
        ]));

        return new GroupResource($group);
    }

    /**
     * Delete group with all its items.
     */
    public function destroy(Group $group): Response
    {
        DB::transaction(function () use ($group) {
            Remote::whereIn('credential_id', $group->credentials()->select('id'))->delete();
            $group->credentials()->delete();
            $group->notes()->delete();
            $group->delete();
        });

        return response()->noContent();
    }


    // OTHER

    private function allowedTypes(): array
    {
        return [GroupTypeEnum::Credential->value, GroupTypeEnum::Note->value];
    }
}
