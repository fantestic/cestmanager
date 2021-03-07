<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\PhpMock\FunctionProvider;

use phpmock\functions\FunctionProvider;

/**
 * Mimicks the native file_put_contents function, but disableds LOCK_EX.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class FilePutContentsFunctionProvider implements FunctionProvider
{
    /**
     * 
     * @return callable 
     */
    public function getCallable() :callable
    {
        /**
         * @return string|false
         */
        return function (string $filename, mixed $data, int $flags = 0, $context = null) {
            if (is_null($context)) {
                return file_put_contents($filename, $data, 0);
            } else {
                return file_put_contents($filename, $data, 0, $context);
            }
        };
    }
}