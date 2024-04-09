<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Filter extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '@filter';
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
            '@submit' => 'button[type=submit]',
        ];
    }

    public function assertFiltering(Browser $browser, array $filterParams = [])
    {
        foreach ($filterParams as $param){
            $browser->select($param['field'], $param['value']);
        }

        $browser
            ->click('@submit')
            ->pause(2000);

        foreach ($filterParams as $param){
            $browser->assertValue($param['field'], $param['value']);
        }
    }

    public function assertFilteringReset(Browser $browser, array $filterColumns)
    {
        $browser
            ->click('@reset')
            ->pause(2000);

        foreach ($filterColumns as $param){
            $browser->assertValue($param, null);
        }
    }
}
