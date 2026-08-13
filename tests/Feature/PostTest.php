<?php

use App\Models\User;
use App\Models\Post;
use App\Models\PostCategory;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function validPostForm(array $overrides = []): array
{
    $title = 'How to test a post creation';

    return array_merge([
        'title' => $title,
        'slug' => Str::slug($title),
        'excerpt' => 'This is a short excerpt for testing',
        'content' => '<p>This is the main content of the post for testing purposes.</p>',
        'published_at' => now()->format('Y-m-d'),
        'tags' => ['testing', 'laravel'],
        'status' => 'draft',
    ], $overrides);
}

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
    actingAs($this->user); // logs in via 'web' guard
    $this->table = with(new Post)->getTable();
});

describe('panel access', function () {

    it('can render page', fn() => $this->get(PostResource::getUrl())->assertSuccessful());

    it('can see posts', function () {
        $post = Post::factory()->for($this->user)->create();

        Livewire::actingAs($this->user)
            ->test(ListPosts::class)
            ->assertCanSeeTableRecords([$post]);
    });

    it('cannot see other user posts', function () {
        $otherPost = Post::factory()->for(User::factory()->createOne())->create();

        Livewire::test(ListPosts::class)
            ->assertCanNotSeeTableRecords([$otherPost]);
    });

    it('can create post', function () {

        $category = PostCategory::factory()->for($this->user)->create();
        $testForm = validPostForm(['post_category_id' => $category->id]);

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
        $newCategory = PostCategory::factory()->for($this->user)->createOne();

        $updatedForm = validPostForm([
            'title' => 'How to test a post update',
            'slug' => Str::slug('How to test a post update'),
            'post_category_id' => $newCategory->id,
            'excerpt' => 'This is a short excerpt for testing update',
            'content' => '<p>This is the main content of the post for testing updation purposes.</p>',
            'tags' => ['testing', 'laravel', 'updated'],
            'status' => 'published',
        ]);

        Livewire::actingAs($this->user)
            ->test(EditPost::class, [
                'record' => $oldPost->getRouteKey()
            ])
            ->fillForm($updatedForm)
            ->call('save')
            ->assertHasNoFormErrors();

        assertDatabaseHas($this->table, [
            'title' => $updatedForm['title'],
            'slug' => $updatedForm['slug'],
            'post_category_id' => $updatedForm['post_category_id'],
            'excerpt' => $updatedForm['excerpt'],
            'content' => $updatedForm['content'],
            'status' => $updatedForm['status'],
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
});

describe('policy', function () {

    it('owner can view own posts via policy', function () {
        $post = Post::factory()->for($this->user)->create();

        expect($this->user->can('view', $post))->toBeTrue();
    });

    it('cannot view another users post via policy', function () {
        $otherPost = Post::factory()->for(User::factory()->create())->create();

        expect($this->user->can('view', $otherPost))->toBeFalse();
    });

    it('token holder can view owners post if ability granted', function () {
        Sanctum::actingAs($this->user, ['posts:view']);
        $post = Post::factory()->for($this->user)->create();

        expect($this->user->can('view', $post))->toBeTrue();
    });

    it('token holder cannot view owner post without the ability', function () {
        Sanctum::actingAs($this->user, ['posts:create']); // not read
        $post = Post::factory()->for($this->user)->create();

        expect($this->user->can('view', $post))->toBeFalse();
    });

    it('token holder cannot update owner post without the ability', function () {
        Sanctum::actingAs($this->user, ['posts:view']); // not update
        $post = Post::factory()->for($this->user)->create();

        expect($this->user->can('update', $post))->toBeFalse();
    });

    it('token holder cannot delete owner post without the ability', function () {
        Sanctum::actingAs($this->user, ['posts:view']);
        $post = Post::factory()->for($this->user)->create();

        expect($this->user->can('delete', $post))->toBeFalse();
    });
});
