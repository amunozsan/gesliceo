<?php

namespace App\Models;

use App\Letter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Course extends Model
{
    protected $fillable = [
        'name',
        'season_id',
        'letter'
    ];

    protected $casts = [
        'letter' => Letter::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}


