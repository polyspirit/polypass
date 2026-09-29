<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\GroupTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Http\Resources\NoteSummaryResource;
use App\Models\Group;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class NoteController extends Controller
{
    protected $modelClassName = Note::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API

    /**
     * List without content. Filters: group_id, favorite, search.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'group_id' => ['integer'],
            'favorite' => ['boolean'],
            'search' => ['string', 'max:127'],
        ]);

        $notes = Note::where('user_id', $request->user()->id)
            ->when($request->has('group_id'), fn ($q) => $q->where('group_id', $request->input('group_id')))
            ->when($request->has('favorite'), fn ($q) => $q->where('favorite', $request->boolean('favorite')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->orderBy('name')
            ->get();

        return NoteSummaryResource::collection($notes);
    }

    public function store(Request $request): NoteResource
    {
        $data = $request->validate(array_merge($this->validationRules($request), [
            'name' => ['required', 'string', 'max:127', 'min:1'],
        ]));

        $data['user_id'] = $request->user()->id;
        $data['group_id'] ??= Group::where('type', GroupTypeEnum::Root->value)->value('id');

        return new NoteResource(Note::create($data)->refresh());
    }

    public function show(Note $note): NoteResource
    {
        return new NoteResource($note);
    }

    public function update(Request $request, Note $note): NoteResource
    {
        $note->update($request->validate($this->validationRules($request)));

        return new NoteResource($note);
    }

    public function destroy(Note $note): Response
    {
        $note->delete();

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
                        ->where('type', GroupTypeEnum::Note->value)
                        ->where('user_id', $request->user()->id))),
            ],
            'name' => ['string', 'max:127', 'min:1'],
            'note' => ['nullable', 'string'],
            'favorite' => ['boolean'],
        ];
    }
}
