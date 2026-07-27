<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Livewire\Livewire;
use App\Models\User;
use App\Filament\Resources\PostCategories\Pages\ManagePostCategories;
use App\Models\PostCategory;
use Filament\Actions\Testing\TestAction;

test('can render page', function () {
    $user = User::factory()->createOne();

    actingAs($user)
        ->get(ManagePostCategories::getUrl())
        ->assertSuccessful();
});


test('can see categories', function () {
    $user = User::factory()->createOne();
    $category = PostCategory::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManagePostCategories::class)
        ->assertCanSeeTableRecords([$category]);
});


test('cannot see other user categories', function () {
    $user = User::factory()->createOne();
    $other_user = User::factory()->createOne();
    $other_category = PostCategory::factory()->for($other_user)->create();

    Livewire::actingAs($user)
        ->test(ManagePostCategories::class)
        ->assertCanNotSeeTableRecords([$other_category]);
});

test('can create category', function () {
    $user = User::factory()->createOne();
    $testForm = [
        'name' => 'new category',
        'slug' => 'new-category',
        'is_active' => true
    ];

    Livewire::actingAs($user)
        ->test(ManagePostCategories::class)
        ->mountAction('create')
        ->fillForm($testForm)
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $testForm['user_id'] = $user->id;

    assertDatabaseHas('post_categories', $testForm);
});


test('can update category', function () {
    $user = User::factory()->createOne();
    $oldCategory = PostCategory::factory()->for($user)->create();
    $newCategoryUpdated = [
        'name' => 'updated category',
        'slug' => 'updated-category',
        'is_active' => false
    ];

    Livewire::actingAs($user)
        ->test(ManagePostCategories::class)
        ->mountAction(TestAction::make('edit')->table($oldCategory))
        ->fillForm($newCategoryUpdated)
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $newCategoryUpdated['user_id'] = $user->id;

    assertDatabaseHas('post_categories', $newCategoryUpdated);
});

test('can delete category', function () {
    $user = User::factory()->createOne();
    $category = PostCategory::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManagePostCategories::class)
        ->mountTableAction('delete', $category->id)
        ->callMountedAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('post_categories', ['id' => $category->id]);
});
