<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineCollection;
use App\Mail\WelcomeEmail;
use App\Models\Favorite;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class testController extends Controller
{

    //Laravel Get Logged User Data using Auth Facade
    public function user_data(Request $request)
    {
        $user = $request->user() ;
        return $user ;
    }

    public function ware(){
         $warehouse = Auth::user()  ;
         $id = Warehouse::query()->where('id' , '=' , $warehouse['warehouse_id']) ;
         return response()->json([
            'data' => $warehouse,
         ]);
    }


    public function med() {
        $data = Medicine::latestAddition()->get();
        return $data ;
    }

    public function delete(){

        $isExist = '1700553305.jpg' ;
        return File::delete(public_path('storage/image'.$isExist));

    }

    public function test(Request $request)
    {
        $user = $request->user();
        $warehouse = Warehouse::query()->where('name' , '=' , $request['warehouse_name'])->get() ;

        $order = Order::query()->where('user_id' , '=' , $user['id'] )->get() ;

        return $order ;
    }

    public function collection(Request $request)
    {

        return new MedicineCollection(Medicine::all()) ;
    }

      private array $data = [1 , 2 , 3];
      public function print()
      {
          $id = 5 ;
          if(in_array($id , $this->data)){
              log::info('fuck');
          }
          log::info('fuck you');

      }


      public function GetImage(){
          $image = '1700553305.jpg' ;
          $imagePath = asset('images/' . $image);
          return response()->json(['image_url' => $imagePath]);
      }

      public function check(){
          try {
              DB::connection()->getPdo();
              if(DB::connection()->getDatabaseName()){
                  echo "Yes! Successfully connected to the DB: " . DB::connection()->getDatabaseName();
              }else{
                  die("Could not find the database. Please check your configuration.");
              }
          } catch (\Exception $e) {
              die("Could not open connection to database server.  Please check your configuration.");
          }
      }

    }
