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
 * MyEHandler
 * 
 * @package PHPharo
 * @copyright 2013
 * @version 0.5
 * @access public
 */
class MyEHandler
{
    
    /**
     * MyEHandler::handle()
     * start handling errors
     * @return
     */
    public static function handle()
    {
        set_exception_handler(__CLASS__ . '::exception_handler');
        set_error_handler(__CLASS__ . '::error_handler');
    }
     
    /**
     * MyEHandler::exception_handler()
     * exception template
     * @param mixed $e
     * @return
     */
    public static function exception_handler($e)
    {
        return self::error_template('Exception', $e->getMessage(), $e->getFile(), $e->getLine
            ());
    }
    
    /**
     * MyEHandler::error_handler()
     * 
     * @param int $e_num
     * @param string $e_str
     * @param path $e_file
     * @param int $e_line
     * @return string
     */
    public static function error_handler($e_num, $e_str, $e_file, $e_line)
    {
        if (($e_num & error_reporting()) == $e_num)
            return self::error_template($e_num, $e_str, $e_file, $e_line);
    }
    
    /**
     * MyEHandler::_fatal()
     * 
     * @return
     */
    public static function _fatal()
    {
        $e = error_get_last();
        if(count($e) < 1) return 0;
        die(self::error_template($e['type'],$e['message'],$e['file'],$e['line']));
    }
    
    /**
     * MyEHandler::error_template()
     * error template
     * @param int $e_num
     * @param string $e_str
     * @param path $e_file
     * @param int $e_line
     * @return
     */
    protected static function error_template($e_num, $e_str, $e_file, $e_line)
    {
        $css = 'font-weight:bolder;padding:10px;border:1px solid #222;background:#333;color:#eee;width:70%;margin:auto;box-shadow:0 0 5px #555;margin-top:2%';
        
        $e = '<div style="'.$css.'">
                An error occured at file &laquo;<i style="color:yellowgreen">'.$e_file.'</i>&raquo; <br />
                 on line &laquo;<i style="color:yellowgreen">'.$e_line.'</i>&raquo;<br />
                 it`s &laquo;<i style="color:yellowgreen">'.$e_str.'</i>&raquo;
                 &nbsp; Try <a target="_blank" style="text-decoration:none;color:#09f" href="http://google.com/search?q=php '.$e_str.'">Help</a>
              </div>';

            echo $e;
    }
 
}
