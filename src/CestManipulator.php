<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use ArrayObject;
use Exception;
use Fantestic\CestManager\Contract\CollectionInterface;
use Fantestic\CestManager\Unparser\PrettyPrinter;
use Fantestic\CestManager\Exception\FileExistsException;
use Fantestic\CestManager\Exception\FileNotFoundException;
use Fantestic\CestManager\Exception\InsufficientPermissionException;
use Fantestic\CestManager\Unparser\AstBuilder;
use Iterator;
use PhpParser\Error;
use LogicException;
use RuntimeException;

/**
 * Updates CestFiles
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class CestManipulator
{
    /**
     * @throws FileNotFoundException
     * @param string $basepath 
     * @return void 
     */
    public function __construct(
        private AstBuilder $astBuilder,
        private PrettyPrinter $prettyPrinter,
        private Finder $finder
    ) {}

    /**
     * 
     * @param CollectionInterface $collection 
     * @return void 
     * @throws Error 
     * @throws LogicException 
     * @throws FileExistsException 
     * @throws InsufficientPermissionException 
     * @throws RuntimeException 
     */
    public function createCest(CollectionInterface $collection) :void
    {
        try {
            $ast = $this->astBuilder->buildCollectionAst($collection);
            $this->finder->createFile(
                $collection->getSubpath(),
                $this->prettyPrinter->prettyPrintFile($ast)
            );
        } catch (Exception $e) {
            throw $e;
        }
    }
}
