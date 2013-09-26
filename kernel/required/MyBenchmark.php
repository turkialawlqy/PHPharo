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

/** MyBenchmark ShortCut */
define('B', 'MyBenchmark', TRUE);

/**
 * MyBenchmark
 * 
 * @package PHPharo 
 * @copyright 2013
 * @version 0.2
 */
class MyBenchmark
{
    protected static $points = array();
    
    /**
     * MyBenchmark::new_point()
     * create new point
     * @param mixed $point_name
     * @return void
     */
    public static function new_point($point_name)
    {
        self::$points[$point_name] = microtime(true);
    }
    
    /**
     * MyBenchmark::exec_time()
     * get execution time of 2 points
     * @param string $point1
     * @param string $point2
     * @param integer $decimals
     * @return string
     */
    public static function exec_time($point1, $point2 = '', $decimals = 5)
    {
        if(!isset(self::$points[$point1])) return '';
        if(empty($point2)) {
            $count = count(self::$points);
            $point2 = 'point_'.(++$count);
        }
        if(!isset(self::$points[$point2])) self::$points[$point2] = microtime(true);
        
         $p1 = self::$points[$point1];
         $p2 = self::$points[$point2];
        
        return number_format(($p2 - $p1), $decimals);
    }
    
    /**
     * MyBenchmark::memory_usage()
     * get memory usage
     * @return string
     */
    public static function memory_usage()
    {
        return memory_get_usage(true) / 1024 / 1024;
    }
}