<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Charger;
use App\Models\Alarm;
use App\Models\Owner;
use App\Models\ChargeStation;
use App\Models\Oem;
use App\Models\ChargerModel;
use App\Models\Config;
use App\Models\User;
use App\Models\DeviceModel;
use App\Models\Tariff;
use App\Models\Tax;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Auth;
use File;
use DB;
// use Firebase\JWT\JWT;
use session;

class Save extends BaseController
{
function postoem(Request $req){
	$input=$req->All();	
		if(isset($input["id"])){
		Oem::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"OEM Updated Successfully"));
				
	}
	else {
		Owner::create($input);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"OEM Added Successfully"));
				
	}
}
function postdevicemodel(Request $req){
	$input=$req->All();
	if(isset($input["id"])){
		DeviceModel::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Device Model Updated Successfully"));
				
	}
	else {
		DeviceModel::create($input);  
		http_response_code(200);
                echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Device Model Added Successfully"));
				
	}
}
function postpartner(Request $req){
	$input=$req->All();
	
		if(isset($input["id"])){
		Owner::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Partner Updated Successfully"));
				
	}
	else {
		Owner::create($input);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"Partner Added Successfully"));
				
	}
}
function poststation(Request $req){
	$input=$req->All();
	
		if(isset($input["id"])){
		ChargeStation::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Station Updated Successfully"));
				
	}
	else {
		ChargeStation::create($input);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"Station Added Successfully"));
				
	}
}
function postcharger(Request $req){
	$input=$req->All();
	
		if(isset($input["id"])){
		Charger::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Charger Updated Successfully"));
				
	}
	else {
		Charger::create($input);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"Charger Added Successfully"));
				
	}
}
function posttariff(Request $req){
	$input=$req->All();
	$input["owner_id"]="1";

	
		if(isset($input["id"])){
		Tariff::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Tariff Updated Successfully"));
				
	}
	else {
		Tariff::create($input);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"Tariff Added Successfully"));
				
	}
}
function posttax(Request $req){
	$input=$req->All();
	$input["owner_id"]="1";
	
		if(isset($input["id"])){
		Tax::find($input["id"])->update($input);
		echo json_encode(Array("data"=>$input,"result"=>"success","message"=>"Tax Updated Successfully"));
				
	}
	else {
		Tax::create($input);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"Tax Added Successfully"));
				
	}
}
function postuser(Request $req){

		$input=$req->All();
		////if($input["password"]==$input["password2"]){
		if(isset($input["id"])){
			$input["password"]= Hash::make($input['password']);
			User::find($input["id"])->update($input);
			http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"User Updated Successfully"));
		
		}else{
        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
			'partner_id' =>$input['partner_id'],
        ]);
		http_response_code(200);
                echo json_encode(Array("result"=>"success","message"=>"User Added Successfully"));
		
		}
		//}
			
			
			
			
			
		
}


}