<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Dto;

use Fantestic\CestManager\Contract\ActionInterface;
use Fantestic\CestManager\Contract\StepInterface;

/**
 * One Step inside a scenario
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Step implements StepInterface
{
    public function __construct(
        protected int $position,
        protected ActionInterface $action,
        protected iterable $arguments
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
