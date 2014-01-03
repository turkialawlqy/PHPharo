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
 * Pharo Input Class
 *
 * This is Input manager class .
 *
 * @package		Input
 * @author		Mohammed Alashaal
 */
class Input
{
    protected static $init;
    
    /**
     * Input::getInstance()
     * get instance of this class
     * @return object
     */
    public static function getInstance()
    {
        if(empty(self::$init) or !self::$init) self::$init = new self();
        return self::$init;
    }
    
    /**
     * Input::__call()
     * used for magic methods .
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function __call($name, $args)
    {
        // set the input array
        switch(strtolower($name)):
            case 'get' : $input = $_GET; break;
            case 'post' : $input = $_POST; break;
            case 'file' : case 'files' : $input = $_FILES; break;
            default : parse_str(file_get_contents('php://input'), $input); break;
        endswitch;
        
        // set the args
        @list($key, $pattern) = $args;
        
        // return false if the key not-exists
        if(!isset($input[$key]))
            return false;
        
        // prepare the pattern
        if(empty($pattern)) $pattern = '::any';
        $pattern = str_replace(array('::any', '::int', '::alpha', '::alnum', '::str'),
                               array('(.+)', '[\d]+', '[[:alpha:]]+', '[[:alnum:]]+', '[\w\.-_\d]+'),
                               $pattern);
        
        // let's fetch the key
        return $this->_filter($input[$key], $pattern);
    }
    
    /**
     * Input::_filter()
     * filter a var
     * @param string $var
     * @param string $pattern
     * @return string
     */
    protected function _filter($var, $pattern)
    {
        preg_match_all('/'.$pattern.'/', $var, $matches);
        return implode('', (array)$matches[0]);
    }
}