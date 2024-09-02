<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measurement extends Model
{
    protected $fillable = [
        'measurement_day_id',
        'parameter_id',
        'value'
    ];

    public function measurementDay(): BelongsTo
    {
        return $this->belongsTo(MeasurementDay::class, 'measurement_day_id', 'id');
    }
    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class, 'parameter_id', 'id');
    }
}
