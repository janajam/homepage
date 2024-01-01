<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineCollection;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Medicine;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use HttpResponses;
    public function __construct(){
        $this->middleware('User_Role') ;
    }
    public function GetUserData(Request $request): JsonResponse
    {
        $userData = $request->user();

        $cart = Cart::query()->where('user_id' , '=' , $userData['id'])->first();

        $data = [
            'user' => $userData ,
            'cart_id' => $cart['id'] ,
        ] ;

        $message = 'profile data' ;
        return $this->success($data  ,$message ) ;
    }

    public function addFavorite(Request $request): JsonResponse
    {
        $id = $request['medicine_id'] ;
        $user = $request->user();
        $medicine = Medicine::query()->findOrFail($id);

        if (!$medicine) {
            $message = 'invalid' ;
            return $this->error([] , $message , 404) ;
        }
        $favorite = Favorite::query()->where('user_id' , '=' , $user['id'] )
                                     ->where('medicine_id' , '=' , $id)->first();
        if($favorite){
            $favorite->delete();
            $message = 'has been deleted successfully' ;
            return $this->success(false , $message) ;
        }

        Favorite::query()->create([
            'user_id' => $user['id'],
            'medicine_id' => $id,
        ]);

        $message = 'add successfully' ;
        return $this->success(true ,$message) ;
    }

    public function GetFavorite(Request $request): JsonResponse
    {
        $data = [];
        $user = $request->user();
        $favorites = Favorite::query()->where('user_id' , '=' , $user['id']);

        if (!$favorites->exists()) {
            $message = 'you do not have any Favorites items' ;
            return $this->error([] , $message , 404) ;
        }
        $favorites = Favorite::query()->where('user_id' , '=' , $user['id'])->get();

        $count = 0 ;
        foreach ($favorites as $favorite) {
            $medicine = Medicine::query()->where('id','=',$favorite['medicine_id'])->first();
            $data[$count] = $medicine;
            $count++;
        }

        $data = new MedicineCollection($data) ;
        $message = 'all Favorite' ;
        return $this->success($data ,$message) ;

    }

}

