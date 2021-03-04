<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Doubles;

use Fantestic\CestManager\Contract\ActionInterface;
use Fantestic\CestManager\Contract\ParameterInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;

/**
 * Test-Double for Scenario interface
 *
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Parameter implements ParameterInterface
{
    public function __construct(
        public string $variableName,
        public string $variableType
    ) { }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getVariableType(): string
    {
        return $this->variableType;
    }
}
