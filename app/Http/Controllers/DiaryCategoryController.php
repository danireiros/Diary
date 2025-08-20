<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiaryCategoryRequest;
use App\Http\Requests\UpdateDiaryCategoryRequest;
use App\Http\Resources\DiaryCategoryResource;
use App\Models\DiaryCategory;

class DiaryCategoryController extends Controller
{
    public function index()
    {
        return DiaryCategoryResource::collection(DiaryCategory::query()->latest()->get());
    }

    public function store(StoreDiaryCategoryRequest $request)
    {
        $category = DiaryCategory::create($request->validated());
        return new DiaryCategoryResource($category);
    }

    public function show(DiaryCategory $diaryCategory)
    {
        return new DiaryCategoryResource($diaryCategory);
    }

    public function update(UpdateDiaryCategoryRequest $request, DiaryCategory $diaryCategory)
    {
        $diaryCategory->update($request->validated());
        return new DiaryCategoryResource($diaryCategory);
    }

    public function destroy(DiaryCategory $diaryCategory)
    {
        $diaryCategory->delete();
        return response()->noContent();
    }
}


