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
namespace Robotusers\Tactician\Test\TestCase\Locator;

use Cake\TestSuite\TestCase;
use Robotusers\Tactician\Locator\ConventionsLocator;

/**
 * Description of ConventionsLocatorTest
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class ConventionsLocatorTest extends TestCase
{

    /**
     * @dataProvider commandNameProvider
     */
    public function testResolveHandlerName($commandName, $handlerName, $config)
    {
        $locator = new ConventionsLocator($config);

        $name = $locator->resolveHandlerName($commandName);
        $this->assertEquals($handlerName, $name);
    }

    /**
     * @dataProvider commandHandlerProvider
     */
    public function testGetHandlerForCommand($commandName, $handlerName, $config)
    {
        $locator = new ConventionsLocator($config);

        $name = $locator->getHandlerForCommand($commandName);
        $this->assertInstanceOf($handlerName, $name);
    }

    /**
     * @expectedException League\Tactician\Exception\MissingHandlerException
     */
    public function testGetHandlerForCommandMissing()
    {
        $locator = new ConventionsLocator();

        $locator->getHandlerForCommand('App\\Model\\Command\\BarCommand');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetHandlerForCommandInvalid()
    {
        $locator = new ConventionsLocator();

        $locator->getHandlerForCommand('Foo');
    }

    public function commandNameProvider()
    {
        $config = [
            'commandNamespace' => 'Command',
            'commandSuffix' => 'BusCommand',
            'handlerNamespace' => 'Handler',
            'handlerSuffix' => 'BusHandler',
        ];

        return [
            ['App\\Model\\Command\\FooCommand', 'Foo', []],
            ['App\\Model\\Command\\Foo', 'Foo', []],
            ['App\\Model\\Command\\FooCommand', 'Foo', []],
            ['App\\Model\\Command\\Foo', 'Foo', []],
            ['My\\Plugin\\Model\\Command\\BarCommand', 'My/Plugin.Bar', []],
            ['App\\Command\\FooBusCommand', 'Foo', $config],
            ['App\\Command\\Foo', 'Foo', $config],
            ['App\\Command\\FooBusCommand', 'Foo', $config],
            ['App\\Command\\Foo', 'Foo', $config],
            ['My\\Plugin\\Command\\BarBusCommand', 'My/Plugin.Bar', $config]
        ];
    }

    public function commandHandlerProvider()
    {
        return [
            ['App\\Model\\Command\\FooCommand', 'App\\Model\\Handler\\FooHandler', []],
            ['App\\Command\\FooBusCommand', 'App\\Handler\\FooBusHandler', [
                'commandNamespace' => 'Command',
                'commandSuffix' => 'BusCommand',
                'handlerNamespace' => 'Handler',
                'handlerSuffix' => 'BusHandler',
            ]],
        ];
    }
}
