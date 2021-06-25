<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'name' => 'required|string',
            'price' => 'required',
            'size' => 'required|string',
            'image' => 'required',
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }
        $resume = time() . '.' .  $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move('https://teacity-serve.herokuapp.com/storage/app/public', $resume);
        $product = new Product($request->all());
        $product->image = $resume;
        $product->save();
        return  response()->json($product);
    }
    public function index()
    {
        $listProduct =  Product::all();
        foreach ($listProduct as $key) {
            $key['image'] = env('APP_URL') . '/storage/' . $key['image'];
        }
        return $listProduct;
    }
    public function destroy(Request $request)
    {
        $product =  Product::find($request->id);
        unlink(storage_path('app/public/' . $product->image));
        return response()->json($product->delete());
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'name' => 'required|string',
            'price' => 'required',
            'size' => 'required|string',
            'image' => 'required',
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }
        $product =  Product::find($request->id);
        unlink(storage_path('app/public/' . $product->image));

        $resume = time() . '.' .  $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(base_path() . '/storage/app/public', $resume);

        $product->description = $request->description;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->size = $request->size;
        $product->image = $resume;
        $product->category_id = $request->category_id;
        return response()->json($product->save());
    }
}
