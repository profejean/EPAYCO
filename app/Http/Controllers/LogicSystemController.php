<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RechargeWallet;
use App\Models\Payment;
use App\Mail\PaymentConfirmMail;
use App\Mail\PaidMail;
use Validator; 

class LogicSystemController extends Controller
{
    public function register_client(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|unique:users|numeric',
            'email' => 'required|unique:users|email',
            'tlf' => 'required|unique:users|numeric',
            'name' => 'required',
      
        ]);

        if ($validator->fails()) {
            return $validator->getMessageBag()->all();
            
          
        }

       

        $client = new User();
        $client->document = $request->get('document');     
        $client->email = $request->get('email'); 
        $client->tlf = $request->get('tlf');
        $client->name = $request->get('name');  
        $client->password = $request->get('password');                
        $client->save();  
        
        $data = [
            "status" => "200",
            "success" => true
        ];

        return response()->json($data);
    }

    public function recharge_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|numeric',        
            'tlf' => 'required|numeric',
            'cant' => 'required|numeric',
    
        ]);

        if ($validator->fails()) {
            return $validator->getMessageBag()->all(); 
        }

        $client = User::where('document','=',$request->document)->where('tlf','=',$request->tlf)->count();

        if($client > 0)
        {
            

            $recharge = new RechargeWallet();
            $recharge->document = $request->get('document');     
            $recharge->tlf = $request->get('tlf'); 
            $recharge->cant = $request->get('cant');                     
            $recharge->save();      

            
            $data = [
                "status" => "00",
                "success" => true
            ];

            return response()->json($data);

        }
        else{ 
         

            $data = [
                "status" => "01",
                "success" => false,
                "message" => "Credentials phone or document, they are not compatible!"
            ];

            return response()->json($data);

            
        }
    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|exists:users,document|numeric',
            'price' => 'required|numeric',     
      
        ]);

    
        if ($validator->fails()) {
            return $validator->getMessageBag()->all();
            
        
        }
    
        $wallet = RechargeWallet::where('document','=',$request->document)->get(); 
        $pay = Payment::where('document','=',$request->document)->get(); 

        $debe = 0;
        foreach($wallet as $w)
        {
            $debe = $w->cant + $debe;
        }

        $haber = 0;
        foreach($pay as $w)
        {
            $haber = $w->price + $haber;
        }

        $value = $debe - $haber;

        if($value >= $request->price)
        {
            $permitted   = '0123456789abcdefghijklmnopqrstuvwxyz';
            $token      = substr(str_shuffle($permitted), 0, 6);
            $client        = User::where('document','=',$request->document)->first();
            $client->token = $token;
            $client->save();

            $details = [
                'token' => $token,
                'price' => $request->price,                
            ];

            \Mail::to($client->email)->send(new PaymentConfirmMail($details));

           

            $data = [
                "status" => "00",
                "success" => true,
                "message" => "Send email, Please confirm your payment!"
            ];

            return response()->json($data);
        }
        else
        {     

            $data = [
                "status" => "01",
                "success" => false,
                "message" => "You do not have enough money in your wallet!, Your have " . $value
            ];

            return response()->json($data);
        }

    

    }

    public function confirm_payment($token,$price)
    {
        
        $count = User::where('token','=',$token)->count();

        if($count > 0)
        {
            $client = User::where('token','=',$token)->first();

            $payment = new Payment();
            $payment->document = $client->document;     
            $payment->price = $price;
            $payment->save();

            \Mail::to($client->email)->send(new PaidMail());

           

            $data = [
                "status" => "00",
                "success" => true,
                "message" => "Your payment has been confirmed successfully!"
            ];

            return response()->json($data);

        }
        else
        { 
            $data = [
                "status" => "01",
                "success" => false,
                "message" => "Your token is not correct!"
            ];

            return response()->json($data);

        }

        

        

    }

    public function consult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|exists:users|numeric',
            'tlf' => 'required|exists:users|numeric',  
      
        ]);
        if ($validator->fails()) {
            return $validator->getMessageBag()->all();
            
        
        }
        
        $count = User::where('document','=',$request->document)->where('tlf','=',$request->tlf)->count();

        if($count > 0)
        {
            $client = User::where('document','=',$request->document)->where('tlf','=',$request->tlf)->first();

            $wallet = RechargeWallet::where('document','=',$client->document)->get(); 
            $pay = Payment::where('document','=',$client->document)->get(); 
    
            $debe = 0;
            foreach($wallet as $w)
            {
                $debe = $w->cant + $debe;
            }
    
            $haber = 0;
            foreach($pay as $w)
            {
                $haber = $w->price + $haber;
            }
    
            $value = $debe - $haber;

     

            $data = [
                "status" => "00",
                "success" => true,
                "message" => "Your amount is = " .$value
            ];

            return response()->json($data);
        }
        else
        {
            $data = [
                "status" => "01",
                "success" => false,
                "message" => "Incorrect credentials!"
            ];

            return response()->json($data);
        }



    }

}
