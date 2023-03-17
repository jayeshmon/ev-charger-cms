?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Charger;
use App\Models\Alarm;
use App\Models\ChargeStation;
use App\Models\Oem;
use App\Models\ChargerModel;
use App\Models\ChargeTransaction;
use Auth;
use File;
use DB;
// use Firebase\JWT\JWT;
use session;

class Api extends BaseController
{

function register(Request $request)
    {
       
        $data = json_decode(file_get_contents("php://input"));
       
	   //$enocde = base64_encode($request->first_name.$request->last_name.$request->email);
        $exist = DB::table('users')->where(array('email' => $request->email))->first();
        if($exist != '')
        {
            
                http_response_code(200);
                echo json_encode(array("response" => "failed","message" => "Email already exist."));
            
        }else
        {
           
            $info = array(
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => $request->password
							);
                        
                 
            $insert = DB::table('users')->insert($info);
            if($insert == 1)
            {
                http_response_code(200);
                echo json_encode(array("response" => "success","message" => "User registered successfully ."));
            }else
            {
                http_response_code(200);
                echo json_encode(array("response" => "failed","message" => "User registration Error."));
            }
        }
        
    }
function login(Request $request)
    {
        $data = json_decode(file_get_contents("php://input"));
        $info = array(
                        'email' => $request->email,
                        'password' => $request->password
                    );
                    
        $check = DB::table('users')->where($info)->first();
        
        if($check != '')
        {
            $id = $check->id;
            $name = $check->name;
            $password  = $check->password;
            
        if($password == $request->password)
        {
            
            echo json_encode(
                array(
                    "response" => "success",
                    "message" => "Successful login.",
                    "email" => $check->email,
                    "user_id" => $check->id,
                    "name" => $check->name
                ));
        }else{
    
            http_response_code(401);
            echo json_encode(array("response" => "failed","message" => "Password Not Match."));
            }
        }
        else{
    
            http_response_code(401);
            echo json_encode(array("response" => "failed","message" => "Login failed."));
        }
    }

}