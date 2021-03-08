<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use ArrayObject;
use Exception;
use Fantestic\CestManager\Contract\CollectionInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Unparser\PrettyPrinter;
use Fantestic\CestManager\Exception\FileExistsException;
use Fantestic\CestManager\Exception\FileNotFoundException;
use Fantestic\CestManager\Exception\InsufficientPermissionException;
use Fantestic\CestManager\Exception\MethodExistsException;
use Fantestic\CestManager\Unparser\AstBuilder;
use Fantestic\CestManager\Parser\NodeVisitor\AddMethodNodeVisitor;
use Fantestic\CestManager\Parser\NodeVisitor\FindMethodNodeVisitor;
use Iterator;
use PhpParser\Error;
use LogicException;
use PhpParser\NodeTraverser;
use PhpParser\Node\Stmt\ClassMethod;
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


    public function createScenario(
        CollectionInterface $collection,
        ScenarioInterface $scenario
    ) :void
    {
        $ast = $this->astBuilder->buildCollectionAst($collection);
        $methodNode = $this->findScenarioInAst($scenario, $ast);
        if (!is_null($methodNode)) {
            throw new MethodExistsException(
                sprintf(
                    'Method "%s" already exists in Cest "%s".',
                    $scenario->getMethodName(),
                    $collection->getNamespace().'\\'.$collection->getClassname()
                )
            );
        }
        $this->injectScenarioIntoAst($scenario, $ast);
        $this->finder->writeFile(
            $collection->getSubpath(),
            $this->prettyPrinter->prettyPrintFile($ast)
        );
    }


    /**
     * 
     * @param ScenarioInterface $scenario 
     * @param array $ast 
     * @return null|MethodNode 
     * @throws LogicException 
     */
    private function findScenarioInAst(ScenarioInterface $scenario, array $ast) :?ClassMethod
    {
        $findMethodNodeVisitor = new FindMethodNodeVisitor($scenario->getMethodName());
        $traverser = new NodeTraverser();
        $traverser->addVisitor($findMethodNodeVisitor);
        $traverser->traverse($ast);
        return $findMethodNodeVisitor->getMethodNode();
    }


    private function injectScenarioIntoAst(
        ScenarioInterface $scenario,
        array $ast
    ) :void
    {
        $scenarioAst = $this->astBuilder->buildScenarioAst($scenario);
        $addMethodNodeVisitor = new AddMethodNodeVisitor($scenarioAst->getNode());
        $traverser = new NodeTraverser();
        $traverser->addVisitor($addMethodNodeVisitor);
        $traverser->traverse($ast);
    }
}
