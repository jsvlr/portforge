<?php

use App\Models\Post;
use App\Models\User;


$resource = (string) with(new Post)->getTable();
$endpoint = "/api/$resource/";

uses()->beforeEach(function () {
    $this->owner = User::factory()->createOne();
    $this->ownerToken = $this->owner->createToken('my-token')->plainTextToken;

    $this->post = Post::factory()->for($this->owner)->create();

    $this->attacker = User::factory()->createOne();
    $this->attackerToken = $this->attacker->createToken('my-token')->plainTextToken;
});

it('can access user\'s own posts API', function () use ($endpoint) {
    $this
        ->actingAs($this->owner)
        ->withToken($this->ownerToken)
        ->withHeader('Accept', 'application/json')
        ->getJson($endpoint)
        ->assertStatus(200);
});

it('prevents a user from visiting another user\'s posts via API', function () use ($endpoint) {
    $this->withToken($this->attackerToken)
        ->withHeader('Accept', 'application/json')
        ->getJson("$endpoint{$this->post->id}")
        ->assertNotFound();
});

it('requires authentication to access posts', function () {
    $this->getJson('/api/posts')
        ->assertUnauthorized();

    $this->getJson("/api/posts/{$this->post->id}")
        ->assertUnauthorized();
});

/*
it('lets the owner access their own post', function () {
    $token = $this->owner->createToken('owner-token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/posts/{$this->post->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $this->post->id);
});

it('only returns the authenticated user\'s posts in the index', function () {
    $otherPost = Post::factory()->for($this->owner)->create();
    Post::factory()->for($this->attacker)->create();

    $token = $this->owner->createToken('owner-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/posts')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();

    expect($ids)->toContain($this->post->id)
        ->and($ids)->toContain($otherPost->id)
        ->and($ids)->toHaveCount(2);
});
*/