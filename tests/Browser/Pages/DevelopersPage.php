<?php

namespace Tests\Browser\Pages;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Project;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Filter;
use Tests\Browser\Components\Pagination;
use Tests\Browser\Components\Sorting;

class DevelopersPage extends Page
{
    public function __construct()
    {
        $this->routePrefix = 'developers';
        $this->entityName = 'Developer';
        $this->modelName = 'App\Models\Developer';
    }
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/developers';
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
                    ->assertVisible('@first_name')
                    ->assertVisible('@last_name')
                    ->assertVisible('@rate')
                    ->assertVisible('@status')
                    ->assertSorting('first_name')
                    ->assertSorting('first_name', 'false')
                    ->assertSorting('last_name')
                    ->assertSorting('last_name', 'false')
                    ->assertSorting('rate')
                    ->assertSorting('rate', 'false')
                    ->assertSorting('status')
                    ->assertSorting('status', 'false');
            });
    }

    public function assertFiltering(Browser $browser): void
    {
        $developer = Developer::firstOrFail();
        $browser
            ->within(new Filter, function (Browser $browser) use ($developer) {
                $browser
                    ->assertVisible('@first_name')
                    ->assertVisible('@last_name')
                    ->assertVisible('@rate')
                    ->assertVisible('@status')
                    ->assertFiltering([
                        [
                            'field' => '@first_name',
                            'value' => $developer->first_name
                        ],
                        [
                            'field' => '@last_name',
                            'value' => $developer->last_name
                        ],
                        [
                            'field' => '@rate',
                            'value' => $developer->rate
                        ],
                        [
                            'field' => '@status',
                            'value' => $developer->status ? 'true' : 'false'
                        ],
                    ])
                    ->assertFilteringReset(['@first_name', '@last_name', '@rate', '@status']);
            });
    }

    public function assertPagination(Browser $browser): void
    {
        $browser->assertMissing((new Pagination)->selector());
        Developer::factory(60)->create();
        $browser->script('location.reload();');
        $browser
            ->within(new Pagination, function (Browser $browser) {
                $browser->assertPagination();
            });
    }
}
