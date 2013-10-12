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
 * dbr
 * Database Array Based
 * @author PHPharo
 * @copyright 2013
 * @access public
 */
class dbr
{
    private static $file = null;


    /**
     * dbr::check()
     * 
     * @return void
     */
    private static function check()
    {
        (!file_exists(self::$file) || empty(self::$file)) ? die(self::error('You did not set the DB file yet')) :
            '';
        (!is_writable(self::$file)) ? chmod(self::$file, 0777) : '';
        (!is_writable(self::$file)) ? die(self::error('Chmod the  ('.self::$file.') from ftp to 777')) : '';
    }

    /**
     * dbr::config()
     * config dbr
     * @param mixed $file_path
     * @return void
     */
    public static function config($file_path)
    {
        (file_exists($file_path)) ? self::$file = $file_path : die(self::error('file "'.file_path.'" not found'));
    }

    /**
     * dbr::get()
     * get key
     * @param string $get
     * @return string or array if it`s '*'
     */
    public static function get($get = '*')
    {
        self::check();
        $r = file_get_contents(self::$file);
        (empty($r) || $r == '' ) ? $r = serialize(array()) : '';
        $r = unserialize($r);
        if ($get == '*')
        {
            return $r;
        } else
        {
            if(isset($r[$get])) 
                return $r[$get];
            else 
                return false;
        }
    }

    /**
     * dbr::set()
     * set a key & value
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        self::check();
        $all = self::get();
        $all[$key] = $value;
        $all = serialize($all);
        
        return file_put_contents(self::$file,$all);
    }
    
    /**
     * dbr::delete()
     * delete key
     * @param mixed $key
     * @return bool
     */
    public static function delete($key)
    {
        self::check();
        $all = self::get();
        if(isset($all[$key])) {
            unset($all[$key]);
            $all = serialize($all);
            
            return file_put_contents(self::$file,$all);
        } else 
            return false;
        
    }

	protected static function error($error)
	{
		return '<h4 style="color:maroon;margin:auto">dbr error: '.$error.'</h4>';
	}
}
