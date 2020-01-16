<?php

namespace Tailgate\Common\Event;

use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Common\Event\Event;

class EventPublisher implements EventPublisherInterface
{
    /**
     * contains all globally registered event handlers
     * [event => [event handlers]]
     * @var array
     */
    private static $events = [];

    /**
     * the event service instance itself
     * @var null
     */
    private static $instance = null;

    /**
     * instantiates and returns the event service as a singleton
     * @return object the event service
     */
    public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    private function __construct()
    {
    }

    public function __clone()
    {
        throw new \Exception('Clone is not supported');
    }

    /**
     * attaches an event handler to an event
     *
     * 4 types of handlers can be attached
     * a global function Ex: 'trim'
     * a callable object and method Ex: [$object, 'methodName']
     * a closure Ex: function($event){}
     * a file path with a global function Ex: ['filePath', 'functionName']
     *
     * @param  string  $name    name of the event
     * @param  mixed  $handler [description]
     * @param  mixed  $data    the data to be passed to the event handler when the event is published
     * @param  boolean $append  set append to false if you want to add the handler to the beginning of the handler list
     */
    public static function on($name, $handler, $data = null, $append = true)
    {
        if (!is_string($handler) && !is_array($handler) && !$handler instanceof \Closure) {
            throw new \Exception("Only strings, arrays, closures are accepted.");
        }

        // if passing a string as the handler, the function must exist
        if (is_string($handler) && !function_exists($handler)) {
            throw new \Exception("The global function {$handler} does not exist.");
        }

        // if passing an array that is not callable, the file and function must exist
        if (is_array($handler) && !is_callable($handler)) {
            if (is_object($handler[0])) {
                throw new \Exception("The object is not callable.");
            }

            $filePath = $handler[0];
            $functionName = $handler[1];

            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception("The file {$filePath} does not exist.");
            }

            require_once $filePath;

            if (!function_exists($functionName)) {
                throw new \Exception("The function {$functionName} in file {$filePath} does not exist.");
            } else {
                // at this point we have already required the file so just set the function name as handler
                $handler = $functionName;
            }
        }

        // add the event handler to the end of array of handlers for the event
        if ($append || empty(self::$events[$name])) {
            self::$events[$name][] = [$handler, $data];
        // add the handler to the beginning of array of handlers for the event
        } else {
            array_unshift(self::$events[$name], [$handler, $data]);
        }
    }

    /**
     * detach an event handler from an event
     *
     * @param  string $name    name of the event
     * @param  mixed $handler the handler to be removed
     * @return bool whether or not the handler is found and removed
     */
    public function off($name, $handler = null)
    {
        // return if no handlers for event
        if (empty(self::$events[$name])) {
            return false;
        }

        // detach all handlers if handler is not specified
        if ($handler === null) {
            unset(self::$events[$name]);
            return true;
        }

        $removed = false;

        if (isset(self::$events[$name])) {
            foreach (self::$events[$name] as $key => $event) {
                if ($event[0] === $handler) {
                    unset(self::$events[$name][$key]);
                    $removed = true;
                }
            }

            // if the event handler was removed then reindex the keys
            if ($removed) {
                self::$events[$name] = array_values(self::$events[$name]);
                return $removed;
            }
        }

        return $removed;
    }

    /**
     * The subscriber object adds events using the on method
     * @param  object $subscriber [description]
     */
    public function subscribe(EventSubscriberInterface $subscriber)
    {
        $subscriber->subscribe($this);
    }

    /**
     * get all event handlers
     * @return array all events
     */
    public function getEventHandlers()
    {
        return self::$events;
    }

    /**
     * remove all events
     */
    public static function offAll()
    {
        self::$events = [];
    }

    /**
     * determine if an event has any handlers
     * @param  string  $name name of the event
     * @return boolean       [description]
     */
    public function hasEventHandlers($name)
    {
        return !empty(self::$events[$name]);
    }

    /**
     * activate all event handlers for an event
     * @param  string $name name of the event
     * @param  mixed $data the data to be passed to the event handler when the event is published.
     * passing data to the publish method will overwrite the data passed during the on() method for all handlers
     */
    public static function publish($name, $data = null)
    {
        $eventHandlers = [];

        // get event handlers
        if (!empty(self::$events[$name])) {
            $eventHandlers = array_merge(self::$events[$name], $eventHandlers);
        }

        // get wildcard handlers
        if (!empty(self::$events['*'])) {
            $eventHandlers = array_merge(self::$events['*'], $eventHandlers);
        }
        
        if (empty($eventHandlers)) {
            return;
        }

        $event = new Event;
        $event->name = $name;

        if (!empty($eventHandlers)) {
            foreach ($eventHandlers as $handler) {
                $data = $data ?? $handler[1];
                $event->data = $data;
                call_user_func($handler[0], $event);
            }
        }
    }
}
