<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::get();

        return response()->json($posts);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return response()->json($post);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post = Post::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . Str::random(5),
            'content'     => $request->content,
            'category_id' => $request->category_id,
            'user_id'     => $request->user()->id,
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $request->validate([
            'title'       => 'required|string',
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . Str::random(5),
            'content'     => $request->content,
            'category_id' => $request->category_id,
        ]);

        return response()->json($post);
    }

    public function destroy($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus']);
    }
}
