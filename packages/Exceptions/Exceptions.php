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
 * Pharo Exceptions Handler Class
 *
 * This class enables you to use handle errors/exceptions in a better way .
 *
 * @package		Exceptions
 * @author		Mohammed Alashaal
 */
class Exceptions
{
    /**
     * Exceptions::handleDefault()
     * handle default/basic errors/exception
     * @return void
     */
    static function handleDefault()
    {
        set_exception_handler(__CLASS__ . '::_exceptionHandler');
        set_error_handler(__CLASS__ . '::_errorHandler');
    }
    
    /**
     * Exceptions::handleFatal()
     * handle the fatalErrors
     * @return void
     */
    static function handleFatal()
    {
        error_reporting(0);
        register_shutdown_function(__CLASS__ . '::_fatalHandler');
    }
    
    /**
     * Exceptions::_errorHandler()
     * 
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @return
     */
    static function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (($errno & error_reporting()) == $errno)
            self::_errorTemplate($errstr, $errfile, $errline, null);
    }
    
    /**
     * Exceptions::_fatalHandler()
     * 
     * @return
     */
    static function _fatalHandler()
    {
        $e = error_get_last();
        if(count($e) < 1) return 0;
        die(self::_errorTemplate($e['message'],$e['file'],$e['line']));
    }
    
    /**
     * Exceptions::_exceptionHandler()
     * 
     * @param mixed $e
     * @return
     */
    static function _exceptionHandler($e)
    {
        $errstr = $e->getMessage();
        $errfile = $e->getFile();
        $errline = $e->getLine();
        $trace = $e->getTrace();
        unset($e);
        $trace = end($trace);
        self::_errorTemplate($errstr, $errfile, $errline, $trace);
    }
    
    /**
     * Exceptions::_errorTemplate()
     * 
     * @param mixed $message
     * @param mixed $file
     * @param mixed $line
     * @return
     */
    static function _errorTemplate($message, $file, $line, $trace)
    {
        $css = 'font-weight:bolder;padding:10px;border:1px solid #222;background:#333;color:#eee;width:70%;margin:2% auto;box-shadow:0 0 10px #555;margin-top:2%;word-wrap:break-word;border-radius: 5px';
        
        $e = '<div style="'.$css.'">
                 Oops ! , Sorry but there is and error ! <br />
                 at file &laquo;<i style="color:yellowgreen">'.$file.'</i>&raquo; <br />
                 on line &laquo;<i style="color:yellowgreen">'.$line.'</i>&raquo;<br />
                 it says &laquo;<i style="color:yellowgreen">'.wordwrap(strip_tags($message), 200).'</i>&raquo; <br />';
                 if(!empty($trace)) $e.=        
                 'Trace: File &laquo;<i style="color:yellowgreen">'.$trace['file'].'</i>&raquo;<br />
                  Trace: Line &laquo;<i style="color:yellowgreen">'.$trace['line'].'</i>&raquo;<br />';
                  $e .=
                 'try <a target="_blank" style="text-decoration:none;color:#09f" href="http://google.com/search?q=php '.strip_tags($message).'">Google-Help</a>
              </div>';

            echo $e;
    }
}