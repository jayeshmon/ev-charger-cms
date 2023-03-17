<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargingReservations extends Model
{
    use HasFactory;
	protected $table="charging_transactions";
   

	public function user() {
        return $this->belongsTo(User::class);
    }
	public function charger() {
        return $this->belongsTo(Charger::class);
    }
}
