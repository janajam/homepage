<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
       'quantity' ,
        'price' ,
        'total_price',
        'order_id',
        'medicine_id' ,
    ] ;

    protected $hidden = [
      'created_at',
      'updated_at',
    ];

    public function order() : BelongsTo{
        return $this->belongsTo(Order::class) ;
    }
}
