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

if (!function_exists('str_exists')):

    /**
     * str_exists()
     * check if string exists in subject
     * @param subject $source
     * @param string $needed
     * @return bool
     */
    function str_exists($source, $needed)
    {
        return (bool)(strpos($source, $needed) !== false);
    }

endif;

/* ------------------------------------------------------------------ */

if (!function_exists('str_unrepeat')):

    /**
     * str_unrepeatr()
     * unrepeat string from subject
     * @param subject $source
     * @param string $str
     * @return string
     */
    function str_unrepeat($source, $str)
    {
        return preg_replace('#' . addslashes($str) . '+#i', $str, $source);
    }

endif;

/* ------------------------------------------------------------------ */

if (!function_exists('letters_count')):

    /**
     * letters_count()
     * Get Letters Count in Subject
     * @param string $source
     * @return array
     */
    function letters_count($source)
    {
        $source = preg_replace('#\s#', '', $source);
        $a = array();
        $length = strlen($source);

        for ($i = 0; $i < $length; ++$i):

            if (isset($a[$source{$i}])) {
                ++$a[$source{$i}];
            } else
                if ($source{$i} !== '')
                    $a[$source{$i}] = 1;

        endfor;

        asort($a);
        return array_reverse($a);
    }

endif;

/* ------------------------------------------------------------------ */

if (!function_exists('words_count')):

    /**
     * words_count()
     * Get Words Count in subject
     * @param string $source
     * @return array
     */
    function words_count($source)
    {
        $a = array();
        $x = explode(' ', $source);

        for ($i = 0; $i < count($x); ++$i):

            if (isset($a[$x[$i]]))
                ++$a[$x[$i]];
            else
                if (!empty($x[$i]))
                    $a[$x[$i]] = 1;

        endfor;

        asort($a);
        return array_reverse($a);
    }

endif;

/* ------------------------------------------------------------------ */

if (!function_exists('xtrim')):

    /**
     * xtrim()
     * trim non-word chars
     * @param string $source
     * @param mixed $chars
     * @return string
     */
    function xtrim($source)
    {
        return (string )str_unrepeat(preg_replace(array("#[^\p{L}]#u", "#^[\W]$#i"), ' ',
            $source), ' ');
    }

endif;

/* ------------------------------------------------------------------ */

if (!function_exists('c2UTF8')):

    /**
     * c2UTF8()
     * Convert To UTF-8
     * @param subject $source
     * @return string
     */
    function c2UTF8($source)
    {
        return iconv(mb_detect_encoding($source), 'UTF-8//IGNORE', $source);
    }

endif;

/* ------------------------------------------------------------------- */

if(!function_exists('str_format')):

    function str_format($str, $limit = null)
    {
        if(!empty($limit))
            if(preg_match('/^.{1,'.$limit.'}\b/su', $str, $match))
                $str = $match[0];
        return str_unrepeat(preg_replace('#[^\p{L}\p{N}]#ui', '-', $str), '-');
    }

endif;

/* ------------------------------------------------------------------- */

if(!function_exists('get_hash_tags')):

    /**
     * get_hash_tags()
     * get hastags
     * @param string $str
     * @return array
     */
    function get_hash_tags($str){
        preg_match_all('~#(\w)+~iu', $str, $m);
        return $m[0];
    }

endif;