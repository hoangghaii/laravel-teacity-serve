<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $appends = ['order_detail'];
    protected $fillable = [
        'user_id', 'total_price', 'address', 'name', 'phone','status', 'discount', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];
    public function order_detail()
    {
        return $this->hasMany('App\model\OrderDetail', 'order_id', 'id');
    }
    public function getOrderDetailAttribute()
    {
        return $this->order_detail();
    }
}