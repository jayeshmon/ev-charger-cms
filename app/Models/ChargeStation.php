<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeStation extends Model
{
	protected $table="charge_stations";
	protected $fillable=[

'station_name',
'latitude',
'longitude',
'owner_id',
'user_id',
'service_provider_id',
'address',
'city',
'state',
'country',
'pincode',
'operating_hours_start',
'operating_hours_end',
'commissioned_on',
'status',
];
    use HasFactory;


	public function owner() {
        return $this->belongsTo(Owner::class,'owner_id');
    }
	public function serviceProvider() {
        return $this->belongsTo(ServiceProvider::class,'service_provider_id');
    }
	public function pincode() {
        return $this->belongsTo(Pincode::class);
    }
}
