<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Exception;

use DomainException;

/**
 * Thrown if a scenario cannot be read/processed.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class UnprocessableScenarioException extends DomainException
{
    // intentionally left blank
}
