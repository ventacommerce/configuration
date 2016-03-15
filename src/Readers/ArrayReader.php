<?php

namespace Venta\Configuration\Readers;

use Venta\Configuration\Repository;

/**
 * Class ArrayReader
 *
 * @package Venta\Configuration
 */
class ArrayReader extends AbstractConfigurationReader
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'array';

    /**
     * {@inheritdoc}
     */
    public function read(array $data = [])
    {
        return (new Repository($data))->toArray();
    }
}