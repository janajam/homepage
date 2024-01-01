<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineCollection;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Warehouse;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use HttpResponses ;
    public function __construct(){
        $this->middleware('User_Role') ;
    }
    public function GetAllCategory() : JsonResponse
    {
        $category = Category::all() ;

        return response()->json([
            'status' => 1 ,
            'data' => $category  ,
            'message' => 'all categories'
        ]);

    }
    public function GetCategoryItem(Request $request) : JsonResponse
    {
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


    public function GetAllMedicine() : JsonResponse
    {
        //$med = Medicine::query()->paginate(2);
       $medicine = new MedicineCollection(Medicine::query()->orderByDesc('created_at')->get())  ;
       return $this->success($medicine , 'all medicine');

    }
    public function GetLastAddMedicine(Request $request) : JsonResponse
    {
        $med = Medicine::query()->orderByDesc('created_at')->take(10)->get() ;
         $medicine = new MedicineCollection($med);
         return $this->success($medicine , 'last 10 add');
    }


    public function searchForMedicineName(Request $request) : JsonResponse
    {
        $text = $request['text'] ;
        $medicines = Medicine::query()->where('trade_name' , 'LIKE' , '%'.$text.'%')->get() ;
        $category = Category::query()->where('name' , 'LIKE' , '%'.$text.'%')->get();

        $med = new MedicineCollection($medicines) ;
        $data = [
            'medicine' => $med ,
            'category' => $category,
        ];
        return response()->json([
            'status' => 1 ,
            'data' => $data ,
            'message' => 'result'  ,
         ]);
    }

    public function GetAllCategoryMedicine(Request $request) : JsonResponse
    {
        $category_id = $request['category_id'] ;
        $category = Category::query()->where('id' , '=' , $category_id) ;
        if(!$category){
            return response()->json([
               'status' => 0 ,
               'data' =>  [] ,
               'message' => 'invalid...'
            ]);
        }
        $categoryItems = Medicine::query()->where('category_id' , '=' , $category['id']);
        $data = new MedicineCollection($categoryItems) ;
        return response()->json([
           'status' => 1 ,
           'data' => $data ,
           'message' => 'all category items' ,
        ]);
    }


    public function GetWarehouseCategory(Request $request) : JsonResponse
    {
        $warehouse_id = $request['warehouse_id'] ;
        $warehouse = Warehouse::query()->where('id' , '=' , $warehouse_id) ;
        if(!$warehouse){
            return response()->json([
               'status' => 0 ,
               'data' => [] ,
               'message' => 'invalid....' ,
            ]);
        }

        $category = Category::query()->where('warehouse_id' , '=' , $warehouse['id']);
        return response()->json([
           'status' => 0 ,
           'data' => $category ,
           'message' => 'all category' ,
        ]);
    }

}
