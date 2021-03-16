<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit;

use Fantestic\CestManager\CestReader\ParserCestReader;
use Fantestic\CestManager\CestWriter;
use Fantestic\CestManager\CestReader\ReflectionCestReader;
use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Finder;
use Fantestic\CestManager\Tests\Doubles\Action;
use Fantestic\CestManager\Tests\Doubles\Argument;
use Fantestic\CestManager\Tests\Doubles\Collection;
use Fantestic\CestManager\Tests\Doubles\Parameter;
use Fantestic\CestManager\Tests\Doubles\Scenario;
use Fantestic\CestManager\Tests\Doubles\Step;
use Fantestic\CestManager\Tests\VfsTestCase;
use Fantestic\CestManager\Unparser\AstBuilder;
use Fantestic\CestManager\Unparser\PrettyPrinter;
use PHPUnit\Framework\TestCase;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class CestWriterTest extends  VfsTestCase
{
    public function testCreateScenarioCreatesScenario() :void
    {
        $scenario = $this->sampleScenarioTree();
        $collection = $this->getRootfileCollection();
        
        
        $manipulator = $this->makeCestManipulator();
        $manipulator->createScenario($collection, $scenario);
        $content = $this->getFinder()->getFileContents(self::ROOTFILE_PATH);

        // rough test to see if the new method has been written
        $this->assertStringContainsString($scenario->getMethodName(), $content);
        $this->assertStringContainsString(
            $scenario->getSteps()[0]->getArguments()[0]->getValue(),
            $content
        );
    }


    public function testUpdateScenarioUpdatesScenario() :void
    {
        $scenario = $this->sampleScenarioTree();
        $scenario->methodName = 'theFirstTest';
        $collection = $this->getRootfileCollection();
        

        $manipulator = $this->makeCestManipulator();
        $manipulator->updateScenario($collection, $scenario);
        $content = $this->getFinder()->getFileContents(self::ROOTFILE_PATH);

        // rough string the new lines are written
        $this->assertStringContainsString(
            $scenario->getSteps()[0]->getaction()->getMethodName(),
            $content
        );
    }


    private function makeCestManipulator() :CestWriter
    {
        return new CestWriter(
            new AstBuilder(),
            new PrettyPrinter(),
            $this->getFinder(),
            new ParserCestReader()
        );
    }


    private function sampleScenarioTree() :ScenarioInterface
    {
        return new Scenario(
            'aNewTest',
            [
                new Step(
                    0,
                    new Action('amOnPage', [
                        new Parameter('url', 'string')
                    ]),
                    [new Argument('/example')]
                ),
                new Step(
                    1,
                    new Action('see', [
                        new Parameter('text', 'string')
                    ]),
                    [new Argument('Example')]
                )
            ]
        );
    }
}
