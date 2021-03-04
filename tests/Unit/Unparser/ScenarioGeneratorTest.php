<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit\Unparser;

use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Unparser\ScenarioGenerator;
use Fantestic\CestManager\Tests\Doubles\Action;
use Fantestic\CestManager\Tests\Doubles\Argument;
use Fantestic\CestManager\Tests\Doubles\Parameter;
use Fantestic\CestManager\Tests\Doubles\Scenario;
use Fantestic\CestManager\Tests\Doubles\Step;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class ScenarioGeneratorTest extends TestCase
{
    public function testGeneratesValidAst() :void
    {
        $gen = new ScenarioGenerator(new BuilderFactory());
        $res = $gen->generateScenarioAst($this->sampleTree());
        $pp  = new Standard();
        $code = $pp->prettyPrint([$res->getNode()]);
        $this->assertSame($this->sampleTreeExpectation(), $code);
    }

    private function sampleTree() :ScenarioInterface
    {
        return new Scenario(
            'theFirstTest',
            [
                new Step(
                    0,
                    new Action('amOnPage', [
                        new Parameter('url', 'string')
                    ]),
                    [new Argument('/')]
                ),
                new Step(
                    1,
                    new Action('see', [
                        new Parameter('text', 'string')
                    ]),
                    [new Argument('Homepage')]
                )
            ]
        );
    }

    private function sampleTreeExpectation() :string
    {
        return <<<CODE
        /** @fantestic */
        function theFirstTest(\AcceptanceTester \$I) : void
        {
            \$I->amOnPage('/');
            \$I->see('Homepage');
        }
        CODE;
    }
}
