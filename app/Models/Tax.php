<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;
	
protected $fillable =[ 
'owner_id',
'name',
'tax_type_id',
'rate'];

}
