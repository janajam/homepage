<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\medicine_wherehouse;
use App\Models\Category_item;

class Category extends Model
{
   protected $fillable=[
      'name',
   ];

   public function wherehouse(){
    return $this->belonfsTo(medicine_wherehouse::class);
   }

   public function Category_item(){
    return $this->hasMany(Category_item::class);
   }

}
