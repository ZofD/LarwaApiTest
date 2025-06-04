<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Creates nw order with products.
     * 
     * @param string $customerName
     * @param array $productsData  // tablica [ ['product_id' => 1, 'quantity' => 2], ... ]
     * @return Order
     */
    public function createOrder(string $customerName, array $productsData): Order
    {
        return DB::transaction(function () use ($customerName, $productsData) {
            $order = Order::create(['customer_name' => $customerName]);
            $totalPrice = 0;

            foreach ($productsData as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'] ?? 1;
                $linePrice = $product->price * $quantity;
                $totalPrice += $linePrice;

                $order->orderDetails()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

            $order->total_price = $totalPrice;
            $order->save();

            return $order->load('orderDetails.product');
        });
    }

    /**
     * @param int $orderId
     * @return Order|null
     */
    public function getOrderWithDetails(int $orderId): ?Order
    {
        return Order::with(['orderDetails.product'])
            ->find($orderId);
    }

    /**
     * Get all orders with their details and related products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllOrdersWithDetails()
    {
        return Order::with(['orderDetails.product'])->get();
    }

    /**
     * Updates the total price of an order
     *
     * @param int $orderId
     * @return void
     */
    public function updateOrderTotal(int $orderId): void
    {
        $order = Order::with('orderDetails')->findOrFail($orderId);

        $totalPrice = $order->orderDetails->sum(function ($detail) {
            return $detail->price * $detail->quantity;
        });

        $order->total_price = $totalPrice;
        $order->save();
    }
}