<?php
/*
 * The MIT License
 *
 * Copyright 2018 Robert Pustułka <robert.pustulka@gmail.com>.
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
namespace Robotusers\Tactician\Console;

use Cake\Console\CommandFactoryInterface;
use Robotusers\Commander\CommandBusAwareInterface;
use Robotusers\Tactician\Core\BusApplicationInterface;

/**
 * CommandFactory class.
 *
 * @author Robert Pustułka <robert.pustulka@gmail.com>
 */
class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var BusApplicationInterface
     */
    protected $app;

    /**
     * @var CommandFactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param CommandFactoryInterface $factory Command Factory.
     * @param BusApplicationInterface $app Application with commandBus hook.
     */
    public function __construct(CommandFactoryInterface $factory, BusApplicationInterface $app)
    {
        $this->factory = $factory;
        $this->app = $app;
    }

    /**
     * Command bus getter.
     *
     * @return CommandBusInterface
     */
    public function getCommandBus()
    {
        if ($this->commandBus === null) {
            $this->commandBus = $this->app->commandBus();
        }

        return $this->commandBus;
    }

    /**
     * Injects a command bus into a compatible console command/shell.
     *
     * {@inheritDoc}
     */
    public function create($className)
    {
        $command = $this->factory->create($className);
        if ($command instanceof CommandBusAwareInterface) {
            $commandBus = $this->getCommandBus();
            $command->setCommandBus($commandBus);
        }

        return $command;
    }
}