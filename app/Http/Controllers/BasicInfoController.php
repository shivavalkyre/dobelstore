<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BasicInfoController extends Controller
{
    public function get_province()
    {
         $result =Http::asForm()->get('https://api.rajaongkir.com/starter/province', [
                    'key' => 'c2ebd0c43315ad0936dace31a9bda376',

                ]);
                
         $jsonString = json_encode($result->json());
                $result = json_decode($jsonString,true);

                $services = $result['rajaongkir']['results'];

                $province_arr = array();
                foreach ($services as $item)
                {
                                $province_id = $item['province_id'];
                                $province_name = $item['province'];
                                $province_arr= ('id' => $province_id,'province' => $province_name);
                }               

         return response()->json([
                    'id' => $province_id,
                    'province' => $province_name
                ]);
    }
}
