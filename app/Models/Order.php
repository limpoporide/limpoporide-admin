<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'unit_price',
        'product_price',
        'shipping_charg',
        'coupon_discount',
        'grand_total',
        'qty',
        'address_id',
        'status',
        'payment_status',
        'payment_type',
        'coupon_id',
        'order_date',
    ];
}
