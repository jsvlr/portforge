<?php

use App\Models\User;

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
});

it('returns the current bearer token and all tokens for the authenticated user', function () {
    $tokenOne = $this->user->createToken('token-one');
    $tokenTwo = $this->user->createToken('token-two');

    $response = $this
        ->withToken($tokenTwo->plainTextToken)
        ->getJson('/api/user');

    $response->assertSuccessful();

    // The current_token should match the token used to authenticate the request.
    expect($response->json('data.current_token.id'))->toBe($tokenTwo->accessToken->id);
    expect($response->json('data.current_token.name'))->toBe('token-two');

    // All tokens belonging to the user should be listed, regardless of which was used.
    $tokenIds = collect($response->json('data.tokens'))->pluck('id');
    expect($tokenIds)->toContain($tokenOne->accessToken->id, $tokenTwo->accessToken->id);
    expect($tokenIds)->toHaveCount(2);
});

it('does not return other users tokens', function () {
    $otherUser = User::factory()->createOne();
    $otherUser->createToken('other-user-token');

    $myToken = $this->user->createToken('my-token');

    $response = $this
        ->withToken($myToken->plainTextToken)
        ->getJson('/api/user');

    $response->assertSuccessful();

    $tokenNames = collect($response->json('data.tokens'))->pluck('name');
    expect($tokenNames)->toContain('my-token');
    expect($tokenNames)->not->toContain('other-user-token');
});

it('rejects unauthenticated requests', function () {
    $this->getJson('/api/user')->assertUnauthorized();
});
