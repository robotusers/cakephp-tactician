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
        $split = explode('\\Command\\', $commandName);
        if (count($split) < 2) {
            throw new InvalidArgumentException('Invalid command name.');
        }
        list($plugin, $alias) = $split;
        $plugin = $this->_fixNamespace($plugin);
        $name = $this->_fixNamespace($alias);

        if ($plugin !== '' && $plugin !== Configure::read('App.namespace')) {
            $name = $plugin . '.' . $name;
        }

        return preg_replace('/Command$/', '', $name);
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
    protected function _create($class, $alias, $config)
    {
        return new $class;
    }

    /**
     * {@inheritDoc}
     */
    protected function _resolveClassName($class)
    {
        return App::className($class, 'Handler', 'Handler');
    }

    /**
     * {@inheritDoc}
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        if ($plugin) {
            $class = "$plugin.$class";
        }

        throw MissingHandlerException::forCommand($class);
    }
}
