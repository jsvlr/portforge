<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Models\Post;

Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

Route::middleware('auth:sanctum')->group(function () {

    // table name is the endpoints of each sources
    Route::apiResource(with(new Post)->getTable(), PostController::class);
});
