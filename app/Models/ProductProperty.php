<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ProductProperty extends Model
{
    use HasFactory;

    public const SLUG_COLOR = 'color';
    public const SLUG_WEIGHT = 'weight';
    public const COLORS = [
        'red', 'black', 'white', 'blue',
    ];

    public function propertyValues(): HasMany
    {
        return $this->hasMany(ProductPropertyValue::class);
    }
}
