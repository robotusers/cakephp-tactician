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

### Conventions locator

CakePHP Conventions locator will look for command handlers based on a convention,
that commands should reside under `App\Command\` namespace and be suffixed with `Command` string
and handlers should reside under `App\Handler\` namespace and be suffixed with `Handler` string.


```php

//CakePHP convention locator
$locator = new ConventionsLocator();
$extractor = new ClassNameExtractor();
$inflector = new HandleClassNameInflector();

$commandBus = new CommandBus(
    [
        new CommandHandlerMiddleware($extractor, $locator, $inflector)
    ]
);
```

### Transaction middleware

Transaction middleware is a wrapper for CakePHP `ConnectionInterface::transactional()`.
It does not work with all commands by default. You should include a list of supported commands.

A list supports FQCN or convention supported name (eq `Plugin.Name`).

```php

//default connection
$connection = ConnectionManager::get('default');

$commandBus = new CommandBus(
    [
        //CakePHP transaction middleware with a connection and a list of commands.
        new TransactionMiddleware($connection, [
            FooCommand::class,
            'My/Plugin.Bar',
        ]),
        $commandHandlerMiddleware
    ]
);
```
