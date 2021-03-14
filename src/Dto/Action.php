<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Dto;

use Fantestic\CestManager\Contract\ActionInterface;

/**
 * 
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Action implements ActionInterface
{
    public function __construct(
        protected string $methodName,
        protected iterable $parameters
    ) {}


    public function getMethodName(): string
    {
        return $this->methodName;
    }


    public function getParameters(): iterable
    {
        return $this->parameters;
    }
}
