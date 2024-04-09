<?php

namespace Tests\Browser\Pages;

use App\Models\Client;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Filter;
use Tests\Browser\Components\Pagination;
use Tests\Browser\Components\Sorting;

class ClientsPage extends Page
{
    public function __construct()
    {
        $this->routePrefix = 'clients';
        $this->entityName = 'Client';
        $this->modelName = 'App\Models\Client';
    }

    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/clients';
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
                    ->assertVisible('@rate')
                    ->assertVisible('@status')
                    ->assertSorting('name')
                    ->assertSorting('name', 'false')
                    ->assertSorting('rate')
                    ->assertSorting('rate', 'false')
                    ->assertSorting('status')
                    ->assertSorting('status', 'false');
            });
    }

    public function assertFiltering(Browser $browser): void
    {
        $client = Client::firstOrFail();
        $browser
            ->within(new Filter, function (Browser $browser) use ($client) {
                $browser
                    ->assertVisible('@name')
                    ->assertVisible('@rate')
                    ->assertVisible('@status')
                    ->assertFiltering([
                        [
                            'field' => '@name',
                            'value' => $client->name
                        ],
                        [
                            'field' => '@rate',
                            'value' => $client->rate
                        ],
                        [
                            'field' => '@status',
                            'value' => $client->status ? 'true' : 'false'
                        ],
                    ])
                    ->assertFilteringReset(['@name', '@rate', '@status']);
            });
    }

    public function assertPagination(Browser $browser): void
    {
        $browser->assertMissing((new Pagination)->selector());
        Client::factory(60)->create();
        $browser->script('location.reload();');
        $browser
            ->within(new Pagination, function (Browser $browser) {
                $browser
                    ->assertPagination();
            });
    }
}
