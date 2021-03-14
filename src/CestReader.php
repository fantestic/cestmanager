<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use Fantestic\CestManager\CestReader\ParserCestReader;
use Fantestic\CestManager\Contract\CollectionInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Dto\Scenario;
use Fantestic\CestManager\Exception\ClassNotFoundException;
use Fantestic\CestManager\Unparser\AstBuilder;
use ReflectionClass;

/**
 * Gives access to a Cest
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class CestReader
{
    public function __construct(
        private AstBuilder $astBuilder,
        private ParserCestReader $parserCestReader
    ) {}


    /**
     * Retrieves a list of all Scenarios
     * 
     * @return string[] 
     */
    public function getScenarios(string $classname) :iterable
    {
        return $this->parserCestReader->getScenarios($classname);
    }


    public function getScenario(
        CollectionInterface $collection,
        ScenarioInterface $scenario
    ) :Scenario
    {
        return $this->parserCestReader->getScenario(
            $collection->getClassname(),
            $scenario->getMethodName()
        );
    }


    private function makeReflectionClass(string $classname) :ReflectionClass
    {
        if (!class_exists($classname)) {
            throw new ClassNotFoundException(
                "The class '{$classname}' could not be found!"
            );
        }
        return new ReflectionClass($classname);
    }
}
