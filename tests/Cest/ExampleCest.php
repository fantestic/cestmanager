<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Cest;

class ExampleCest
{
    public function _before(\AcceptanceTester $I)
    {
        // nothing to run
    }


    /**
     * @fantestic
     * @return void 
     */
    public function theFirstTest(\AcceptanceTester $I) :void
    {
        $I->amOnPage('/');
        $I->see('Homepage');
    }
}