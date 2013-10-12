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
 * langs
 * 
 * @package PHPharo
 * @author gencyolcu
 * @copyright 2013
 * @version 0.1
 * @access public
 */
class langs
{
    protected static $all = array();
    protected static $lang = null;
    
    /**
     * langs::set_lang()
     * set the lang you want to deal with
     * @param string $lang
     * @return void
     */
    public static function set_lang($lang)
    {
        self::$lang = $lang;
    }
    
    /**
     * langs::add()
     * add translation or array of trnslations
     * @param mixed $src
     * @param string $trans
     * @return void
     */
    public static function add($src, $trans = '')
    {
        if(empty(self::$lang)) return false;
        if(is_array($src))
            foreach($src as $k => $v) self::$all[self::$lang][$k] = $v;
        else
            self::$all[self::$lang][$src] = $trans;
    }
    
    /**
     * langs::get()
     * get a translate string
     * @param string $src
     * @return string or array if it`s '*'
     */
    public static function get($src = '*')
    {
        if(empty(self::$lang)) return false;
        if($src == '*') return self::$all[self::$lang];
        return self::$all[self::$lang][$src];
    }
    
    /**
     * langs::parse()
     * parse and translate subject
     * @param mixed $subject
     * @param mixed $extra
     * @return string
     */
    public static function parse($subject, array $extra = array())
    {
        if(empty(self::$lang)) return false;
        self::$all[self::$lang] = array_merge(self::$all[self::$lang], $extra);
        return str_replace(array_keys(self::$all[self::$lang]), array_values(self::$all[self::$lang]), $subject);
    }
}