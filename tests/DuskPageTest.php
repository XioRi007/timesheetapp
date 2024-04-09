<?php

namespace Tests;
use Laravel\Dusk\Browser;
use Tests\Browser\Traits\FilteringTrait;
use Tests\Browser\Traits\PaginationTrait;
use Tests\Browser\Traits\SortingTrait;

class DuskPageTest extends DuskTestCase
{
    use SortingTrait, FilteringTrait, PaginationTrait;
    protected string $pageClass;
    protected string $routePrefix;
    protected string $userId;
    protected string $entityName;

    public function testCreatePageOpenClosing()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visitRoute( $this->routePrefix . '.index')
                ->assertVisible('@create')
                ->click('@create')
                ->waitForRoute($this->routePrefix . '.create')
                ->assertVisible('@close')
                ->click('@close')
                ->waitForRoute($this->routePrefix . '.index');
        });
    }

    public function testEditPageOpenClosing()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visit(new $this->pageClass)
                ->assertEditPage();
        });
    }

    public function testDeleteForm()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->userId)
                ->visit(new $this->pageClass)
                ->assertDeleteForm();
        });
    }
}
