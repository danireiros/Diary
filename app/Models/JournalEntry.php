<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'date',
        'title',
        'content',
        'diary_category_id',
        'hidden',
    ];

    protected $casts = [
        'date' => 'date',
        'hidden' => 'boolean',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function diaryCategory()
    {
        return $this->belongsTo(DiaryCategory::class);
    }
}


