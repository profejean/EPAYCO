<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login_token(Request $request)
    {  
        $data1 = [

            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),        
            'username' => $request->get('email'),
            'password' => $request->get('password')      
        ];  
       
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('services.passport.login_endpoint'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "post",
            CURLOPT_POSTFIELDS => json_encode($data1),
            CURLOPT_HTTPHEADER => array(                
                "content-type: application/json"
            ),
        ));
   

        $response = curl_exec($curl);
        $e = curl_error($curl);

        curl_close($curl);
      
        if ($e) {         

            if ($e->getCode() === 400) {
                $data = [
                    "status" => $e->getCode(),
                    "success" => false,
                    "message" => "Invalid Request. Please enter a email or a password. " 
                ];
                return response()->json($data);
            } else if ($e->getCode() === 401) {      
            
                $data = [
                    "status" => $e->getCode(),
                    "success" => false,
                    "message" => "Your credentials are incorrect. Please try again" 
                ];
                return response()->json($data);
            }

        

            $data = [
                "status" => $e->getCode(),
                "success" => false,
                "message" => "Something went wrong on the server" 
            ];
            return response()->json($data);

        }else{           

            $res = json_decode($response, true); 
            if(isset($res["access_token"])){
                $data = [
                    "status" => "00",
                    "success" => true,
                    "message" => "Bearer ".$res["access_token"]
                ];
                return response()->json($data);
            }
            else{
                $data = [
                    "status" => "01",
                    "success" => false,
                    "message" => $res
                ];
                return response()->json($data);
        
            }

        
            



        }
    }
}
