<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit;

use Fantestic\CestManager\Exception\FileNotFoundException;
use Fantestic\CestManager\Finder;
use Fantestic\CestManager\Tests\PhpMock\FunctionProvider\RealpathFunctionProvider;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpmock\MockBuilder;
use phpmock\functions\FunctionProvider;
use PHPUnit\Framework\TestCase;

/**
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class FinderTest extends  TestCase
{
    use \phpmock\phpunit\PHPMock;

    private static $initialized = false;
    private vfsStreamDirectory $root;

    const NON_EXISTING_FILE = '/ANonExistingFile.php';


    public function setUp() :void
    {
        if (false === self::$initialized) {
            $this->buildFilesystemMock('Fantestic\CestManager', 'realpath', new RealpathFunctionProvider());
            self::$initialized = true;
        }
        $this->root = vfsStream::setup('root', null, $this->sampleStructure());
    }


    public function testDoesNotConstructOnInvalidPath() :void
    {
        $this->expectException(FileNotFoundException::class);
        new Finder($this->root->url() . self::NON_EXISTING_FILE);
    }


    public function testHasFileReturnsTrueIfFileExists() :void
    {
        $finder = new Finder($this->root->url());
        $filename = key($this->sampleStructure());
        $this->assertTrue($finder->hasFile($filename));
    }


    public function testHasFileReturnsFalseIfFileDoesntExists() :void
    {
        $finder = new Finder($this->root->url());
        $this->assertFalse($finder->hasFile(self::NON_EXISTING_FILE));
    }


    public function testListFilesListsFilesRecursively() :void
    {
        $files = (new Finder($this->root->url()))->listFiles();
        $expected = [
            'ExampleCest.php',
            'SubDir/Example2Cest.php',
        ];
        $this->assertSame($expected, iterator_to_array($files));
    }


    public function testGetFileContentsReturnsContent() :void
    {
        $finder = new Finder($this->root->url());
        $content = $finder->getFileContents('/ExampleCest.php');
        $this->assertSame($this->sampleStructure()['ExampleCest.php'], $content);
    }


    public function testGetFileContentsThrowsExceptionIfFileDoesntExist() :void
    {
        $this->expectException(FileNotFoundException::class);
        $finder = new Finder($this->root->url());
        $finder->getFileContents(self::NON_EXISTING_FILE);
    }


    private function sampleStructure() :array
    {
        return [
            'ExampleCest.php' => 'SampleContent',
            'SubDir' => [
                'Example2Cest.php' => '',
            ],
        ];
    }


    private function buildFilesystemMock(
        string $namespace,
        string $functionName,
        FunctionProvider $functionProvider
    ) :void
    {
        $builder = new MockBuilder();
        $builder->setNamespace($namespace)
            ->setName($functionName)
            ->setFunction($functionProvider->getCallable())
            ->build()
            ->enable();
    }
}