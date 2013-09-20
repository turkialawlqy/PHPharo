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

if(!function_exists('ob_compress')):

    /**
     * ob_compress()
     * Compress the buffer 
     * @param string $buffer "ob_get_clean / ob_get_contents"
     * @param string $type "gzip / deflate"
     * @param integer $level
     * @return compressed string
     */
    function ob_compress($buffer = null, $type = 'gzip', $level = 4)
    {   
        (empty($buffer)) ? $buffer = ob_get_clean() : '';
        
        if(!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) return $buffer;
        
        if($type == 'gzip') {
            header('Content-Encoding: gzip');
            $content = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $content .= gzcompress($buffer, $level);
            return $content;
        } else {
                header('Content-Encoding: deflate');
                $content = gzdeflate($buffer, $level);
                return $content;
            }
    }

endif;

/* ------------------------------------------------------------------- */

if(!function_exists('ob_get')):

    /**
     * ob_get()
     * get the buffer and choose if you want to flush or not
     * @param bool $flush
     * @return buffer
     */
    function ob_get($flush = false)
    {
        return ($flush) ? ob_get_clean() : ob_get_contents();
    }

endif;

/* ------------------------------------------------------------------- */

if(!function_exists('ob_tidy')):

    /**
     * ob_tidy()
     * Fix , Butify & Remove Whitespaces
     * @param buffer $buffer
     * @param string $type
     * @param bool $remove_whitespaces
     * @return buffer
     */
    function ob_tidy($buffer = null ,$type = 'html' ,$remove_whitespaces = true)
    {
        $type = strtolower($type);
        if(empty($buffer)) $buffer = ob_get_clean();
        if($type == false) return $buffer;
        $dom = new DOMDocument;
        $content = '';
        libxml_use_internal_errors(true);
        $dom->preserveWhiteSpace = false;
        $dom->recover = true;
        $dom->formatOutput = true;
        if( $type == 'html' ) {
            $dom->loadHTML('<!DOCTYPE html>'.$buffer);
            $content = $dom->saveHTML();
        }
        else{
                $dom->loadXML($buffer);
                $content = $dom->saveXML();
            }
        ($remove_whitespaces) ? $content = preg_replace('|\s+|', ' ', $content) : '';
        return $content;
    }

endif;