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

namespace Robotusers\Tactician\Test\TestCase\Event;

use Cake\Event\Event;
use InvalidArgumentException;
use League\Tactician\CommandBus;
use Robotusers\Commander\CommandBusAwareInterface;
use Robotusers\Commander\CommandBusInterface;
use Robotusers\Tactician\Event\BusListener;
use Robotusers\Tactician\Test\TestCase\Php71TestCase;
use stdClass;

/**
 * Description of TacticianListenerTest
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class BusListenerTest extends Php71TestCase
{

    public function testCommandBusInterface()
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $listener = new BusListener($commandBus);

        $this->assertSame($commandBus, $listener->getCommandBus());
    }

    public function testCommandBusTactician()
    {
        $commandBus = $this->createMock(CommandBus::class);
        $listener = new BusListener($commandBus);

        $this->assertInstanceOf(CommandBusInterface::class, $listener->getCommandBus());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid command bus encountered. Expected `Robotusers\Commander\CommandBusInterface` or `League\Tactician\CommandBus`, got `stdClass`.
     */
    public function testCommandBusInvalid()
    {
        $commandBus = new stdClass();
        new BusListener($commandBus);
    }

    public function testImplementedEvents()
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $listener = new BusListener($commandBus);

        $events = $listener->implementedEvents();
        $this->assertEquals([
            'Controller.initialize' => 'injectCommandBus',
            'Model.initialize' => 'injectCommandBus'
        ], $events);
    }

    public function testImplementedEventsCustom()
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $listener = new BusListener($commandBus, [
            'events' => 'Foo.bar'
        ]);

        $events = $listener->implementedEvents();
        $this->assertEquals([
            'Foo.bar' => 'injectCommandBus'
        ], $events);
    }
    public function testInjectCommandBus()
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $listener = new BusListener($commandBus);

        $subject = $this->createMock(CommandBusAwareInterface::class);
        $subject = $this->createMock(CommandBusAwareInterface::class);
        $subject->expects($this->once())
            ->method('setCommandBus')
            ->with($commandBus);

        $event = new Event('Foo.bar', $subject);
        $listener->injectCommandBus($event);
    }
}
