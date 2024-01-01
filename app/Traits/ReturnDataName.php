<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\User;
use App\Models\Warehouse;

trait ReturnDataName{
    protected function CategoryName($category_id) : string
    {
        $category = Category::query()->where('id' , '=' , $category_id)->first();
        return $category['name'];
    }

    protected function WarehouseName($warehouse_id) : string
    {
        $warehouse = Warehouse::query()->where('id' , '=' , $warehouse_id)->first();
        return $warehouse['name'];
    }

    protected function UserName($user_id)
    {
       $user = User::query()->where('id' , '=' , $user_id)->first();
       return $user['name'];
    }

}
