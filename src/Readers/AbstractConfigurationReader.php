<?php

namespace Venta\Configuration\Readers;

use Venta\Contracts\Configuration\ConfigurationReaderContract;

/**
 * Class AbstractConfigurationReader
 *
 * @package Venta\Configuration
 */
abstract class AbstractConfigurationReader implements ConfigurationReaderContract
{
    /**
     * Reader name holder
     *
     * @var null|string
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        if (!is_string($this->name)) {
            throw new \LogicException(
                sprintf('You must set name for configuration loader class %s', static::class)
            );
        }

        return $this->name;
    }

    /**
     * Default implementation of this function
     *
     * {@inheritdoc}
     */
    public function toArray(array $data = [])
    {
        return $this->read($data);
    }
}