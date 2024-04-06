<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class LoginPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/login';
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
     * Fill the login form
     * @param Browser $browser
     * @param string $email
     * @param string $password
     * @return  void
     */
    public function fillForm(Browser $browser, string $email, string $password = 'password'): void
    {
        $browser
            ->type('@email', $email)
            ->type('@password', $password)
            ->press('@submit');
    }

    public function logoutClick(Browser $browser): void
    {
        $browser
        ->press('@profile_btn')
        ->press('@logout');
    }
}
