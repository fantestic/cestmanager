<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use Fantestic\CestManager\CestReader\ParserCestReader;
use Fantestic\CestManager\Contract\CollectionInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;
use Fantestic\CestManager\Dto\Scenario;
use Fantestic\CestManager\Dto\Collection;
use Fantestic\CestManager\Exception\ClassNotFoundException;
use Fantestic\CestManager\Exception\MethodNotFoundException;
use Fantestic\CestManager\Exception\UnprocessableScenarioException;
use Fantestic\CestManager\Unparser\AstBuilder;
use LogicException;
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
     * 
     * @param string $fullyQualifiedName 
     * @return Collection 
     * @throws ClassNotFoundException 
     * @throws LogicException 
     * @throws UnprocessableScenarioException 
     */
    public function getCollection(CollectionInterface $collection) :Collection
    {
        $scenarios = $this->parserCestReader->getScenarios($collection);
        return new Collection(
                $collection->getClassname(),
                $collection->getNamespace(),
                $scenarios
        );
    }

    public function getScenario(
        CollectionInterface $collection,
        ScenarioInterface $scenario
    ) :Scenario
    {
        return $this->parserCestReader->getScenario(
            $collection,
            $scenario->getMethodName()
        );
    }

    public function hasScenario(
        CollectionInterface $collection,
        ScenarioInterface $scenario
    ) {
        try {
            $this->getScenario($collection,$scenario);
        } catch (ClassNotFoundException|MethodNotFoundException $e) {
            return false;
        }
        return true;
    }
}
