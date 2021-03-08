<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Tests;

use Fantestic\CestManager\CestReader\ReflectionCestReader;
use Fantestic\CestManager\Finder;
use Fantestic\CestManager\Tests\Doubles\Collection;
use Fantestic\CestManager\Tests\Doubles\Scenario;
use Fantestic\CestManager\Tests\PhpMock\FunctionProvider\FilePutContentsFunctionProvider;
use Fantestic\CestManager\Tests\PhpMock\FunctionProvider\RealpathFunctionProvider;
use phpmock\functions\FunctionProvider;
use phpmock\MockBuilder;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Test-case with helpers for running tests inside a Virtual-File-System.
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
abstract class VfsTestCase extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    private static $initialized = false;
    protected vfsStreamDirectory $root;

    const NON_EXISTING_FILE = '/ANonExistingFile.php';
    const SUBDIR_PATH = 'SubDir';

    const NESTEDFILE_NAME = 'Example2Cest.php';
    const NESTEDFILE_PATH = 'SubDir/Example2Cest.php';
    const NESTEDFILE_CLASSNAME = 'Example2Cest';
    const NESTEDFILE_NAMESPACE = 'Example2Cest';

    const ROOTFILE_PATH = 'ExampleCest.php';
    const ROOTFILE_CLASSNAME = 'ExampleCest';
    const ROOTFILE_NAMESPACE = 'Fantestic\CestManager\Tests\Cest';


    public function setUp() :void
    {
        if (false === self::$initialized) {
            $this->buildFilesystemMock('Fantestic\CestManager', 'realpath', new RealpathFunctionProvider());
            $this->buildFilesystemMock('Fantestic\CestManager', 'file_put_contents', new FilePutContentsFunctionProvider());
            self::$initialized = true;
        }
        $this->root = vfsStream::setup('root', null, $this->sampleStructure());
    }


    protected function sampleStructure() :array
    {
        return [
            self::ROOTFILE_PATH => file_get_contents(__DIR__ . '/Cest/' . self::ROOTFILE_PATH),
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


    protected function getFinder() :Finder
    {
        return new Finder($this->root->url());
    }


    protected function getRootfileCollection() :Collection
    {
        $reader = new ReflectionCestReader();
        $scenarioNames = $reader->getScenarioNames(self::ROOTFILE_NAMESPACE.'\\'.self::ROOTFILE_CLASSNAME);
        $scenarios = [];
        foreach ($scenarioNames as $name) {
            $scenarios[] = new Scenario($name, []);
        }
        
        return new Collection(
            self::ROOTFILE_NAMESPACE,
            self::ROOTFILE_CLASSNAME,
            $scenarios,
            self::ROOTFILE_PATH
        );
    }
}
