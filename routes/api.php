<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiaryCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\OccurrenceController;

Route::apiResources([
    'categories' => CategoryController::class,
    'diary-categories' => DiaryCategoryController::class,
    'tasks' => TaskController::class,
    'notes' => NoteController::class,
    'journal-entries' => JournalEntryController::class,
]);

Route::get('occurrences', [OccurrenceController::class, 'index']);


