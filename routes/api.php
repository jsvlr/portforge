<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Models\Post;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
    });


    // table name is the endpoints of each sources
    Route::apiResource(with(new Post)->getTable(), PostController::class);
});

/*
    ! test only very dangerous
*/
Route::get('/tables', function () {
    $models = collect(Finder::create()->files()->in(app_path('Models')))
        ->map(function ($file) {
            return
                'App\\Models\\' . Str::replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getRealPath(), app_path('Models') . DIRECTORY_SEPARATOR)
                );
        })
        ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class) && (new $class)->getTable() !== 'users')
        ->mapWithKeys(fn($class) => [Str::headline(class_basename($class)) => (new $class)->getTable()]);

    return $models->keys();
});
