<?php

/**
 * Class FileReaderTest
 */
class FileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Disk mock instance holder
     *
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $_disk;

    public function setUp()
    {
        $this->_disk = \org\bovigo\vfs\vfsStream::setup('root', null, [
            'config.ini' => 'database=localhost' . PHP_EOL . 'user=KZ',
            'broken.php' => "<?php return 'three';",
            'config.php' => "<?php return [
                'database' => 'localhost',
                'user' => [
                    'name' => 'K',
                    'lastname' => 'Z'
                ]
            ];"
        ]);
    }

    public function testBasicFlow()
    {
        $reader = new \Venta\Configuration\Sources\PhpFileSource;

        $this->assertEquals([
            'database' => 'localhost',
            'user' => [
                'name' => 'K',
                'lastname' => 'Z'
            ]
        ], $reader->toArray(
            [$this->_disk->getChild('config.php')->url()]
        ));
    }

    public function testAcceptOnlyPhpFiles()
    {
        $reader = new \Venta\Configuration\Sources\PhpFileSource;

        $this->assertEquals([], $reader->toArray(
            [$this->_disk->getChild('config.ini')->url()]
        ));
    }

    public function testWontFallOnBrokenConfigFile()
    {
        $reader = new \Venta\Configuration\Sources\PhpFileSource;

        $this->assertEquals([], $reader->toArray(
            [$this->_disk->getChild('broken.php')->url()]
        ));
    }
}