<?php
/*
 * The MIT License
 *
 * Copyright 2017 Robert Pustułka <r.pustulka@robotusers.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Robotusers\Tactician\Locator;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Core\ObjectRegistry;
use InvalidArgumentException;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;

/**
 * CakePHP convention-based locator.
 *
 * This will look for `App\Handler\DoSomethingHandler`
 * for `App\Handler\DoSomethingCommand` command.
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class ConventionsLocator extends ObjectRegistry implements HandlerLocator
{
    use InstanceConfigTrait;

    /**
     * Config.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'commandNamespace' => 'Model\\Command',
        'commandSuffix' => 'Command',
        'handlerNamespace' => 'Model\\Handler',
        'handlerSuffix' => 'Handler'
    ];

    /**
     * Constructor.
     *
     * @param array $config Config array.
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * {@inheritDoc}
     */
    public function getHandlerForCommand($commandName)
    {
        $name = $this->resolveHandlerName($commandName);

        return $this->load($name);
    }

    /**
     * Resolves a handler name from a command name.
     *
     * @param string $commandName Command class name.
     * @return string
     */
    public function resolveHandlerName($commandName)
    {
        $namespace = $this->_config['commandNamespace'];

        $split = explode("\\$namespace\\", $commandName);
        if (count($split) < 2) {
            throw new InvalidArgumentException('Invalid command name.');
        }
        list($plugin, $alias) = $split;
        $plugin = $this->_fixNamespace($plugin);
        $name = $this->_fixNamespace($alias);

        if ($plugin !== '' && $plugin !== Configure::read('App.namespace')) {
            $name = $plugin . '.' . $name;
        }

        $suffix = $this->_config['commandSuffix'];

        return preg_replace("/$suffix$/", '', $name);
    }

    /**
     * Converts backslashes into slashes.
     *
     * @param string $string Namespace
     * @return string
     */
    protected function _fixNamespace($string)
    {
        return str_replace('\\', '/', trim($string, '\\'));
    }

    /**
     * {@inheritDoc}
     */
    protected function _create($class, string $alias, array $config)
    {
        return new $class;
    }

    /**
     * {@inheritDoc}
     */
    protected function _resolveClassName(string $class): ?string
    {
        return App::className($class, $this->_config['handlerNamespace'], $this->_config['handlerSuffix']);
    }

    /**
     * {@inheritDoc}
     */
    protected function _throwMissingClassError(string $class, ?string $plugin): void
    {
        if ($plugin) {
            $class = "$plugin.$class";
        }

        throw MissingHandlerException::forCommand($class);
    }
}
