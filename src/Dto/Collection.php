<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Dto;

use Fantestic\CestManager\Contract\CollectionInterface;

/**
 * 
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Collection
{
    public function __construct(
        protected string $classname,
        protected string $namespace,
        protected iterable $scenarios
    ) {}


    public function getScenarios(): iterable
    {
        return $this->scenarios;
    }


    public function getClassname(): string
    {
        return $this->classname;
    }


    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
