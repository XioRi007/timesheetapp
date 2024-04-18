<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProfilePage;
use Tests\DuskTestCase;

class ProfileTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Test that the page is opening.
     */
    public function testPageRenderingForAdmin(): void
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new ProfilePage)
                ->assertSee('Profile')
                ->logout();
        });
    }

    /**
     * Test that the update profile information form.
     */
    public function testProfileInformationUpdate(): void
    {
        $user = User::factory()->create();
        $newEmail = 'email1@email.com';
        $this->browse(function (Browser $browser) use ($user, $newEmail) {
            $browser
                ->loginAs($user)
                ->visit(new ProfilePage)
                ->fillTheUpdateForm($newEmail)
                ->waitForText('Saved.')
                ->logout();
        });
    }

    /**
     * Test that the update profile information form.
     */
    public function testPasswordUpdate(): void
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new ProfilePage)
                ->fillThePasswordUpdateForm('password', 'newpassword', 'newpassword')
                ->waitForText('Saved.')
                ->logout();
        });
    }

    /**
     * Test that the wrong password is not accepted.
     */
    public function testWrongPasswordUpdate(): void
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new ProfilePage)
                ->fillThePasswordUpdateForm('wrongpassword', 'newpassword', 'newpassword')
                ->waitForText('The password is incorrect')
                ->logout();
        });
    }

    /**
     * Test that the not confirmed password is not accepted.
     */
    public function testNotConfirmedPasswordUpdate(): void
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new ProfilePage)
                ->fillThePasswordUpdateForm('password', 'newpassword', 'notnewpassword')
                ->waitForText('The password field confirmation does not match.')
                ->logout();
        });
    }

    /**
     * Test that the not valid password is not accepted.
     */
    public function testValidationPasswordUpdate(): void
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(new ProfilePage)
                ->fillThePasswordUpdateForm('password', '12', 'notnewpassword')
                ->waitForText('The password field must be at least 8 characters.')
                ->logout();
        });
    }
}
