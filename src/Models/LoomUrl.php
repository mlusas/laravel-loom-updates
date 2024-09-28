<?php

namespace mlusas\LaravelLoomUpdates\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoomUrl extends Model
{
    protected $fillable = [
        'url', 'file_path', 'line_number', 'date', 'author', 'title', 'image_url', 'tag'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}