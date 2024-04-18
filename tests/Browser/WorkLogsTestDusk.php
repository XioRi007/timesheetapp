<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form;
use Tests\DuskPageTest;

class WorkLogsTestDusk extends DuskPageTest
{
    use DatabaseTruncation;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->userId = $user->id;
        $this->pageClass = 'Tests\Browser\Pages\WorkLogsPage';
        $this->routePrefix = 'worklogs';

        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id
        ]);
        $developer = Developer::factory()->create();
        WorkLog::factory()->create([
            'project_id' => $project->id,
            'developer_id' => $developer->id,
        ]);
    }


    public function testCreatePage(): void
    {
        $total = fake()->numberBetween(0, 99999999);
        $this->browse(function (Browser $browser) use ($total) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->assertSee('Create Work Log')
                ->assertVisible('@date')
                ->assertVisible('@developer_id')
                ->assertVisible('@project_id')
                ->assertVisible('@hrs')
                ->assertVisible('@rate')
                ->assertVisible('@total')
                ->assertVisible('@submit')
                ->select('@developer_id', 1)
                ->select('@project_id', 1)
                ->type('@hrs', 1)
                ->type('@rate', fake()->numberBetween(0, 999))
                ->type('@total', $total)
                ->press('@submit')
                ->waitForText('Work Log was successfully created')
                ->assertSee($total . '.00')
                ->assertRouteIs($this->routePrefix . '.index');
        });
    }

    public function testCreatePageRateLoading(): void
    {
        $client = Client::factory()->create([
            'rate'=>12
        ]);
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'rate'=>13
        ]);
        $developer = Developer::factory()->create([
            'rate'=>14
        ]);
        $this->browse(function (Browser $browser) use ($developer) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->select('@developer_id', $developer->id)
                ->pause(2000)
                ->assertInputValue('@rate', $developer->rate);
        });
        $developer->update([
            'rate' => 0
        ]);
        $this->browse(function (Browser $browser) use ($project) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->select('@project_id', $project->id)
                ->pause(2000)
                ->assertInputValue('@rate', $project->rate);
        });
        $project->update([
            'rate' => 0
        ]);
        $this->browse(function (Browser $browser) use ($project, $client) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->select('@project_id', $project->id)
                ->pause(2000)
                ->assertInputValue('@rate', $client->rate);
        });
    }

    public function testCreatePageTotalCalculating(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.create')
                ->type('@hrs', 10)
                ->type('@rate', 99)
                ->click('@total')
                ->pause(1000)
                ->assertInputValue('@total', 990);
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
                                'name' => 'Developer',
                                'real' => 'developer_id',
                                'type' => 'select'
                            ],
                            [
                                'name' => 'Project',
                                'real' => 'project_id',
                                'type' => 'select'
                            ],
                            [
                                'name' => 'Hours',
                                'real' => 'hrs',
                                'type' => 'decimal_52'
                            ],
                            [
                                'name' => 'Rate',
                                'real' => 'rate',
                                'type' => 'decimal_52'
                            ],
                            [
                                'name' => 'Total',
                                'real' => 'total',
                                'type' => 'decimal_102'
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
                                'name' => '@date',
                                'value' => date('Y-m-d')
                            ],
                            [
                                'name' => '@hrs',
                                'value' => '0'
                            ],
                            [
                                'name' => '@rate',
                                'value' => '0'
                            ],
                            [
                                'name' => '@total',
                                'value' => '0'
                            ],
                        ]);
                });
        });
    }


    public function testEditPage(): void
    {
        $total = fake()->numberBetween(0, 99999999);
        $this->browse(function (Browser $browser) use ($total) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', 1)
                ->assertSee('Edit work log')
                ->assertVisible('@date')
                ->assertVisible('@developer_id')
                ->assertVisible('@project_id')
                ->assertVisible('@hrs')
                ->assertVisible('@rate')
                ->assertVisible('@total')
                ->assertVisible('@submit')
                ->select('@developer_id', 1)
                ->select('@project_id', 1)
                ->type('@hrs', 1)
                ->type('@rate', fake()->numberBetween(0, 999))
                ->type('@total', $total)
                ->press('@submit')
                ->waitForText('Work Log was successfully updated')
                ->assertRouteIs($this->routePrefix . '.index')
                ->assertSee($total);
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
                                'name' => 'Hours',
                                'real' => 'hrs',
                                'type' => 'decimal_52'
                            ],
                            [
                                'name' => 'Rate',
                                'real' => 'rate',
                                'type' => 'decimal_52'
                            ],
                            [
                                'name' => 'Total',
                                'real' => 'total',
                                'type' => 'decimal_102'
                            ],
                        ]);
                })
                ->assertSee('Fill the form correctly');
        });
    }


    public function testEditPageReset(): void
    {
        $worklog = WorkLog::firstOrFail();
        $this->browse(function (Browser $browser) use ($worklog) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute($this->routePrefix . '.edit', $worklog->id)
                ->within(new Form, function (Browser $browser) use ($worklog) {
                    $browser
                        ->assertReset([
                            [
                                'name' => '@date',
                                'value' => $worklog->date->format('Y-m-d')
                            ],
                            [
                                'name' => '@developer_id',
                                'value' => $worklog->developer->full_name
                            ],
                            [
                                'name' => '@project_id',
                                'value' => $worklog->project->name
                            ],
                            [
                                'name' => '@hrs',
                                'value' => $worklog->hrs
                            ],
                            [
                                'name' => '@rate',
                                'value' => $worklog->rate
                            ],
                            [
                                'name' => '@total',
                                'value' => $worklog->total
                            ],
                            [
                                'name' => '@status',
                                'value' => $worklog->status ? 'Paid' : 'Unpaid'
                            ],
                        ]);
                });
        });
    }
}
