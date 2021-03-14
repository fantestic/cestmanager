<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\CestReader;

use Fantestic\CestManager\CestReader\Traits\ReflectionMaker;
use Fantestic\CestManager\Exception\ClassNotFoundException;

/**
 * Read Information from CestFiles
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface CestReaderInterface
{
    /**
     * Retrieves a list of all Scenarios
     * 
     * @return string[]
     * @throws ClassNotFoundException
     */
    public function getScenarioNames(string $class) :array;
}
