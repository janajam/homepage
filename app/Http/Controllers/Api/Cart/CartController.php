<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineCollection;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Medicine;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use HttpResponses;
    public function __construct(){
        $this->middleware('User_Role') ;
    }
    public function AddToCart(Request $request) : JsonResponse
    {
        $request->validate([
           'medicine_id' => 'required' ,
           'quantity' => 'integer',
        ]);

        $user = $request->user() ;
        $medicine = Medicine::query()->where('id' , '=' , $request['medicine_id']);

        if(!$medicine->exists()){
            $message = 'invalid';
            return $this->error([] , $message , 404) ;
        }
        $medicines = Medicine::query()->where('id' , '=' , $request['medicine_id'])->first();

        if($request['quantity'] > $medicines['quantity_available']){
            $message = 'the quantity you want it is not available now ' ;
            return $this->error([] , $message , 406) ;
        }
        else if($request['quantity'] > $medicines['quantity_for_sale']){
            $message = 'the quantity you want it is not available now' ;
            return $this->error([] , $message , 406) ;
        }

        $cart = Cart::query()->where('user_id' , '=' , $user['id'])->first();

        $cartItem = CartItem::query()->where('cart_id' , '=' , $cart['id'])
                                     ->where('medicine_id' , '=' , $medicines['id']);
        if($cartItem->exists()){
            $message = 'you have already added it ' ;
            $cartItem = CartItem::query()->where('cart_id' , '=' , $cart['id'])
                                         ->where('medicine_id' , '=' , $medicines['id'])->first();
            if($cartItem['quantity'] != $request['quantity']){
                CartItem::query()->where('id' , '=' , $cartItem['id'])->update([
                    'quantity' => $request['quantity'],
                ]);
                $message = 'updated successfully';
                return $this->success([] , $message);
            }
            return $this->error([] , $message , 400);
        }

        CartItem::query()->create([
           'cart_id' => $cart['id']  ,
           'medicine_id' => $medicines['id'],
           'quantity' => $request['quantity'] ,
        ]);

        $message  = 'add successfully' ;
        return $this->success([] , $message);


    }

    public function ShowCartItem(Request $request) : JsonResponse
    {

        $user = $request->user() ;

        $cart = Cart::query()->where('user_id' , '=' , $user['id'])->first();
        if(!$cart){
            $message = 'invalid' ;
            return $this->error([] , $message , 404);
        }
        $cartItems = CartItem::query()->where('cart_id' , '=' , $cart['id'])->get();

        if(count($cartItems) == 0){
            $message = 'empty cart';
            return $this->success([] , $message ,);
        }
        $medicines = [] ;
        $count = 0 ;
        foreach ($cartItems as $cartItem)
        {
            $medicine = Medicine::query()->where('id' ,  '=', $cartItem['medicine_id'])->first();
            $medicines[$count] = $medicine ;
            $medicines[$count]['cartItem_id'] = $cartItem['id'];
            $count++;
        }

        $data = new MedicineCollection($medicines) ;
        $message = 'all Cart Item ' ;
        return $this->success($data , $message);


    }

    public function EditCartItemQuantity(Request $request) : JsonResponse
    {
        $request->validate([
           'cartItem_id' => 'required',
           'quantity' => 'required|integer' ,
        ]);

        $user = $request->user() ;

        $cart = Cart::query()->where('user_id' , '=' , $user['id']) ;
        if(!$cart){
            return $this->error([] , 'not found' ,404);
        }
        $cartItem = CartItem::query()->where('id' , '=' , $request['cartItem_id'])->first();

        if(!$cartItem){
            return $this->error([] , 'invalid cartItem  ID :)' , 404);
        }

        $medicine = Medicine::query()->where('id' , '=' , $cartItem['medicine_id'])->first();
        $quantity = $medicine['quantity_available'] ;
        $quantity_for_sale = $medicine['quantity_for_sale'];

        if($quantity < $request['quantity']){
            $message = 'the available quantity is : ' . $quantity ;
            return $this->error([] , $message , 406);
        }
        else if ($quantity_for_sale < $request['quantity']){
            $message = 'the available quantity for sale is : ' . $quantity_for_sale ;
            return $this->error([] , $message , 406);
        }

        CartItem::query()->where('id' , '=' , $request['cartItem_id'])->update([
           'quantity' => $request['quantity'],
        ]);
        $cartItem  = CartItem::query()->where('id' , '=' , $request['cartItem_id'])->first() ;

        return $this->success($cartItem , 'updated successfully');
    }


    public function deleteCartItem(Request $request) : JsonResponse
    {
        $user = $request->user() ;
        //cartItem_id ;
        $id = $request['cartItem_id'];
        $cart = Cart::query()->where('user_id' , '=' , $user['id'])->first();
        $cartItem = CartItem::query()->where('id' , '=' , $id)->first();

        if($cart['id'] != $cartItem['cart_id']){
            $message = 'Unauthorized';
            return $this->error([] , $message , 401);
        }

        $cartItem->delete() ;
        $message = 'cartItem has been deleted successfully';
        return $this->success([],$message);
    }
}
