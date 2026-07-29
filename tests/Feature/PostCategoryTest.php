<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Livewire\Livewire;
use App\Models\User;
use App\Filament\Resources\PostCategories\Pages\ManagePostCategories;
use App\Models\PostCategory;
use Filament\Actions\Testing\TestAction;

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
    actingAs($this->user);
});

test('can render page', function () {
    $this
        ->get(ManagePostCategories::getUrl())
        ->assertSuccessful();
});


test('can see categories', function () {
    $category = PostCategory::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->assertCanSeeTableRecords([$category]);
});


test('cannot see other user categories', function () {
    $other_user = User::factory()->createOne();
    $other_category = PostCategory::factory()->for($other_user)->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->assertCanNotSeeTableRecords([$other_category]);
});

test('can create category', function () {
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


test('can update category', function () {
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

test('can delete category', function () {
    $category = PostCategory::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ManagePostCategories::class)
        ->mountTableAction('delete', $category->id)
        ->callMountedAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('post_categories', ['id' => $category->id]);
});
