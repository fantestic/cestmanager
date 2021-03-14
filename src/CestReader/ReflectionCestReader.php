<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\CestReader;

use Fantestic\CestManager\CestReader\Traits\ReflectionMaker;

/**
 * Read Information via the Php-Reflection API. Can be used for high-performance
 * Read-Access to Cest-Objects.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ReflectionCestReader implements CestReaderInterface
{
    use ReflectionMaker;

    /**
     * Retrieves a list of all Scenarios
     * 
     * @return string[] 
     */
    public function getScenarioNames(string $classname) :array
    {
        $scenarios = [];
        foreach($this->makeReflectionClass($classname)->getMethods() as $method) {
            if ($method->isPublic() && substr($method->getName(), 0, 1) !== '_') {
                $scenarios[] = $method->getName();
            }
        }
        return $scenarios;
    }

}
