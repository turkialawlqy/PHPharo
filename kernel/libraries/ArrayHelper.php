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

if(!function_exists('Array2Object')):

    /**
     * Array2Object()
     * Convert Array to Object
     * @param mixed $array
     * @return object
     */
    function Array2Object(array $array)
    {
        return json_decode(json_encode($array));
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('Object2Array')):

    /**
     * Object2Array()
     * Convert Array to Object
     * @param object $object
     * @return array(assoc)
     */
    function Object2Array($object)
    {
        return json_decode(json_encode($object), true);
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('array_odds')):

    /**
     * array_odds()
     * get array of odd elemnts
     * @param array $array
     * @return array
     */
    function array_odds(array $array)
    {
        $new_array = array(); 
        for( $i=1; $i<count($array); $i = $i + 2):
            $new_array[] = $array[$i];
        endfor;
        return $new_array;
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('array_evens')):

    /**
     * array_evens()
     * get array of even elemnts
     * @param array $array
     * @return array
     */
    function array_evens(array $array)
    {
        $new_array = array(); 
        for( $i=0; $i<count($array); $i = $i + 2):
            $new_array[] = $array[$i];
        endfor;
        return $new_array;
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('array_shifts')):

    /**
     * array_shifts()
     * shift first & last element off the array
     * @param array $array
     * @return array
     */
    function array_shifts(array $array)
    {
        array_shift($array);
        $array = array_reverse($array);
        array_shift($array);
        return array_reverse($array);
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('array_trim')):

    /**
     * array_trim()
     * remove empty keys => vals from an array
     * @param array $array
     * @return array
     */
    function array_trim($array)
    {
        unset($array[''], $array[' ']);
        return array_filter($array);
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('array_get')):

    /**
     * array_get()
     * check and get key from an array
     * if it exists return the key ,
     * if not-exists return false
     * @param array $array
     * @param string $key
     * @return mixed
     */
    function array_get($array, $key)
    {
        if(isset($array[$key])) return $array[$key];
        else return false;
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('array_xsearch')):

    /**
     * array_xsearch()
     * search an array for given word(s)
     * it's look like search engine
     * @param array $data
     * @param mixed $needed
     * @return array
     */
    function array_xsearch(array $data, $needed)
    {
        if(!is_array($needed) && strpos($needed, ' '))
            $needed = array_trim(explode(' ', $needed));
        
        elseif(is_string($needed)) return preg_grep('#'.$needed.'#i', $data);            

        if(is_array($needed)):
            $pattren1 = '#(' . implode('|',$needed) . ')#i';
            return preg_grep($pattren1, $data);
        endif;
    }

endif;