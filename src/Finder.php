<?php

declare(strict_types = 1);
namespace Fantestic\CestManager;

use ArrayObject;
use Fantestic\CestManager\Exception\FileExistsException;
use Fantestic\CestManager\Exception\FileNotFoundException;
use Fantestic\CestManager\Exception\InsufficientPermissionException;
use Iterator;
use RuntimeException;

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
     * Checks if a file exists in the current repository.
     * 
     * @param string $subpath 
     * @return bool 
     */
    public function hasFile(string $subpath): bool
    {
        return file_exists($this->buildFullpath($subpath));
    }


    /**
     * 
     * @param string $subpath 
     * @return string 
     * @throws FileNotFoundException 
     */
    public function getFileContents(string $subpath) :string
    {
        if (!$this->hasFile($subpath)) {
            throw new FileNotFoundException(
                "File '{$subpath}' could not be found."
            );
        }
        return file_get_contents($this->buildFullpath($subpath));
    }


    /**
     * Returns a flat list of all files.
     * 
     * @return string[] 
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
     * @return void 
     * @throws FileNotFoundException
     * @throws InsufficientPermissionException
     * @throws RuntimeException 
     */
    public function removeFile(string $subpath) :void
    {
        if (!$this->hasFile($subpath)) {
            throw new FileNotFoundException(
                "File '{$subpath}' could not be found."
            );
        }
        if (!$this->isFile($subpath)) {
            throw new FileNotFoundException("'{$subpath}' is not a file.");
        }
        if(!$this->canWriteInContainingDirectory($subpath)) {
            throw new InsufficientPermissionException(
                "Cant remove file '{$subpath}' due to insufficient permissions."
            );
        }
        $status = unlink($this->buildFullpath($subpath));
        if (true !== $status) {
            throw new \RuntimeException(
                "Could not delete '{$subpath}'."
            );
        }
    }


    /**
     * 
     * @param string $subpath 
     * @param string $contents 
     * @return void 
     * @throws FileExistsException 
     * @throws InsufficientPermissionException 
     * @throws RuntimeException 
     */
    public function createFile(string $subpath, string $contents) :void
    {
        if ($this->hasFile($subpath)) {
            throw new FileExistsException(
                "The file '{$subpath}' already exists."
            );
        }
        if(!$this->canWriteInContainingDirectory($subpath)) {
            throw new InsufficientPermissionException(
                "Cant create file '{$subpath}' due to insufficient permissions."
            );
        }
        $status = file_put_contents(
            $this->buildFullpath($subpath),
            $contents,
            LOCK_EX
        );
        if (false === $status) {
            throw new \RuntimeException(
                "Could not create file '{$subpath}'."
            );
        }
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


    /**
     * 
     * @param string $subpath 
     * @return bool 
     */
    private function isFile(string $subpath) :bool
    {
        return is_file($this->buildFullpath($subpath));
    }


    /**
     * 
     * @param string $fullpath 
     * @return array 
     */
    private function list(string $fullpath) :array
    {
        return array_diff(scandir($fullpath), ['.', '..']);
    }


    /**
     * Returns true if the directory of the containing $subpath is writeable
     * 
     * @param string $subpath 
     * @return string 
     */
    private function canWriteInContainingDirectory(string $subpath) :bool
    {
        $fullDirectoryPath = dirname($this->buildFullpath($subpath));
        return $this->isWriteable(
            $this->buildSubpath($fullDirectoryPath)
        );
    }


    /**
     * 
     * @param string $subpath 
     * @return bool 
     */
    private function isWriteable(string $subpath) :bool
    {
        return is_writable($this->buildFullpath($subpath));
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


    private function buildSubpath(string $fullpath) :string
    {
        return substr($fullpath, strlen($this->basepath)+1);
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
