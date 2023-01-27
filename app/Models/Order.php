<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const PERCENTAGE_IF_ZERO_REFERRERS = 5;
    const PERCENTAGE_IF_FROM_FIVE_TO_TEN_REFERRERS = 10;
    const PERCENTAGE_IF_FROM_ELEVEN_TO_TWENTY_REFERRERS = 15;
    const PERCENTAGE_IF_FROM_TWENTY_ONE_TO_THIRTY_REFERRERS = 20;
    const PERCENTAGE_IF_FROM_THIRTY_ONE_AND_ABOVE_REFERRERS = 30;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';


    protected $casts = [
        'order_date' => 'date:Y-m-d',
    ];


    public function products()
    {

        // return $this->belongsToMany(Product::class)->withPivot('quantity');
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')->withPivot('quantity');
    }

    public function user()
    {

        return $this->belongsTo(User::class, 'purchaser_id');
    }

    // public function disributor()
    // {
    //     return $this->hasMany(User::class, 'referred_by');
    // }


    public function getOrderDetails($id)
    {
        return Order::with('products', 'user')->find($id);
    }
}
