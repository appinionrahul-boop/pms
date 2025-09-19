<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable = [
        'package_id','package_no','requisition_status_id','estimated_cost_bdt','unit_id','quantity',
        'department_id','procurement_type_id','assigned_officer_id','tech_spec_file',
        'approving_authority_id','signing_date','lc_status_id','reference_annex',
        'description','procurement_method_id','vendor_name','official_estimated_cost_bdt',
        'requisition_receiving_date','delivery_date','reference_link','comments','officer_name',
    ];

    public function package(){ return $this->belongsTo(Package::class); }
    public function status(){ return $this->belongsTo(RequisitionStatus::class,'requisition_status_id'); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function department(){ return $this->belongsTo(Department::class); }
    public function procurementType(){ return $this->belongsTo(ProcurementType::class); }
    public function method(){ return $this->belongsTo(ProcurementMethod::class,'procurement_method_id'); }
    public function lcStatus(){ return $this->belongsTo(LcStatus::class); }
    public function approvingAuthority(){ return $this->belongsTo(ApprovingAuthority::class); }
}

