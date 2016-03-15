<?php

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectAccess()
    {
        $repo = $this->_getRepo();

        $this->assertEquals('testing', $repo->get('env'));
        $this->assertEquals('root', $repo->get('database.user'));
        $this->assertTrue($repo->has('env'));

        $repo->set('database.host', 'localhost');
        $this->assertTrue($repo->has('database.host'));
        $this->assertEquals('localhost', $repo->get('database.host'));

        $repo->set(['database.timeout.read' => 10, 'database.timeout.write' => 5]);
        $this->assertTrue($repo->has('database.timeout.read'));

        $this->assertEquals([
            'database' => [
                'user' => 'root',
                'password' => 'root',
                'host' => 'localhost',
                'timeout' => [
                    'read' => 10,
                    'write' => 5
                ]
            ],
            'env' => 'testing'
        ], $repo->toArray());
    }

    public function testArrayAccess()
    {
        $repo = $this->_getRepo();

        $this->assertEquals('testing', $repo['env']);
        $this->assertEquals('root', $repo['database.user']);
        $this->assertTrue(isset($repo['database.password']));

        $repo['database.host'] = 'localhost';
        $this->assertTrue(isset($repo['database.host']));
        $this->assertEquals('localhost', $repo['database.host']);

        unset($repo['database.host']);
        $this->assertFalse(isset($repo['database.host']));
    }

    /**
     * Returns repo instance
     *
     * @return \Venta\Configuration\Repository
     */
    protected function _getRepo()
    {
        return new \Venta\Configuration\Repository([
            'database' => [
                'user' => 'root',
                'password' => 'root'
            ],
            'env' => 'testing'
        ]);
    }
}