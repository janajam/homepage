<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\medicine_wherehouse;

class Admin extends Model
{
    protected $fillable=[
        'name',
        'email',
        'phone_number',
        'password',
    ];

    public function wherehouse(){
        return $this->belonfsTo(medicine_wherehouse::class);
    }
}
