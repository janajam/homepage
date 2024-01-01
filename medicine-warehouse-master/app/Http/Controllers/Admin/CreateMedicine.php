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


class CreateMedicine extends Controller
{
    use HttpResponses , ReturnDataName;
    public function __construct(){
        $this->middleware('Admin_Role') ;
    }
    public function create(Request $request) : JsonResponse
    {
        $request->validate([
            'scientific_name' => 'required',
            'trade_name' => 'required',
            'price' => 'required' ,
            'company' => 'required' ,
            'quantity_available' => 'required' ,
            'expiration_date' => 'required' ,
            'category_name' => 'required' ,
            'quantity_for_sale' => 'required' ,
            'image' => 'image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $admin = $request->user()  ;
        $category = Category::query()->where('name' , '=' , $request['category_name'])->first();
        if(!$category){
            $message = 'invalid category';
            return $this->error([] , $message , 404);
        }

//        $imageName = time() . '.' . $request['image']->extension();
//        $request['image']->storeAs('images', $imageName);


        $medicine = Medicine::query()->create([
           'scientific_name' => $request['scientific_name'] ,
           'trade_name' => $request['trade_name'] ,
           'price' => $request['price'] ,
           'company' => $request['company'] ,
           'quantity_available' => $request['quantity_available'] ,
           'expiration_date' => $request['expiration_date'] ,
           'warehouse_id' => $admin['warehouse_id'],
           'category_id' => $category['id'],
           'quantity_for_sale' => $request['quantity_for_sale'],
            'image' => '' ,

        ]);

        if(!$medicine){
            $message = 'fail' ;
            return $this->error([], $message , 400);
        }
         $medicine['category'] = $this->CategoryName($medicine['category_id']);
         $medicine['warehouse'] = $this->WarehouseName($medicine['warehouse_id']);
        $message = 'created successfully' ;
        //$data = new MedicineCollection($medicine) ;
        return $this->success($medicine , $message);

    }

    public function deleteMedicine(Request $request) : JsonResponse
    {
        $id = $request->route('med_id');
        $medicine  = Medicine::query()->where('id' , '=' , $id)->first() ;
        if(!$medicine){
            $message = 'not exist' ;
            return $this->error([] , $message , 404);
        }
//        File::delete(public_path('storage/image'.$medicine['image']));

        $medicine->delete() ;
        $message = 'deleted successfully';
        return $this->success([] , $message) ;
    }


    public function editQuantity(Request $request) : JsonResponse
    {
        $request->validate([
            'medicine_id' => 'required' ,
            'quantity' => 'required|integer|min:0' ,
        ]);


        $med = Medicine::query()->where('id','=' ,$request['medicine_id'])->first();

        if(!$med){
            return $this->error([] , 'invalid ID' , 404);
        }
       $med->update([
             'quantity_available' => $request['quantity']
          ]);
        $med = Medicine::query()->where('id' , '=' , $request['medicine_id'])->get();
        $message = ' has been updated successfully ' ;
        $data = new MedicineCollection($med) ;
        return $this->success($data, $message);
    }

    public function editPrice(Request $request) : JsonResponse
    {
        $request->validate([
            'medicine_id' => 'required' ,
            'price' => 'required' ,
        ]);

        $med = Medicine::query()->where('id','=' ,$request['medicine_id'])->first();
        if(!$med){
            return $this->error([] , 'invalid ID' , 404);
        }
        $med->update([
            'price' => $request['price'],
        ]);
        $med = Medicine::query()->where('id' , '=' , $request['medicine_id'])->get();
        $message = 'has been updated successfully ' ;
        $data = new MedicineCollection($med) ;
        return $this->success($data, $message);
    }

    public function GetMedicine(Request $request) : JsonResponse
    {
        $Admin = $request->user() ;
        $medicines = Medicine::query()->where('warehouse_id' , '=' , $Admin['warehouse_id'])->get();
        if(count($medicines) == 0)
        {
            $message = 'empty';
            return $this->error([] , $message , 404);
        }
        $data = new MedicineCollection($medicines);
        $message = 'all medicine';
        return $this->success($data , $message);

    }


    public function searchForMedicineCategoryName(Request $request) : JsonResponse
    {
        $admin = $request->user();
        $text = $request['text'] ;

        $medicines = Medicine::query()->where('warehouse_id' , '=' , $admin['warehouse_id']);
        $medicines = $medicines->where('trade_name' , 'LIKE' , '%'.$text.'%')->get();

        $category = Category::query()->where('warehouse_id' , '=' , $admin['warehouse_id']);
        $category = $category->where('name' , 'LIKE' , '%'.$text.'%')->get();

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




}
