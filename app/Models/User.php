<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Order;

class User extends Model
{
    protected $fillable=[
        'name',
        'email',
        'phone_number',
        'password',
    ];

    public function order(){
        return $this->hasMany(Order::class);
       }

    

       protected $hidden = [
        'password',
        'remember_token',
    ];

   

    protected $casts = [
        'password' => 'hashed',
    ];

}
