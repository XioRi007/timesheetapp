<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\DashboardPage;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Dashboard test.
     */
    public function testDashboardLinks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new DashboardPage)
                ->waitForText('Dashboard')
                ->assertSee('Total Paid')
                ->assertSee('Total Unpaid')
                ->assertCalendarLinks();
        });
    }

    public function testDashboardValues(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new DashboardPage)
                ->assertPaidUnpaid()
                ->assertTime();
        });
    }
}
