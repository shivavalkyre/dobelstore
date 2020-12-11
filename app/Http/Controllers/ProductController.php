<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuantity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
   public function add_product(Request $request)
   {
    $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'product_name'     => 'required',
        'kategori'   => 'required',
        'product_desc' =>'required',
        'unit' => 'required',
    ],);

    if($validator->fails()) {

        return response()->json([
            'success' => false,
            'message' => 'Silahkan isi dengan lengkap',
            'data'    => $validator->errors()
        ],401);

    }

    $user = User::firstwhere([['id',$request->user_id],['is_seller',1]]);
    if($user){
        $product = new Product;
        $product->user_id           = $request->user_id;
        $product->product_name      = $request->product_name;
        $product->kategori          = $request->kategori;
        $product->product_desc      = $request->product_desc;
        $product->unit              = $request->unit;
        $product->unit_price        = $request->unit_price;
        $product->weight            = $request->weight;
        $saved = $product->save();
        if(!$saved){
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi dengan lengkap',
                'data'    => $validator->errors()
            ],401);
        }else
        {

            $product_qty = new ProductQuantity();
            $product_qty->user_id = $request->user_id;
            $product_qty->product_id = $product->id;
            $product_qty->qty = $request->qty;
            $product_qty->save();

            return response()->json([
                'success' => true,
                'message' => 'Tambah data Sukses',
                'data'    => Null
            ],200);
        }
      }else{
        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan',
            'data'    => null
        ],401);
      }
   }

   public function add_product_qty(Request $request)
   {
    $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'product_id'     => 'required',
        'qty'   => 'required',

    ],);

    if($validator->fails()) {

        return response()->json([
            'success' => false,
            'message' => 'Silahkan isi dengan lengkap',
            'data'    => $validator->errors()
        ],401);

    }
        $product_qty = new ProductQuantity;
        $product_qty->user_id = $request->user_id;
        $product_qty->product_id = $request->product_id;
        $product_qty->qty = $request->qty;
        $product_qty->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data'    =>  null
        ],200);
   }

   public function edit_product_qty(Request $request,$id)
   {
        $check_product = ProductQuantity::firstwhere('id',$id);
        if($check_product){
            $product = ProductQuantity::find($id);
            $product->product_id = $request->product_id;
            $product->qty = $request->qty;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    =>  null
            ],200);
        }
   }

   public function edit_product(Request $request,$id)
   {

    $check_product = Product::firstwhere('id',$id);
    if($check_product){
    $product = Product::find($id);
    $product->product_name      = $request->product_name;
    $product->kategori          = $request->kategori;
    $product->product_desc      = $request->product_desc;
    $product->unit              = $request->unit;
    $product->unit_price        = $request->unit_price;
    $product->weight            = $request->weight;
    $product->save();
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

   public function get_products()
   {

    //$product = DB::table('products')
    //->join('product_quantities', [['products.user_id', '=', 'product_quantities.user_id'],['products.id','=','product_quantities.product_id']])
    //->select('products.id','products.product_name','products.kategori','products.product_desc', DB::raw('SUM(product_quantities.qty) as qty'),'products.product_pict')
    //->where('product_quantities.is_deleted','=',0)
    //->groupBy('products.id', 'products.product_name','products.kategori','products.product_desc','products.unit')
    //->get();
        $TSQL="SELECT
        `products`.`id`,
         `products`.`user_id`,
         `products`.`product_name`,
         `products`.`kategori`,
         `products`.`product_desc`,
         `products`.`unit`,
         `products`.`unit_price`,
         `products`.`weight`,
         `products`.`product_pict`,
           IF(is_checkout=1,(`s`.`Sum_qty`-`shopping_carts`.`qty`),`s`.`Sum_qty`) AS `qty`,
           `products`.`created_at`,
           `products`.`updated_at`,
           `products`.`is_actived`,
           `products`.`is_advertized`
       FROM
           products

       INNER JOIN
       (SELECT
         `user_id`, `product_id`, SUM(`qty`) AS 'Sum_qty', `is_deleted`
       FROM
         `product_quantities`
       WHERE
         (`is_deleted` = 0)
       GROUP BY
         `user_id`, `product_id`, `is_deleted`) s ON `products`.`user_id` = `s`.`user_id` AND
       `products`.`id` = `s`.`product_id`

        LEFT JOIN
        `shopping_carts` ON `s`.`user_id` = `shopping_carts`.`seller_id`
       AND `s`.`product_id` = `shopping_carts`.`product_id`
        WHERE `is_actived` = 1";

        $product = DB::select($TSQL);

        //$product = Product::where('is_deleted',0)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditemukan',
            'data'    =>  $product
        ],200);
   }

   public function get_product($id)
   {
    //$product = Product::firstwhere([['id',$id],['is_deleted',0]]);
    //$product = DB::table('products')
    //->join('product_quantities', [['products.user_id', '=', 'product_quantities.user_id'],['products.id','=','product_quantities.product_id']])
    //->select('products.id','products.product_name','products.kategori','products.product_desc', DB::raw('SUM(product_quantities.qty) as qty'),'products.product_pict')
    //->where([['products.id', '=', $id],['product_quantities.is_deleted','=',0]])
    //->groupBy('products.id', 'products.product_name','products.kategori','products.product_desc','products.unit')
    //->get();
    $TSQL=" SELECT
    `products`.`id`,
     `products`.`user_id`,
     `products`.`product_name`,
     `products`.`kategori`,
     `products`.`product_desc`,
     `products`.`unit`,
     `products`.`unit_price`,
     `products`.`weight`,
     `products`.`product_pict`,
     IF(is_checkout=1,(`s`.`Sum_qty`-`shopping_carts`.`qty`),`s`.`Sum_qty`) AS `qty`,
           `products`.`created_at`,
           `products`.`updated_at`,
           `products`.`is_actived`,
           `products`.`is_advertized`
   FROM
       products

   INNER JOIN
   (SELECT
     `user_id`, `product_id`, SUM(`qty`) AS 'Sum_qty', `is_deleted`
   FROM
     `product_quantities`
   WHERE
     (`is_deleted` = 0)
   GROUP BY
     `user_id`, `product_id`, `is_deleted`) s ON `products`.`user_id` = `s`.`user_id` AND
   `products`.`id` = `s`.`product_id`

    LEFT JOIN
    `shopping_carts` ON `s`.`user_id` = `shopping_carts`.`seller_id`
   AND `s`.`product_id` = `shopping_carts`.`product_id`

    WHERE
    `products`.`id`=$id and  `products`.`is_actived` = 1";

    $product= DB::select($TSQL);
    if($product){
        //return $product;
        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data'    =>  $product
        ],200);
        }else
        {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    =>  $product
            ],404);
        }
   }

   public function delete_product($id)
   {
    $check_product = Product::firstwhere('id',$id);
    if($check_product)
    {
        //Product::destroy($id);
        $product = Product::find($id);
        $product->is_deleted = 1;
        $product->save();
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

   public function delete_product_qty($id)
   {
    $check_product = ProductQuantity::firstwhere('id',$id);
    if($check_product)
    {
        //Product::destroy($id);
        $product = ProductQuantity::find($id);
        $product->is_deleted = 1;
        $product->save();
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

   public function image_product(Request $request,$id)
   {
       $request->validate([
           'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
       ]);

       $imageName = time().'.'.$request->image->extension();

       $request->image->move(public_path('images'), $imageName);

       /* Store $imageName name in DATABASE from HERE */

       $check_product = Product::firstwhere('id',$id);
       if($check_product){
       $product = Product::find($id);
       $product->product_pict =  $imageName;
       $product->save();

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
}
