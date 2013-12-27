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
 * Pharo String functions
 *
 * Those are a collection of functions usefull for string coding .
 *
 * @package		Pharo
 * @subpackage	Helpers
 * @author		Mohammed Alashaal
 */
 
if(!function_exists('str_limit')):

    /**
     * str_limit()
     * advanced alternative for substr
     * @param string $subject
     * @param int $offset
     * @param int $length
     * @param bool $as_words
     * @return string
     */
    function str_limit($subject, $offset, $length, $as_words = false)
    {
        $subject = preg_replace('~\/s+~', ' ', $subject);
        if($as_words === true) 
            return implode(' ', array_slice(explode(' ', $subject), (int)$offset, (int)$length));
        else
            return mb_substr($subject, $offset, $length, mb_detect_encoding($subject));
    }

endif;
 
// ------------------------------------------------------------------------

if(!function_exists('str_highlight')):

    /**
     * str_highlight()
     * highlight some string(s) in a subject
     * @param string $subject
     * @param mixed $needle
     * @param string $style
     * @param string $tag
     * @return string
     */
    function str_highlight($subject, $needle, $style = null, $tag = 'span')
    {
        $words = !is_array($needle) ? explode(' ', $needle) : $needle;
        $style = 'color:red; background:yellow; ' . $style;
        $count = count($words);
        for($i=0; $i<$count; ++$i) 
            $subject = preg_replace('/('.$words[$i].')/i', '<'.$tag.' style="'.$style.'" >$1</$tag>', $subject);
        return $subject;
    }

endif;

// ------------------------------------------------------------------------

if(!function_exists('str_unrepeat')):

    /**
     * str_unrepeat()
     * unrepeat a repeated string from a subject
     * @param string $subject
     * @param string $repeated
     * @return string
     */
    function str_unrepeat($subject, $repeated = '\s')
    {
        $s = addcslashes($repeated, '/\'".');
        return preg_replace("/{$s}+/i", $repeated, $subject);
    }

endif;

// ------------------------------------------------------------------------

if(!function_exists('str_slug')):

    /**
     * str_slug()
     * generate a seo slug for uri
     * @param string $string
     * @param integer $max_length
     * @param string $separator
     * @return string
     */
    function str_slug($string, $max_length = 60, $separator = '-')
    {
        return strtolower(str_unrepeat(str_limit(preg_replace('/\W/', $separator, $string), 0, $max_length), $separator));
    }

endif;

// ------------------------------------------------------------------------

if(!function_exists('str_exists')):

    /**
     * str_exists()
     * check if string exists or not
     * @param string $subject
     * @param string $needle
     * @param bool $strict
     * @return bool
     */
    function str_exists($subject, $needle, $strict = false)
    {
        $m = '';
        if($strict !== true)
            $m = 'i';
        return (bool)preg_match('/'.addcslashes($needle, '/.\'"')."/{$m}", $subject);
    }

endif;