<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'category_id',
        'status',
        'all_day',
        'start_at',
        'end_at',
        'duration_minutes',
        'recurrence_json',
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'recurrence_json' => 'array',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}


