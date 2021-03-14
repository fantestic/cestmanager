<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Dto;

use Fantestic\CestManager\Contract\ScenarioInterface;

/**
 * One Step inside a scenario
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Scenario implements ScenarioInterface
{
    public function __construct(
        protected string $methodName,
        protected iterable $steps
    ) {}


    public function getSteps(): iterable
    {
        return $this->steps;
    }


    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
