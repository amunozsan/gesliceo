<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'code',
        'name',
        'phonecode',
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
