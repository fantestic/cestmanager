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
interface ActionInterface
{
    /**
     * 
     * @return ParameterInterface[] 
     */
    public function getParameters() :iterable;

    public function getMethodName() :string;
}
