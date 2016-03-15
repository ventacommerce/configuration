<?php

namespace Venta\Configuration;

use Venta\Contracts\Configuration\ConfigurationLoaderContract;
use Venta\Contracts\Configuration\ConfigurationReaderContract;

/**
 * Class Loader
 *
 * @package Venta\Configuration
 */
class Loader implements ConfigurationLoaderContract
{
    /**
     * Array of readers
     *
     * @var array
     */
    protected $_readers = [];

    /**
     * Local loaded data cache
     *
     * @var array
     */
    protected $_loadedData;

    /**
     * {@inheritdoc}
     */
    public function addReader(ConfigurationReaderContract $reader, $priority = 0)
    {
        $this->_checkName($reader->getName());

        $this->_readers[$reader->getName()] = [
            'name' => $reader->getName(),
            'reader' => $reader,
            'priority' => is_int($priority) && $priority >= 0 ? $priority : 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function performReading(array $data = [])
    {
        if ($this->_loadedData === null) {
            $this->_loadedData = [];
            usort($this->_readers, [$this, '_sortByPriority']);

            foreach ($this->_readers as $readerInfo) {
                /** @var \Venta\Contracts\Configuration\ConfigurationReaderContract $reader */
                $reader = $readerInfo['reader'];
                $localData = array_key_exists($readerInfo['name'], $data) ? $data[$readerInfo['name']] : [];

                $this->_loadedData = array_replace_recursive(
                    $this->_loadedData,
                    $reader->toArray($localData)
                );
            }
        }

        return $this->_loadedData;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $data = [])
    {
        return $this->performReading($data);
    }

    /**
     * Performs sorting based on priority for loaders array.
     * Used as callback to usort() function
     *
     * @param array $first
     * @param array $second
     * @return int
     */
    protected function _sortByPriority(array $first, array $second)
    {
        if ($first['priority'] == $second['priority']) {
            return 0;
        }

        return $first['priority'] < $second['priority'] ? -1 : 1;
    }

    /**
     * Checks, if name can be used
     *
     * @param  string $name
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function _checkName($name)
    {
        if (array_key_exists($name, $this->_readers)) {
            throw new \InvalidArgumentException(
                sprintf('Name "%s" is already in use for configuration loaders', $name)
            );
        }
    }
}