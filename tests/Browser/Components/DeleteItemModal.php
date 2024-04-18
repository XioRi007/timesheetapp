<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DeleteItemModal extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '@delete_modal';
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

    public function assertItemDeleting(Browser $browser): void
    {
        $browser
            ->waitFor('@delete_item')
            ->click('@delete_item')
            ->waitUntilMissing('@delete_item');
    }

    public function assertDeleteClosing(Browser $browser): void
    {
        $browser
            ->waitFor('@delete_item')
            ->click('@close_modal')
            ->assertMissing('@close_modal');
    }
}
