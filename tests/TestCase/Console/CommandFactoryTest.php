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
namespace Robotusers\Tactician\Test\TestCase\Console;

use App\Console\TestCommand;
use Cake\Console\CommandFactoryInterface;
use Robotusers\Commander\CommandBusInterface;
use Robotusers\Tactician\Console\CommandFactory;
use Robotusers\Tactician\Core\BusApplicationInterface;
use Robotusers\Tactician\Test\TestCase\Php71TestCase;

/**
 * @author Robert Pustułka <robert.pustulka@gmail.com>
 */
class CommandFactoryTest extends Php71TestCase
{

    public function testGetCommandBus()
    {
        $commandFactory = $this->createMock(CommandFactoryInterface::class);
        $app = $this->createMock(BusApplicationInterface::class);

        $app->method('commandBus')
            ->willReturnCallback(function () {
                return $this->createMock(CommandBusInterface::class);
            });
        $bus1 = $app->commandBus();
        $bus2 = $app->commandBus();
        $this->assertNotSame($bus2, $bus1);

        $factory = new CommandFactory($commandFactory, $app);
        $bus1 = $factory->getCommandBus();
        $bus2 = $factory->getCommandBus();
        $this->assertSame($bus2, $bus1);
    }

    public function testCreate()
    {
        $commandFactory = $this->createMock(CommandFactoryInterface::class);
        $app = $this->createMock(BusApplicationInterface::class);
        $commandBus = $this->createMock(CommandBusInterface::class);
        $command = new TestCommand;

        $app->method('commandBus')
            ->willReturn($commandBus);

        $commandFactory
            ->expects($this->once())
            ->method('create')
            ->with(TestCommand::class)
            ->willReturn($command);

        $factory = new CommandFactory($commandFactory, $app);

        $result = $factory->create(TestCommand::class);
        $this->assertInstanceOf(TestCommand::class, $result);
        $this->assertSame($commandBus, $result->getCommandBus());
    }
}
