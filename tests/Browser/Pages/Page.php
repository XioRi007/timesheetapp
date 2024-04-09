<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use Tests\Browser\Components\DeleteItemModal;

abstract class Page extends BasePage
{
    protected string $routePrefix;
    protected string $entityName;
    protected string $modelName;

    /**
     * Get the global element shortcuts for the site.
     *
     * @return array<string, string>
     */
    public static function siteElements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }

    public function assertEditPage(Browser $browser): void
    {
        $item = ($this->modelName)::firstOrFail();
        $browser
            ->assertVisible('@edit')
            ->click('@edit')
            ->waitForRoute($this->routePrefix . '.edit', $item->id)
            ->assertVisible('@close')
            ->click('@close')
            ->waitForRoute($this->routePrefix . '.index');
    }

    public function assertDeleteForm(Browser $browser): void
    {
        $browser
            ->waitFor('@delete')
            ->click('@delete')
            ->waitFor((new DeleteItemModal)->selector())
            ->within(new DeleteItemModal, function (Browser $browser) {
                $browser->assertDeleteClosing();
            })
            ->waitFor('@delete')
            ->click('@delete')
            ->waitFor((new DeleteItemModal)->selector())
            ->within(new DeleteItemModal, function (Browser $browser) {
                $browser->assertItemDeleting();
            })
            ->pause(2000)
            ->assertSee($this->entityName . ' was successfully deleted');
    }
}
