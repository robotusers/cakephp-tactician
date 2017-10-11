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

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use Robotusers\Tactician\Locator\ConventionsLocator;

/**
 * Description of Factory
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class Factory
{

    /**
     * Creates a command bus.
     *
     * @param array|null $middleware List of middleware.
     * @return CommandBus
     */
    public static function createCommandBus(array $middleware = null)
    {
        if ($middleware === null) {
            $middleware = [
                static::createCommandHandlerMiddleware()
            ];
        }

        return new CommandBus($middleware);
    }

    /**
     * Creates default CommandHandlerMiddleware instance.
     *
     * @return CommandHandlerMiddleware
     */
    public static function createCommandHandlerMiddleware()
    {
        $locator = new ConventionsLocator();
        $extractor = new ClassNameExtractor();
        $inflector = new HandleClassNameInflector();

        return new CommandHandlerMiddleware($extractor, $locator, $inflector);
    }
}
