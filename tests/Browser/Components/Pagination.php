<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Pagination extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '@pagination';
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }

    public function assertPagination(Browser $browser): void
    {
        $browser->assertSee('Next');
        $browser->assertSee('Previous');
        $browser->assertSee('1');

        $browser
            ->clickLink('Next')
            ->pause(2000)
            ->assertQueryStringHas('page', 2)
            ->clickLink('Previous')
            ->pause(2000)
            ->assertQueryStringHas('page', 1)
            ->clickLink('1')
            ->pause(2000)
            ->assertQueryStringHas('page', 1);
    }
}
