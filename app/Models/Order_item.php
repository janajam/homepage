<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Medicine;
use App\Models\Order;

class Order_item extends Model
{
   protected $fillable=[
     'quantity',
     'price',
   ];

   public function mediciness(){
    return $this->belonfsTo(Medicine::class);
   }

   public function Order(){
    return $this->belonfsTo(Order::class);
   }
}
