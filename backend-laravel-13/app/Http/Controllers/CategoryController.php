<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|unique:categories,category_name',
        ]);

        $category = Category::create([
            'category_name' => $request->category_name,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category tidak ditemukan'], 404);
        }

        $request->validate([
            'category_name' => 'required|string|unique:categories,category_name,' . $id,
        ]);

        $category->update([
            'category_name' => $request->category_name,
        ]);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category tidak ditemukan'], 404);
        }

        if ($category->posts()->count() > 0) {
            return response()->json([
                'message' => "Category \"{$category->category_name}\" tidak bisa dihapus karena masih digunakan oleh {$category->posts()->count()} post.",
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category berhasil dihapus']);
    }
}
