<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserRegisterController extends Controller
{
    Public function index()
    {
        return 'hello';
    }

    Public function register_buyer(Request $request)
    {
            //validate data
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'email'   => 'required',
                'password'   => 'required',
                'dob' =>'required',
                'mobile_no' => 'required',
                'is_buyer' => 'required',
            ],

            );

            if($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan isi dengan lengkap',
                    'data'    => $validator->errors()
                ],401);

            } else
            {
                //check data first

                //$token = Str::random(60);
                $userstore = new User;
                $result = User::where('email','=', $request->email)->first();
                //return response()->json([
                //    'success' => true,
                //    'message' => '',
                //    'data'    => $result->id
                //],200);

                if($result==null){

                $userstore ->name = $request->name;
                $userstore ->email = $request->email;
                $userstore ->password = Hash::make($request->password);
                $userstore ->dob = $request->dob;
                $userstore ->mobile_no = $request->mobile_no;
                //$userstore ->remember_token = $token;
                $userstore ->is_buyer = $request->is_buyer;
                $saved = $userstore ->save();

                if(!$saved){
                    return response()->json([
                        'success' => false,
                        'message' => 'Silahkan isi dengan lengkap',
                        'data'    => $validator->errors()
                    ],401);
                }else
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Registrasi Sukses',
                        'data'    => Null
                    ],200);
                }
             }else{

                $user = User::find($result->id);
                if ($user->is_buyer != $request->is_buyer)
                {
                    $user->password= Hash::make(($request->password));
                    $user->is_buyer= $request->is_buyer;
                    $user->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Update berhasil',
                        'data'    => ""
                    ],200);
                }
                else{
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah ada',
                    'data'    => ""
                ],401);
                }
            }
            }
    }


    Public function register_seller(Request $request)
    {

            //validate data
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'email'   => 'required',
                'dob' =>'required',
                'mobile_no' => 'required',
                'is_seller' => 'required',
            ],

            );

            if($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan isi dengan lengkap',
                    'data'    => $validator->errors()
                ],401);

            } else
            {
                //check data first

                //$token = Str::random(60);
                $userstore = new User;
                $result = User::where('email','=', $request->email)->first();
                //return response()->json([
                //    'success' => true,
                //    'message' => '',
                //    'data'    => $result->id
                //],200);

                if($result==null){

                $userstore ->name = $request->name;
                $userstore ->email = $request->email;
                $userstore ->password = Hash::make(($request->password_seller));
                $userstore ->dob = $request->dob;
                $userstore ->mobile_no = $request->mobile_no;
                //$userstore ->remember_token = $token;
                $userstore ->is_seller = $request->is_seller;
                $saved = $userstore ->save();

                if(!$saved){
                    return response()->json([
                        'success' => false,
                        'message' => 'Silahkan isi dengan lengkap',
                        'data'    => $validator->errors()
                    ],401);
                }else
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Registrasi Sukses',
                        'data'    => Null
                    ],200);
                }
             }else{

                $user = User::find($result->id);
                if ($user->is_seller != $request->is_seller)
                {
                    //$user->password_seller= hash::make($request->password_seller);
                    $user->is_seller= $request->is_seller;
                    $user->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Update berhasil',
                        'data'    => ""
                    ],200);
                }
                else{
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah ada',
                    'data'    => ""
                ],401);
                }
            }
            }
    }
}
