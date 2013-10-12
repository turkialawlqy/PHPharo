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

/** MyRouter ShortCut */
define('R', 'MyRouter', TRUE);

/**
 * MyRouter
 * Advaned Url Router / Dispatcher
 * @package PHPharo
 * @version 0.7
 * @access public
 */
class MyRouter
{
    protected static $URI;
    protected static $URLs;
    protected static $MOD_REWRITE = false;
    protected static $REQUESTS;
    protected static $REGEX = array(
                                    '{INT}' => '[0-9]+',
                                    '{ALL}' => '.+',
                                    '{ANY}' => '.+',
                                    '{ALPHA}' => '[\p{L}a-zA-Z-_]+{::ui::}',
                                    '{ALNUM}' => '[\p{L}a-zA-Z0-9-_]+{::ui::}',
                                    '{FLOAT}' => '[0-9]+\.+[0-9]+');

/* ------------------------------------------------------------- */

    /**
     * MyRouter::start()
     * Start The Class
     * @return void
     */
    public static function start()
    {
        // use mod_rewrite redirect
        (isset($_SERVER['USE_MOD_REWRITE']))
        ? self::$MOD_REWRITE = true : '';
        
        // redirect to the script name `index.php/` if no mod_rewrite
        (!self::$MOD_REWRITE && !isset($_SERVER['PATH_INFO']))
        ? self::redirect(basename($_SERVER['SCRIPT_NAME']) . '/') : '';
        
        // set the pathinfo to / if not set
        (!isset($_SERVER['PATH_INFO']))
        ? $_SERVER['PATH_INFO'] = '/' : '';
        
        // clean the path info
        $_SERVER['PATH_INFO'] = str_replace(array('./', '../'), '/', $_SERVER['PATH_INFO']);
        
        // set the current uri to `/uri/`
        self::$URI = self::prepare_Uri($_SERVER['PATH_INFO']);
    
        // register 404 error
        self::add('e404', array(__CLASS__, 'error'), array('404 Not Found', 'Requested Page Not Found', true), array('e'));
    }
    
    /**
     * MyRouter::add()
     * register uri regex
     * @param string $uri
     * @param callback $callback
     * @param array $args
     * @return void
     */
    public static function add($uri, $callback, array $args = array())
    {
        $uri = str_replace(array_keys(self::$REGEX),array_values(self::$REGEX),$uri);
        $uri = self::prepare_Uri($uri);
        self::$URLs[$uri] = array($callback, $args);
    }
    
    /**
     * MyRouter::addArray()
     * add array of urls
     * @param mixed $array
     * @return void
     */
    public static function addArray(array $array)
    {
        if(!empty($array)) {
            foreach($array as $single) {
                call_user_func_array(array(__CLASS__, 'add'), $single);
            }
        }
    }
    
    /**
     * MyRouter::remove()
     * Remove Registered Uri
     * @param string $uri
     * @return void
     */
    public static function remove($uri)
    {
        $uri = self::prepare_Uri($uri);
        if(isset(self::$URLs[$uri]))
            unset(self::$URLs[$uri]);
    }
    
    /**
     * MyRouter::get()
     * Get Key From Uri
     * @param string $key
     * @param string $extension
     * @return array if '*' or false if not exists
     */
    public static function get($key = '*', $extension = '.html')
    {
        $x = self::$URI;
        $x = explode('/', $x);
        $x = array_filter($x);
        if($key == '*')
            return $x;
        else
            if(isset($x[$key])) {
                ($extension !== false && strpos($x[$key], $extension))
                ? $x[$key] = substr($x[$key], 0, (strlen($extension)))
                : '';
                return $x[$key];
            } else
                return false;
    }
    
    /**
     * MyRouter::route()
     * Start Routing
     * @return void
     */
    public static function route()
    {
        $state = self::uri_exists();
        // is found
        if($state == false) {
            self::redirect(self::Url(0,'e404'));
        } else {
            list($callback, $args) = $state;
            // is callable
            if(!is_callable($callback)) {
                self::redirect(self::Url(0, 'e404'));
            } else { 
                call_user_func_array($callback, $args);  
              }
        }
        
    }
    
    /**
     * MyRouter::routes()
     * Get Array Of Registered Routes
     * @return array
     */
    public static function routes()
    {
        return self::$URLs;
    }
    
    /**
     * MyRouter::Url()
     * Get The Url
     * @param bool $direct
     * @param string $path
     * @return string
     */
    public static function Url($direct = false, $path = '')
    {
        $url = ((isset($_SEVER['HTTPS'])) ? 'https' : 'http') . '://';
        $url = $url . $_SERVER['SERVER_NAME'] ; 
        $url = $url . substr($_SERVER['SCRIPT_NAME'], 0,-strlen(basename($_SERVER['SCRIPT_NAME'])));
        
        if($direct) 
            return $url . $path;
        
        $url = $url . ((self::$MOD_REWRITE) ? '' : basename($_SERVER['SCRIPT_NAME']) . '/');
        return $url . $path;
    }
    
    /**
     * MyRouter::error()
     * error template
     * @param string $title
     * @param string $body
     * @param bool $echo
     * @return string
     */
    public static function error( $title, $body, $echo = false )
    {
        $boxCss = 'margin:18% auto 18% auto;border:1px solid #ccc;padding:15px;color:#555;border-radius:10px;width:350px;box-shadow:0 0 10px #ddd;text-shadow:0 0 1px #fff';
        $titCss = 'border-bottom: 1px solid #ddd;padding:5px;font-size:20px;color:maroon';
        $bodCss = 'margin-top:5px;word-wrap:break-word;';

        $e  = '<!DOCTYPE html><html><head><title>' . $title . '</title></head><body>';
        $e .= '<div style=\'' . $boxCss . '\'>';
            $e .= '<div style=\'' . $titCss . '\'><b>'.$title.'</b></div>';
            $e .= '<div style=\'' . $bodCss . '\'><b>'.$body.'</b></div>';
        $e .= '</div></body></html>';

        if($echo)
            die($e);
        else 
            return $e;
    }
    
/* ------------------------------------------------------------- */

    /**
     * MyRouter::prepare_Uri()
     * Prepare The Uri
     * @param string $Uri
     * @return string
     */
    protected static function prepare_Uri($Uri)
    {
        $Uri = '/' . ltrim(rtrim($Uri, '/') , '/') . '/';
        $Uri = preg_replace('#\/+#' ,'/', $Uri);
        
        return $Uri;
    }
    
    /**
     * MyRouter::uri_exists()
     * check if current uri registered
     * @return array on success or false on fail
     */
    protected static function uri_exists()
    {
        $all = self::$URLs;
        $uri = self::$URI;
        
        if(!empty($all)):
            foreach( $all as $regex => $array )
            {
                $mods = self::get_regex_opts($regex);
                $regex = self::remove_regex_opts($regex);
                $regex = "#^{$regex}$#{$mods}";
                if( (bool)preg_match("{$regex}", $uri) )
                    return $array;
            }
        endif;
        
        return false;
    }
    
    protected static function get_regex_opts($str)
    {
        $mods = (preg_match('|{::(.+)::}|i', $str,$m)) ? $m[1] : '';
        return $mods;
    }
    
    protected static function remove_regex_opts($str)
    {
        return preg_replace('|{::(.+)::}|', '', $str);
    }
    
    /**
     * MyRouter::redirect()
     * Redirect
     * @param string $url
     * @param integer $code
     * @return void
     */
    protected static function redirect($url, $code = 302)
    {
        header('Location: '.$url, true, 302);
        exit(0);
    }
    
}
