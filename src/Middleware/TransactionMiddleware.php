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

namespace Robotusers\Tactician\Middleware;

use Cake\Core\App;
use Cake\Datasource\ConnectionInterface;
use League\Tactician\Middleware;

/**
 * CakePHP transactional support.
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class TransactionMiddleware implements Middleware
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var string[]
     */
    protected $commands = [];

    /**
     * @var string[]
     */
    protected $excluded = [];

    /**
     * @var bool
     */
    protected $all = false;

    /**
     * Consrtuctor.
     *
     * @param ConnectionInterface $connection Connection instance.
     * @param string[]|bool $commands A list of suported command class names or `true` to include all commands.
     * @param string[] $excluded A list of excluded command class names.
     */
    public function __construct(ConnectionInterface $connection, $commands = [], array $excluded = [])
    {
        $this->connection = $connection;
        if ($commands === true) {
            $this->addAll();
        } else {
            foreach ((array)$commands as $name) {
                $this->addCommand($name);
            }
        }
        foreach ($excluded as $name) {
            $this->excludeCommand($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function execute($command, callable $next)
    {
        if (!$this->needsTransaction($command)) {
            return $next($command);
        }

        return $this->connection->transactional(function () use ($next, $command) {
            return $next($command);
        });
    }

    /**
     *
     * @return $this
     */
    public function addAll()
    {
        $this->all = true;

        return $this;
    }

    /**
     * Add a supported command class name which should be wrapped in a transaction.
     *
     * @param string $name Command class name.
     * @return $this
     */
    public function addCommand($name)
    {
        $class = $this->resolveClassName($name);
        $this->commands[] = ltrim($class, '\\');

        return $this;
    }

    /**
     * Exclude a command class name which should not be wrapped in a transaction.
     *
     * @param string $name Command class name.
     * @return $this
     */
    public function excludeCommand($name)
    {
        $class = $this->resolveClassName($name);
        $this->excluded[] = ltrim($class, '\\');

        return $this;
    }

    /**
     * Resolves command class name.
     *
     * @param string $name Command class name.
     * @return string
     */
    protected function resolveClassName($name)
    {
        return (string)App::className($name, 'Command', 'Command');
    }

    /**
     * Returns true if a command needs to be wrapped in a transaction.
     *
     * @param string $command Command class name.
     * @return bool
     */
    public function needsTransaction($command)
    {
        $class = ltrim(get_class($command), '\\');

        $include = $this->all || in_array($class, $this->commands);
        $excluded = in_array($class, $this->excluded);

        return $include && !$excluded;
    }
}
