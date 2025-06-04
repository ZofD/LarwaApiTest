<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderService->getAllOrdersWithDetails();
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order = $this->orderService->createOrder(
            $request->input('customer_name'),
            $request->input('products') // array of products with 'product_id' and 'quantity'
        );

        return response()->json($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = $this->orderService->getOrderWithDetails($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = $this->orderService->getOrderWithDetails($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
