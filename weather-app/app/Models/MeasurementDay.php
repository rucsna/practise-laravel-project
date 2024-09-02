<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementDay extends Model
{
    protected $fillable = ['date'];

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class, 'measurement_day_id', 'id');
    }
}
