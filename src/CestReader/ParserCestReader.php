<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\CestReader;

use Fantestic\CestManager\CestReader\Traits\ReflectionMaker;
use Fantestic\CestManager\Dto\Scenario;
use Fantestic\CestManager\Dto\Step;
use Fantestic\CestManager\Exception\MethodNotFoundException;
use Fantestic\CestManager\Parser\NodeVisitor\FindMethodNodeVisitor;
use Fantestic\CestManager\Dto\Action;
use Fantestic\CestManager\Dto\ArgumentOut;
use Fantestic\CestManager\Exception\ClassNotFoundException;
use Fantestic\CestManager\Exception\UnprocessableScenarioException;
use Fantestic\CestManager\Parser\NodeVisitor\FindMethodsNodeVisitor;
use LogicException;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract, Parser, ParserFactory};
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

/**
 * Read via PHP-Parser. Slow performance but can give access to Scenario-Body.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ParserCestReader
{
    use ReflectionMaker;

    /**
     * 
     * @return iterable | Scenario[]
     */
    public function getScenarios(string $classname) :iterable
    {
        $traverser = new NodeTraverser();
        $findMethodNodeVisitor = new FindMethodsNodeVisitor();
        $traverser->addVisitor($findMethodNodeVisitor);
        $traverser->traverse($this->getAstForClass($classname));
        $scenarios = [];
        foreach ($findMethodNodeVisitor->getMethodNodes() as $methodNode) {
            if ($this->isFantesticScenarioNode($methodNode)) {
                $scenarios[] = $this->buildScenarioFromNode($methodNode);
            }
        }
        return $scenarios;
    }


    /**
     * 
     * @param string $classname 
     * @param string $methodname 
     * @return Scenario 
     * @throws ClassNotFoundException 
     * @throws LogicException 
     * @throws MethodNotFoundException 
     * @throws UnprocessableScenarioException 
     */
    public function getScenario(string $classname, string $methodname) :Scenario
    {
        $traverser = new NodeTraverser();
        $findMethodNodeVisitor = new FindMethodNodeVisitor($methodname);
        $traverser->addVisitor($findMethodNodeVisitor);
        $traverser->traverse($this->getAstForClass($classname));
        if (false === $findMethodNodeVisitor->methodWasFound()) {
            throw new MethodNotFoundException(
                "The method '{$methodname}' does not exist in class '{$classname}'."
            );
        }
        if (!$this->isFantesticScenarioNode($findMethodNodeVisitor->getMethodNode())) {
            throw new MethodNotFoundException(
                sprintf('The method "%s" is not a valid Fantestic Scenario.', $methodname)
            );
        }
        return $this->buildScenarioFromNode($findMethodNodeVisitor->getMethodNode());
    }


    private function isFantesticScenarioNode(ClassMethod $methodNode) :bool
    {
        $startsWithUnderscore = ('_' === substr($methodNode->name->toString(), 0, 1));
        return ($methodNode->isPublic() && !$startsWithUnderscore);
    }


    private function buildScenarioFromNode(ClassMethod $methodNode) :Scenario
    {
        $steps = [];
        $pos = 0;
        foreach ($methodNode->getStmts() as $node) {
            if (!$this->seemsLikeValidFantesticStep($node)) {
                throw new UnprocessableScenarioException(
                    "The body of '{$methodNode->name}' has a statement that can't be processed by Fantestic."
                );
            }
            $steps[$pos] = new Step(
                $pos,
                $this->getAction($node->expr->name->name),
                $this->getArguments($node->expr->args)
            );
            $pos++;
        }
        return new Scenario($methodNode->name->toString(), $steps);
    }


    /**
     * Does a quick check to see if the node seems to be a valid Fantestic Step.
     * This function is not reliable for in-depth check as it does not walk the AST.
     * 
     * @param ClassMethod $node 
     * @return bool 
     */
    private function seemsLikeValidFantesticStep(Node $node) :bool
    {
        return (
            $node instanceof \PhpParser\Node\Stmt\Expression &&
            $node->expr instanceof \PhpParser\Node\Expr\MethodCall &&
            $node->expr->var->name === 'I'
        );
    }


    /**
     * 
     * @param iterable $astArguments 
     * @return iterable 
     * @throws UnprocessableScenarioException 
     */
    private function getArguments(iterable $astArguments) :iterable
    {
        $arguments = [];
        foreach ($astArguments as $argument) {
            $arguments[] = ArgumentOut::fromPhpParserNode($argument->value);
        }
        return $arguments;
    }


    private function getAction(string $methodname) :Action
    {
        // @TODO implement Reader
        return new Action($methodname, []);
    }


    private function getAstForClass(string $classname) :array
    {
        $reflectionClass = $this->makeReflectionClass($classname);
        return $this->getParser()->parse(
            file_get_contents($reflectionClass->getFileName())
        );
    }

    private function getParser() :Parser
    {
        $factory = new ParserFactory();
        return $factory->create(ParserFactory::PREFER_PHP7);
    }

}
