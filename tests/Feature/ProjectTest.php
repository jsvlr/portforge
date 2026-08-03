<?php

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\User;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses()->beforeEach(function () {
    $this->user = User::factory()->createOne();
    $this->table = with(new Project)->getTable();
    actingAs($this->user);
});


it('can render page', function () {
    $this
        ->get(ProjectResource::getUrl())
        ->assertSuccessful();
});


it('can see project', function () {
    $project = Project::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ListProjects::class)
        ->assertCanSeeTableRecords([$project]);
});

it('cannot see other user projects', function () {
    $otherProject = Project::factory()->for(User::factory()->createOne())->create();

    Livewire::actingAs($this->user)
        ->test(ListProjects::class)
        ->assertCanNotSeeTableRecords([$otherProject]);
});

it('can create project', function () {

    $testForm = [
        'title' => 'How to test project creation',
        'slug' => 'how-to-test-project-creation',
        'content' => '<p>This is the main content of the project for testing purposes.</p>',
        'client' => 'test',
        'project_role_id' => ProjectRole::factory()->for($this->user)->create()->id,
        'status' => 'published',
        'gallery' => ['testing.jpeg'],
        'started_at' => now()->format('Y-m-d'),
        'completed_at' => now()->subDay()->format('Y-m-d'),
        'views' => 0,
    ];

    Livewire::actingAs($this->user)
        ->test(CreateProject::class)
        ->fillForm($testForm)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas($this->table, [
        'title' => $testForm['title'],
        'slug' => $testForm['slug'],
        'user_id' => $this->user->id,
        'content' => $testForm['content'],
        'client' => $testForm['client'],
        'project_role_id' => $testForm['project_role_id'],
        'status' => $testForm['status'],
        'gallery' => json_encode($testForm['gallery']),
        'started_at' => $testForm['started_at'] . ' 00:00:00',
        'completed_at' => $testForm['completed_at'] . ' 00:00:00',
        'views' => $testForm['views'],
    ]);
});

it('can update project', function () {
    $oldProject = Project::factory()->for($this->user)->create();
    $updatedProject = [
        'title' => 'How to test project update',
        'slug' => 'how-to-test-project-update',
        'content' => '<p>This is the main content of the project for testing purposes.</p>',
        'client' => 'test',
        'project_role_id' => ProjectRole::factory()->for($this->user)->create()->id,
        'status' => 'published',
        'gallery' => ['testing.jpeg'],
        'started_at' => now()->format('Y-m-d'),
        'completed_at' => now()->subDay()->format('Y-m-d'),
        'views' => 0,
    ];

    Livewire::actingAs($this->user)
        ->test(EditProject::class, ['record' => $oldProject->getRouteKey()])
        ->fillForm($updatedProject)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas($this->table, [
        'title' => $updatedProject['title'],
        'slug' => $updatedProject['slug'],
        'user_id' => $this->user->id,
        'content' => $updatedProject['content'],
        'client' => $updatedProject['client'],
        'project_role_id' => $updatedProject['project_role_id'],
        'status' => $updatedProject['status'],
        'gallery' => json_encode($updatedProject['gallery']),
        'started_at' => $updatedProject['started_at'] . ' 00:00:00',
        'completed_at' => $updatedProject['completed_at'] . ' 00:00:00',
    ]);
});

it('cannot edit other user project', function () {
    $otherProject = Project::factory()->for(User::factory()->createOne())->create();

    expect(fn() => Livewire::actingAs($this->user)
        ->test(EditProject::class, ['record' => $otherProject->getRouteKey()]))
        ->toThrow(ModelNotFoundException::class);
});

it('can delete project', function () {
    $project = Project::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(ListProjects::class)
        ->callTableAction('delete', $project)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing($this->table, [
        'id' => $project->id
    ]);
});

it('cannot delete other user project', function () {
    $otherProject = Project::factory()->for(User::factory()->createOne())->create();

    expect(fn() => Livewire::actingAs($this->user)
        ->test(ListProjects::class)
        ->callTableAction('delete', $otherProject)
        ->callMountedTableAction())
        ->toThrow(ActionNotResolvableException::class);
});
