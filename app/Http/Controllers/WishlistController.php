<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    Public function add_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'product_id'     => 'required',
        ],);

        if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi dengan lengkap',
                'data'    => $validator->errors()
            ],401);

        }

        $wishlist = new Wishlist();
        $wishlist->user_id = $request->user_id;
        $wishlist->product_id = $request->product_id;
        $saved=$wishlist->save();
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
                'data'    => Null
            ],200);
        }
    }

    public function get_wishlist($user_id)
    {

            $wishlist= Wishlist::firstwhere([['user_id',$user_id],['is_deleted',0]]);
            if ($wishlist){
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditemukan',
                'data'    =>  $wishlist
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    =>  $wishlist
            ],400);
        }
    }
    public function edit_wishlist(Request $request,$id)
    {

            $check_wishlist= Wishlist::firstwhere([['id',$id],['is_deleted',0]]);
            if ( $check_wishlist){

                $wishlist = Wishlist::find($id);
                $wishlist->user_id = $request->user_id;
                $wishlist->product_id = $request->product_id;
                $wishlist->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    =>  $wishlist
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data'    =>  null
            ],400);
        }
    }
    public function delete_wishlist (Request $request,$user_id)
    {
        $check_wishlist = Wishlist::firstwhere([['product_id',$request->product_id],['user_id',$user_id]]);
        if($check_wishlist)
        {
            //$wishlist = Wishlist::find();
            //return $check_wishlist->get();
            $wishlist = Wishlist::find($check_wishlist->id);
            $wishlist->is_deleted = 1;
            $wishlist->save();
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
            ],400);
        }
    }
}
