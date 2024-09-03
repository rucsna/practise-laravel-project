<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    protected $fillable = [
        'name',
        'unit'
    ];

    public function measurements ()
    {
        $this->hasMany(Measurement::class, 'parameter_id', 'id');
    }
}
