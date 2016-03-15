<?php

namespace Venta\Configuration\Readers;

/**
 * Class FileReader
 *
 * @package Venta\Configuration
 */
class FileReader extends AbstractConfigurationReader
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'php-files';

    /**
     * Local loaded data cache
     *
     * @var array|null
     */
    protected $_loadedData;

    /**
     * {@inheritdoc}
     */
    public function read(array $data = [])
    {
        if ($this->_loadedData === null) {
            $this->_loadedData = [];

            foreach ($data as $file) {
                if ($this->_isValid($file)) {
                    $data = require $file;

                    $this->_loadedData = array_replace_recursive(
                        $this->_loadedData,
                        is_array($data) ? $data : []
                    );
                }
            }
        }

        return $this->_loadedData;
    }

    /**
     * Defines, if file can be loaded
     *
     * @param  string $file
     * @return bool
     */
    protected function _isValid($file)
    {
        return file_exists($file)
        && is_file($file)
        && pathinfo($file, PATHINFO_EXTENSION) === 'php';
    }
}