<?php

namespace Tests\Browser;

use App\Models\Developer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LayoutTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Test admin nav links
     */
    public function testAdminNavLinks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit('/')
                ->waitForLink('Dashboard')
                ->waitForLink('Clients')
                ->waitForLink('Projects')
                ->waitForLink('Work Logs');
        });
    }

    /**
     * Test developer nav links
     */
    public function testDeveloperNavLinks(): void
    {
        $developer = Developer::factory()->create();

        $this->browse(function (Browser $browser) use ($developer) {
            $browser
                ->loginAs($developer)
                ->visitRoute('developers.worklogs', $developer->id)
                ->waitForLink('Work Logs');
        });
    }
}
