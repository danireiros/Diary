<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;

class NoteController extends Controller
{
    public function index()
    {
        return NoteResource::collection(Note::query()->latest()->get());
    }

    public function store(StoreNoteRequest $request)
    {
        $note = Note::create($request->validated());
        return new NoteResource($note);
    }

    public function show(Note $note)
    {
        return new NoteResource($note);
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update($request->validated());
        return new NoteResource($note);
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return response()->noContent();
    }
}


