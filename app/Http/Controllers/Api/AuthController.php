<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogInRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Cart;
use App\Models\User;
use App\Traits\HttpResponses;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;
    public function Register(Request $request) : JsonResponse{

        $request->validate([
            'name' => 'required|min:3',
            'phone_number' => 'required|min:10|unique:users',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'pharmacy_name' => 'required|unique:users',
            'image' => 'image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $imageName = '' ;
        //$imageName = time() . '.' . $request['image']->extension();
        //$request['image']->storeAs('images', $imageName);

        $user =  User::query()->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make( $request['password']),
            'phone_number' => $request['phone_number'],
            'pharmacy_name' => $request['pharmacy_name'],
            'image' => $imageName ,
        ]);

        $token = $user->createToken("Auth Token")->plainTextToken ;

        $data = [
            'user' => $user ,
            'token' => $token ,
        ] ;

        $cart = Cart::query()->create([
           'user_id' =>  $user['id'] ,
        ]);

        $message = 'welcome ,your account has been registered';
        return $this->success($data , $message );
    }

    public function login(Request $request) : JsonResponse{

        $request->validate([
            'phone_number' => 'required|exists:users,phone_number' ,
            'password' => 'required' ,
        ]);

        $user = User::query()->where('phone_number' , '=' , $request['phone_number'])->first();

        if(!$user || !Hash::check($request['password'] , $user['password'])){
            $message = 'the phone number or password is incorrect';
            return $this->error([] , $message , 422) ;
        }

        if($user['role'] == 'admin'){
            $message = 'welcome sir but you can not login from your mobile please log in from your PC :)' ;
            return $this->error([] , $message , 404) ;
        }

        $token = $user->createToken("Auth Token")->plainTextToken ;

        $data =[
            'user' => $user ,
            'token' => $token ,
        ];

        $message = 'logged in successfully' ;
        return $this->success($data , $message);
    }

    public function Logout() : JsonResponse{

        Auth::user()->currentAccessToken()->delete() ;
        $message = 'logged out successfully' ;
        return $this->success([] , $message);

    }
}
