<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oem extends Model
{
	protected $fillable=[
	

'name',
'website',
'address',
'supplier_name',
'supplier_address',
'phone_no'
	
	];
    use HasFactory;

   
}
