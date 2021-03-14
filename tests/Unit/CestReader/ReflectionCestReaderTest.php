<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit\CestReader;

use Fantestic\CestManager\CestReader\ReflectionCestReader;
use Fantestic\CestManager\Tests\Cest\ExampleCest;
use Fantestic\CestManager\Exception\ClassNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class ReflectionCestReaderTest extends TestCase
{
    public function testThrowsClassNotFoundException() :void
    {
        $this->expectException(ClassNotFoundException::class);
        $cestReader = new ReflectionCestReader();
        $cestReader->getScenarioNames('Fantestic\CestManager\A\NonExisting\ClassName');
    }


    public function testGetScenarioNamesReturnsMethods() :void
    {
        $parser = new ReflectionCestReader();
        $expected = [
            'theFirstTest'
        ];
        $this->assertSame($expected, $parser->getScenarioNames(ExampleCest::class));
    }
}
