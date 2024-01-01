<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Traits\ReturnDataName;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfUserController extends Controller
{
    use ReturnDataName ;
//    public function __construct(){
//        $this->middleware('User_Role') ;
//    }

    public function generateUserPdf(Request $request)
    {
        $user = $request->user() ;
        // Fetch orders with related items
        $orders = Order::query()->where('user_id' , '=' , $user['id'])->get();
        foreach ($orders as $order){
            $cartItem = OrderItem::query()->where('order_id' , '=' , $order['id'])->get();
            foreach ($cartItem as $item){
                $med = Medicine::query()->where('id' , '=' , $item['medicine_id'])->first();
                $item['medicine'] = $med['trade_name'];
            }
            $order['user'] = $user['name'];
            $order['CartItem'] = $cartItem ;
            $order['warehouse'] = $this->WarehouseName($order['warehouse_id']);
        }

        // Generate PDF using Laravel PDF
        $pdf = PDF::loadView('pdf.User_order_report', ['orders' => $orders ]);

        // Set the name for the downloaded PDF file
        $filename = 'order_report_' . date('YmdHis') . '.pdf';

        // Download the PDF file
        return $pdf->download($filename);
    }
}
