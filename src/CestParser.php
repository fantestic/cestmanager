<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use Fantestic\CestManager\Exception\ClassNotFoundException;
use ReflectionClass;

/**
 * Gives access to a Cest
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class CestParser
{
    private ReflectionClass $reflection;

    public function __construct(string $classname)
    {
        if (!class_exists($classname)) {
            throw new ClassNotFoundException(
                "The class '{$classname}' could not be found!"
            );
        }
        $this->reflection = new ReflectionClass($classname);
    }


    /**
     * Retrieves a list of all Scenarios
     * 
     * @return string[] 
     */
    public function getScenarioNames(string $class) :array
    {
        $scenarios = [];
        foreach($this->reflection->getMethods() as $method) {
            if ($method->isPublic() && substr($method->getName(), 0, 1) !== '_') {
                $scenarios[] = $method->getName();
            }
        }
        return $scenarios;
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
