<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalSpec extends Model
{
    protected $fillable = ['package_id','spec_name','quantity','unit_price_bdt','total_price_bdt','erp_code'];

    public function package(){ return $this->belongsTo(Package::class); }
}

