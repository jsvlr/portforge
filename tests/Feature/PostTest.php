<?php

use App\Models\User;
use App\Models\Post;
use App\Models\PostCategory;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\withToken;

use Livewire\Livewire;

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
    $this->token = $this->user->createToken('my-token')->plainTextToken;
    $this->table = with(new Post)->getTable();
    actingAs($this->user);
    withToken($this->token);
});

it('can render page', function () {
    $this
        ->get(PostResource::getUrl())
        ->assertSuccessful();
});

it('can see post', function () {
    $post = Post::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ListPosts::class)
        ->assertCanSeeTableRecords([$post]);
});

it('cannot see other user posts', function () {
    $otherUser = User::factory()->createOne();
    $otherPost = Post::factory()->for($otherUser)->create();

    Livewire::actingAs($this->user)
        ->test(ListPosts::class)
        ->assertCanNotSeeTableRecords([$otherPost]);
});

it('can create post', function () {
    $category = PostCategory::factory()->for($this->user)->create();

    $testForm = [
        'title' => 'How to test a post creation',
        'slug' => 'how-to-test-a-post-creation',
        'post_category_id' => $category->id,
        'excerpt' => 'This is a short excerpt for testing',
        'content' => '<p>This is the main content of the post for testing purposes.</p>',
        'published_at' => now()->format('Y-m-d'),
        'tags' => ['testing', 'laravel'],
        'status' => 'draft',
    ];

    Livewire::actingAs($this->user)
        ->test(CreatePost::class)
        ->fillForm($testForm)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas($this->table, [
        'title' => $testForm['title'],
        'slug' => $testForm['slug'],
        'user_id' => $this->user->id,
        'post_category_id' => $category->id,
        'status' => 'draft',
    ]);
});

it('can update post', function () {
    $oldPost = Post::factory()->for($this->user)->create();
    $newCategoryUpdated = PostCategory::factory()->for($this->user)->createOne();
    $newPostUpdated = [
        'title' => 'How to test a post update',
        'slug' => 'how-to-test-a-post-update',
        'post_category_id' => $newCategoryUpdated->id,
        'excerpt' => 'This is a short excerpt for testing update',
        'content' => '<p>This is the main content of the post for testing updation purposes.</p>',
        'published_at' => now()->format('Y-m-d'),
        'tags' => ['testing', 'laravel', 'updated'],
        'status' => 'published',
    ];

    Livewire::actingAs($this->user)
        ->test(EditPost::class, [
            'record' => $oldPost->getRouteKey()
        ])
        ->fillForm($newPostUpdated)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas($this->table, [
        'title' => $newPostUpdated['title'],
        'slug' => $newPostUpdated['slug'],
        'post_category_id' => $newPostUpdated['post_category_id'],
        'excerpt' => $newPostUpdated['excerpt'],
        'content' => $newPostUpdated['content'],
        'status' => $newPostUpdated['status'],
    ]);
});

it('cannot edit other user post', function () {
    $otherPost = Post::factory()->for(User::factory()->createOne())->create();

    expect(fn() => Livewire::actingAs($this->user)
        ->test(EditPost::class, ['record' => $otherPost->getRouteKey()]))
        ->toThrow(ModelNotFoundException::class);
});

it('can delete post', function () {
    $post = Post::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ListPosts::class)
        ->callTableAction('delete', $post->getKey())
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing($this->table, [
        'id' => $post->id,
    ]);
});

it('cannot delete other user post', function () {
    $otherPost = Post::factory()->for(User::factory()->createOne())->create();

    expect(fn() => Livewire::actingAs($this->user)
        ->test(ListPosts::class)
        ->callTableAction('delete', $otherPost->getKey()))
        ->toThrow(ActionNotResolvableException::class);
});
