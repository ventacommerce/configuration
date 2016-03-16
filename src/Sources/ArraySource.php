<?php

namespace Venta\Configuration\Sources;

use Venta\Configuration\Repository;

/**
 * Class ArrayReader
 *
 * @package Venta\Configuration
 */
class ArraySource extends AbstractConfigurationSource
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