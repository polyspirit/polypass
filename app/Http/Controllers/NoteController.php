<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Crypt;
use App\Enums\GroupTypeEnum;
use App\Models\Group;
use App\Models\Note;

class NoteController extends Controller
{
    protected $modelClassName = Note::class;

    public function __construct()
    {
        $this->checkAuthorization();
    }


    // API
    public function index(): View
    {
        $groupRoot = Group::where('type', GroupTypeEnum::Root->value)->first();
        $notes = Note::where(['group_id' => $groupRoot->id])->orderBy('name', 'asc')->get();
        $this->checkItemsPolicy($notes);

        $groups = Group::where('type', GroupTypeEnum::Note->value)->get();
        $this->checkItemsPolicy($groups);

        return view(
            'pages.notes.list',
            [
                'groups' => $groups,
                'notes' => $notes,
                'title' => __('notes.list')
            ]
        );
    }

    public function create(Request $request): View
    {
        $breadcrumbs = ['/notes/' => __('entities.notes')];
        if ($request->has('group_id')) {
            $group = Group::find($request->input('group_id'));
            if (!$group->isRoot()) {
                $breadcrumbs['/groups/' . $group->id] = $group->name;
            }
        }

        return view(
            'pages.notes.create',
            [
                'groups' => $this->getGroupsOptions(),
                'group_id' => $request->input('group_id'),
                'breadcrumbs' => $breadcrumbs,
                'title' => __('notes.create')
            ]
        );
    }

    public function store(Request $request): View
    {
        $request->merge(['user_id' => auth()->user()->id]);
        $request->merge(['favorite' => $request->has('favorite')]);

        $validationRules = [
            'group_id' => ['integer'],
            'name' => ['required', 'string', 'max:127', 'min:1'],
            'note' => ['nullable', 'string'],
            'favorite' => ['boolean']
        ];

        $request->validate($validationRules);

        if (!$request->has('group_id')) {
            $groupRoot = Group::where('type', GroupTypeEnum::Root->value)->first();
            $request->merge(['group_id' => $groupRoot->id]);
        }

        $this->encryptData($request);

        $note = Note::create($request->all());

        return $this->show($note);
    }

    public function show(Note $note): View
    {
        return view(
            'pages.notes.detail',
            [
                'groups' => $this->getGroupsOptions(),
                'note' => $note,
                'breadcrumbs' => $this->getBreadcrumbs($note),
                'title' => $note->name
            ]
        );
    }

    public function edit(Note $note): View
    {
        return view(
            'pages.notes.edit',
            [
                'groups' => $this->getGroupsOptions(),
                'note' => $note,
                'breadcrumbs' => $this->getBreadcrumbs($note),
                'title' => __('global.edit') . ' ' . $note->name
            ]
        );
    }

    public function update(Request $request, Note $note): RedirectResponse
    {
        $request->merge(['favorite' => $request->has('favorite')]);

        $validationRules = [
            'group_id' => ['integer'],
            'name' => ['string', 'max:127', 'min:1'],
            'note' => ['nullable', 'string'],
            'favorite' => ['boolean']
        ];

        $request->validate($validationRules);
        $this->encryptData($request);

        $note->update($request->all());

        return redirect()->route(
            'notes.edit',
            ['note' => $note]
        )->with('status', __('notes.message-updated'));
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return redirect()->route('notes.index');
    }


    // OTHER

    private function getGroupsOptions(): array
    {
        $groups = Group::where('type', GroupTypeEnum::Note->value)
            ->orWhere('type', GroupTypeEnum::Root->value)
            ->get();

        $groupsOptions = [];
        foreach ($groups as $group) {
            $groupsOptions[$group->id] = $group->name;
        }

        return $groupsOptions;
    }

    private function encryptData(Request &$request): Request
    {
        if ($request->has('note') && !empty($request->note)) {
            // Validate that note is valid JSON
            $noteData = $request->note;
            if (is_string($noteData)) {
                $decoded = json_decode($noteData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->merge(['note' => Crypt::encryptString($noteData)]);
                } else {
                    // If not valid JSON, treat as plain text
                    $request->merge(['note' => Crypt::encryptString($noteData)]);
                }
            }
        }

        return $request;
    }

    private function getBreadcrumbs(Note $note): array
    {
        $breadcrumbs = ['/notes/' => __('entities.notes')];
        if (!$note->group->isRoot()) {
            $breadcrumbs['/groups/' . $note->group->id] = $note->group->name;
        }

        return $breadcrumbs;
    }
}
