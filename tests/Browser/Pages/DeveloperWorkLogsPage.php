<?php

namespace Tests\Browser\Pages;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Project;
use App\Models\WorkLog;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Filter;
use Tests\Browser\Components\Pagination;
use Tests\Browser\Components\Sorting;

class DeveloperWorkLogsPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/developers/1/worklogs';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return  array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }

    public function assertSorting(Browser $browser): void
    {
        $browser
            ->within(new Sorting, function (Browser $browser) {
                $browser
                    ->assertVisible('@date')
                    ->assertVisible('@project.name')
                    ->assertVisible('@rate')
                    ->assertVisible('@hrs')
                    ->assertVisible('@total')
                    ->assertVisible('@status')
                    ->assertSorting('date')
                    ->assertSorting('date', 'false')
                    ->assertSorting('project.name')
                    ->assertSorting('project.name', 'false')
                    ->assertSorting('rate')
                    ->assertSorting('rate', 'false')
                    ->assertSorting('hrs')
                    ->assertSorting('hrs', 'false')
                    ->assertSorting('total')
                    ->assertSorting('total', 'false')
                    ->assertSorting('status')
                    ->assertSorting('status', 'false');
            });
    }

    public function assertFiltering(Browser $browser): void
    {
        $worklog = WorkLog::firstOrFail();
        $browser
            ->within(new Filter, function (Browser $browser) use ($worklog) {
                $browser
                    ->assertVisible('@date')
                    ->assertVisible('@project_id')
                    ->assertVisible('@rate')
                    ->assertVisible('@hrs')
                    ->assertVisible('@total')
                    ->assertVisible('@status')
                    ->assertFiltering([
                        [
                            'field' => '@date',
                            'value' => $worklog->date->format('Y-m-d')
                        ],
                        [
                            'field' => '@project_id',
                            'value' => $worklog->project->id
                        ],
                        [
                            'field' => '@rate',
                            'value' => $worklog->rate
                        ],
                        [
                            'field' => '@hrs',
                            'value' => $worklog->hrs
                        ],
                        [
                            'field' => '@total',
                            'value' => $worklog->total
                        ],
                        [
                            'field' => '@status',
                            'value' => $worklog->status ? 'true' : 'false'
                        ],
                    ])
                    ->assertFilteringReset(['@date', '@project_id', '@rate', '@hrs', '@total','@status']);
            });
    }

    public function assertPagination(Browser $browser): void
    {
        $browser->assertMissing((new Pagination)->selector());
        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id
        ]);
        WorkLog::factory(60)->create([
            'project_id' => $project->id,
            'developer_id' => 1,
        ]);
        $browser->script('location.reload();');
        $browser
            ->within(new Pagination, function (Browser $browser) {
                $browser->assertPagination();
            });
    }
}
