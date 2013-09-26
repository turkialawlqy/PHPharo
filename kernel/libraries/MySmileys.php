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


class MySmileys
{
    protected static $smileys;
    
    /**
     * MySmileys::register()
     * register new smiley
     * @param mixed $smileys
     * @return
     */
    public static function register(array $smileys)
    {
        foreach( $smileys as $smiley => $values ):
            if(!isset($values[1])) $values[1] = '20';
            if(!isset($values[2])) $values[2] = '20';
            if(!isset($values[3])) $values[3] = $smiley;
            self::$smileys[$smiley] = "<img src='{$values[0]}' alt='{$values[3]}' title ='{$smiley} - {$values[3]}' width='{$values[1]}' height='{$values[2]}' />";
        endforeach;
    }
    
    /**
     * MySmileys::get()
     * get smiley / smileys array
     * @param string $smiley
     * @return
     */
    public static function get($smiley = '*')
    {
        if($smiley == '*') return (array)self::$smileys;
        return self::$smileys[$smiley];
    }
    
    /**
     * MySmileys::parse()
     * parse string and replace smileys you registered
     * @param mixed $source
     * @return
     */
    public static function parse($source)
    {
        return str_replace(array_keys(self::$smileys), array_values(self::$smileys), $source);
    }
}