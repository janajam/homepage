<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Medicine;
use App\Models\Category;

class Category_item extends Model
{
    public function medicines(){
        return $this->belonfsTo(Medicine::class);
       }
       public function Category(){
        return $this->belonfsTo(Category::class);
       }
}
