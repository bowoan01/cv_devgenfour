<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(TeamMember::query()->orderBy('order_index'))
                ->addColumn('actions', fn (TeamMember $member) => $member->id)
                ->toJson();
        }

        return view('admin.team.index');
    }

    public function store(TeamMemberRequest $request): JsonResponse
    {
        $data = $this->prepareData($request);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('team', 'public');
        }

        $member = TeamMember::create($data);

        return response()->json(['message' => 'Team member created', 'data' => $member]);
    }

    public function update(TeamMemberRequest $request, int $id): JsonResponse
    {
        $member = TeamMember::findOrFail($id);
        $data = $this->prepareData($request);

        if ($request->hasFile('photo')) {
            if ($member->photo_path) {
                Storage::disk('public')->delete($member->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('team', 'public');
        }

        $member->update($data);

        return response()->json(['message' => 'Team member updated', 'data' => $member]);
    }

    public function destroy(int $id): JsonResponse
    {
        $member = TeamMember::findOrFail($id);
        if ($member->photo_path) {
            Storage::disk('public')->delete($member->photo_path);
        }
        $member->delete();

        return response()->json(['message' => 'Team member removed']);
    }

    protected function prepareData(TeamMemberRequest $request): array
    {
        $data = $request->validated();
        $social = collect($request->input('social_links', []))
            ->filter(fn ($link) => filled(Arr::get($link, 'url')))
            ->map(fn ($link) => [
                'label' => Arr::get($link, 'label'),
                'url' => Arr::get($link, 'url'),
            ])->values()->all();

        $data['social_links'] = $social;
        $data['is_visible'] = (bool) ($data['is_visible'] ?? false);
        $data['order_index'] = $data['order_index'] ?? 0;

        unset($data['photo']);

        return $data;
    }
}
