<?php

/**
 * Class ArraySourceTest
 */
class ArraySourceTest extends \PHPUnit_Framework_TestCase
{
    public function testMainFunctions()
    {
        $reader = new \Venta\Configuration\Sources\ArraySource;

        $this->assertEquals([
            'database' => [
                'host' => 'localhost',
                'user' => 'root',
                'password' => 'root'
            ],
            'env' => 'testing',
            'one' => [
                'two' => ['three' => 123]
            ]
        ], $reader->toArray([
            'database.host' => 'localhost',
            'database.user' => 'root',
            'database.password' => 'root',
            'env' => 'testing',
            'one.two.three' => 123
        ]));
    }
}