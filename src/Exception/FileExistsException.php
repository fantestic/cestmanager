<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Exception;

use RuntimeException;

/**
 * Thrown if a file unexpectedly exists.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class FileExistsException extends RuntimeException
{
    // intentionally left blank
}
