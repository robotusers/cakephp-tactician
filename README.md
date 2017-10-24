# CakePHP Tactician

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/robotusers/cakephp-tactician.svg?branch=master)](https://travis-ci.org/robotusers/cakephp-tactician)
[![codecov](https://codecov.io/gh/robotusers/cakephp-tactician/branch/master/graph/badge.svg)](https://codecov.io/gh/robotusers/cakephp-tactician)

CakePHP plugin for `league/tactician`.

**NOTE: The plugin is under development.**

## Installation

```
composer require robotusers/cakephp-tactician:dev-master
bin/cake plugin load Robotusers/Tactician
```

## Using the plugin

### CakePHP integration

This plugin provides Controller and Model integration through [Commander](https://github.com/robotusers/commander) library.

Commander is a command bus abstraction library for PHP which enables you to decouple your code from a concrete command bus implementation.

#### Using the Commander (PHP 7.1+)

Install `robotusers/commander`:

```
composer require robotusers/commander
```

Set up your controllers:

```php
use Cake\Controller\Controller;
use Robotusers\Commander\CommandBusAwareInterface;
use Robotusers\Commander\CommandBusAwareTrait;

class OrdersController extends Controller implements CommandBusAwareInterface
{
   use CommandBusAwareTrait;

   public function makeOrder()
   {
        // ...
        $command = new MakeOrderCommand($data);
        $this->handleCommand($command);
        // ...
   }

}
```

For more information, read [the docs](https://github.com/robotusers/commander/blob/master/README.md).

Next you should configure the command bus which will be injected into your controllers and models that implement the `CommandBusAwareInterface`.

#### Application hook (CakePHP 3.3+)

If your application supports middleware you can configure the command bus using an application hook.

```php
use Cake\Http\BaseApplication;
use League\Tactician\CommandBus;
use Robotusers\Tactician\Core\BusApplicationInterface;
use Robotusers\Tactician\Core\BusMiddleware;

class Application extends BaseApplication implements BusApplicationInterface
{
    public function commandBus()
    {
        $bus = new CommandBus([
            // your middleware
        ]);

        return $bus;
    }

    public function middleware($middleware)
    {
        // ...
        $middleware->add(new BusMiddleware($this));
        // ...

        return $middleware;
    }
}
```

You can use helper factory methods for building `CommandBus` or CakePHP convention enabled `CommandHandlerMiddleware`:

```php
use Robotusers\Tactician\Bus\Factory;

public function commandBus()
{
    return Factory::createCommandBus([
        // your middleware
        Factory::createCommandHandlerMiddleware();
    ]);
}
```

The command bus configured here will be injected into controllers and models in `Model.initialize` and `Controller.initialize` event listener.

#### Bootstrap

If you're still on pre 3.3 stack you can set up the listener in your `bootstrap.php` file.

You can use build in *quick start* class:

```php
// bootstrap.php

use Robotusers\Tactician\Event\QuickStart;

QuickStart::setUp($commandBus);
```

`QuickStart` can load simple CakePHP convention enabled bus if it hasn't been provided:

```php
// bootstrap.php

use Robotusers\Tactician\Event\QuickStart;

QuickStart::setUp();
```

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

You can change default namespace and suffix using configuration options:

```php
$locator = new ConventionsLocator([
    'commandNamespace' => 'Bus\\Command',
    'commandSuffix' => '',
    'handlerNamespace' => 'Bus\\Handler',
    'handlerSuffix' => '',
]);
```

In this example `App\Bus\Command\MakeOrder` command will map to `App\Bus\Handler\MakeOrder` handler. Note a different namespace and no suffix.


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
