<?php

namespace App\Http\Controllers;

use App\Models\UserLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class UserLoginController extends Controller
{
    Public function login_buyer (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'required',
            'password'   => 'required',
            'is_buyer' => 'required',
        ]
        );

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
            $user = $request->user();
            $tokenResult = $user->createToken('Laravel Personal Access Client');
            $token = $tokenResult->token;
            $token->save();



            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
    }


    Public function login_seller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'required',
            'password'   => 'required',
            'is_buyer' => 'required',
        ]
        );

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::firstwhere([['email',$request->email],['is_seller',1]]);

        if($user){

        $user = $request->user();
        $tokenResult = $user->createToken('Laravel Personal Access Client');

        $token = $tokenResult->token;
        $token->save();

        // get user id

        $user_login = User::where('email',$request->email)->first();
        $user_id = $user_login->id;
        $is_seller_active = $user_login->is_seller_active;

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'user_id' => $user_id,
            'is_seller_active' => $is_seller_active,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
            
        ]);

            }else
            {

                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'data'    => null
                ],401);

            }
    }
    
     public function get_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'required',
            'password'   => 'required',
            'is_buyer' => 'required',
        ]
        );

     if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi dengan lengkap',
                'data'    => $validator->errors()
            ],401);

        }

                // get user id

                $user_login = User::where('email',$request->email)->first();
                 $user_id = $user_login->id;
                $user_name = $user_login->name;
                $user_email = $request->email;
                $user_dob = $request->dob;
                $user_phone =$user_login->mobile_no;
                $user_bank_name =$user_login->bank_name;
                $user_bank_account =$user_login->bank_account;
                $user_is_seller = $user_login->is_seller;
                $user_is_seller_active = $user_login->is_seller_active;
                $user_is_buyer = $user_login->is_buyer;
                $user_province = $user_login->province;
                $user_city = $user_login->city;

                //return  $user_city;
                
                $result = Http::withHeaders([
                'key' => 'c2ebd0c43315ad0936dace31a9bda376'
                 ])->get('https://api.rajaongkir.com/starter/city?id='.$user_city.'&province='.$user_province);


                //return $result;

                $jsonString = json_encode($result->json());
                $result = json_decode($jsonString,true);

                $services = $result['rajaongkir']['results'];
            
                //return response()->json($services);
                

                $province_name = $services['province'];
                $city_name = $services['city_name'];



                return response()->json([
                    'id' => $user_id,
                    'username' =>  $user_name,
                    'email' => $user_email,
                    'dob' => $user_dob,
                    'phone' => $user_phone,
                    'bank_name' => $user_bank_name,
                    'bank_no' => $user_bank_account,
                    'is_seller' => $user_is_seller,
                    'is_buyer' => $user_is_buyer,
                    'province_id' =>  $user_province,
                    'province' => $province_name,
                    'city_id' => $user_city,
                    'city' => $city_name

                ]);



    }
    
     public function edit_location(Request $request)
    {
        $check_user = User::firstwhere('id',$request->user_id);
        if($check_user){
        $user = User::find($request->user_id);
        $user->province      = $request->province;
        $user->city          = $request->city;


        $user->save();
             return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    =>  null
            ],200);
        }else{

             return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    =>  null
            ],404);
        }
    }
    
    public function edit_bank(Request $request)
    {
        $check_user = User::firstwhere('id',$request->user_id);
        if($check_user){
        $user = User::find($request->user_id);
        $user->bank_name      = $request->bank_name;
        $user->bank_account          = $request->bank_account;


        $user->save();
             return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    =>  null
            ],200);
        }else{

             return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    =>  null
            ],404);
        }
    }
    
    public function edit_phone(Request $request)
    {
        $check_user = User::firstwhere('id',$request->user_id);
        if($check_user){
        $user = User::find($request->user_id);
        $user->mobile_no      = $request->phone;

        $user->save();
             return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    =>  null
            ],200);
        }else{

             return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    =>  null
            ],404);
        }
    }
    
    public function get_province()
    {
    
        
         $result =Http::asForm()->get('https://api.rajaongkir.com/starter/province', [
                    'key' => 'c2ebd0c43315ad0936dace31a9bda376',

                ]);
                
         $jsonString = json_encode($result->json());
                $result = json_decode($jsonString,true);

                $services = $result['rajaongkir']['results'];

                $arr= array();
                foreach ($services as $item)
                {
                                $province_id = $item['province_id'];
                                $province_name = $item['province'];
                                $a=array("id"=>$province_id,"province"=>$province_name);
                                array_push($arr,$a);
                }        

         return $arr;
    }
    
        public function get_city($province_id)
    {
    
        //$url = 'https://api.rajaongkir.com/starter/city?id&province=5';
        //$result =Http::asForm()->get($url, [
        //            'key' => 'c2ebd0c43315ad0936dace31a9bda376',

        //        ]);
        
        $result = Http::withHeaders([
            'key' => 'c2ebd0c43315ad0936dace31a9bda376'
            ])->get('https://api.rajaongkir.com/starter/city?id&province='.$province_id);
                
         $jsonString = json_encode($result->json());
                $result = json_decode($jsonString,true);

                $services = $result['rajaongkir']['results'];

                $arr= array();
                foreach ($services as $item)
                {
                                $city_id = $item['city_id'];
                                $city_name = $item['city_name'];
                                $a=array("id"=>$city_id,"city_name"=>$city_name);
                                array_push($arr,$a);
                }   
         

         return $arr;
    }
    
    public function do_login()
    {
        return view('error');
    }


}
