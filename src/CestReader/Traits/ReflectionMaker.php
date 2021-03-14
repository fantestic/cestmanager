<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\CestReader\Traits;

use ReflectionClass;
use Fantestic\CestManager\Exception\ClassNotFoundException;

/**
 * Read Information via the Php-Reflection API. Can be used for high-performance
 * Read-Access to Cest-Objects.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
trait ReflectionMaker
{
    /**
     * 
     * @param string $classname 
     * @return ReflectionClass 
     * @throws ClassNotFoundException 
     */
    protected function makeReflectionClass(string $classname) :ReflectionClass
    {
        if (!class_exists($classname)) {
            throw new ClassNotFoundException(
                "The class '{$classname}' could not be found!"
            );
        }
        return new ReflectionClass($classname);
    }
}