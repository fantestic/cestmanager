<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests\Unit;

use Fantestic\CestManager\Exception\FileExistsException;
use Fantestic\CestManager\Exception\FileNotFoundException;
use Fantestic\CestManager\Exception\InsufficientPermissionException;
use Fantestic\CestManager\Finder;
use Fantestic\CestManager\Tests\PhpMock\FunctionProvider\FilePutContentsFunctionProvider;
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
final class FinderTest extends  TestCase
{
    use \phpmock\phpunit\PHPMock;

    private static $initialized = false;
    private vfsStreamDirectory $root;

    const NON_EXISTING_FILE = '/ANonExistingFile.php';
    const ROOTFILE_PATH = 'ExampleCest.php';
    const SUBDIR_PATH = 'SubDir';
    const NESTEDFILE_NAME = 'Example2Cest.php';
    const NESTEDFILE_PATH = 'SubDir/Example2Cest.php';


    public function setUp() :void
    {
        if (false === self::$initialized) {
            $this->buildFilesystemMock('Fantestic\CestManager', 'realpath', new RealpathFunctionProvider());
            $this->buildFilesystemMock('Fantestic\CestManager', 'file_put_contents', new FilePutContentsFunctionProvider());
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
            self::NESTEDFILE_PATH,
        ];
        $this->assertSame($expected, iterator_to_array($files));
    }


    public function testGetFileContentsReturnsContent() :void
    {
        $finder = new Finder($this->root->url());
        $content = $finder->getFileContents(self::ROOTFILE_PATH);
        $this->assertSame($this->sampleStructure()[self::ROOTFILE_PATH], $content);
    }


    public function testGetFileContentsThrowsExceptionIfFileDoesntExist() :void
    {
        $this->expectException(FileNotFoundException::class);
        $finder = new Finder($this->root->url());
        $finder->getFileContents(self::NON_EXISTING_FILE);
    }


    public function testRemoveFileRemovesFile() :void
    {
        $finder = new Finder($this->root->url());
        $finder->removeFile(self::NESTEDFILE_PATH);
        $this->assertFalse($finder->hasFile(self::NESTEDFILE_PATH));
    }


    public function testFileRemovesThrowsExceptionWhenDeletingAFolder() :void
    {
        $this->expectException(FileNotFoundException::class);
        $finder = new Finder($this->root->url());
        $finder->removeFile(self::SUBDIR_PATH);
    }


    public function testFileRemovesThrowsExceptionWhenFileIsLocked() :void
    {
        $this->expectException(InsufficientPermissionException::class);
        $this->root->getChild(self::SUBDIR_PATH)->chmod(0);
        $finder = new Finder($this->root->url());
        $finder->removeFile(self::NESTEDFILE_PATH);
    }


    public function testFileRemovesThrowsExceptionWhenFileIsNotFound() :void
    {
        $this->expectException(FileNotFoundException::class);
        $finder = new Finder($this->root->url());
        $finder->removeFile(self::NON_EXISTING_FILE);
    }


    public function testCreateFileCreatesFile() :void
    {
        $filename = 'NewFile.php';
        $content = '<?php echo "content";';
        $finder = new Finder($this->root->url());
        $finder->createFile($filename, $content);
        $this->assertSame($content, $finder->getFileContents($filename));
    }


    public function testCreateFileCreatesRecursiveFolders() :void
    {
        $filename = 'Folder1/Folder2/RecursiveCreate.php';
        $content = '<?php echo "recursive created";';
        $finder = new Finder($this->root->url());
        $finder->createFile($filename, $content);
        $this->assertSame($content, $finder->getFileContents($filename));
    }


    public function testCreateFileThrowsExceptionIfFileExists() :void
    {
        $this->expectException(FileExistsException::class);
        $finder = new Finder($this->root->url());
        $finder->createFile(self::ROOTFILE_PATH, 'contents');
    }


    public function testCreateFileThrowsExceptionIfInsufficientPermissions() :void
    {
        $this->expectException(InsufficientPermissionException::class);
        $finder = new Finder($this->root->url());
        $this->root->getChild(self::SUBDIR_PATH)->chmod(0);
        $finder->createFile(self::SUBDIR_PATH.'/ShouldFail.txt', 'contents');
    }


    private function sampleStructure() :array
    {
        return [
            self::ROOTFILE_PATH => 'SampleContent',
            self::SUBDIR_PATH => [
                self::NESTEDFILE_NAME => '',
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