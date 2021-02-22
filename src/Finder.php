<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use App\CodeParser\Filesystem\Exception\FileNotFoundException as ExceptionFileNotFoundException;
use ArrayObject;
use Fantestic\CestManager\Exception\FileNotFoundException;
use Iterator;

/**
 * Searches the filesystem for files and folders.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Finder
{
    private string $basepath;

    /**
     * @throws FileNotFoundException
     * @param string $basepath 
     * @return void 
     */
    public function __construct(string $basepath)
    {
        $realpath = realpath($basepath);
        if (false === $realpath) {
            throw new FileNotFoundException(
                "Directory '{$basepath}' is no valid directory!"
            );
        }
        $this->basepath = $realpath;
    }


    /**
     * @inheritdoc 
     */
    public function hasFile(string $subpath): bool
    {
        return file_exists($this->buildFullpath($subpath));
    }


    /**
     * @inheritdoc
     */
    public function getFileContent(string $subpath) :string
    {
        if (!$this->hasFile($subpath)) {
            throw new FileNotFoundException(
                "File '{$subpath}' could not be found."
            );
        }
        return file_get_contents($this->buildFullpath($subpath));
    }


    /**
     * @inheritdoc
     */
    public function listFiles() :Iterator
    {
        // We do not support generator yet, so we mimick the behavior for the meantime
        $arrayObject = new ArrayObject($this->walkRecursively());
        return $arrayObject->getIterator();
    }


    /**
     * 
     * @param string $subpath 
     * @return array 
     */
    private function walkRecursively(string $subpath = '') :array
    {
        $files = [];
        foreach ($this->list($this->buildFullpath($subpath)) as $fileToCheck) {
            if (is_dir($this->buildFullpath($this->mergePath($subpath, $fileToCheck)))) {
                $files = array_merge($files, $this->walkRecursively($this->mergePath($subpath, $fileToCheck)));
            } else {
                $files[] = $this->mergePath($subpath, $fileToCheck);
            }
        }
        return $files;
    }


    private function list(string $fullpath) :array
    {
        return array_diff(scandir($fullpath), ['.', '..']);
    }


    private function convertToSubpath(string $fullpath) :string
    {
        return substr($fullpath, strlen($this->basepath));
    }


    /**
     * Builds a full directory path
     * 
     * @param string $subpath 
     * @return string 
     */
    private function buildFullpath(string $subpath) :string
    {
        return $this->mergePath($this->basepath, $subpath);
    }


    /**
     * Merges 2 paths
     * 
     * @param string $basepath 
     * @param string $subpath 
     * @return string 
     */
    private function mergePath(string $basepath, string $subpath) :string
    {
        if ($basepath === '' || $subpath === '') {
            return $basepath . $subpath;
        }
        return $basepath . DIRECTORY_SEPARATOR . $subpath;
    }
}