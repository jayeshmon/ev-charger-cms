<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
	protected $table="tariffs";
	protected $fillable =[ 
'owner_id',
'name',
'tariff_type_id',
'rate'];
    use HasFactory;
	
}
