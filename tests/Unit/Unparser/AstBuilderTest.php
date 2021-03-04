<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit\Unparser;

use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Unparser\AstBuilder;
use Fantestic\CestManager\Tests\Doubles\Action;
use Fantestic\CestManager\Tests\Doubles\Collection;
use Fantestic\CestManager\Tests\Doubles\Argument;
use Fantestic\CestManager\Tests\Doubles\Parameter;
use Fantestic\CestManager\Tests\Doubles\Scenario;
use Fantestic\CestManager\Tests\Doubles\Step;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class AstBuilderTest extends TestCase
{
    public function testBuildScenarioCreatesValidAst() :void
    {
        $gen = new AstBuilder();
        $res = $gen->buildScenarioAst($this->sampleScenarioTree());
        $pp  = new Standard();
        $code = $pp->prettyPrint([$res->getNode()]);
        $this->assertSame($this->sampleScenarioTreeExpectation(), $code);
    }


    public function testBuildCollectionCreatesValidAst() :void
    {
        $collection = new Collection(
            'Fantestic\CestManager\Test',
            'SampleCest',
            [
                $this->sampleScenarioTree()
            ]
        );
        $builder = new AstBuilder();
        $ast = $builder->buildCollectionAst($collection);
        $pp = new Standard();
        $code = $pp->prettyPrint($ast);
        $this->assertSame($this->sampleCollectionTreeExpectation(), $code);
    }


    private function sampleScenarioTree() :ScenarioInterface
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


    private function sampleScenarioTreeExpectation() :string
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


    private function sampleCollectionTreeExpectation() :string
    {
        return <<<CODE
        declare (strict_types=1);
        namespace Fantestic\CestManager\Test;

        /**
         * These tests are managed by fantestic. Manual changes might break things.
         *
         * @fantestic
         */
        final class SampleCest
        {
            /** @fantestic */
            function theFirstTest(\AcceptanceTester \$I) : void
            {
                \$I->amOnPage('/');
                \$I->see('Homepage');
            }
        }
        CODE;
    }
}
