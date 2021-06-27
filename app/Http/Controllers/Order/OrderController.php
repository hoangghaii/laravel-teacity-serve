<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listProduct' => 'required',
            'total_price' => 'required',
            'address' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'user_id' =>  'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }

        return DB::transaction(function () use ($request) {

            $order_request = [
                'total_price' => $request->total_price,
                'discount' => $request->discount,
                'user_id' =>  $request->user_id,
                'address' =>   $request->address,
                'name' => $request->name,
                'phone' => $request->phone,
                'discount' => 0,
                'status' => 0
            ];
            $order = Order::create($order_request);
            $listOrderDetail = [];

            foreach ($request->listProduct as $key) {
                $detail = [
                    'product_id' => $key['id'],
                    'order_id' => $order['id'],
                    'count' => $key['count']
                ];
                array_push($listOrderDetail, $detail);
            }
            OrderDetail::insert($listOrderDetail);
            return response()->json($order);
        });
    }
    public function index()
    {
        return Order::with('order_detail')->get();
    }
    public function update(Request $request)
    {
        return DB::transaction(function () use ($request) {
            OrderDetail::where('order_id', $request->id)->delete();
            $order = Order::find($request->id);
            $order->total_price =  $request->total_price;
            $order->user_id =  $request->user_id;
            $order->address =  $request->address;
            $order->name =  $request->name;
            $order->phone =  $request->phone;
            $order->discount =  0;
            $order->status = $request->status;
            $order->save();
            $listOrderDetail = [];

            foreach ($request->listProduct as $key) {
                $detail = [
                    'product_id' => $key['id'],
                    'order_id' => $order['id'],
                    'count' => $key['count']
                ];
                array_push($listOrderDetail, $detail);
            }
            OrderDetail::insert($listOrderDetail);
            return response()->json($order);
        });
    }
    public function getById(Request $request)
    {
        $order = Order::find($request->id);
        $order2 = [
            'total_price' => $order->total_price,
            'discount' => $order->discount,
            'user_id' =>  $order->user_id,
            'address' =>   $order->address,
            'name' => $order->name,
            'phone' => $order->phone,
            'discount' => $order->discount,
            'status' => $order->status,
            'order_detail' => []
        ];
        $listDetails = OrderDetail::where('order_id', $order->id)->get();
        $list = [];
        foreach ($listDetails as $key) {
            $item = (object)[
                'id' => $key->id,
                'product_id' => $key->product_id,
                'count' => $key->count,
                'order_id' => $key->order_id,
                'product' => Product::find($key->product_id)
            ];
            array_push($list, $item);
        }
        $order2['order_detail'] = $list;
        return $order2;
    }
}
