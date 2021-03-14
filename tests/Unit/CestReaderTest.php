<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit;

use Fantestic\CestManager\CestReader;
use Fantestic\CestManager\CestReader\ParserCestReader;
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
    public function testGetCollectionReturnsCollection() :void
    {
        $cestReader = new CestReader(new AstBuilder(), new ParserCestReader());

        $collection = $cestReader->getCollection(
            self::ROOTFILE_NAMESPACE.'\\'.self::ROOTFILE_CLASSNAME
        );
    }
}
