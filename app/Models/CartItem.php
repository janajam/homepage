<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable =[
      'cart_id' ,
      'medicine_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function cart() : BelongsTo
    {
      return $this->belongsTo(Cart::class) ;
    }
}
