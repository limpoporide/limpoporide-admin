<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
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
        'user_name',
        'user_email',
        'user_mobile',
        'user_address',
        'product_name',
        'unit_price',
        'product_price',
        'shipping_charg',
        'coupon_discount',
        'grand_total',
        'status',
        'payment_status',
        'payment_type',
        'order_date',
    ];
}
