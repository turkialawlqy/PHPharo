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
 * Pharo Filesystem Remote Class
 *
 * This class enables you to use manage remote ftp files/folders .
 *
 * @package		Filesystem
 * @author		Mohammed Alashaal
 */
class Filesystem_Remote
{
    protected $stream = false;
    
    /**
     * Filesystem_Remote::__construct()
     * connect to remote ftp-host
     * @return mixed
     */
    function __construct($host, $port = 21, $ssl = false, $timeout = 60)
    {
        // if ssl is true, do ftp_ssl_connect .
        // else do basic connect .
        if($ssl === true) {
            if(!($this->stream = ftp_ssl_connect($host, $port, $timeout)))
                return false;
        } elseif(!($this->stream = ftp_connect($host, $port, $timeout)))
            return false;
        return $this;
    }
    
    /**
     * Filesystem_Remote::__call()
     * 
     * @param mixed $name
     * @param mixed $args
     * @return
     */
    function __call($name, $args)
    {
        $params = array();
        $params[] = $this->stream;
        $params = array_merge($params, $args);
        unset($args);
        return call_user_func_array('ftp_' . $name, $params);
    }
    
    /**
     * Filesystem_Remote::_()
     * 
     * @param mixed $str
     * @param bool $die
     * @return
     */
    function _($str, $die = true)
    {
        throw new FilesystemRemoteException(__CLASS__ . ' ' .$str);
        if($die) exit(0);
    }
    
}

class FilesystemRemoteException extends Exception {}