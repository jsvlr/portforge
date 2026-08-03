<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Livewire\Livewire;
use App\Models\User;
use App\Filament\Resources\PostCategories\Pages\ManagePostCategories;
use App\Models\Post;
use App\Models\PostCategory;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
    $this->table = with(new PostCategory)->getTable();

    actingAs($this->user);
});

IT('can render page', function () {
    $this
        ->get(ManagePostCategories::getUrl())
        ->assertSuccessful();
});


it('can see categories', function () {
    $category = PostCategory::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->assertCanSeeTableRecords([$category]);
});


it('cannot see other user categories', function () {
    $otherUser = User::factory()->createOne();
    $otherCategory = PostCategory::factory()->for($otherUser)->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->assertCanNotSeeTableRecords([$otherCategory]);
});

it('can create category', function () {
    $testForm = [
        'name' => 'new category',
        'slug' => 'new-category',
        'is_active' => true
    ];

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->mountTableAction('create')
        ->fillForm($testForm)
        ->callMountedTableAction()
        ->assertHasNoFormErrors();

    $testForm['user_id'] = $this->user->id;

    assertDatabaseHas('post_categories', $testForm);
});


it('can update category', function () {
    $oldCategory = PostCategory::factory()->for($this->user)->create();
    $newCategoryUpdated = [
        'name' => 'updated category',
        'slug' => 'updated-category',
        'is_active' => false
    ];

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->mountAction(TestAction::make('edit')->table($oldCategory))
        ->fillForm($newCategoryUpdated)
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $newCategoryUpdated['user_id'] = $this->user->id;

    assertDatabaseHas('post_categories', $newCategoryUpdated);
});

it('cannot edit other user post category', function () {
    $otherPostCategory = PostCategory::factory()->for(User::factory()->createOne())->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->mountAction(TestAction::make('edit')->table($otherPostCategory))
        ->assertActionNotMounted();
});

it('cannot delete other user post category', function () {
    $otherPostCategory = PostCategory::factory()->for(User::factory()->createOne())->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->mountAction(TestAction::make('delete')->table($otherPostCategory))
        ->assertActionNotMounted();

    assertDatabaseHas('post_categories', ['id' => $otherPostCategory->id]);
});

it('can delete category', function () {
    $category = PostCategory::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->mountTableAction('delete', $category->id)
        ->callMountedAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('post_categories', ['id' => $category->id]);
});
