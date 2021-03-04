<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Doubles;

use Fantestic\CestManager\Contract\ActionInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;

/**
 * Test-Double for Scenario interface
 *
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Action implements ActionInterface
{
    public function __construct(
        public string $methodName,
        public iterable $parameters
    ) { }

    public function getParameters(): iterable
    {
        return $this->parameters;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
