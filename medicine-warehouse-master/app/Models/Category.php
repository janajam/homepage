<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'warehouse_id' ,
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
    ];

    public function warehouse() : BelongsTo {
        return $this->belongsTo(Warehouse::class);
    }

    public function categoryItem() : HasMany{
        return $this->hasMany(CategoryItem::class);
    }

    public function medicines() : HasMany{
        return $this->hasMany(Medicine::class) ;
    }



}
