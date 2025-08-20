<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;

class JournalEntryController extends Controller
{
    public function index()
    {
        return JournalEntryResource::collection(JournalEntry::query()->latest()->get());
    }

    public function store(StoreJournalEntryRequest $request)
    {
        $entry = JournalEntry::create($request->validated());
        return new JournalEntryResource($entry);
    }

    public function show(JournalEntry $journalEntry)
    {
        return new JournalEntryResource($journalEntry);
    }

    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry)
    {
        $journalEntry->update($request->validated());
        return new JournalEntryResource($journalEntry);
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $journalEntry->delete();
        return response()->noContent();
    }
}


