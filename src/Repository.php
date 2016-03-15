<?php

namespace Venta\Configuration;

use Venta\Contracts\Configuration\RepositoryContract;

/**
 * Class Repository
 *
 * @package Venta\Configuration
 */
class Repository implements RepositoryContract
{
    /**
     * Items holder as an dot access array
     *
     * @var array
     */
    protected $_dotItems;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $items = [])
    {
        $this->_dotItems = $this->_convertToDotArray($items);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->_dotItems[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value = null)
    {
        if (is_string($key)) {
            $this->_dotItems[$key] = $value;
        }

        if (is_array($key)) {
            foreach ($key as $realKey => $realValue) {
                $this->set($realKey, $realValue);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return array_key_exists($key, $this->_dotItems);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->_convertFromDotArray($this->_dotItems);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Converts dot access array to multi-dimensional array
     *
     * @param  array $array
     * @return array
     */
    protected function _convertFromDotArray($array)
    {
        $converted = [];

        foreach ($array as $key => $value) {
            $path = explode('.', $key);
            $first = array_shift($path);

            if (count($path) === 0) {
                $converted[$first] = $value;
            } else {
                $childTree = $this->_convertFromDotArray([
                    implode('.', $path) => $value
                ]);

                if (array_key_exists($first, $converted) && is_array($converted[$first])) {
                    $converted[$first] = array_replace_recursive($converted[$first], $childTree);
                } else {
                    $converted[$first] = $childTree;
                }
            }
        }

        return $converted;
    }

    /**
     * Converts passed multi-dimensional array to dot access array
     *
     * @param  array $array
     * @param  string $prefix
     * @return array
     */
    protected function _convertToDotArray($array, $prefix = '')
    {
        $converted = [];

        foreach ($array as $key => $item) {
            $prefixedKey = $prefix !== '' ? implode('.', [$prefix, $key]) : $key;

            if (is_array($item)) {
                $converted = array_merge($converted, $this->_convertToDotArray($item, $prefixedKey));
            } else {
                $converted[$prefixedKey] = $item;
            }
        }

        return $converted;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->_dotItems[$offset]);
    }
}