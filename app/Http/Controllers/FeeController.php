<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeRequest;
use App\Models\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::query()->latest();

        if ($request->filled('association_id')) {
            $query->where('association_id', (int) $request->integer('association_id'));
        }

        if (! $request->user()->isSuperAdmin()) {
            $query->whereIn('association_id', $request->user()->associations()->pluck('associations.id'));
        }

        return response()->json($query->paginate(10));
    }

    public function store(StoreFeeRequest $request)
    {
        $fee = Fee::create($request->validated());

        return response()->json($fee, 201);
    }

    public function show(Fee $fee)
    {
        $this->authorize('view', $fee);

        return response()->json($fee);
    }

    public function update(Request $request, Fee $fee)
    {
        $this->authorize('update', $fee);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'due_day' => ['nullable', 'integer', 'between:1,31'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $fee->update($validated);

        return response()->json($fee);
    }

    public function destroy(Fee $fee)
    {
        $this->authorize('delete', $fee);
        $fee->delete();

        return response()->json(status: 204);
    }
}
