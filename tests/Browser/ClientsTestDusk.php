<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form;
use Tests\DuskPageTest;

class ClientsTestDusk extends DuskPageTest
{
    use DatabaseTruncation;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->userId = $user->id;
        $this->pageClass = 'Tests\Browser\Pages\ClientsPage';
        $this->routePrefix = 'clients';
        $this->entityName = 'Client';
        Client::factory()->create();
    }

    public function testCreatePage(): void
    {
        $name = fake()->name();
        $this->browse(function (Browser $browser) use ($name) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->assertSee('Create Client')
                ->assertVisible('@name')
                ->assertVisible('@rate')
                ->assertVisible('@submit')
                ->type('@name', $name)
                ->type('@rate', fake()->numberBetween(0, 999.99))
                ->press('@submit')
                ->waitForText('Client was successfully created')
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
                ->assertSee('Edit Client')
                ->assertVisible('@name')
                ->assertVisible('@rate')
                ->assertVisible('@status')
                ->assertVisible('@submit')
                ->type('@name', $name)
                ->type('@rate', fake()->numberBetween(0, 999.99))
                ->value('@status', fake()->boolean())
                ->press('@submit')
                ->waitForText('Client was successfully updated')
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
        $client = Client::firstOrFail();
        $this->browse(function (Browser $browser) use ($client) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', $client->id)
                ->within(new Form, function (Browser $browser) use ($client) {
                    $browser
                        ->assertReset([
                            [
                                'name' => '@name',
                                'value' => $client->name
                            ],
                            [
                                'name' => '@rate',
                                'value' => $client->rate
                            ],
                            [
                                'name' => '@status',
                                'value' => $client->status ? 'Active' : 'Inactive'
                            ],
                        ]);
                });
        });
    }
}
