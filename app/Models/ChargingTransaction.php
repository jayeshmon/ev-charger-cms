<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargingTransaction extends Model
{
   protected $table="charging_transactions";
    use HasFactory;

	public function user() {
        return $this->belongsTo(User::class);
    }
	public static function charger() {
        return $this->belongsTo(Charger::class);
    }
	public function transactionmode() {
        return $this->belongsTo(TransactionMode::class);
    }
	public function connectortype() {
        return $this->belongsTo(ConnectorType::class);
    }
	public function paymenttype() {
        return $this->belongsTo(PaymentType::class);
    }
	public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
	
}
