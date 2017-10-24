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
namespace Robotusers\Tactician\Test\TestCase\Middleware;

use App\Model\Command\FooCommand;
use Cake\Datasource\ConnectionInterface;
use Cake\TestSuite\TestCase;
use Robotusers\Tactician\Middleware\TransactionMiddleware;
use stdClass;

/**
 * Description of TransactionMiddlewareTest
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class TransactionMiddlewareTest extends TestCase
{
    /**
     * @dataProvider commandNameProvider
     */
    public function testNeedsTransaction($command, $result)
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $middleware = new TransactionMiddleware($connection, [
            FooCommand::class
        ]);

        $this->assertSame($result, $middleware->needsTransaction($command));
    }

    public function testAddCommand()
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $middleware = new TransactionMiddleware($connection);

        $command = new FooCommand;
        $this->assertFalse($middleware->needsTransaction($command));

        $middleware->addCommand(FooCommand::class);
        $this->assertTrue($middleware->needsTransaction($command));
    }

    public function testExecuteWithTransaction()
    {
        $command = new FooCommand;
        $next = function ($command) {
            $this->assertInstanceOf(FooCommand::class, $command);

            return true;
        };

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('transactional')
            ->with($this->callback(function ($callable) {
                $this->assertTrue($callable());

                return $callable;
            }))
            ->willReturn(true);

        $middleware = new TransactionMiddleware($connection, [
            FooCommand::class
        ]);

        $result = $middleware->execute($command, $next);
        $this->assertTrue($result);
    }

    public function testExecuteWithoutTransaction()
    {
        $command = new FooCommand;
        $next = function ($command) {
            $this->assertInstanceOf(FooCommand::class, $command);

            return true;
        };

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->never())
            ->method('transactional');

        $middleware = new TransactionMiddleware($connection);

        $result = $middleware->execute($command, $next);
        $this->assertTrue($result);
    }

    public function commandNameProvider()
    {
        return [
            [new FooCommand, true],
            [new stdClass, false]
        ];
    }
}
