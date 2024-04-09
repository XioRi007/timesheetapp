<?php


namespace Tests\Browser\Traits;

use Laravel\Dusk\Browser;

trait SortingTrait {
    public function testSorting(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visit(new $this->pageClass)
                ->assertSorting();
        });
    }
}