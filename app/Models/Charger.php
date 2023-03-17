<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charger extends Model
{
	
    use HasFactory;
	
	protected $fillable = [
        

'oem_id',
'chargestation_id',
'charger_model_id',
'display_name',
'commissioned_date',
'charger_latitude',
'charger_longitude',
'charger_reservation_enabled',
'tariff_type_id',
'tariff_id',
'tax_id',
'configuration_url',
    ];
	public function chargeStation() {
        return $this->belongsTo(ChargeStation::class,'chargestation_id');
    }
	public function oem() {
        return $this->belongsTo(Oem::class);
    }
	public function tariffType() {
        return $this->belongsTo(TariffType::class,'tariff_type_id');
    }
	public function tariff() {
        return $this->belongsTo(Tariff::class,'tariff_id');
    }
	public function tax() {
        return $this->belongsTo(Tax::class,'tax_id');
    }
	public function station() {
        return $this->belongsTo(ChargeStation::class,'chargestation_id');
    }
	public function model() {
        return $this->belongsTo(DeviceModel::class,'charger_model_id');
    }
	public function chargerstatus() {
        return $this->belongsTo(ChargerStatus::class);
    }
	public function approvalstatus() {
        return $this->belongsTo(ApprovalStatus::class);
    }
	public function publishstatus() {
        return $this->belongsTo(PublishStatus::class);
    }
}
