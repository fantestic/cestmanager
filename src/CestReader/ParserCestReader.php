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
use LogicException;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract, Parser, ParserFactory};

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
        $steps = [];
        $pos = 0;
        $methodNode = $findMethodNodeVisitor->getMethodNode();
        foreach ($methodNode->getStmts() as $node) {
            if (
                !$node instanceof \PhpParser\Node\Stmt\Expression ||
                !$node->expr instanceof \PhpParser\Node\Expr\MethodCall ||
                $node->expr->var->name !== 'I'
            ) {
                throw new UnprocessableScenarioException(
                    "The body of '{$methodname}' has a statement that can't be processed by Fantestic."
                );
            }
            $steps[$pos] = new Step(
                $pos,
                $this->getAction($node->expr->name->name),
                $this->getArguments($node->expr->args)
            );
            $pos++;
        }
        return new Scenario($methodname, $steps);
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
