<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargerModel extends Model
{
    use HasFactory;
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
'manufacturer_details',	
	];
	

	public function oem() {
        return $this->belongsTo(Oem::class,'oem_id');
    }
}
