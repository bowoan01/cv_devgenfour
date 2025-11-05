<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectImageRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectImageController extends Controller
{
    public function store(ProjectImageRequest $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $path = $request->file('image')->store('projects/gallery', 'public');
        $nextOrder = ($project->images()->max('display_order') ?? 0) + 1;

        $image = $project->images()->create([
            'path' => $path,
            'caption' => $request->input('caption'),
            'display_order' => $nextOrder,
        ]);

        return response()->json(['message' => 'Image uploaded', 'data' => $image]);
    }

    public function destroy(int $imageId): JsonResponse
    {
        $image = ProjectImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Image removed']);
    }
}
