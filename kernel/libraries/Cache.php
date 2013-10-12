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
 * Cache
 * PHP FLatFile & Http Cache
 * @package PHPharo
 * @author <m7medalash3al4@gmail.com>
 * @copyright 2013
 * @version 0.1
 * @access public
 */
class Cache
{
    protected static $Tmp;
    protected static $Time;
    protected static $Uri;
    protected static $start;
    protected static $cached = false;
    
    /**
     * Cache::start()
     * start caching
     * @param string $Tmp
     * @param time $time
     * @return void
     */
    public static function start($Tmp, $time = 20)
    {
        self::$Tmp = rtrim($Tmp, '/') . '/';
        self::$Time = $time;
        self::$Uri = $_SERVER['REQUEST_URI'];
        self::$start = microtime(true);
        ob_start();
        header('Cache-Control: public, max-age='.$time.', pre-check='.$time);
        self::cache_start();
    }
    
    /**
     * Cache::end()
     * end cache
     * @return void
     */
    public static function end()
    {
        if(!self::$cached):
            $file = self::$Tmp . self::gen_name(self::$Uri);
            $time     = gmdate('d D,M Y H:i:s');
            $expired  = time() + self::$Time;
            $expired  = gmdate('d D,M Y H:i:s', $expired);
            $content  = "<!-- \n This Page Cached On : {$time}  , Expires On : {$expired} \n -->";
            $content .= ob_get_clean();
            file_put_contents($file, $content);
            echo $content;
        endif;
    }
    
    /**
     * Cache::cache_start()
     * start cache
     * @return void
     */
    protected static function cache_start()
    {
        $file = self::$Tmp . self::gen_name(self::$Uri);
        if(!self::expired($file) && self::cached($file)):
            header('HTTP/1.1 304 Not Modified', true, 304);
            self::$cached = true;
            $content = file_get_contents($file);
            $content .= "<!-- \n Total Execution Time : ". number_format((microtime(true) - self::$start), 5) ." Second(s) \n -->";
            $content = preg_replace("#\s#", " ", $content);
            echo $content;
            ob_clean();
        endif;
        
        if(self::expired($file))
            @unlink($file);
    }
    
    /**
     * Cache::expired()
     * Check if Cache Already Expired
     * @param string $file
     * @return bool
     */
    protected static function expired($file)
    {
        return ( (time() - @filemtime($file)) >= self::$Time );
    }
    
    /**
     * Cache::cached()
     * check if page already cached
     * @param string $file
     * @return bool
     */
    protected static function cached($file)
    {
        return file_exists($file);
    }
    
    /**
     * Cache::gen_name()
     * generate cache file name
     * @param string $uri
     * @return string
     */
    protected static function gen_name($uri)
    {
        return 'phpharo_' . md5($uri) . '.cache';
    }
    
    /**
     * Cache::checker()
     * My Checker
     * @return void
     */
    protected static function checker()
    {
        if(!file_exists(self::$Tmp))
            self::error('the temp folder not exists');
            
        if(!is_writable(self::$Tmp))
            if(!chmod(self::$Tmp, 0777))
                self::error('the tmp folder must be writable');
    }
    
    /**
     * Cache::error()
     * show an error
     * @param string $err_str
     * @return void
     */
    protected static function error($err_str)
    {
        die('<h4 style="color:red;textalign:center;padding:3px;margin:auto">'.$err_str.'</h4>');
    }

}
