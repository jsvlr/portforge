<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;

class PostCategoryController extends Controller
{
    public function index()
    {
        return PostCategory::withCount('posts')->get();
    }

    public function show(PostCategory $postCategory)
    {
        return $postCategory->loadCount('posts');
    }
}
