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
interface StepInterface
{
    public function getPosition() :int;

    public function getAction() :ActionInterface;

    /**
     * 
     * @return ArgumentInterface[] 
     */
    public function getArguments() :iterable;
}
