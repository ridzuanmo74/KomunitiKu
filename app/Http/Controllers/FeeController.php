<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeRequest;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validated();
        if (($validated['frequency'] ?? null) !== Fee::FREQUENCY_MONTHLY) {
            $validated['due_day'] = null;
        }

        $fee = Fee::create($validated);

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
            'frequency' => ['sometimes', 'string', Rule::in([Fee::FREQUENCY_ONE_TIME, Fee::FREQUENCY_MONTHLY, Fee::FREQUENCY_YEARLY])],
            'due_day' => ['nullable', 'integer', 'between:1,31'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $effectiveFrequency = $validated['frequency'] ?? $fee->frequency;
        if ($effectiveFrequency !== Fee::FREQUENCY_MONTHLY) {
            $validated['due_day'] = null;
        } elseif (! array_key_exists('due_day', $validated)) {
            $validated['due_day'] = $fee->due_day;
        }

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
