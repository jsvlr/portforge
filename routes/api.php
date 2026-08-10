<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    $user = $request->user();

    return response()->json([
        // The access token used to authenticate the current request.
        // Will be a Laravel\Sanctum\PersonalAccessToken when the request
        // is authenticated via a bearer token, or a TransientToken
        // (empty object) when authenticated via the "web" session guard.
        'current_token' => $user->currentAccessToken(),

        // Every personal access token that has ever been created for this user.
        'tokens' => $user->tokens,
    ]);
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {

    // table name is the endpoints of each sources
    Route::apiResource(with(new Post)->getTable(), PostController::class);
});
