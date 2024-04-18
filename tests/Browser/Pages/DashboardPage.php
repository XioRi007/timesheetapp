<?php

namespace Tests\Browser\Pages;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Project;
use App\Models\WorkLog;
use Illuminate\Support\Carbon;
use Laravel\Dusk\Browser;

class DashboardPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/';
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
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }

    public function assertCalendarLinks(Browser $browser): void
    {
        $now = Carbon::now();
        $currentLink = Carbon::createFromFormat('m', $now->month)->format('F') . ' ' . $now->year;
        $next = Carbon::now()->addMonth(1);
        $nextLink = Carbon::createFromFormat('m', $next->month)->format('F') . ' ' . $next->year;
        $prev = Carbon::now()->subMonth(1);
        $prevLink = Carbon::createFromFormat('m', $prev->month)->format('F') . ' ' . $prev->year;
        $browser
            ->assertSeeLink($currentLink)
            ->assertSeeLink($nextLink)
            ->assertSeeLink($prevLink);
    }

    public function assertPaidUnpaid(Browser $browser)
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id
        ]);
        $developer = Developer::factory()->create();
        WorkLog::factory(1)->create([
            'developer_id' => $developer->id,
            'project_id' => $project->id,
            'total' => 2000,
            'hrs'=>3
        ]);
        WorkLog::factory(1)->create([
            'developer_id' => $developer->id,
            'project_id' => $project->id,
            'total' => 1000,
            'status' => true,
            'hrs'=>4
        ]);
        $browser->script('location.reload();');
        $browser
            ->waitForText('2000')
            ->waitForText('1000');
    }

    public function assertTime(Browser $browser): void
    {
        $browser
            ->waitForText('7');
    }
}
