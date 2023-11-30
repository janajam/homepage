<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\medicine_wherehouse;
use App\Models\Category_item;
use App\Models\Order_item;

class Medicine extends Model
{
    protected $fillable=[
        'scientific_name',
        'trade_name',
        'price',
        'company',
        'quantity_available',
        'expiration_date',
   ];

   public function wherehouse(){
    return $this->belonfsTo(medicine_wherehouse::class);
   }

   public function category_item(){
    return $this->hasOne(Category_item::class);
   }

   public function order_item(){
    return $this->hasOne(Order_item::class);
   }
}
