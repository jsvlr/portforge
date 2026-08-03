<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use App\Filament\Resources\ProjectRoles\Pages\ManageProjectRoles;
use App\Models\ProjectRole;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
    actingAs($this->user);
});

test('it can render page', function () {
    $this
        ->get(ManageProjectRoles::getUrl())
        ->assertSuccessful();
});

test('can see roles', function () {
    $projectRole = ProjectRole::factory()->for($this->user)->createOne();

    Livewire::actingAs($this->user)
        ->test(ManageProjectRoles::class)
        ->assertCanSeeTableRecords([$projectRole]);
});

test('cannot see other user project roles', function () {
    $otherUser = User::factory()->createOne();
    $otherProjectRole = ProjectRole::factory()->for($otherUser)->createOne();

    Livewire::actingAs($this->user)
        ->test(ManageProjectRoles::class)
        ->assertCanNotSeeTableRecords([$otherProjectRole]);
});

test('can create project role', function () {
    $testForm = [
        'name' => 'new project role',
        'slug' => 'new-project-role',
    ];

    Livewire::actingAs($this->user)
        ->test(ManageProjectRoles::class)
        ->mountTableAction('create')
        ->fillForm($testForm)
        ->callMountedTableAction()
        ->assertHasNoFormErrors();

    $testForm['user_id'] = $this->user->id;

    assertDatabaseHas('project_roles', $testForm);
});

test('can update project role', function () {
    $oldProjectRole = ProjectRole::factory()->for($this->user)->create();
    $newProjectRoleUpdated = [
        'name' => 'updated project role',
        'slug' => 'updated-project-role'
    ];

    Livewire::actingAs($this->user)
        ->test(ManageProjectRoles::class)
        ->mountAction(TestAction::make('edit')->table($oldProjectRole))
        ->fillForm($newProjectRoleUpdated)
        ->callMountedAction()
        ->assertHasNoFormErrors();

    assertDatabaseHas('project_roles', $newProjectRoleUpdated);
});

test('can delete project role', function () {
    $projectRole = ProjectRole::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ManageProjectRoles::class)
        ->mountTableAction('delete', $projectRole->id)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('project_roles', ['id' => $projectRole->id]);
});
