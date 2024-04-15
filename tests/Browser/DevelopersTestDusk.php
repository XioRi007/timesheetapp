<?php

namespace Tests\Browser;

use App\Models\Developer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form;
use Tests\DuskPageTest;

class DevelopersTestDusk extends DuskPageTest
{
    use DatabaseTruncation;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->userId = $user->id;
        $this->pageClass = 'Tests\Browser\Pages\DevelopersPage';
        $this->routePrefix = 'developers';
        $this->entityName = 'Developer';
        Developer::factory()->create();
    }

    public function testCreatePage(): void
    {
        $name = fake()->firstName();
        $this->browse(function (Browser $browser) use ($name) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->assertSee('Create Developer')
                ->assertVisible('@first_name')
                ->assertVisible('@last_name')
                ->assertVisible('@rate')
                ->assertVisible('@submit')
                ->type('@first_name', $name)
                ->type('@last_name', fake()->lastName())
                ->type('@email', fake()->email())
                ->type('@password', fake()->password(8))
                ->type('@rate', fake()->numberBetween(0, 999.99))
                ->press('@submit')
                ->waitForText('Developer was successfully created')
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
                                'name' => 'First Name',
                                'real' => 'first_name',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'Last Name',
                                'real' => 'last_name',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'Email',
                                'real' => 'email',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'Password',
                                'real' => 'password',
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
                                'name' => '@first_name',
                                'value' => ''
                            ],
                            [
                                'name' => '@last_name',
                                'value' => ''
                            ],
                            [
                                'name' => '@email',
                                'value' => ''
                            ],
                            [
                                'name' => '@password',
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
                ->assertSee('Edit developer')
                ->assertVisible('@first_name')
                ->assertVisible('@last_name')
                ->assertVisible('@rate')
                ->assertVisible('@status')
                ->assertVisible('@submit')
                ->type('@first_name', $name)
                ->type('@last_name', fake()->lastName())
                ->type('@rate', fake()->numberBetween(0, 999.99))
                ->value('@status', fake()->boolean())
                ->press('@submit')
                ->waitForText('Developer was successfully updated')
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
                                'name' => 'First Name',
                                'real' => 'first_name',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'Last Name',
                                'real' => 'last_name',
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
        $developer = Developer::firstOrFail();
        $this->browse(function (Browser $browser) use ($developer) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', $developer->id)
                ->within(new Form, function (Browser $browser) use ($developer) {
                    $browser
                        ->assertReset([
                            [
                                'name' => '@first_name',
                                'value' => $developer->first_name
                            ],
                            [
                                'name' => '@last_name',
                                'value' => $developer->last_name
                            ],
                            [
                                'name' => '@rate',
                                'value' => $developer->rate
                            ],
                            [
                                'name' => '@status',
                                'value' => $developer->status ? 'Active' : 'Inactive'
                            ],
                        ]);
                });
        });
    }
}
