<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Form extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '@form';
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

    public function assertValidation(Browser $browser, array $fields): void
    {
        foreach ($fields as $field) {
            $browser->assertVisible("@$field[real]");
            switch ($field['type']) {
                case 'decimal_52':
                    $browser
                        ->clear("@$field[real]")
                        ->press('@submit');
                    $browser->waitForText("$field[name] is a required field");

                    $browser
                        ->type("@$field[real]", -100)
                        ->press('@submit')
                        ->waitForText("$field[name] must be greater than or equal to 0");

                    $browser
                        ->type("@$field[real]", 9999)
                        ->press('@submit')
                        ->waitForText("$field[name] must be less than or equal to 999.99");

                    $browser
                        ->type("@$field[real]", 99.999999)
                        ->press('@submit')
                        ->waitForText("$field[name] should have at most 2 decimal places");
                    break;

                case 'decimal_102':
                    $browser
                        ->clear("@$field[real]")
                        ->press('@submit');
                    $browser->waitForText("$field[name] is a required field");

                    $browser
                        ->type("@$field[real]", -100)
                        ->press('@submit')
                        ->waitForText("$field[name] must be greater than or equal to 0");

                    $browser
                        ->type("@$field[real]", 100000000)
                        ->press('@submit')
                        ->waitForText("$field[name] must be less than or equal to 99999999.99");

                    $browser
                        ->type("@$field[real]", 9.999999)
                        ->press('@submit')
                        ->waitForText("$field[name] should have at most 2 decimal places");
                    break;

                case 'text':
                    $browser
                        ->waitFor("@$field[real]")
                        ->clear("@$field[real]")
                        ->press('@submit')
                        ->pause(2000)
                        ->assertSee("$field[name] is a required field");
                    break;

                case 'select':
                    $browser
                        ->waitFor("@$field[real]")
                        ->value("@$field[real]", '')
                        ->press('@submit')
                        ->pause(2000)
                        ->assertSee("$field[name] is a required field");
                    break;

                default:
                    $browser
                        ->clear("@$field[real]")
                        ->press('@submit')
                        ->assertSee("$field[name] is a required field");
            }
        }
    }

    public function assertReset(Browser $browser, array $defaultValues)
    {
        $browser->click('@reset');
        foreach ($defaultValues as $field) {
            $browser->assertInputValue($field['name'], $field['value']);
        }
    }

}
