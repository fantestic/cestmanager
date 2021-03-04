<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Unparser;

use Fantestic\CestManager\Contract\CollectionInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Contract\StepInterface;
use LogicException;
use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;

/**
 * Builds AST from internal Test-Representations. The AST can then be unparsed
 * into PHP-code.
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic\CestManager
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class AstBuilder
{
    private BuilderFactory $builder;

    public function __construct()
    {
        $this->builder = new BuilderFactory();
    }


    public function buildCollectionAst(CollectionInterface $collection) :array
    {
        $strictTypes = new Declare_([
            new DeclareDeclare(new Identifier('strict_types'), new LNumber(1))
        ]);
        $class = $this->builder->class($collection->getClassname())
            ->makeFinal()
            ->setDocComment('/**
                * These tests are managed by fantestic. Manual changes might break things.
                *
                * @fantestic
            */');
        foreach ($collection->getScenarios() as $scenario) {
            $class->addStmt($this->buildScenarioAst($scenario));
        }
        $ns = $this->builder->namespace($collection->getNamespace())
            ->addStmt($class);
        return [
            $strictTypes,
            $ns->getNode()
        ];
    }


    /**
     * 
     * @param ScenarioInterface $scenario 
     * @return Method 
     * @throws LogicException 
     */
    public function buildScenarioAst(ScenarioInterface $scenario) :Method
    {
        $f = $this->builder;
        $method = $f->method($scenario->getMethodName())
            ->addParam($f->param('I')->setType(new FullyQualified('AcceptanceTester')))
            ->setReturnType('void')
            ->setDocComment('/** @fantestic */');
        

        foreach($scenario->getSteps() as $step) {
            // @TODO sorting by position
            $method->addStmt($this->buildStepAst($step));
        }
        return $method;
    }


    /**
     * 
     * @param StepInterface $step 
     * @return MethodCall 
     * @throws LogicException 
     */
    private function buildStepAst(StepInterface $step) :MethodCall
    {
        $f = $this->builder;
        $args = [];
        foreach ($step->getArguments() as $argument) {
            // @TODO validate against Parameter to ensure validity
            $args[] = $argument->getValue();
        }

        return $f->methodCall(
            $f->var('I'),
            $step->getAction()->getMethodName(),
            $args
        );
    }
}
