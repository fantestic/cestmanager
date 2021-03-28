<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit\CestReader;

use Fantestic\CestManager\Tests\Cest\ExampleCest;
use PHPUnit\Framework\TestCase;
use Fantestic\CestManager\CestReader\ParserCestReader;
use Fantestic\CestManager\Dto\Action;
use Fantestic\CestManager\Dto\ArgumentOut;
use Fantestic\CestManager\Dto\Scenario;
use Fantestic\CestManager\Dto\Step;
use Fantestic\CestManager\Finder;
use Fantestic\CestManager\Tests\VfsTestCase;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class ParserCestReaderTest extends VfsTestCase
{
    public function testGetScenariosReturnsScenarios() :void
    {
        $reader = new ParserCestReader($this->getFinder());
        $scenarios = $reader->getScenarios(ExampleCest::class);
        $this->assertEquals([$this->getTheFirstScenarioExpectation()], $scenarios);
    }


    public function testGetScenarioReturnsScenario() :void
    {
        $reader = new ParserCestReader($this->getFinder());
        $scenario = $reader->getScenario(ExampleCest::class, 'theFirstTest');

        $this->assertEquals($this->getTheFirstScenarioExpectation(), $scenario);
    }

    private function getTheFirstScenarioExpectation() :Scenario
    {
        $steps = [
            new Step(0, new Action('amOnPage', []), [new ArgumentOut("/", 'string', "'/'")]),
            new Step(1, new Action('see', []), [new ArgumentOut("Homepage", 'string', "'Homepage'")]),
        ];
        return new Scenario('theFirstTest', $steps);
    }
}