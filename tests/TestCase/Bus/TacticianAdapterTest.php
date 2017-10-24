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

namespace Robotusers\Tactician\Test\TestCase\Bus;

use App\Model\Command\FooCommand;
use Cake\TestSuite\TestCase;
use League\Tactician\CommandBus;
use Robotusers\Tactician\Bus\TacticianAdapter;

/**
 * Description of TacticianAdapter
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class TacticianAdapterTest extends TestCase
{
    public function testHandle()
    {
        $commandBus = $this->createMock(CommandBus::class);
        $commandBus->expects($this->once())
            ->method('handle')
            ->with($this->logicalAnd(
                $this->isInstanceOf(FooCommand::class),
                $this->callback(function ($command) {
                    return $command->arg1 === 'Bar';
                }),
                $this->callback(function ($command) {
                    return $command->arg2 === 'Baz';
                })
            ));

        $adapter = new TacticianAdapter($commandBus);

        $adapter->handle('Foo', 'Bar', 'Baz');
    }
}
