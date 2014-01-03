<?php

/**
 * PHP-Pharo .
 *
 * An open source application development framework for PHP 5.2.17 or newer .
 *
 * @package		PHP-Pharo
 * @author		Mohammed Alashaal, fb.me/alash3al
 * @copyright	Copyright (c) 2013 - 2015 .
 * @license		GPL-v3
 * @link		http://github.com/alash3al/PHPharo
 */

// ------------------------------------------------------------------------

/**
 * Pharo Event Class
 *
 * This class enables you to build event driven app(s) .
 * 
 *
 * @package		Event
 * @author		Mohammed Alashaal
 */
class Event
{
    protected static $Event = array();
    
    /**
     * Event::listen()
     * attach/add new event .
     * @param string $group
     * @param mixed $event_id
     * @param callback $callback
     * @param integer $priority
     * @param array $callback_args
     * @return void
     */
    static function listen($group, $event_id, $callback, $priority = 5, array $callback_args = array())
    {
        if(isset(self::$Event[$group][(int)$priority][$event_id]))
            die(trigger_error(__METHOD__ . " (please choose another unique id instead of '{$event_id}' it is already exists)"));
        elseif(!is_callable($callback))
            die(trigger_error(__METHOD__ . " (this callback '{$event_id}' is not valid)"));
        else
            self::$Event[$group][$priority][$event_id] = array($callback, $callback_args);  
        ksort(self::$Event[$group]);
    }
    
    /**
     * Event::dispatch()
     * despatch (run/execute) event(s) .
     * @param string $group
     * @param mixed $event_id
     * @return mixed
     */
    static function dispatch($group, $event_id = null)
    {
        if(!isset(self::$Event[$group])) return null;
        $returns = null;
        foreach(self::$Event[$group] as $priority => &$event):
            foreach($event as $id => &$e):
                list($callback, $args) = $e;
                if(empty($event_id))
                    $returns = call_user_func_array($callback, $args);
                else
                    if($id === $event_id)
                        return call_user_func_array($callback, $args);
            endforeach;
        endforeach;
        return $returns;
    }
    
    /**
     * Event::remove()
     * remove an event .
     * @param string $group
     * @param mixed $event_id
     * @return void
     */
    static function remove($group, $event_id)
    {
        if(!isset(self::$Event[$group])) return null;
        foreach(self::$Event[$group] as $priority => &$event) {
            if(isset($event[$event_id])) {
                unset(self::$Event[$group][$priority][$event_id]);
                break;
            }
        }
    }

    /**
     * Event::unsetGroup()
     * remove group of Event .
     * @param string $group
     * @return void
     */
    static function unsetGroup($group)
    {
        unset(self::$Event[$group]);
    }
    
    /**
     * Event::dump()
     * dump all Event .
     * @param bool $return
     * @return mixed
     */
    static function dump($return = false)
    {
        if($return) return (array)self::$Event;
        echo '<pre>';
        print_r(self::$Event);
        echo '</pre>';
    }
}
