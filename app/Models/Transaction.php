<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected $table="pay";
    use HasFactory;
	public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
	public function charger() {
        return $this->belongsTo(Charger::class,'charger_id');
    }
	public function station() {
        return $this->belongsTo(ChargeStation::class,'station_id');
    }
}

 

