<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit;

use Fantestic\CestManager\CestReader;
use Fantestic\CestManager\CestReader\ParserCestReader;
use Fantestic\CestManager\Tests\Doubles\Collection;
use Fantestic\CestManager\Tests\Doubles\Scenario;
use Fantestic\CestManager\Tests\VfsTestCase;
use Fantestic\CestManager\Unparser\AstBuilder;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
final class CestReaderTest extends  VfsTestCase
{
    /*
    Update once DTO's are split in in/out
    public function testGetCollectionReturnsCollection() :void
    {
        $cestReader = new CestReader(new AstBuilder(), new ParserCestReader());

        $collection = $cestReader->getCollection(
            self::ROOTFILE_NAMESPACE.'\\'.self::ROOTFILE_CLASSNAME
        );
        $this->assertEquals(
            $this->getRootfileCollection(),
            $collection
        );
    }
    */


    public function testHasScenarioReturnsTrueIfScenarioExists() :void
    {
        $cestReader = new CestReader(new AstBuilder(), new ParserCestReader());
        $collection = new Collection(
            self::ROOTFILE_NAMESPACE,
            self::ROOTFILE_CLASSNAME,
            [],
            self::ROOTFILE_PATH
        );
        $scenario = new Scenario('theFirstTest', []);
        $this->assertTrue($cestReader->hasScenario($collection, $scenario));
    }


    public function testHasScenarioReturnsFalseIfScenarioDoesntExists() :void
    {
        $cestReader = new CestReader(new AstBuilder(), new ParserCestReader());
        $collection = new Collection(
            self::ROOTFILE_NAMESPACE,
            self::ROOTFILE_CLASSNAME,
            [],
            self::ROOTFILE_PATH
        );
        $scenario = new Scenario('aNonExistingMethod', []);
        $this->assertFalse($cestReader->hasScenario($collection, $scenario));
    }
}
