<?php

namespace Tests\Browser\Pages;

use App\Models\Client;
use App\Models\Project;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\DeleteItemModal;
use Tests\Browser\Components\Filter;
use Tests\Browser\Components\Pagination;
use Tests\Browser\Components\Sorting;

class ProjectsPage extends Page
{
    public function __construct()
    {
        $this->routePrefix = 'projects';
        $this->entityName = 'Project';
        $this->modelName = 'App\Models\Project';
    }
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/projects';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathBeginsWith($this->url());
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
                    ->assertVisible('@name')
                    ->assertVisible('@client.name')
                    ->assertVisible('@rate')
                    ->assertVisible('@status')
                    ->assertSorting('name')
                    ->assertSorting('name', 'false')
                    ->assertSorting('client.name')
                    ->assertSorting('client.name', 'false')
                    ->assertSorting('rate')
                    ->assertSorting('rate', 'false')
                    ->assertSorting('status')
                    ->assertSorting('status', 'false');
            });
    }

    public function assertFiltering(Browser $browser): void
    {
        $project = Project::firstOrFail();
        $browser
            ->within(new Filter, function (Browser $browser) use ($project) {
                $browser
                    ->assertVisible('@name')
                    ->assertVisible('@rate')
                    ->assertVisible('@status')
                    ->assertVisible('@client_id')
                    ->assertFiltering([
                        [
                            'field' => '@name',
                            'value' => $project->name
                        ],
                        [
                            'field' => '@rate',
                            'value' => $project->rate
                        ],
                        [
                            'field' => '@client_id',
                            'value' => $project->client->id
                        ],
                        [
                            'field' => '@status',
                            'value' => $project->status ? 'true' : 'false'
                        ],
                    ])
                    ->assertFilteringReset(['@name', '@rate', '@status', '@client_id']);
            });
    }

    public function assertPagination(Browser $browser): void
    {
        $browser->assertMissing((new Pagination)->selector());
        $client = Client::factory()->create();
        Project::factory(60)->create([
            'client_id' => $client->id
        ]);
        $browser->script('location.reload();');
        $browser
            ->within(new Pagination, function (Browser $browser) {
                $browser->assertPagination();
            });
    }
}
