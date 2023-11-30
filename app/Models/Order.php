<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\medicine_wherehouse;
use App\Models\User;
use App\Models\Order_item;

class Order extends Model
{
    protected $fillable=[
        'order_date',
        'total_amount',
        'status',
        'payment_status',
    ];

    public function wherehouse(){
        return $this->belonfsTo(medicine_wherehouse::class);
    }

    public function user(){
        return $this->belonfsTo(User::class);
       }

       public function Order_item(){
        return $this->hasMany(Order_item::class);
       }
}
