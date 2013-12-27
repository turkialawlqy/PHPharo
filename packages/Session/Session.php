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
 * Pharo Session Class
 *
 * This is a simple full-options session system .
 *
 * @package		session
 * @author		Mohammed Alashaal
 */
 
class Session
{
    /* -- private properies -- */
    private static $started = false;
    
    /* -- public properties -- */
    public static $lifetime = 1800;
    public static $save_path = null;
    public static $http_only = null;
    public static $regenerate = true;
    public static $session_name = 'PHAROSESSID';
    public static $domain = null;
    public static $domain_path = null;
    public static $secure_only = null;
    public static $hash_function = 1;
    public static $hash_bits = 6;
    
    /* -- public methods -- */
    /**
     * Session::start()
     * start new secure session if not started
     * @return void
     */
    public static function start()
    {
        // if it is already started , don't start it again
        if(self::active()) return null;
        // start the session
        self::$started = true;
        // set session hash function
        @ini_set('session.hash_function', self::$hash_function);
        // set session hash bits per one charctar
        @ini_set('session.hash_bits_per_character', self::$hash_bits);
        // use only cookies [prevent session attacks]
        @ini_set('session.use_only_cookies', 1);
        // session save path
        if(empty(self::$save_path)) self::$save_path = session_save_path();
        else if(file_exists(self::$save_path)) session_save_path(self::$save_path);
        // get default cookie params settings
        $def = session_get_cookie_params();
            if(empty(self::$domain)) self::$domain = $def['domain'];
            if(empty(self::$domain_path)) self::$domain_path = $def['path'];
            if(empty(self::$secure_only)) self::$secure_only = $def['secure'];
            if(empty(self::$http_only))   self::$http_only = true;
        // set session cookie params settings
        session_set_cookie_params(self::$lifetime, self::$domain_path, self::$domain, self::$secure_only, self::$http_only);  
        // set the session name
        session_name(self::$session_name);
        // finally start the session
        session_start();
        // regenerate it (for session security)
        if(self::$regenerate) session_regenerate_id(true);
        // custom timer (for lifetime setting)
        if(!isset($_SESSION['{session.timer_start}'])) $_SESSION['{session.timer_start}'] = time();
        // end session if expired
        if(self::expired($_SESSION['{session.timer_start}'], self::$lifetime)) self::end();
        // goodby
        return null;
    }
    
    /**
     * Session::end()
     * end & destroy the open sessions
     * @return true
     */
    public static function end()
    {   if(!self::$started) return true;
        self::$started = false;
        unset($_SESSION);
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Session::get()
     * get a session value of certain key
     * @param mixed $session_key
     * @return [mixed|false 'if not set or session not active'] 
     */
    public static function get($session_key)
    {
        if(!self::active()) return false;
        if(isset($_SESSION[$session_key])) return $_SESSION[$session_key];
        else return false;
    }
    
    /**
     * Session::set()
     * set a session key value
     * @param mixed $key
     * @param mixed $value
     * @return void or false if session not active
     */
    public static function set($key, $value)
    {
        if(!self::active()) return false;
        // block overriding of {session.timer_start}
        if($key === '{session.timer_start}') die(trigger_error('session: cannont set this key, "'.$key.'"'));
        // let's set it
        $_SESSION[$key] = $value;
        return null;
    }
    
    /**
     * Session::active()
     * check if the session has been iniialized or not
     * @return bool
     */
    public static function active()
    {
        return (self::$started === true && session_id() !== '');
    }
    
    /* -- private methods -- */
    /**
     * Session::expired()
     * check if session is expired
     * @param int $lasttime
     * @param int $lifetime
     * @return bool
     */
    private static function expired($lasttime, $lifetime)
    {
        return (time() >= ($lasttime + $lifetime));
    }
}