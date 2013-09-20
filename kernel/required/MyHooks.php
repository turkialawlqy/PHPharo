<?php

/**
 * PHP Pharaoh 'PHPharo'
 * PHPharo is a full featured oop non-mvc modular framework that
 * helps you create any type of app(s)
 * it`s very fast light
 * @license GPL-V3
 * @author PHPharo | Mohammed Abdullah Al-Ashaal
 * @link <https://twitter.com/phpharo> <PHPharo@Gmail.Com>
 * @copyright 2013
 */

/* ------------------------------------------------------------- */

/**
 * MyHooks
 * PHP Hooks System That implements Actions & Filters
 * @package PHPharo
 * @version 0.2
 * @access public
 */
class MyHooks
{
    /* -- properties */
    protected static $actions ;
    protected static $filters ;
    
    /* -- actions functions -- */
    
    
    /**
     * MyHooks::register_action()
     * register a new action to be executed in the tag/category $tag
     * @param name $tag
     * @param callback $callback
     * @param array $args
     * @param integer $order
     * @return void
     */
    public static function register_action($tag, $callback, array $args = array(), $order = 5)
    {
        $actions = (array) self::$actions;
        $actions[$tag][$order][] = array($callback, $args);
        ksort($actions[$tag]);
        self::$actions[$tag] = $actions[$tag];
    }
    
    /**
     * MyHooks::unregiser_action()
     * remove a callback from an action tag
     * @param name $tag
     * @param callback $callback
     * @return void
     */
    public static function unregiser_action($tag, $callback)
    {
        if(!isset(self::$actions[$tag])) return;
        
        $actions = (array) self::$actions;
        foreach($actions[$tag] as $order => $ids)
        {
           foreach($ids as $id => $action):
                if($action[0] == $callback):
                    unset($actions[$tag][$order][$id]);
                endif;
           endforeach;
        }
        self::$actions[$tag] = $actions[$tag];
    }
    
    /**
     * MyHooks::apply_actions()
     * execute actions under the wanted tag/category $tag
     * @param name $tag
     * @return void
     */
    public static function apply_actions($tag)
    {
        if(!isset(self::$actions[$tag])) return false;
        if(empty(self::$actions[$tag])) return false;
        
        $actions = (array) self::$actions[$tag];
        foreach($actions as $order => $ids) {
            foreach($ids as $id => $action):
                if(is_callable($action[0])):
                    call_user_func_array($action[0],$action[1]);
                endif;
            endforeach;
        }
    }
    
    /**
     * MyHooks::get_actions()
     * get array of current registered actions
     * @return array
     */
    public static function get_actions()
    {
        return (array)self::$actions;
    }
    
    /* -- filters functions -- */
    
    
    /**
     * MyHooks::register_filter()
     * register a new filter to be executed in the tag/category $tag
     * @param name $tag
     * @param callback $callback
     * @param array $args
     * @param integer $order
     * @return void
     */
    public static function register_filter($tag, $callback, $args = array(), $order = 5)
    {
        $filters = (array) self::$filters;
        $filters[$tag][$order][] = array($callback, $args);
        ksort($filters[$tag]);
        self::$filters[$tag] = $filters[$tag];
    }
    
    /**
     * MyHooks::unregiser_filter()
     * remove a callback from a filter tag
     * @param name $tag
     * @param callback $callback
     * @return void
     */
    public static function unregiser_filter($tag, $callback)
    {
        if(!isset(self::$filters[$tag])) return;
        
        $filters[$tag] = (array) self::$filters[$tag];
        foreach($filters[$tag] as $order => $ids)
        {
           foreach($ids as $id => $filter):
                if($filter[0] == $callback):
                    unset($filters[$tag][$order][$id]);
                endif;
           endforeach;
        }
        self::$filters[$tag] = $filters[$tag];
    }
    
    /**
     * MyHooks::apply_filters()
     * execute filters under the wanted tag/category $tag
     * @param name $tag
     * @return result
     */
    public static function apply_filters($tag)
    {
        if(!isset(self::$filters[$tag])) return false;
        if(empty(self::$filters[$tag])) return false;
        
        $filters = (array) self::$filters[$tag];
        $result = false;
        foreach($filters as $order => $ids) {
            foreach($ids as $id => $filter):
                if(is_callable($filter[0])):
                    $result = call_user_func_array($filter[0],$filter[1]);
                endif;
            endforeach;
        }
        return $result;
    }
    
    /**
     * MyHooks::get_filters()
     * get array of current registered filters
     * @return array
     */
    public static function get_filters()
    {
        return (array)self::$filters;
    }

}