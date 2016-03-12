# Venta eCommerce framework Configuration package

## Usage example
```php
$reader = new \Venta\Configuration\Reader;
$reader->addReader('array', \Venta\Configuration\Reader\Array::class);
$reader->addReader('php-files', \Venta\Configuration\Reader\PhpFiles::class);

$configuration = new \Venta\Configuration\Repository($reader->toArray());

$configuration->set('item', 123);
$configuration->set(['item.one' => 1, 'three' => 3]);
$configuration->get('item', 'default');
$configuration->has('item');
$configuration->all(); // ['item' => ['one' => 1], 'three' => 3];

$writer = new \Venta\Configuration\Writer;
$writer->addWriter(\Venta\Configuration\Writer\PhpFiles::class);
$writer->performWriting($configuration);
```

## Generic flow

1. Configuration read manager instance is created.
2. Several sources (readers) are added to read manager in order to be used for condifguration reading.
3. Configuration repository is created. Items, loaded by configuration read manager, are passed to it as configuration itself.
4. Repository is used during application run time in order to get and set configuration values
5. On application terminating, configuration writer is created in order to save configuration to permanent location and/or cache
6. Writing handlers are added to writer
7. Writer is performing write itself on repository passed in.
