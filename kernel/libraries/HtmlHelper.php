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

if(!function_exists('tag')):

    /**
     * tag()
     * Write HtmlTag
     * @param array $array
     * @param string $attrs
     * @param string $text
     * @return string
     */
    function tag(array $array, $attrs = '', $text = '')
    {
        return (!isset($array[1]))
               ? "<{$array[0]} {$attrs} />"
               : "<{$array[0]} {$attrs}>{$text}</{$array[1]}>";
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('anchor')):

    /**
     * anchor()
     * Write Anchor link
     * @param string $attrs
     * @param string $text
     * @return string
     */
    function anchor($attrs, $text)
    {
        return tag(array('a', 'a'), $attrs, $text);
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('img')):

    /**
     * img()
     * Write img link
     * @param string $attrs
     * @param string $text
     * @return string
     */
    function img($attrs, $text)
    {
        return tag(array('img'), $attrs, $text);
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('br')):

    /**
     * br()
     * Write br tag number of times
     * @param integer $number
     * @return string
     */
    function br($number = 1)
    {
        return str_repeat('<br />', (int) $number);
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('nbsp')):

    /**
     * nbsp()
     * Write whitespace (entities)
     * @param integer $number
     * @return string
     */
    function nbsp($number = 1)
    {
        return str_repeat('&nbsp;', (int) $number);
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('c2HyperText')):

    /**
     * c2HyperText()
     * Convert Plain Text Urls in subject to hypertext
     * @param mixed $subject
     * @param string $more_attrs
     * @return string
     */
    function c2HyperText($subject, $more_attrs = '')
    {
        $pattrens = array('#((http|https|ftp|ftps)\:\/\/(\S*))#i');
        return preg_replace($pattrens, "<a href='$1' {$more_attrs}>$1</a>", $subject);
    }

endif;