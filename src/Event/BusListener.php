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

namespace Robotusers\Tactician\Event;

use Cake\Core\InstanceConfigTrait;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use InvalidArgumentException;
use League\Tactician\CommandBus;
use Robotusers\Commander\CommandBusAwareInterface;
use Robotusers\Commander\CommandBusInterface;
use Robotusers\Tactician\Bus\TacticianAdapter;

/**
 * Event listener for a command bus.
 *
 * @author Robert Pustułka <r.pustulka@robotusers.com>
 */
class BusListener implements EventListenerInterface
{

    use InstanceConfigTrait;
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'events' => [
            'Controller.initialize',
            'Model.initialize'
        ],
        'adapter' => []
    ];

    /**
     * Constructor.
     *
     * You can configure events using `events` config option.
     *
     * @param CommandBusInterface|CommandBus $commandBus Command bus instance.
     * @param array $config Configuration.
     * @return void
     */
    public function __construct($commandBus, array $config = [])
    {
        $this->setConfig($config);
        $this->setCommandBus($commandBus);
    }

    /**
     * Sets command bus.
     *
     * @param CommandBusInterface|CommandBus $commandBus Command bus instance.
     * @return $this
     * @throws InvalidArgumentException When invalid argument passed.
     */
    public function setCommandBus($commandBus)
    {
        if (!$commandBus instanceof CommandBusInterface) {
            if (!$commandBus instanceof CommandBus) {
                $classes = implode('` or `', [CommandBusInterface::class, CommandBus::class]);
                $type = is_object($commandBus) ? get_class($commandBus) : gettype($commandBus);
                $message = sprintf('Invalid command bus encountered. Expected `%s`, got `%s`.', $classes, $type);

                throw new InvalidArgumentException($message);
            }

            $commandBus = new TacticianAdapter($commandBus, $this->_config['adapter']);
        }

        $this->commandBus = $commandBus;

        return $this;
    }

    /**
     * Returns a command bus instance.
     *
     * @return CommandBusInterface
     */
    public function getCommandBus()
    {
        return $this->commandBus;
    }

    /**
     * Callback.
     *
     * @param Event $event Event instance.
     * @return void
     */
    public function injectCommandBus(Event $event)
    {
        $subject = $event->getSubject();
        if ($subject instanceof CommandBusAwareInterface) {
            $subject->setCommandBus($this->commandBus);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        $events = (array)$this->_config['events'];

        return array_fill_keys($events, 'injectCommandBus');
    }
}
