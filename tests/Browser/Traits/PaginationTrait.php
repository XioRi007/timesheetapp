<?php


namespace Tests\Browser\Traits;

use Laravel\Dusk\Browser;

trait PaginationTrait {
    public function testPagination(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visit(new $this->pageClass)
                ->assertPagination();
        });
    }
}