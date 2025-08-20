<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\RecurrenceService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class OccurrenceController extends Controller
{
    public function index(Request $request, RecurrenceService $recurrenceService)
    {
        $validated = $request->validate([
            'start' => ['required','date'],
            'end' => ['required','date','after_or_equal:start'],
            'category_ids' => ['nullable','array'],
            'category_ids.*' => ['string','exists:categories,id'],
        ]);

        $start = CarbonImmutable::parse($validated['start'], 'Atlantic/Canary');
        $end = CarbonImmutable::parse($validated['end'], 'Atlantic/Canary');

        $query = Task::query();
        if (!empty($validated['category_ids'])) {
            $query->whereIn('category_id', $validated['category_ids']);
        }

        $tasks = $query->get();
        $occurrences = $recurrenceService->expandMany($tasks, $start, $end);

        return response()->json($occurrences);
    }
}


