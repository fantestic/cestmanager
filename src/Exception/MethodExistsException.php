<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Exception;

use RuntimeException;

/**
 * Thrown if a method with the same name already exists in the class.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class MethodExistsException extends RuntimeException
{
    // intentionally left blank
}
