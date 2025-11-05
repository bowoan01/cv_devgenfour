<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Project::query()->with(['tags', 'images'])->withCount('images')->orderByDesc('created_at');

            return DataTables::of($query)
                ->addColumn('actions', fn (Project $project) => $project->id)
                ->addColumn('tag_ids', fn (Project $project) => $project->tags->pluck('id'))
                ->toJson();
        }

        $tags = \App\Models\Tag::orderBy('name')->get();

        return view('admin.projects.index', compact('tags'));
    }

    public function store(ProjectRequest $request): JsonResponse
    {
        $project = new Project($this->prepareData($request));

        if ($request->hasFile('cover_image')) {
            $project->cover_image = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $project->save();
        $this->syncRelations($request, $project);

        return response()->json(['message' => 'Project created', 'data' => $project->fresh(['tags'])]);
    }

    public function update(ProjectRequest $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $project->fill($this->prepareData($request));

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }
            $project->cover_image = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $project->save();
        $this->syncRelations($request, $project);

        return response()->json(['message' => 'Project updated', 'data' => $project->fresh(['tags'])]);
    }

    public function destroy(int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }
        $project->delete();

        return response()->json(['message' => 'Project deleted']);
    }

    public function reorder(Request $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $orders = $request->input('order', []);

        foreach ($orders as $imageId => $order) {
            ProjectImage::where('project_id', $project->id)
                ->where('id', $imageId)
                ->update(['display_order' => (int) $order]);
        }

        return response()->json(['message' => 'Images reordered']);
    }

    protected function prepareData(ProjectRequest $request): array
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?? $data['title']);
        $stackInput = $request->input('tech_stack');
        if (is_string($stackInput)) {
            $items = preg_split('/[,\n]+/', $stackInput);
        } elseif (is_array($stackInput)) {
            $items = $stackInput;
        } else {
            $items = [];
        }
        $data['tech_stack'] = array_values(array_filter(array_map('trim', $items)));
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        return $data;
    }

    protected function syncRelations(ProjectRequest $request, Project $project): void
    {
        $tags = $request->input('tags', []);
        $project->tags()->sync($tags);
    }
}
