<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Order;

class medicine_wherehouse extends Model
{
   protected $fillable=[
        'name',
   ];

   public function admin(){
    return $this->hasMany(Admin::class);
   }

   public function medicine(){
    return $this->hasMany(Medicine::class);
   }

   public function category(){
    return $this->hasMany(Category::class);
   }

   public function order(){
    return $this->hasMany(Order::class);
   }
}
