<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
    ];
}
