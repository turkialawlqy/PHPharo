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
 * Pharo Router Class
 *
 * This is the pharo-smart router system .
 *
 * @package		Router
 * @author		Mohammed Alashaal
 */
 
class Router
{
    protected static $server_url_rewrite = false;
    protected static $maps = array();
    protected static $uri;
    protected static $regex = array();

    /**
     * Router::init()
     * initialize the router
     * @return void
     */
    static function  init()
    {
        // set the main site url
        $_SERVER['SERVER_URL'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
                                . '://' . $_SERVER['SERVER_NAME'] . '/';
        // set script-url
        $_SERVER['SCRIPT_URL'] = rtrim($_SERVER['SERVER_URL'] . ltrim(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'), '/'), '/') . '/';
        // set script-uri
        $_SERVER['SCRIPT_URI'] = (self::$server_url_rewrite === true) 
                                 ? $_SERVER['SCRIPT_URL']
                                 : $_SERVER['SERVER_URL'] . ltrim($_SERVER['SCRIPT_NAME'], '/') . '/';
        // force-rewrite if not enabled
        if(!self::$server_url_rewrite and !isset($_SERVER['PATH_INFO'])):
            self::redirect($_SERVER['SCRIPT_URI']);
        endif;
        /** ------------------------------------------------ */
        // remove the directory name and the working filename if exists in request uri
        // remove the dirname if exists
        if(strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0)
            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
        // remove the base of script_name if exists
        elseif(strpos($_SERVER['REQUEST_URI'], dirname($_SERVER['SCRIPT_NAME'])) === 0)
            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
        // remove the '/' from right
        $_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/');
        // force path_info (some servers have problems, let's solve it)
        $x = (array)explode('?', $_SERVER['REQUEST_URI'], 2);
        // update query string
        $_SERVER['QUERY_STRING'] = (isset($x[1]) ? $x[1] : "");
        // update _GET array
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        // update path info
        $_SERVER['PATH_INFO'] = isset($x[0]) ? $x[0] : "/";
        /** ------------------------------------------------ */
        // trim dublicated '/'
        $_SERVER['PATH_INFO'] = preg_replace('/\/+/', '/', rtrim(ltrim($_SERVER['PATH_INFO'], '/'), '/'));
        // if it empty , add one slash
        if(empty($_SERVER['PATH_INFO']))$_SERVER['PATH_INFO'] = '/';
        // set the current-uri
        self::$uri = $_SERVER['PATH_INFO'];
        // Build the 404 error page
        self::addUri('e404', create_function(null, '
            '.__CLASS__.'::errorDoc(\'404 Page Not Found\', \'The Requested Page Not Found Here .\');
        '));
    }

    /**
     * Router::serverUrlReRewrite()
     * set the state of server url_rewrite (mod_rewrite)
     * if you using it in e.g: htaccess just set this to true
     * else set to false
     * @param bool $on
     * @return void
     */
    public static function serverUrlReRewrite($enabled)
    {
        self::$server_url_rewrite = (bool)$enabled;
    }
    
    /**
     * Router::regexShortcut()
     * set a regex shortcut
     * @param string $shortcut
     * @param string $pattern
     * @return void
     */
    public static function regexShortcut($shortcut, $pattern)
    {
        self::$regex[$shortcut] = $pattern;
    }
    

    /**
     * Router::addUri()
     * addUri to the router map [will override any simillar]
     * @param mixed $uri
     * @param callback $function
     * @param string $regex_modifiries
     * @return void
     */
    public static function addUri($uri, $function, $regex_modifiries = 'i')
    {
        // force the uri to be an array
        $uri = is_array($uri) ? $uri : array($uri);
        // tmp-function
        $func = create_function('$str', 'return preg_replace("/\/+/", "/", rtrim(ltrim($str, "/"), "/"));');
        // now prepare the uri[0]
        $uri[0] = str_ireplace(array_keys(self::$regex), array_values(self::$regex), $func($uri[0]));
        $uri[0] = (array)explode('/', $uri[0]);
        if(empty($uri[0])) $uri[0] = array();
        // regex params = '(?<key>value)'
        // now force the $uri[1] to be auto-path if not provided
        if(!isset($uri[1]) or empty($uri[1]) or sizeof(trim($uri[1])) < 1) {
            $c = count($uri[0]);
            for($i=0; $i<$c; ++$i) {
                $uri[1][] = 'param_' . $i;
            }
            // free some memory
            unset($x, $c, $i);
        }
        // now if the uri[1] is provided let's check it .
        if(!is_array($uri[1])) {
            $uri[1] = $func($uri[1]);
            $uri[1] = (array)explode('/', $uri[1]);
            if(empty($uri[1])) $uri[1] = array();
        }
        // now the count of uri[0 and 1] must be the same
        if(($c = count($uri[0])) === count($uri[1])) {
            // the final-pattern will be here
            $pattern = null;
            // now assign the parts ... 
            for($i = 0; $i < $c; ++$i) {
                if(empty($uri[0][$i])) $uri[0][$i] = '\/';
                $pattern[] = '(?<'. $uri[1][$i] .'>'. $uri[0][$i] .')';
            }
            // now implode '\/' to the pattern array ...
            $pattern = '/^'. implode('\/', $pattern) . '$/' . $regex_modifiries;
        }
         else self::_(__FUNCTION__ . ' the uri string must be the same count as the alias string');
        // the function must be valid callback
        if(!is_callable($function)) self::_(__FUNCTION__ . ' the callback function "'.$function.'" is not-valid');
        self::$maps[implode('/', $uri[0])] = array($function, $pattern);
        // free some memory
        unset($pattern, $uri, $i, $c);
    }
    
    /**
     * Router::addGroup()
     * addGroup of uris with a shared callback
     * @param mixed $Uris
     * @param mixed $callback
     * @return void
     */
    public static function addGroup(array $Uris, $callback)
    {
        foreach($Uris as &$Uri)
            self::addUri($Uri, $callback);
    }
    
    /**
     * Router::applyRoutes()
     * apply all addedUriMaps
     * @return void
     */
    public static function applyRoutes()
    {
        $Found = false;
        foreach(self::$maps as &$map) {
            list($callback, $pattern) = $map;
            //echo self::$uri, '  ,  ', htmlspecialchars($pattern), '<br />';
            if(preg_match($pattern, self::$uri, $m)) {
                $Found = true;
                //echo '<pre>';
                //var_dump(self::$maps); echo '<hr />';
                call_user_func_array($callback, array($m));
            }
        }
        // free some memory
        unset($pattern, $map);
        if($Found === false)
             // redirect to the e404 if the found is false
             self::redirect($_SERVER['SCRIPT_URI'] . 'e404');
    }

    /**
     * Router::redirect()
     * redirect using (javascript|html|http)
     * @param string $url
     * @param string $type
     * @param integer $httpCode
     * @return void
     */
    public static function redirect($url, $type = 'http', $httpCode = 302)
    {
        header('Location: ' . $url, $httpCode);
        exit(0);
    }
    
    /**
     * Router::errorDoc()
     * show error page
     * @param string $title
     * @param string $body
     * @param bool $echo
     * @return mixed
     */
    public static function errorDoc($title, $body, $echo = true)
    {
        // end and clean the last buffer
        ob_get_clean();
        // create new buffer
        ob_start();
        $boxCss = 'margin:18%;border:1px solid #ccc;padding:15px;color:#555;border-radius:10px;width:60%;box-shadow:0 0 10px #ddd;text-shadow:0 0 1px #fff';
        $titCss = 'border-bottom: 1px solid #ddd;padding:5px;font-size:20px;color:maroon';
        $bodCss = 'margin-top:5px;word-wrap:break-word;';
        $e  = '<!DOCTYPE html><html><head><title>' . $title . '</title></head><body>';
        $e .= '<div style=\'' . $boxCss . '\'>';
        $e .= '<div style=\'' . $titCss . '\'><b>' . $title . '</b></div>';
        $e .= '<div style=\'' . $bodCss . '\'><b>' . $body . '</b></div>';
        $e .= '</div></body></html>';
        unset($body,$title,$bodCss,$boxCss);
        if ($echo)
            die($e);
        else
            return $e;
    }

    /**
     * Router::_()
     * Used to throw RouterException
     * @param string $err_str
     * @param bool $die
     * @return
     */
    protected static function _($err_str, $die = true)
    {
        throw new RouterException(__CLASS__ . ' ' . $err_str);
        if($die) exit;
    }
}
class RouterException extends Exception{}