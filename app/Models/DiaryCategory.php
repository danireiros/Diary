<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiaryCategory extends Model
{
    use HasFactory;

    protected $table = 'diary_categories';

    protected $fillable = [
        'id', 'name', 'color',
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

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'diary_category_id');
    }
}


