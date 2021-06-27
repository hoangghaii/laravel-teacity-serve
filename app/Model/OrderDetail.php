<?php

namespace App\Model;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_detail';
    protected $appends = ['product'];
    protected $fillable = [
        'product_id', 'count', 'order_id', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];
    public function product()
    {
        return $this->hasOne(Product::class, 'id');
    }
    public function getproductAttribute()
    {
        return $this->product();
    }
    public function order()
    {
        return $this->belongsTo('App\model\Order', 'order_id');
    }
}
