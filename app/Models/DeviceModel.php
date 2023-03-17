<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
	protected $table="device_models";
	protected $fillable=[
	

'model_no',	
'oem_id',
'connector_type_id',	
'max_kwh',
'no_of_slots',
'device_type_id',
'device_class',	
'warrenty',	
'description',
'manufacturer_details',	];
    use HasFactory;
public function oem() {
	
        return $this->belongsTo(Oem::class,'oem_id');
    }
	public function deviceType  () {
	
        return $this->belongsTo(DeviceType::class,'device_type_id');
    }
public function connector_type  () {
	
        return $this->belongsTo(ConnectorType::class,'connector_type_id');
    }
	public function deviceClass() {
	
        return $this->belongsTo(DeviceClass::class,'device_class_id');
    }
}
