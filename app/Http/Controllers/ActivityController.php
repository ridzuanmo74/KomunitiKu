<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->latest('activity_date');

        if ($request->filled('association_id')) {
            $query->where('association_id', (int) $request->integer('association_id'));
        }

        if (! $request->user()->isSuperAdmin()) {
            $query->whereIn('association_id', $request->user()->associations()->pluck('associations.id'));
        }

        return response()->json($query->paginate(10));
    }

    public function store(StoreActivityRequest $request)
    {
        $activity = Activity::create($request->validated() + ['created_by' => $request->user()->id]);

        return response()->json($activity, 201);
    }

    public function show(Activity $activity)
    {
        $this->authorize('view', $activity);

        return response()->json($activity);
    }

    public function update(Request $request, Activity $activity)
    {
        $this->authorize('update', $activity);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'activity_date' => ['sometimes', 'date'],
        ]);

        $activity->update($validated);

        return response()->json($activity);
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);
        $activity->delete();

        return response()->json(status: 204);
    }
}
