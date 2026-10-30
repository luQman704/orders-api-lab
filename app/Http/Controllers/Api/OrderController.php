<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    //

    public function index (Request $request): AnonymousResourceCollection
    {
        $status = $request->query('status', 'shipped');
        $perPage = min((int) $request->query('per_page', 100), 200);
        $orders = Order::query()
                    ->where('status', $status)
                    ->orderByDesc('placed_at')
                    ->paginate($perPage);
        return OrderResource::collection($orders);            
    }
}
