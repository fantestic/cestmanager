<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Doubles;

use Fantestic\CestManager\Contract\ActionInterface;
use Fantestic\CestManager\Contract\ArgumentInterface;
use Fantestic\CestManager\Contract\ScenarioInterface;

/**
 * Test-Double for Scenario interface
 *
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Argument implements ArgumentInterface
{
    public function __construct(
        public mixed $value,
    ) { }

    public function getValue(): mixed
    {
        return $this->value;
    }

}
