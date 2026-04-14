<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query()->latest('published_at');

        if ($request->filled('association_id')) {
            $query->where('association_id', (int) $request->integer('association_id'));
        }

        if (! $request->user()->isSuperAdmin()) {
            $query->whereIn('association_id', $request->user()->associations()->pluck('associations.id'));
        }

        return response()->json($query->paginate(10));
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $announcement = Announcement::create($request->validated() + ['created_by' => $request->user()->id]);

        return response()->json($announcement, 201);
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        return response()->json($announcement);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $announcement->update($validated);

        return response()->json($announcement);
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        $announcement->delete();

        return response()->json(status: 204);
    }
}
