<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form;
use Tests\DuskPageTest;

class ProjectsTestDusk extends DuskPageTest
{
    use DatabaseTruncation;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->userId = $user->id;
        $this->pageClass = 'Tests\Browser\Pages\ProjectsPage';
        $this->routePrefix = 'projects';
        $client = Client::factory()->create();
        Project::factory()->create([
            'client_id' => $client->id
        ]);
    }


    public function testCreatePage(): void
    {
        $name = fake()->name();
        $this->browse(function (Browser $browser) use ($name) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->assertSee('Create Project')
                ->assertVisible('@name')
                ->assertVisible('@rate')
                ->assertVisible('@client_id')
                ->assertVisible('@submit')
                ->type('@name', $name)
                ->type('@rate', fake()->numberBetween(0, 999.99))
                ->select('@client_id', 1)
                ->press('@submit')
                ->waitForText('Project was successfully created')
                ->assertRouteIs($this->routePrefix . '.index');
        });
    }

    public function testCreatePageError(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->within(new Form, function (Browser $browser) {
                    $browser
                        ->assertValidation([
                            [
                                'name' => 'Name',
                                'real' => 'name',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'Client',
                                'real' => 'client_id',
                                'type' => 'select'
                            ],
                            [
                                'name' => 'Rate',
                                'real' => 'rate',
                                'type' => 'decimal_52'
                            ],
                        ]);
                })
                ->assertSee('Fill the form correctly');
        });
    }


    public function testCreatePageReset(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->within(new Form, function (Browser $browser) {
                    $browser
                        ->assertReset([
                            [
                                'name' => '@name',
                                'value' => ''
                            ],
                            [
                                'name' => '@rate',
                                'value' => '1'
                            ],
                        ]);
                });
        });
    }


    public function testEditPage(): void
    {
        $name = fake()->name();
        $this->browse(function (Browser $browser) use ($name) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', 1)
                ->assertSee('Edit project')
                ->assertVisible('@name')
                ->assertVisible('@client_id')
                ->assertVisible('@rate')
                ->assertVisible('@status')
                ->assertVisible('@submit')
                ->type('@name', $name)
                ->select('@client_id', 1)
                ->type('@rate', fake()->numberBetween(0, 999.99))
                ->value('@status', fake()->boolean())
                ->press('@submit')
                ->waitForText('Project was successfully updated')
                ->assertRouteIs($this->routePrefix . '.index')
                ->assertSee($name);
        });
    }

    public function testEditPageError(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', 1)
                ->within(new Form, function (Browser $browser) {
                    $browser
                        ->assertValidation([
                            [
                                'name' => 'Name',
                                'real' => 'name',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'Rate',
                                'real' => 'rate',
                                'type' => 'decimal_52'
                            ],
                        ]);
                })
                ->assertSee('Fill the form correctly');
        });
    }


    public function testEditPageReset(): void
    {
        $project = Project::firstOrFail();
        $this->browse(function (Browser $browser) use ($project) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', $project->id)
                ->within(new Form, function (Browser $browser) use ($project) {
                    $browser
                        ->assertReset([
                            [
                                'name' => '@name',
                                'value' => $project->name
                            ],
                            [
                                'name' => '@rate',
                                'value' => $project->rate
                            ],
                            [
                                'name' => '@status',
                                'value' => $project->status ? 'Active' : 'Inactive'
                            ],
                        ]);
                });
        });
    }
}
