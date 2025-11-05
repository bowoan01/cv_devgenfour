<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ContactMessage::query()->latest();

            return DataTables::of($query)
                ->addColumn('actions', fn (ContactMessage $message) => $message->id)
                ->toJson();
        }

        return view('admin.contacts.index');
    }

    public function show(int $id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);

        return response()->json($message);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->update([
            'status' => 'handled',
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);

        return response()->json(['message' => 'Message marked as handled']);
    }

    public function destroy(int $id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json(['message' => 'Message deleted']);
    }
}
