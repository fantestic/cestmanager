<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Doubles;

use Fantestic\CestManager\Contract\ActionInterface;
use Fantestic\CestManager\Contract\StepInterface;

/**
 * Test-Double for Scenario interface
 *
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Step implements StepInterface
{
    public function __construct(
        public int $position,
        public ActionInterface $action,
        public iterable $arguments
    ) { }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    public function getArguments(): iterable
    {
        return $this->arguments;
    }
}
