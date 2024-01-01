<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineCollection;
use App\Models\Category;
use App\Models\Medicine;
use App\Traits\HttpResponses;
use App\Traits\ReturnDataName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CreateCategoryController extends Controller
{
    use HttpResponses , ReturnDataName ;

    public function __construct(){
        $this->middleware('Admin_Role') ;
    }
    public function createCategory(Request $request) : JsonResponse
    {
         $request->validate([
             'name' => 'required' ,
             'image' => 'image|mimes:png,jpg,jpeg|max:2048',
         ]);
         $admin = $request->user() ;

         $category = Category::query()->where('name' , '=' , $request['name'])->first() ;
         if($category){
             $message = 'category it is already exits';
             return $this->error([] , $message , 400);
         }

//        $imageName = time() . '.' . $request['image']->extension();
//        $request['image']->storeAs('images', $imageName);

        $category =  Category::query()->create([
             'name' => $request['name'],
             'warehouse_id' => $admin['warehouse_id'],
             'image' => 'new_test.png' ,
         ]);

        $message = 'category has been created successfully' ;
         return $this->success($category , $message);

    }

    public function showCategory(Request $request) : JsonResponse
    {
        $admin = $request->user() ;
        $categories = Category::query()->where('warehouse_id' , '=' , $admin['warehouse_id'])->get() ;

        $count = 0 ;
        foreach ($categories as $category){
            $categories[$count]['warehouse'] = $this->WarehouseName($category['warehouse_id']);
            $count++;
        }

        $message = 'all category' ;
        return $this->success($categories , $message) ;
    }

    public function GetAllCategoryName(Request $request) : JsonResponse
    {
        $admin = $request->user();
        $categories = Category::query()->where('warehouse_id' , '=' , $admin['warehouse_id'])->get();

        $data  = [] ;
        $count = 0 ;
        foreach ($categories as $category){
            $data[$count] = $category['name'];
            $count++;
        }
        $message = 'all categories' ;
        return $this->success($data , $message);
    }

    public function showCategoryItem(Request $request) : JsonResponse
    {
        $admin = $request->user();
        $id = $request->route('id') ;
        $medicines = Medicine::query()->where('category_id' , '=' , $id)->get() ;
        if(count($medicines) == 0){
            $message = 'there is no item' ;
            return $this->error([] , $message , 404) ;
        }
        $message = 'all category item' ;
        $data = new MedicineCollection($medicines);
        return $this->success($data , $message);
    }
}
