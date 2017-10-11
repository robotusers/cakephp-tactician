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
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;
use Robotusers\Commander\CommandBusAwareInterface;
use Robotusers\Commander\CommandBusInterface;
use Robotusers\Tactician\Event\QuickStart;
use Robotusers\Tactician\Event\TacticianListener;

/**
 * Description of QuickStartTest
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class QuickStartTest extends TestCase
{
    public function setUp()
    {
        $this->skipIf(version_compare(PHP_VERSION, '7.1') < 0);

        parent::setUp();
    }

    public function testSetUp()
    {
        QuickStart::setUp();

        $events = ['Model.initialize', 'Controller.initialize'];

        foreach ($events as $event) {
            $listeners = EventManager::instance()->listeners($event);
            $this->assertNotEmpty($listeners);

            $listener = $listeners[0]['callable'];
            $this->assertInstanceOf(TacticianListener::class, $listener[0]);
            $this->assertEquals('injectCommandBus', $listener[1]);
        }
    }

    public function testSetUpCustom()
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        QuickStart::setUp($commandBus, ['events' => 'Foo.bar']);

        $listeners = EventManager::instance()->listeners('Foo.bar');
        $this->assertNotEmpty($listeners);
        $listener = $listeners[0]['callable'];

        $subject = $this->createMock(CommandBusAwareInterface::class);
        $subject->expects($this->once())
            ->method('setCommandBus')
            ->with($commandBus);

        $event = new Event('Foo.bar', $subject);

        $listener($event);
    }
}
