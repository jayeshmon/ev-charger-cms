<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;
	protected $table="owners";
	protected $fillable=[
	

'user_id',
'company',
'website',
	];
	
	public function user() {
	
        return $this->belongsTo(User::class,'user_id');
    }
}
