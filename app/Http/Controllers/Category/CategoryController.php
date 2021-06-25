<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'icon' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }
        $category = Category::insert($request->all());
        return response()->json($category);
    }
    public function index()
    {
        return response()->json(Category::paginate('10'));
    }
    public function update(Request $request)
    {
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->icon = $request->icon;
        $category->save();
        return response()->json($category);
    }
    public function destroy(Request $request)
    {
        return response()->json(Category::find($request->id)->delete());
    }
}