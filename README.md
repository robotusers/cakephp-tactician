# CakePHP Tactician

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/robotusers/cakephp-tactician.svg?branch=master)](https://travis-ci.org/robotusers/cakephp-tactician)
[![codecov](https://codecov.io/gh/robotusers/cakephp-tactician/branch/master/graph/badge.svg)](https://codecov.io/gh/robotusers/cakephp-tactician)

CakePHP plugin for `league/tactician`.

## Installation

```
composer require robotusers/cakephp-tactician
bin/cake plugin load Robotusers/Tactician
```

## Using the plugin

```php

//CakePHP convention locator
$locator = new ConventionsLocator();
$extractor = new ClassNameExtractor();
$inflector = new HandleClassNameInflector();

//default connection
$connection = ConnectionManager::get('default');

$commandBus = new CommandBus(
    [
        //CakePHP transaction middleware
        new TransactionMiddleware($connection, [
            FooCommand::class,
            BarCommand::class,
        ]),
        new CommandHandlerMiddleware($extractor, $locator, $inflector)
    ]
);
```
