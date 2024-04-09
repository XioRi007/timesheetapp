<?php


namespace Tests\Browser\Traits;

use Laravel\Dusk\Browser;

trait FilteringTrait {
    public function testFiltering(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visit(new $this->pageClass)
                ->assertFiltering();
        });
    }
}