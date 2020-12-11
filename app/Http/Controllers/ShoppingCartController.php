<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ShoppingCartController extends Controller
{
    Public function Add_Shopping_Chart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'seller_id' => 'required',
            'product_id'     => 'required',
            'qty'   => 'required',
        ],);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi dengan lengkap',
                'data'    => $validator->errors()
            ],401);
        }
        $user = User::firstwhere([['id',$request->user_id],['is_buyer',1]]);
        if($user)
        {
        $check_shopping_cart =  DB::table('shopping_carts')
        ->where('user_id',$request->user_id)
        ->where('seller_id',$request->seller_id)
        ->where('product_id',$request->product_id)->first();

        if (!$check_shopping_cart){
            // get unit price & weight

            $product = Product::where('id',$request->product_id)->get();
            $shopping_cart = new ShoppingCart();
            $qty = $request->qty;
            $shopping_cart->user_id         = $request->user_id;
            $shopping_cart->seller_id       = $request->seller_id;
            $shopping_cart->product_id      = $request->product_id;
            $shopping_cart->qty             = $qty;
            $shopping_cart->weight          = $qty * $product[0]->weight;
            $shopping_cart->amount          = $qty * $product[0]->unit_price;
            $saved = $shopping_cart->save();

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
                    'message' => 'Tambah data Sukses',
                    'data'    => $shopping_cart
                ],200);
                }
            }else
            {
               //update data qty
               $product = Product::where('id',$request->product_id)->get();
               $shopping_cart_update = ShoppingCart::find( $check_shopping_cart->id);

               $qty = ($shopping_cart_update->qty + $request->qty);

               $shopping_cart_update->qty = $qty;
               $shopping_cart_update->weight          = $qty * $product[0]->weight;
               $shopping_cart_update->amount          = $qty * $product[0]->unit_price;
               $saved =  $shopping_cart_update->save();


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
                    'message' => 'Update data Sukses',
                    'data'    => $shopping_cart_update
                ],200);
                }



            }


    }else
        {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'data'    => null
            ],400);
        }
    }

    public function Edit_Shopping_Chart(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'seller_id' => 'required',
            'product_id'     => 'required',
            'qty'   => 'required',
        ],);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi dengan lengkap',
                'data'    => $validator->errors()
            ],401);
        }

        $check_shopping_cart =  DB::table('shopping_carts')
        ->where('user_id',$request->user_id)
        ->where('seller_id',$request->seller_id)
        ->where('product_id',$request->product_id)->first();

        if (!$check_shopping_cart)
        {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    => null
            ],401);
        }else
        {

                           //update data qty

                           $shopping_cart_update = ShoppingCart::find( $check_shopping_cart->id);
                           $product = Product::where('id',$request->product_id)->get();
                           $shopping_cart_qty = ($shopping_cart_update->qty - $request->qty);
                           if ($shopping_cart_qty <0)
                           {
                            $shopping_cart_qty=0;
                           }

                           $shopping_cart_update->qty =  $shopping_cart_qty;
                           $shopping_cart_update->qty = $shopping_cart_qty;
                           $shopping_cart_update->weight          = $shopping_cart_qty * $product[0]->weight;
                           $shopping_cart_update->amount          = $shopping_cart_qty * $product[0]->unit_price;
                           $saved =  $shopping_cart_update->save();


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
                                'message' => 'Update data Sukses',
                                'data'    => $shopping_cart_update
                            ],200);
                            }

        }
    }

    Public function Get_Shopping_Chart($id)
    {
        $check_shopping_chart = ShoppingCart::where([['user_id',$id],['is_checkout',0],['is_paid',0],['is_deleted',0]])->get();
        if($check_shopping_chart)
        {
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data'    => $check_shopping_chart
            ],200);
        }else
        {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    => $check_shopping_chart
            ],400);
        }
    }

    Public function Delete_Shopping_Chart($id)
    {
        $check_shopping_chart = ShoppingCart::where([['user_id',$id],['is_checkout',0],['is_paid',0],['is_deleted',0]])->get();
        if($check_shopping_chart)
        {
            //Product::destroy($id);
            foreach ( $check_shopping_chart as $chart) {
                $chart->is_deleted = 1;
                $chart->save();
            }
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
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

    Public function Delete_Per_seller_Shopping_Chart(Request $request,$id)
    {
        $check_shopping_chart = ShoppingCart::where([['user_id',$id],['seller_id',$request->seller_id],['is_checkout',0],['is_paid',0],['is_deleted',0]])->get();
        if($check_shopping_chart)
        {
            //Product::destroy($id);
            foreach ( $check_shopping_chart as $chart) {
                $chart->is_deleted = 1;
                $chart->save();
            }
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
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

    Public function Checkout_Shopping_Chart(Request $request,$id)
    {


        $check_shopping_chart = ShoppingCart::where([['user_id',$id],['is_checkout',0],['is_paid',0],['is_deleted',0]])->get();

        //get total weight
        $sub_weight =0;

        foreach ( $check_shopping_chart as $chart) {
            $sub_weight = $sub_weight + $chart->weight;
        }

        $result =Http::asForm()->post('https://api.rajaongkir.com/starter/cost', [
            'key' => 'c2ebd0c43315ad0936dace31a9bda376',
            'origin' =>$request->origin,
            'destination' => $request->destination,
            'weight' => $sub_weight,
            'courier' => $request->courier,
        ]);

        $jsonString = json_encode($result->json());
        $result = json_decode($jsonString,true);

        $services = $result['rajaongkir']['results'];

        //return $services;
        foreach ($services as $item)
        {
            $uses = $item['costs'];
            foreach ($uses as $v)
            {
                $service= $v['service'];
                if ($service==$request->service)
                {
                    $cost =  $v['cost'];
                    foreach ($cost as $it)
                    {
                        $price = $it['value'];
                    }
                    }
            }
        }

        foreach ( $check_shopping_chart as $chart1) {
            $chart1->is_checkout = 1;
            $chart1->from = $request->origin;
            $chart1->to = $request->destination;
            $chart1->courier = $request->courier;
            $chart1->courier_type = $request->service;
            $chart1->courier_amount = $price;
            $chart1->save();
        }

        $check_shopping_chart = DB::table('products')
                                ->join('shopping_carts', [['products.user_id', '=','shopping_carts.seller_id'],['products.id', '=','shopping_carts.product_id']])
                                ->select('shopping_carts.user_id','shopping_carts.seller_id','shopping_carts.product_id','shopping_carts.qty','products.unit_price',DB::raw('shopping_carts.qty * products.unit_price as amount'),'shopping_carts.is_checkout','shopping_carts.is_paid','shopping_carts.created_at','shopping_carts.updated_at','shopping_carts.is_deleted')
                                ->get();
        //array_push($check_shopping_chart,$request->courier,$request->service,$price);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data'    =>  $check_shopping_chart
        ],200);
    }

    Public function Checkout_Per_Seller_Shopping_Chart(Request $request,$id)
    {

        $check_shopping_chart = ShoppingCart::where([['user_id',$id],['seller_id',$request->seller_id],['is_checkout',0],['is_paid',0],['is_deleted',0]])->get();

        $sub_weight =0;

        foreach ( $check_shopping_chart as $chart) {
            $sub_weight = $sub_weight + $chart->weight;
        }

        //$check_shopping_chart
        $result =Http::asForm()->post('https://api.rajaongkir.com/starter/cost', [
            'key' => 'c2ebd0c43315ad0936dace31a9bda376',
            'origin' =>$request->origin,
            'destination' => $request->destination,
            'weight' => $sub_weight,
            'courier' => $request->courier,
        ]);

        $jsonString = json_encode($result->json());
        $result = json_decode($jsonString,true);

        $services = $result['rajaongkir']['results'];

        //return $services;
        foreach ($services as $item)
        {
            $uses = $item['costs'];
            foreach ($uses as $v)
            {
                $service= $v['service'];
                if ($service==$request->service)
                {
                    $cost =  $v['cost'];
                    foreach ($cost as $it)
                    {
                        $price = $it['value'];
                    }
                    }
            }
        }


        foreach ( $check_shopping_chart as $chart1) {
            $chart1->is_checkout = 1;
            $chart1->from = $request->origin;
            $chart1->to = $request->destination;
            $chart1->courier = $request->courier;
            $chart1->courier_type = $request->service;
            $chart1->courier_amount = $price;
            $chart1->save();
        }


        $check_shopping_chart = DB::table('products')
        ->join('shopping_carts', [['products.user_id', '=','shopping_carts.seller_id'],['products.id', '=','shopping_carts.product_id']])
        ->select('shopping_carts.user_id','shopping_carts.seller_id','shopping_carts.product_id','shopping_carts.qty','products.unit_price',DB::raw('shopping_carts.qty * products.unit_price as amount'),'shopping_carts.is_checkout','shopping_carts.is_paid','shopping_carts.created_at','shopping_carts.updated_at','shopping_carts.is_deleted')
        ->where([['shopping_carts.user_id','=',$id],['shopping_carts.seller_id','=',$request->seller_id],['shopping_carts.is_checkout',1],['shopping_carts.is_paid',0],['shopping_carts.is_deleted',0]])
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data'    =>  $check_shopping_chart
        ],200);


    }
    Public function Check_Delivery_Cost (Request $request)
    {

        $result =Http::asForm()->post('https://api.rajaongkir.com/starter/cost', [
            'key' => 'c2ebd0c43315ad0936dace31a9bda376',
            'origin' =>$request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
        ]);

        $jsonString = json_encode($result->json());
        $result = json_decode($jsonString,true);

        $services = $result['rajaongkir']['results'];

        //return $services;
        foreach ($services as $item)
        {
            $uses = $item['costs'];
            foreach ($uses as $v)
            {
                $service= $v['service'];
                if ($service==$request->service)
                {
                    $cost =  $v['cost'];
                    foreach ($cost as $it)
                    {
                        $price = $it['value'];
                    }
                    }
            }
        }
        return $price;
        //foreach($result as $item) { //foreach element in $arr
        //    $uses = $item['results']; //etc
        //    echo $uses;
        //}
        //foreach($test as $key=>$value){
        //    return $key . "=>" . $value . "<br>";
        //}

        //return json_encode($services);
        //$costs = $services['costs'];
        //return response()->json([
        //     'success' => true,
        //       'message' => 'Data berhasil diupdate',
        //        'data'    =>  $services
        //    ],200);

        //foreach($services['services'] as $i => $v)
        //{
        //    echo $v['service'].'<br/>';
        //}
        //return response()->json([
        //    'success' => true,
        //    'message' => 'Data berhasil diupdate',
        //    'data'    =>  $result->json()
        //],200);
    }
}

