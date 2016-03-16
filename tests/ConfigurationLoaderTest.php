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

        $manager->addSource(new \Venta\Configuration\Sources\ArraySource);

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

        $mock = $this->getMock(\Venta\Configuration\Sources\ArraySource::class, ['getName']);
        $mock->method('getName')->willReturn('stub');

        $manager->addSource(new \Venta\Configuration\Sources\PhpFileSource, 10);
        $manager->addSource(new \Venta\Configuration\Sources\ArraySource, 0);
        $manager->addSource($mock, 10);

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
        $loader->addSource(new \TestConfigurationReader);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name "array" is already in use for configuration loaders
     */
    public function testExceptionOnDuplicateName()
    {
        $manager = new \Venta\Configuration\Loader;

        $manager->addSource(new \Venta\Configuration\Sources\ArraySource);
        $manager->addSource(new \Venta\Configuration\Sources\ArraySource);
    }
}

/**
 * Class TestConfigurationReader
 */
class TestConfigurationReader extends \Venta\Configuration\Sources\AbstractConfigurationSource
{
    public function read(array $data = [])
    {
        return [];
    }
}