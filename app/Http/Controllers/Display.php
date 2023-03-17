<?php

namespace App\Http\Controllers;

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
use Auth;
use File;
use DB;
// use Firebase\JWT\JWT;
use session;

class Display extends BaseController
{
	function getoem(Request $req){
	if(isset($req->id)){
	 $exist = Oem::where('id',$req->id)->get();
	}
	else{
		$exist = Oem::All();
	}
        $data=$exist->map(function($item){
		return Array(
		'id'=>$item->id,
					'name'=>$item->name,
					'website'=>$item?->website,
					'address'=>$item->address,
					'supplier_name'=>$item->supplier_name,
					'supplier_address'=>$item->supplier_address,
					'supplier_name'=>$item->supplier_name,
					'phone_no'=>$item->phone_no,
	);
			
		});
	
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data));
            
        }
  }
  
  
  function getdevicemodel(Request $req){
	$oemslist=Oem::All();
	if(isset($req->id)){
		
	 $exist = DeviceModel::where('id',$req->id)->get();
	 
	}
else if(isset($req->create)){
				http_response_code(200);
                echo json_encode(Array("oemslist"=>$oemslist,"deviceclass"=>Config::getDeviceClass(),"connectortype"=>Config::getConnectorType(),"devicetype"=>Config::getDeviceType()));
				return;
}
		
	
	else if(isset($req->oem)){
		$exist=DeviceModel::where(Array('oem_id'=>$req->oem))->get();
		}
	else{
		$exist=DeviceModel::All();
	}
	
	

	$data=$exist->map(function($item){
		return Array(
		'id'=>$item->id,
		'model_no'=>$item->model_no,
					'oem'=>$item?->oem?->name,
					'connector_type_id'=>$item->connector_type_id,
					'max_kwh'=>$item->max_kwh,
					'no_of_slots'=>$item->no_of_slots,
					'type'=>$item->device_type_id,
					'device_class'=>$item->device_class,
					'description'=>$item->description,
					'manufacturer_details'=>$item->manufacturer_details,
					
					
					);
			
		});
	
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data,"oemslist"=>$oemslist,"deviceclass"=>Config::getDeviceClass(),"connectortype"=>Config::getConnectorType(),"devicetype"=>Config::getDeviceType()));
            
        }
  }
  
  
  function getpartner(Request $req){
	$userlist=User::All();
	if(isset($req->id)){
	 $exist = Owner::find($req->id)->get();
	}
	else if(isset($req->create)){
		
				http_response_code(200);
                echo json_encode(Array("userslist"=>$userlist));
				return;
}
	else{
		$exist=Owner::All();
	}
	
	

	$data=$exist->map(function($item){
		return Array(
					'id'=>$item->id,
					'name'=>$item->company,
					'company'=>$item->company,
					'website'=>$item->website,
					
					
					
					);
			
		});
	
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data,"userslist"=>$userlist));
            
        }
  }
  
  
  
  
  
  
  
  
 
    function getstation(Request $req){
		
		$userslist=User::All();
		$ownerslist=Owner::All();
	 	if($req->id){
	 $exist = ChargeStation::where('id',$req->id)->get();
	}
	else if(isset($req->create)){
				http_response_code(200);
                echo json_encode(Array("ownerslist"=>$ownerslist,"userslist"=>$userslist));
				return;
}else{
		$exist = ChargeStation::All();
	}
	$data=$exist->map(function($item){
		return Array(
					'id'=>$item->id,
					'station_name'=>$item->station_name,
					'owner'=>$item->owner->company,
					//'user'=>$item->user_id,
					
					'address'=>$item->address,
					'city'=>$item->city,
					'state'=>$item->state,
					'country'=>$item->country,
					'pincode'=>$item->pincode,
					'operating_hours_start'=>$item->operating_hours_start,
					'operating_hours_end'=>$item->operating_hours_end,
					'latitude'=>$item->latitude,
					'longitude'=>$item->longitude,
					'commissioned_on'=>$item->commissioned_on,
					
					
					
					);
	});
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data,"ownerslist"=>$ownerslist,"userslist"=>$userslist));
            
        }
  }
  
   function getcharger(Request $req){
	   $stationslist=ChargeStation::All();
	   $oemslist=Oem::All();
	   $modelslist=DeviceModel::All();
	   $tariffslist=Tariff::All();
	   $taxeslist=Tax::All();
	 	if($req->id){
	 $exist = Charger::where('id',$req->id)->get();
	}
	else if(isset($req->create)){
				http_response_code(200);
                echo json_encode(Array("stationslist"=>$stationslist,"tariffslist"=>$tariffslist,"oemslist"=>$oemslist,"modelslist"=>$modelslist,"taxeslist"=>$taxeslist));
				return;
}


else{
		$exist= Charger::All();
	}
		$data=$exist->map(function($item){
		return Array(
					'id'=>$item->id,
					'name'=>$item->display_name,
					'charge_station'=>$item->station->station_name,
					'address'=>$item->station->address,
					'owner'=>$item->station->owner->user->name,
					'model'=>$item->model->model_no,
					'oem'=>$item->oem->name,
					'tariff_type'=>$item->tariff_type_id,
					'tariff'=>$item->tariff->name,
					'tax'=>$item->tax->name,
					'charger_latitude'=>$item->charger_latitude,
					'charger_longitude'=>$item->charger_longitude,
					'charger_reservation_enabled'=>$item->charger_reservation_enabled,
					
					
					
					);
			
		});
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data,"stationslist"=>$stationslist,"tariffslist"=>$tariffslist,"oemslist"=>$oemslist,"modelslist"=>$modelslist,"taxeslist"=>$taxeslist));
            
        }
  }
  function gettariff(Request $req){
	 
	 if($req->id){
	 $exist = Tariff::where('id',$req->id)->get();
	}
	else if(isset($req->create)){
				http_response_code(200);
                echo json_encode(Array("tarifftypelist"=>Config::getTariffType()));
				return;
}


else{
		$exist= Tariff::All();
	}
		$data=$exist->map(function($item){
		return Array(
					'id'=>$item->id,
					'name'=>$item->name,
					'tariff_type_id'=>$item->tariff_type_id,
					'rate'=>$item->rate,
					);
			
		});
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data,"tarifftypelist"=>Config::getTariffType()));
            
        }
  }
  function gettax(Request $req){
	 
	 if($req->id){
	 $exist = Tax::where('id',$req->id)->get();
	}
	else if(isset($req->create)){
				http_response_code(200);
                echo json_encode(Array("taxtypelist"=>Config::getTaxType()));
				return;
}


else{
		$exist= Tax::All();
	}
		$data=$exist->map(function($item){
		return Array(
					'id'=>$item->id,
					'name'=>$item->name,
					'tax_type_id'=>$item->tax_type_id,
					'rate'=>$item->rate,
					);
			
		});
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data,"taxtypelist"=>Config::getTaxType()));
            
        }
  }
  function getuser(Request $req){
	 $partnerlist=Owner::All();
	 if($req->id){
	 $exist = User::where('id',$req->id)->get();
	}
	else if(isset($req->create)){
				http_response_code(200);
                echo json_encode(Array(
				"roles"=>Config::getUserType(),
				"partnerlist"=>$partnerlist
				));
				return;
}


else{
		$exist= User::All();
	}
		$data=$exist->map(function($item){
		return Array(
					'id'=>$item->id,
					'name'=>$item->name,
					'email'=>$item->email,
					'role'=>$item->role,
					'partner'=>$item->owner->company,
					);
			
		});
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data));
            
        }
  }
  
  
  
  
  function getalarm(Request $req){
		if($req->id){
	 $exist = Alarm::find($req->id);
	}else{
		$exist = Alarm::All();
	}
        if($exist != '')
        {
				http_response_code(200);
				echo header('Access-Control-Allow-Origin: *');
                echo json_encode($exist);
            
        }
  }
  function getchargetransaction(Request $req){
		if($req->id){
	 $exist = ChargingTransaction::find($req->id);
	 //print_r());
	}else{
		$exist = ChargingTransaction::All();
		
		
	}
        if($exist != '')
        {
				http_response_code(200);
                echo json_encode($exist);
            
        }
  }
function gettransaction(Request $req){
	 	if($req->id){
	 $exist = Transaction::find($req->id);
	}else{
		$exist= Transaction::All();
	}
		$data=$exist->map(function($item){
		return Array(
					'name'=>$item->user->name,
					'company'=>$item->station->owner->company,
					'station'=>$item->station->station_name,
					'charger'=>$item->charger->display_name,
					'amount'=>$item->entity_currency." ".$item->entity_amount,
					'method'=>$item->entity_method,
					'vpa'=>$item->entity_vpa,
					'email'=>$item->entity_email,
					'fee'=>$item->entity_fee,
					'contact'=>$item->entity_contact,
					'status'=>$item->entity_status,
					);
			
		});
        if($data != '')
        {
				http_response_code(200);
                echo json_encode(Array("data"=>$data));
            
        }
  }
}
	  