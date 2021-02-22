<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit;

use Fantestic\CestManager\CestParser;
use Fantestic\CestManager\Tests\Cest\ExampleCest;
use Fantestic\CestManager\Exception\ClassNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class CestParserTest extends TestCase
{
    public function testThrowsClassNotFoundException() :void
    {
        $this->expectException(ClassNotFoundException::class);
        new CestParser('Fantestic\CestManager\A\NonExisting\ClassName');
    }


    public function testGetScenarioNamesReturnsMethods() :void
    {
        $parser = new CestParser(ExampleCest::class);
        $expected = [
            'theFirstTest'
        ];
        $this->assertSame($expected, $parser->getScenarioNames());
    }
}