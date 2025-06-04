<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Get the orders that contain the product.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
