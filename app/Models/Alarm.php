<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
   protected $table="alarms";
    use HasFactory;

	public function charger() {
        return $this->belongsTo(Charger::class);
    }
	
}
