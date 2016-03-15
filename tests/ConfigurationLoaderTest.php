<?php

class ConfigurationLoaderTest extends \PHPUnit_Framework_TestCase
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
            'config.php' => "<?php return [
                'database' => [
                    'password' => 'root'
                ],
                'env' => 'testing'
            ];"
        ]);
    }

    public function testBasicFlow()
    {
        $manager = new \Venta\Configuration\Loader;

        $manager->addReader(new \Venta\Configuration\Readers\ArrayReader);

        $this->assertEquals([
            'database' => [
                'user' => 'root',
                'password' => 'root'
            ]
        ], $manager->toArray([
            'array' => [
                'database.user' => 'root',
                'database.password' => 'root'
            ]
        ]));
    }

    public function testPriorityReaders()
    {
        $manager = new \Venta\Configuration\Loader;

        $mock = $this->getMock(\Venta\Configuration\Readers\ArrayReader::class, ['getName']);
        $mock->method('getName')->willReturn('stub');

        $manager->addReader(new \Venta\Configuration\Readers\FileReader, 10);
        $manager->addReader(new \Venta\Configuration\Readers\ArrayReader, 0);
        $manager->addReader($mock, 10);

        $this->assertEquals([
            'database' => [
                'user' => 'root',
                'password' => 'root'
            ],
            'env' => 'testing'
        ], $manager->toArray([
            'array' => [
                'database.user' => 'root',
                'database.password' => 'password',
                'env' => 'local'
            ],
            'php-files' => [
                $this->_disk->getChild('config.php')->url()
            ]
        ]));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You must set name for configuration loader class TestConfigurationReader
     */
    public function testExceptionOnNotConfiguredLoaderName()
    {
        $loader = new \Venta\Configuration\Loader;
        $loader->addReader(new \TestConfigurationReader);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name "array" is already in use for configuration loaders
     */
    public function testExceptionOnDuplicateName()
    {
        $manager = new \Venta\Configuration\Loader;

        $manager->addReader(new \Venta\Configuration\Readers\ArrayReader);
        $manager->addReader(new \Venta\Configuration\Readers\ArrayReader);
    }
}

/**
 * Class TestConfigurationReader
 */
class TestConfigurationReader extends \Venta\Configuration\Readers\AbstractConfigurationReader
{
    public function read(array $data = [])
    {
        return [];
    }
}