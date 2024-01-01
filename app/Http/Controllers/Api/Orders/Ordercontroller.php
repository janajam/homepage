<?php

namespace App\Http\Controllers\Api\Orders;

use App\Enums\PaymentStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\HttpResponses;
use App\Traits\ReturnDataName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class Ordercontroller extends Controller
{
    use ReturnDataName ,HttpResponses;
    public function __construct(){
        $this->middleware('User_Role') ;
    }

   public function OrderAllCartItem(Request $request) : JsonResponse
   {
       $user = $request->user() ;

       $cart = Cart::query()->where('user_id' , '=' , $user['id'])->first() ;
       if(!$cart) {
           return response()->json([
              'status' => 0 ,
              'data' => [] ,
              'message' => 'invalid...'
           ]);
       }
       $cartItems = CartItem::query()->where('cart_id' , '=' , $cart['id'])->get();

       if(count($cartItems) == 0 ) {
           return response()->json([
               'status' => 0,
               'data' => [],
               'message' => 'your cart is empty',
           ]);
       }
       $count_m = 0 ;
       $count_w = 0 ;
       $medicines = []  ;
       $warehouse_ids = [] ;
       foreach ($cartItems as $cartItem){
           $med = Medicine::query()->where('id' , '=' , $cartItem['medicine_id'])->first() ;
           $id = $med['warehouse_id'] ;
           if(!in_array($id , $warehouse_ids)){
               $warehouse_ids[$count_w] = $id ;
               $count_w++;
           }
           $med['quantity'] = $cartItem['quantity'] ;
           $medicines[$count_m] = $med ;
           $count_m++ ;
       }


       //check quantity_available ...
       foreach ($medicines as $check){
           if($check['quantity_available']  < $check['quantity']){
               return response()->json([
                  'status' => 0 ,
                  'data' => $check['trade_name'] .' => the quantity available : '.$check['quantity_available'],
                  'message' => 'the quantity you want it is not available now .. '
               ]);
           }
           elseif ($check['quantity_for_sale'] < $check['quantity'])
           {
               return response()->json([
                   'status' => 0 ,
                   'data' => $check['trade_name'] .' => the quantity available you can order it just : '.$check['quantity_for_sale'],
                   'message' => 'the quantity you want it not allowed for this medicine sorry .. ',
               ]);
           }
       }

       $count = 0 ;
       $orders = [] ;
       foreach ($warehouse_ids as $id){
           $orders[$count] =  Order::query()->create([
              'order_date' => now(),
              'status' => Status::ReceivedIt->value ,
              'payment_status' => PaymentStatus::UnPaid->value ,
              'user_id'  => $user['id'] ,
              'warehouse_id' =>  $id ,
           ]);
           $count++ ;
       }

       foreach ($orders as $order) {
           $medicine_cnt = count($medicines) ;
           $total_price = 0 ;
           while ($medicine_cnt--){
                  if($order['warehouse_id'] == $medicines[$medicine_cnt]['warehouse_id']) {
                      $orderItem = OrderItem::query()->create([
                           'order_id' => $order['id'] ,
                           'medicine_id' => $medicines[$medicine_cnt]['id'] ,
                           'price' => $medicines[$medicine_cnt]['price'],
                           'total_price'  => $medicines[$medicine_cnt]['price'] * $medicines[$medicine_cnt]['quantity'] ,
                           'quantity' => $medicines[$medicine_cnt]['quantity'] ,
                      ]) ;
                      $total_price = $total_price +  $orderItem['total_price'] ;
                      $med_ =  Medicine::query()->where('id' , '='  , $medicines[$medicine_cnt]['id'])->first();

                      if(($med_['quantity_available']  - $medicines[$medicine_cnt]['quantity'] ) == 0 ){
                          $med_->update([
                              'quantity_available' => $med_['quantity_available'] - $medicines[$medicine_cnt]['quantity'],
                              'sold_out' => true,
                          ]);
                      }else if(($med_['quantity_available'] - $medicines[$medicine_cnt]['quantity']) != 0 ){
                          $med_->update([
                              'quantity_available' => $med_['quantity_available'] - $medicines[$medicine_cnt]['quantity'],
                          ]);
                      }

                  }

           }
           $order->update([
              'total_price' => $total_price ,
           ]);
       }

      foreach ($cartItems as $item){
          $item->delete() ;
      }
      return response()->json([
          'status' => 1 ,
          'data' => [],
          'message' => 'your order is in preparation' ,
      ]) ;

   }

   public function GetAllOrder(Request $request) : JsonResponse
   {
       $user = $request->user() ;
       $orders = Order::query()->where('user_id' , '=' , $user['id'])->orderByDesc('order_date')->get();
       if(count($orders) == 0 ){
           $message = 'you do not have any order .. ';
           return $this->error([] ,$message , 404);
       }

       $count = 0 ;
       foreach ($orders as $order){
           $orders[$count]['warehouse'] = $this->WarehouseName($order['warehouse_id']);
           $orders[$count]['user'] = $this->UserName($order['user_id']);
           $count++;
       }

       $message =' your orders .. ';
       return $this->success($orders , $message);
   }


    public function GetOrderDetails(Request $request) : JsonResponse
    {
        $order_id = $request->route('ID') ;
        $order = Order::query()->where('id' , '=' , $order_id)->first();
        if (!$order){
            return $this->error([] , 'invalid ID .. ' , 404);
        }
        $orderItems = OrderItem::query()->where('order_id' , '=' , $order['id'])->get() ;

        $data = [] ;
        $count = 0;
        foreach ($orderItems as $orderItem){
            $med = Medicine::query()->where('id' , '=' , $orderItem['medicine_id'])->first();
            $med['quantity'] = $orderItem['quantity'];
            $med['total_price'] = $orderItem['total_price'];
            $med['category'] = $this->CategoryName($med['category_id']);
            $med['warehouse'] = $this->WarehouseName($med['warehouse_id']);
            $data[$count] = $med ;
            $count++;
        }
        return $this->success($data, 'your Order');

    }
   public function deleteOrder(Request $request) : JsonResponse
   {
       $id = $request->route('DEL_ID') ;
       $order = Order::query()->where('id' , '=' , $id)->first();
       if(!$order){
           $message = 'invalid .. ';
           return $this->error([] , $message , 404);
       }
       if($order['status'] == Status::InPreparation->value || $order['status'] == Status::OrderSent->value){
           //406 Not Acceptable :
           $message = 'Not Acceptable' ;
           return $this->error([] ,$message  , 406) ;
       }

       $orderItems = OrderItem::query()->where('order_id' , '=' , $order['id'])->get() ;
       foreach ($orderItems as $orderItem){
           $orderItem->delete();
       }
       $order->delete();
       $message = 'order has been deleted successfully';
       return $this->success([] , $message);

   }
}
