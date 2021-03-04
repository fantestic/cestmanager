<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Doubles;

use Fantestic\CestManager\Contract\ScenarioInterface;

/**
 * Test-Double for Scenario interface
 *
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Scenario implements ScenarioInterface
{
    public function __construct(
        public string $methodName,
        public iterable $steps
    ) { }

    public function getSteps(): iterable
    {
        return $this->steps;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
