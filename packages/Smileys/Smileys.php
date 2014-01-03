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
 * Pharo Smielys Class
 *
 * This is a simple-smyle management class .
 *
 * @package		Smileys
 * @author		Mohammed Alashaal
 */
 
class Smileys
{
    protected $smileys;
    protected static $init;
    protected $attrs;
        
    /**
     * Smileys::getInstance()
     * Signlton pattern
     * @param bool $new
     * @return object
     */
    public static function getInstance()
    {
        if(empty(self::$init)) self::$init = new self();
        return self::$init;
    }
    
    /**
     * Smileys::attr()
     * set attributes [only before adding any smiley]
     * @param string $attrs
     * @return void
     */
    function attr($attrs)
    {
        $this->attrs = $attrs;
            
    }    
            
    /**
     * Smileys::add()
     * add new smiley
     * @param string $smiley
     * @param string $src
     * @param string $alt
     * @param integer $width
     * @param integer $height
     * @return void
     */
    function add($smiley, $src, $alt = 'null', $width = 19, $height = 19)
    {                                                                                                
        $this->smileys[$smiley] = '<img src="'.$src.'" alt="'.$alt.'" title="'.$alt.'" width="'.$width.'" height="'.$height.'" '.$this->attrs.' />';
    }
    
    /**
     * Smileys::addArray()
     * add multiple-smileys
     * @param array $smileys
     * @return void
     */
    function addArray(array $smileys)
    {
        foreach($smileys as &$smiley)
            call_user_func_array(array($this, 'add'), (array)$smiley);
    }
    
    /**
     * Smileys::parse()
     * parse and replace smiley-strings with smiley-icons
     * @param string $subject
     * @return string
     */
    function parse($subject)
    {
        return (string)str_ireplace(array_keys($this->smileys), array_values($this->smileys), $subject);
    }
}