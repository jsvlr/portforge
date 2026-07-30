<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::with('post_category')->get();
    }

    public function show(Post $post)
    {
        return $post->load('post_category');
    }
}
