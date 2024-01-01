<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        $admin = $request->user() ;
        // Fetch orders with related items
        $orders = Order::query()->where('warehouse_id' , '=' , $admin['warehouse_id'])->get();
        foreach ($orders as $order){
            $cartItem = OrderItem::query()->where('order_id' , '=' , $order['id'])->get();
            $user = User::query()->where('id' , '=' , $order['user_id'])->first();
            foreach ($cartItem as $item){
                $med = Medicine::query()->where('id' , '=' , $item['medicine_id'])->first();
                $item['medicine'] = $med['trade_name'];
            }
            $order['user'] = $user['name'];
            $order['CartItem'] = $cartItem ;
        }

        // Generate PDF using Laravel PDF
        $pdf = PDF::loadView('pdf.order_report', ['orders' => $orders ]);

        // Set the name for the downloaded PDF file
        $filename = 'order_report_' . date('YmdHis') . '.pdf';

        // Download the PDF file
        return $pdf->download($filename);
    }
}
