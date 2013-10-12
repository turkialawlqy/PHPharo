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
 * PHPharo session helper functions
 * used to easly manage sessions
 * @package PHPharo
 * @version 0.1
 * @author PHPharo|Mohammed Abdullah Alashaal
 * @link <https://twitter.com/phpharo>
 */


if(!function_exists('session')):

    /**
     * session()
     * set session
     * @param string $key
     * @param string $val
     * @return object
     */
    function session($key = '', $val = '')
    {
        if(isset($_SESSION['phpharo.session_started'])):
            if(!empty($key))
                if(is_string($key)) $_SESSION[$key] = $val;
                elseif(is_array($key))
                    foreach($key as $k => $v) $_SESSION[$k] = $v;
            return json_decode(json_encode($_SESSION));
        endif;
    }

endif;

/* ------------------------------------------------------- */

if(!function_exists('start_session')):

    /**
     * start_session()
     * start new session
     * @param int $lifetime
     * @param bool $httpOnly
     * @param bool $regenerate
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @return void
     */
    function start_session($lifetime = '1800', $httpOnly = true,$regenerate = true , $path = '', $domain = '', $secure = '')
    {
        // don't start if already started
        if(isset($_SESSION['phpharo.session_started'])) return '';
        
        // prepare options
        $lifetime = (int)trim($lifetime); $httpOnly = (bool)trim($httpOnly); 
        $path = trim($path); $domain = trim($domain); $secure = (bool) trim($secure);
        
        // create new options
        $default = session_get_cookie_params();
        $new['lifetime'] = $lifetime;
        $new['httponly'] = $httpOnly;
        $new['path'] = (empty($path)) ? $default['path'] : $path;
        $new['domain'] = (empty($domain)) ? $default['domain'] : $domain;
        $new['secure'] = (empty($secure)) ? $default['secure'] : $secure;
        
        // apply options
        session_set_cookie_params($lifetime, $new['path'], $new['domain'], $new['secure'], $new['httponly']);
        
        // start session
        session_start();
        
        // this for security reasons
        session_regenerate_id((bool) $regenerate);
        
        /* Custom Session Timer */
            // set lifetime if not set
            (!isset($_SESSION['phpharo.session_starttime']))
            ? $_SESSION['phpharo.session_starttime'] = time() : '';
            
            // force session end if timeouted
            if(((time() - $_SESSION['phpharo.session_starttime']) + 1) >= $lifetime):
                end_session();
                return 0;
            endif;
        
        // set custom key
        $_SESSION['phpharo.session_started'] = true;
    }

endif;

/* -------------------------------------------------------- */

if(!function_exists('end_session')):

    /**
     * end_session()
     * end current session
     * @return void
     */
    function end_session()
    {
        if(!isset($_SESSION) || empty($_SESSION)) return '';
        $_SESSION = null;
        unset($_SESSION);
        session_unset();
        session_destroy();
    }

endif;