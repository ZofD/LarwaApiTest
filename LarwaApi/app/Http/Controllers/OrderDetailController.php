<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderDetail = new \App\Models\OrderDetail();
        $orderDetail->order_id = $request->input('order_id');
        $orderDetail->product_id = $request->input('product_id');
        $orderDetail->quantity = $request->input('quantity');
        $orderDetail->save();

        // Update order total using a service
        app(\App\Services\OrderService::class)->updateOrderTotal($orderDetail->order_id);

        return response()->json($orderDetail, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderDetail = \App\Models\OrderDetail::find($id);

        if (!$orderDetail) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        return response()->json($orderDetail);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderDetail = \App\Models\OrderDetail::find($id);

        if (!$orderDetail) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        if ($request->has('quantity')) {
            $orderDetail->quantity = $request->input('quantity');
        }

        $orderDetail->save();

        // Update order total using a service
        app(\App\Services\OrderService::class)->updateOrderTotal($orderDetail->order_id);

        return response()->json($orderDetail);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderDetail = \App\Models\OrderDetail::find($id);

        if (!$orderDetail) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $orderId = $orderDetail->order_id;
        $orderDetail->delete();

        // Update order total using a service
        app(\App\Services\OrderService::class)->updateOrderTotal($orderId);

        return response()->json(['message' => 'Order detail deleted successfully']);
    }
}
