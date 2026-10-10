<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $order = null;
        if ($code = strtoupper(trim((string) $request->query('code')))) {
            $order = Order::with('items')->where('code', $code)->first();
        }

        return view('orders.track', ['order' => $order, 'code' => $code]);
    }

    public function show(string $code)
    {
        return redirect()->route('order.track', ['code' => $code]);
    }
}
