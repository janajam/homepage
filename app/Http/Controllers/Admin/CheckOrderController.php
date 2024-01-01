<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\HttpResponses;
use App\Traits\ReturnDataName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckOrderController extends Controller
{
    use HttpResponses  , ReturnDataName;
    public function __construct(){
        $this->middleware('Admin_Role') ;
    }

    public function GetAllWarehouseOrder(Request $request) : JsonResponse
    {
        $admin = $request->user() ;
        $orders = Order::query()->where('warehouse_id' , '=' , $admin['warehouse_id'])
            ->orderByDesc('order_date')->get();

        if(count($orders) == 0 ){
            $message = 'there is no order';
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
        $order_id = $request->route('A_ID') ;
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

    public function ChangePaymentStatusToPaid(Request $request) : JsonResponse
    {
        $id = $request->validate([
            'order_id' => 'required|integer',
        ]);
        $order = Order::query()->where('id' , '=',$id)->first();
        if(!$order){
            $message = 'invalid ID' ;
            return $this->error([] , $message , 404);
        }

        if($order['payment_status'] == PaymentStatus::Paid->value)
        {
            $message = 'close Order';
            return $this->error([] , $message , 406) ;
        }

        if($order['status']  != Status::OrderSent->value)
        {
            $message = 'invalid change';
            return $this->error([], $message,406);
        }
        $order->update([
           'payment_status' => PaymentStatus::Paid->value  ,
        ]);
        $message = 'transform successfully' ;
        return $this->success([] ,$message );
    }

    public function ChangeOrderStatusToInPreparation(Request $request) : JsonResponse
    {

        $id = $request->validate([
            'order_id' => 'required|integer',
        ]);
        $order = Order::query()->where('id' , '=',$id)->first();
        if($order['payment_status'] == PaymentStatus::Paid->value)
        {
            $message = 'close Order';
            $this->error([] , $message , 406) ;
        }
        if($order['status'] == Status::InPreparation->value || $order['status'] == Status::OrderSent->value)
        {
            $message = 'Not Acceptable ' ;
            return $this->error([] , $message , 406) ;
        }
        $order->update([
            'status' => Status::InPreparation->value ,
        ]);
        $message = 'your Order Now in the preparation' ;
        return $this->success([] , $message);

    }


    public function ChangeOrderStatusToOrderSent(Request $request) : JsonResponse
    {
        $id = $request->validate([
            'order_id' => 'required|integer',
        ]);
        $order = Order::query()->where('id' , '=',$id)->first();
        if($order['payment_status'] == PaymentStatus::Paid->value)
        {
            $message = 'close Order';
            $this->error([] , $message , 406) ;
        }
        if($order['status'] == Status::OrderSent->value)
        {
            $message = 'Not Acceptable ' ;
            return $this->error([] , $message , 406) ;
        }
        $order->update([
            'status' => Status::OrderSent->value ,
        ]);
        $message = 'Order in the way ' ;
        return $this->success([] , $message);

    }

}
