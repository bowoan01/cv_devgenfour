<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Service::query()->orderBy('display_order'))
                ->addColumn('actions', fn (Service $service) => $service->id)
                ->toJson();
        }

        return view('admin.services.index');
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        $data = $this->prepareData($request->validated());
        $service = Service::create($data);

        return response()->json(['message' => 'Service created', 'data' => $service]);
    }

    public function update(ServiceRequest $request, int $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->update($this->prepareData($request->validated()));

        return response()->json(['message' => 'Service updated', 'data' => $service]);
    }

    public function destroy(int $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted']);
    }

    protected function prepareData(array $data): array
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['title']);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['display_order'] = $data['display_order'] ?? 0;

        return $data;
    }
}
