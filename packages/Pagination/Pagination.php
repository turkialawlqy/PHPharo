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
 * Pharo Pagination Class
 *
 * This class enables you to use paginate any data in any way you want .
 *
 * @package		Pharo
 * @subpackage	Pagination
 * @author		Mohammed Alashaal
 */
class Pagination
{
    var $offset;
    var $limit;
    var $pages_num;
    var $links;
    
    /**
     * Pagination::__construct()
     * A Powerful smart pagination system .
     * @param integer $data_length
     * @param integer $per_page
     * @param string $link_template
     * @param integer $current_pagenum
     * @param bool $generate_all
     * @return this (Class Object)
     */
    function __construct($data_length, $per_page, $link_template, $current_pagenum, $generate_all = false)
    {
        $this->limit = $per_page;
        // set the length
        // must be at least 1
        $data_length = ($data_length < 1) ? 1 : $data_length;
        // calculate the pages_num
        // pages_num = length/perpage
        $this->pages_num = ceil($data_length / $per_page);
        // the current_pagenum must be lower than or equal the pages_num
        $current_pagenum = ($current_pagenum > $this->pages_num) ? $this->pages_num : $current_pagenum;
        // the current_pagenum must be at least 1
        $current_pagenum = ($current_pagenum < 1) ? 1 : $current_pagenum;
        // calculate the offset
        // offset = (perpage x (current_pagenum - 1)) if current_pagenum > 2 (not equal 1)
        $this->offset = ($current_pagenum < 2) ? 0 : ($per_page * $current_pagenum ) - 1;
        // temp-func for prepare link of page
        $func = create_function('$tpl, $num', 'return sprintf("{$tpl}", $num);');
        // set first page
        $this->links['first'] = $func($link_template, 1);
        // set prev. page only if available
        if(($current_pagenum) > 1) $this->links['prev'] = $func($link_template, $current_pagenum - 1);
        else $this->links['prev'] = false;
        // setup and generate pages_links
        $this->links['now'] = $func($link_template, $current_pagenum);
        // set next page only if available
        if(($current_pagenum) < $this->pages_num) $this->links['next'] = $func($link_template, $current_pagenum + 1);
        else $this->links['next'] = false;
        // set last page
        $this->links['last'] = $func($link_template, $this->pages_num);
        // set mid pages only if pages_num > 5
        if($this->pages_num >=5) {
            $mid = ceil($this->pages_num / 2) - 1;
            $this->links['mid_1'] = $func($link_template, $mid);
            $this->links['mid_2'] = $func($link_template, $mid + 1);
            $this->links['mid_3'] = $func($link_template, $mid + 2);
        } else $this->links['mid_1']= $this->links['mid_2'] = $this->links['mid_3'] = false;
        // generate other links (int) if wanted
        // this is optional , because on big data say i billion it will make loop for one billion !! 
        if($generate_all === true)
            for($i=1; $i<=$this->pages_num; ++$i) $this->links[$i] = $func($link_template, $i);
    }
}
