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
 * MyRequests
 * 
 * @package PHPharo
 * @copyright 2013
 * @version 0.1
 */
class MyRequests
{
    protected static $requests = array();
    protected static $methods = array('GET');
    protected static $regex = array(
                                        '{INT}' => '[0-9]+',
                                        '{ALL}' => '.+',
                                        '{ANY}' => '.+',
                                        '{ALPHA}' => '[\p{Arabic}a-zA-Z-_]+{::ui::}',
                                        '{ALNUM}' => '[\p{Arabic}a-zA-Z0-9-_]+{::ui::}',
                                        '{ARABIC}' => '[\p{Arabic}]{::ui::}',
                                        '{ENGLISH}' => '[a-zA-Z]',
                                        '{FLOAT}' => '[0-9]+.+[0-9]+'
                                    );
    protected static $config;
    
    
    public static function config($e404Callback, $e403Callback)
    {
        self::$config['e404'] = $e404Callback;
        self::$config['e403'] = $e403Callback;
    }
    
    /**
     * MyRequests::register_request()
     * register new request
     * @param method $type
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function register_request($type, $key, $value)
    {
        $value = str_replace(array_keys(self::$regex), array_values(self::$regex), $value);
        self::$requests[strtoupper($type)][$key] = $value;
    }
    
    /**
     * MyRequests::unregister_request()
     * unregister request
     * @param method $type
     * @param string $key
     * @return void
     */
    public static function unregister_request($type, $key)
    {
        if(in_array(self::$requests[strtoupper($type)][$key])) {
            $all = self::$requests;
            unset($all[strtoupper($type)][$key]);
            self::$requests = $all;
        }
    }
    
    /**
     * MyRequests::allow_method()
     * register new request method
     * @param string $method
     * @return void
     */
    public static function allow_method($method)
    {
        self::$methods[$method] = strtoupper($method);
    }
    
    /**
     * MyRequests::disallow_method()
     * remove a method from registered
     * @param string $method
     * @return void
     */
    public static function disallow_method($method)
    {
        if( in_array(strtoupper($method), self::$methods) && strtoupper($method) !== 'GET') {
            $all = self::$methods;
            unset($all[$method]);
            self::$methods = $all;
        }
    }
    
    /**
     * MyRequests::route()
     * start routing / maping requests method
     * @return void
     */
    public static function route()
    {
        /** if the request method is not registered */
        if(!in_array($_SERVER['REQUEST_METHOD'], self::$methods))
            exit(self::e403());
            
        /** if requested request exists */
        $exists = self::exists();
        if($exists == '403') die(self::e403());
        elseif($exists == '404') die(self::e404());
    }
    
    /**
     * MyRequests::exists()
     * 
     * @return
     */
    protected static function exists()
    {
        // get the registered requests
        $requests = self::$requests;
        // get current request method
        $type = $_SERVER['REQUEST_METHOD'];
        // exists or not
        $exists = false;
        // is requested method registered
        if(isset($requests[$type])) {
            // create _GLOBAL Varible
            $rq = '_' . $type; 
            // Get it From Global
            $rq = $GLOBALS[$rq];
            // only when request array not empty
            if(!empty($rq)):
                // loop through the crren request as $k=>$v
                foreach( $rq as $key => $val ):
                    // if $key in the regisered array
                    if(in_array($key, array_keys($requests[$type]))) {
                        // get the current request.type.key regex from registered
                        $regex = $requests[$type][$key];
                        // prepare the regex pattren
                        $mods = (preg_match('|{::(.+)::}|i', $regex,$m)) ? $m[1] : '';
                        $regex = preg_replace('|{::(.+)::}|', '', $regex);
                        $regex = "|{$regex}|{$mods}";
                        if(preg_match($regex, $rq[$key], $m))
                            $exists = 200;
                        else
                            $exists = 403;
                    }
                    else
                        $exists = 404;
                endforeach;
            endif;
            return $exists;
        } else
            die(self::e404());
    }
    
    /**
     * MyRequests::e403()
     * 
     * @return
     */
    protected static function e403()
    {
        if(isset(self::$config['e403']) && is_callable(self::$config['e403']))
            return call_user_func(self::$config['e403']);
        
        header('HTTP/1.1 403 Forbidden', true, 403);
        die(self::error('403 Forbidden', '403 Forbbiden Page'));
    }
    
    /**
     * MyRequests::e404()
     * 
     * @return
     */
    protected static function e404()
    {
        if(isset(self::$config['e404']) && is_callable(self::$config['e404']))
            return call_user_func(self::$config['e404']);
            
        header('HTTP/1.1 404 Not Found', true, 404);
        die(self::error('404 error', '404 page not found'));
    }
    
    /**
     * MyRequests::error()
     * 
     * @param mixed $title
     * @param mixed $body
     * @return
     */
    protected static function error($title, $body)
    {
        $boxCss = 'margin:18% auto 18% auto;border:1px solid #ccc;padding:15px;color:#555;border-radius:10px;width:350px;box-shadow:0 0 10px #ddd;text-shadow:0 0 1px #fff';
        $titCss = 'border-bottom: 1px solid #ddd;padding:5px;font-size:20px;color:maroon';
        $bodCss = 'margin-top:5px;word-wrap:break-word;';

        $e  = '<!DOCTYPE html><html><head><title>' . $title . '</title></head><body>';
        $e .= '<div style=\'' . $boxCss . '\'>';
            $e .= '<div style=\'' . $titCss . '\'><b>'.$title.'</b></div>';
            $e .= '<div style=\'' . $bodCss . '\'><b>'.$body.'</b></div>';
        $e .= '</div></body></html>';

        return $e;
    }
    
    
}