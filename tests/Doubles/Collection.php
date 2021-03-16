<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Doubles;

use Fantestic\CestManager\Contract\CollectionInterface;

/**
 * Test-Double for Collection interface
 *
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Collection implements CollectionInterface
{
    public function __construct(
        public string $namespace,
        public string $classname,
        public iterable $scenarios,
        public string $subpath
    ) { }

    public function getFullyQualifiedClassname(): string
    {
        return $this->getNamespace() . '\\' . $this->getClassname();
    }

    public function getSubpath(): string
    {
        return $this->subpath;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }


    public function getScenarios(): iterable
    {
        return $this->scenarios;
    }
}
