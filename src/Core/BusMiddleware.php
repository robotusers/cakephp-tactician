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

namespace Robotusers\Tactician\Core;

use Cake\Event\EventManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Robotusers\Tactician\Event\BusListener;

/**
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class BusMiddleware
{
    /**
     * @var BusApplicationInterface
     */
    protected $application;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param BusApplicationInterface $application Application instance.
     * @param EventManager $eventManager Event manager.
     * @param array $config Listener config.
     */
    public function __construct(BusApplicationInterface $application, EventManager $eventManager = null, array $config = [])
    {
        $this->application = $application;
        $this->eventManager = $eventManager ?: EventManager::instance();
        $this->config = $config;
    }

    /**
     * Invokes a middleware.
     *
     * @param ServerRequestInterface $request Request.
     * @param ResponseInterface $response Response.
     * @param callable $next Next callback.
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $commandBus = $this->application->commandBus();
        $listener = new BusListener($commandBus, $this->config);

        $this->eventManager->on($listener);

        return $next($request, $response);
    }
}
