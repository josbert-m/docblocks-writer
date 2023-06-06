# DocBlocks Writer

A simple package to write PHPDocs blocks for some PHP class.

It supports PHP ^8 and PHP ^7.4.

## Installation

Require this package with composer using the following command:

```shell
composer require --dev josbert-m/docblocks-writer
```

## How to use?

Create an instance of the writer.

```php
<?php

$writer = new Writer(MyClass::class);
```

Now add a summary, a description or tags for your class, then proceed to write.

```php
$writer->setSummary('My summary class.');
$writer->setDescription('My description class.');

$writer->addTag('property', 'mixed $any');
$writer->addTag('method', 'array $array');

$writer->write();
```
