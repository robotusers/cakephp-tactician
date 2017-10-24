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

namespace Robotusers\Tactician\Bus;

use Cake\Core\App;
use Cake\Core\InstanceConfigTrait;
use League\Tactician\CommandBus;
use Robotusers\Commander\Adapter\TacticianAdapter as BaseAdapter;

/**
 * Description of TacticianAdapter
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class TacticianAdapter extends BaseAdapter
{

    use InstanceConfigTrait;

    /**
     * Config.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'commandNamespace' => 'Model\\Command',
        'commandSuffix' => 'Command'
    ];

    /**
     * Constructor.
     *
     * @param CommandBus $commandBus Tactician command bus.
     * @param array $config Config.
     */
    public function __construct(CommandBus $commandBus, array $config = [])
    {
        parent::__construct($commandBus);

        $this->config($config);
    }

    /**
     * Handles the command.
     *
     * @param string|object $command Command instance or name.
     * @param mixed $args Additional arguments for command.
     * @return mixed
     */
    public function handle($command, ...$args)
    {
        if (is_string($command)) {
            $class = $this->resolveClassName($command);
            $command = new $class(...$args);
        }

        return parent::handle($command);
    }

    /**
     * Resolves command class name.
     *
     * @param string $name Name,
     * @return string
     */
    protected function resolveClassName($name)
    {
        return App::className($name, $this->_config['commandNamespace'], $this->_config['commandSuffix']);
    }
}
