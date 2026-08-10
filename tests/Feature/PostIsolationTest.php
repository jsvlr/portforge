<?php

use App\Models\Post;
use App\Models\User;


$resource = (string) with(new Post)->getTable();
$endpoint = "/api/$resource/";

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();

    $this->userToken = $this->user->createToken(
        name: 'user-token',
        abilities: ['*']
    )->plainTextToken;

    $this->other = User::factory()->createOne();
});

it('can access user\'s own posts API', function () use ($endpoint) {
    $this
        ->withToken($this->userToken)
        ->withHeader('Accept', 'application/json')
        ->getJson($endpoint)
        ->assertOk();
});

it('does not return other users\' posts', function () use ($endpoint) {
    $otherPost = Post::factory()->for($this->other)->create();

    $this
        ->withToken($this->userToken)
        ->withHeader('Accept', 'application/json')
        ->getJson($endpoint)
        ->assertOk()
        ->assertJsonMissing([
            'id' => $otherPost->id // this check if the otherPost id is existed on the user post...
        ]);
});

it('requires authentication to access posts', function () use ($endpoint) {
    $userPost = Post::factory()->for($this->user)->create();

    $this
        ->getJson($endpoint)
        ->assertUnauthorized();

    $this->getJson("$endpoint{$userPost->id}")
        ->assertUnauthorized();
});

it('lets the user access their own post', function () use ($endpoint) {
    $userPost = Post::factory()->for($this->user)->create();

    $this
        ->withToken($this->userToken)
        ->getJson("$endpoint{$userPost->id}")
        ->assertOk()
        ->assertJsonFragment([
            'id' => $userPost->id
        ]);
});

it('only returns the authenticated user\'s posts in the index', function () use ($endpoint) {
    $post = Post::factory()->for($this->user)->create();
    $otherPost = Post::factory()->for($this->other)->create();

    $response = $this
        ->withToken($this->userToken)
        ->getJson($endpoint)
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();

    expect($ids)
        ->toContain($post->id)
        ->not->toContain($otherPost->id);
});
