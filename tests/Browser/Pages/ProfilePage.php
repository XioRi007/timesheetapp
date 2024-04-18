<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class ProfilePage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/profile';
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
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }

    /**
     *
     * @param  Browser  $browser
     * @param  string  $email
     * @return  void
     */
    public function fillTheUpdateForm(Browser $browser, string $email)
    {
        $browser
            ->type('@email', $email)
            ->press('@submit');
    }

    /**
     *
     * @param  Browser  $browser
     * @param  string  $currentPassword
     * @param  string  $newPassword
     * @param  string  $confirmPassword
     * @return  void
     */
    public function fillThePasswordUpdateForm(Browser $browser, string $currentPassword, string $newPassword, string $confirmPassword): void
    {
        $browser
            ->type('#current_password', $currentPassword)
            ->type('#password', $newPassword)
            ->type('#password_confirmation', $confirmPassword)
            ->press('@password_submit');
    }
}
