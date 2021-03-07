<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Contract;


/**
 * 
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface CollectionInterface
{
    /**
     * @return ScenarioInterface[]
     */
    public function getScenarios() :iterable;

    public function getClassname() :string;

    public function getNamespace() :string;

    public function getSubpath() :string;
}
