<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Unparser;

use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Contract\StepInterface;
use LogicException;
use PhpParser\Builder\Method;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;

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
    public function __construct()
    {
        $this->builderFactory = new BuilderFactory();
    }


    /**
     * 
     * @param ScenarioInterface $scenario 
     * @return Method 
     * @throws LogicException 
     */
    public function generateScenarioAst(ScenarioInterface $scenario) :Method
    {
        $f = $this->builderFactory;
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
        $f = $this->builderFactory;
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
