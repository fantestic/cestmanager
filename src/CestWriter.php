<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use ArrayObject;
use Exception;
use Fantestic\CestManager\CestReader\ParserCestReader;
use Fantestic\CestManager\Contract\CollectionInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Unparser\PrettyPrinter;
use Fantestic\CestManager\Exception\FileExistsException;
use Fantestic\CestManager\Exception\FileNotFoundException;
use Fantestic\CestManager\Exception\InsufficientPermissionException;
use Fantestic\CestManager\Exception\MethodExistsException;
use Fantestic\CestManager\Exception\MethodNotFoundException;
use Fantestic\CestManager\Unparser\AstBuilder;
use Fantestic\CestManager\Parser\NodeVisitor\AddMethodNodeVisitor;
use Fantestic\CestManager\Parser\NodeVisitor\FindMethodNodeVisitor;
use Fantestic\CestManager\Parser\NodeVisitor\OverwriteMethodNodeVisitor;
use Iterator;
use PhpParser\Error;
use LogicException;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Node\Stmt\ClassMethod;
use RuntimeException;

/**
 * Updates CestFiles
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class CestWriter
{
    /**
     * @throws FileNotFoundException
     * @param string $basepath 
     * @return void 
     */
    public function __construct(
        private AstBuilder $astBuilder,
        private PrettyPrinter $prettyPrinter,
        private Finder $finder,
        private ParserCestReader $parserCestReader
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
            $this->finder->writeFile(
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


    public function updateScenario(
        CollectionInterface $collection,
        ScenarioInterface $scenario
    ) :void
    {
        $ast = $this->parserCestReader->getAstForClass(
            $collection->getFullyQualifiedClassname()
        );
        $methodNode = $this->findScenarioInAst($scenario, $ast);
        if (is_null($methodNode)) {
            throw new MethodNotFoundException(
                sprintf(
                    'Method "%s" does not exist in Cest "%s".',
                    $scenario->getMethodName(),
                    $collection->getNamespace() . '\\' . $collection->getClassname()
                )
            );
        }
        $updatedAst = $this->overwriteScenarioInAst($scenario, $ast);

        $this->finder->writeFile(
            $collection->getSubpath(),
            $this->prettyPrinter->prettyPrintFile($updatedAst)
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


    private function overwriteScenarioInAst(
        ScenarioInterface $scenario,
        array &$ast
    ) :array
    {
        $scenarioAst = $this->astBuilder->buildScenarioAst($scenario);
        $overwriteMethodNodeVisitor = new OverwriteMethodNodeVisitor(
            $scenario->getMethodName(),
            $scenarioAst->getNode()->getStmts()
        );
        $this->traverse($ast, [$overwriteMethodNodeVisitor]);
        return $ast;
    }


    private function injectScenarioIntoAst(
        ScenarioInterface $scenario,
        array &$ast
    ) :void
    {
        $scenarioAst = $this->astBuilder->buildScenarioAst($scenario);
        $addMethodNodeVisitor = new AddMethodNodeVisitor($scenarioAst->getNode());
        $this->traverse($ast, [$addMethodNodeVisitor]);
    }

    /**
     * 
     * @param array $ast 
     * @param NodeVisitor[] $nodeVisitors 
     * @return void 
     * @throws LogicException 
     */
    private function traverse(array &$ast, array $nodeVisitors) :void
    {
        $traverser = new NodeTraverser();
        foreach ($nodeVisitors as $nodeVisitor) {
            $traverser->addVisitor($nodeVisitor);
        }
        $traverser->traverse($ast);
    }
}
