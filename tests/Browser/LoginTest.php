<?php

namespace Tests\Browser;

use App\Models\Developer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Test that admin can login and logout.
     */
    public function testAdminLoginLogout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new LoginPage)
                ->assertSee('Email')
                ->fillForm($user->email)
                ->waitForRoute('dashboard')
                ->assertSee('Dashboard')
                ->logoutClick()
                ->waitForRoute('login')
                ->assertSee('Email');
        });
    }

    /**
     * Test that developer can login and logout.
     */
    public function testDeveloperLoginLogout(): void
    {
        $developer = Developer::factory()->create();

        $this->browse(function (Browser $browser) use ($developer) {
            $browser->visit(new LoginPage)
                ->assertSee('Email')
                ->fillForm($developer->user->email)
                ->waitForRoute('developers.worklogs', $developer->id)
                ->logoutClick()
                ->waitForRoute('login')
                ->assertSee('Email');
        });
    }

    public function testInvalidCredentialsMessageShowing()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->fillForm('notexistinguser@email.com')
                ->waitForText('These credentials do not match our records.');
        });
    }
}
