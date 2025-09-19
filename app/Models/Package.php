<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'package_id',           // 6-digit auto-generated unique ID
        'package_no',           // unique in system
        'description',
        'procurement_method_id',
        'estimated_cost_bdt',
    ];

    public function method()
    {
        return $this->belongsTo(ProcurementMethod::class, 'procurement_method_id');
    }
    // app/Models/Package.php
    public function requisitions()
    {
        return $this->hasMany(\App\Models\Requisition::class);
    }
    // app/Models/Package.php
// public function requisitions(){ return $this->hasMany(\App\Models\Requisition::class); }
public function technicalSpecs(){ return $this->hasMany(\App\Models\TechnicalSpec::class); }


}
