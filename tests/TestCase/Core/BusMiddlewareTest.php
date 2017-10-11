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
namespace Robotusers\Tactician\Test\TestCase\Core;

use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Robotusers\Tactician\Core\BusApplicationInterface;
use Robotusers\Tactician\Core\BusMiddleware;
use Robotusers\Tactician\Event\BusListener;

/**
 * Description of BusMiddlewareTest
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class BusMiddlewareTest extends TestCase
{
    public function testInvoke()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $bus = $this->createMock(CommandBus::class);

        $manager = $this->createMock(EventManager::class);
        $manager->expects($this->once())
            ->method('on')
            ->with($this->isInstanceOf(BusListener::class));

        $called = false;
        $next = function ($request, $response) use (&$called) {
            $this->assertInstanceOf(ServerRequestInterface::class, $request);
            $this->assertInstanceOf(ResponseInterface::class, $response);
            $called = true;

            return $response;
        };

        $application = $this->createMock(BusApplicationInterface::class);
        $application->expects($this->once())
            ->method('commandBus')
            ->willReturn($bus);

        $middleware = new BusMiddleware($application, $manager);
        $result = $middleware($request, $response, $next);
        $this->assertSame($response, $result);
        $this->assertTrue($called);
    }
}
