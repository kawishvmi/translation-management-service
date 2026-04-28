<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locale extends Model
{
    protected $fillable = [
        'code',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class);
    }
}
