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
 * Pagination
 * this was a basic code on
 * {@link}http://www.strangerstudios.com/sandbox/pagination/diggstyle.php
 * and Converted to OOP By Mohammed Abdullah Alashaal {@link}<fb.me/alash3al>
 * @package Pagination Class
 * @author GNU - GPL
 * @copyright 2013
 * @access public
 */
class Pagination
{

    protected static $start = null;
    protected static $limit = null; // number to show
    protected static $pagination = null; // The links
    protected static $pages = null; // the number of pages

    /**
     * Pagination::config()
     * config pagination vars
     * @param string $adjacents
     * @param string $total_results
     * @param string $targetpage
     * @param string $limit
     * @param string $separator
     * @return void
     */
    public static function config($adjacents = 2, $total_results, $limit, $targetpage,
        $page_name = 'page', $current_page = "get[page]", $separator = '&', $equal = '=')
    {
        $total_pages = $total_results;
        $page = $current_page;
        $page = addslashes(strip_tags($page));
        $page = preg_replace('/[^0-9]/i', '', $page);
        $page = trim($page);
        self::$limit = $limit;
        if ($page) {
            $start = ($page - 1) * $limit;
        } //first item to display on this page
        else {
            $start = 0;
        } //if no page var is given, set start to 0
        self::$start = $start;
        if ($page == 0)
            $page = 1; //if no page var is given, default to 1.
        $lastpage = ceil($total_pages / $limit); //lastpage is = total pages / items per page, rounded up.
        self::$pages = $lastpage;
        if($page > $lastpage) $page = $lastpage;
        $prev = $page - 1; //previous page is page - 1
        $next = $page + 1; //next page is page + 1
        $lpm1 = $lastpage - 1; //last page minus 1
        $pagination = self::$pagination;
        if ($lastpage > 1) {
            self::$pagination .= "<div class=\"pagination\">";
            //previous button
            if ($page > 1)
                self::$pagination .= "<a href=\"{$targetpage}" . $separator . "{$page_name}{$equal}{$prev}\">« previous</a>";
            else
                self::$pagination .= "<span class=\"disabled\">« previous</span>";
            //pages
            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        self::$pagination .= "<span class=\"current\">{$counter}</span>";
                    else
                        self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                //close to beginning; only hide later pages
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page)
                            self::$pagination .= "<span class=\"current\">$counter</span>";
                        else
                            self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                    }
                    self::$pagination .= "...";
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lpm1\">$lpm1</a>";
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lastpage\">$lastpage</a>";
                }
                //in middle; hide some front and some back
                elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}1\">1</a>";
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}2\">2</a>";
                    self::$pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page)
                            self::$pagination .= "<span class=\"current\">$counter</span>";
                        else
                            self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                    }
                    self::$pagination .= "...";
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lpm1\">$lpm1</a>";
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lastpage\">$lastpage</a>";
                }
                //close to end; only hide early pages
                else {
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}1\">1</a>";
                    self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}2\">2</a>";
                    self::$pagination .= "...";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page)
                            self::$pagination .= "<span class=\"current\">$counter</span>";
                        else
                            self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                    }
                }
            }
            //next button
            if ($page < $counter - 1)
                self::$pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$next\">next »</a>";
            else
                self::$pagination .= "<span class=\"disabled\">next »</span>";
            self::$pagination .= "</div>\n";
        }
    }
    
    /**
     * Pagination::css()
     * echo default pagination css `digg style`
     * @return void
     */
    public static function css()
    {
        ?>
            div.pagination{padding:3px;margin:3px}
            div.pagination a{padding:2px 5px 2px 5px;margin:2px;border:1px solid #AAAADD;text-decoration:none;color:#000099}
            div.pagination a:hover,div.pagination a:active{border:1px solid #000099;color:#000}
            div.pagination span.current{padding:2px 5px 2px 5px;margin:2px;border:1px solid #000099;font-weight:bold;background-color:#000099;color:#FFF}
            div.pagination span.disabled{padding:2px 5px 2px 5px;margin:2px;border:1px solid #EEE;color:#DDD}
        <?php
    }

    /**
     * Pagination::showLinks()
     * get the links to pages
     * @return void
     */
    public static function showLinks()
    {
        return self::$pagination;
    }
    
    /**
     * Pagination::pages_num()
     * get the pages number
     * @return int
     */
    public static function pages_num()
    {
        return self::$pages;
    }
    
    /**
     * Pagination::start()
     * get the start from current state
     * @return int
     */
    public static function start()
    {
        return self::$start;
    }
    
    /**
     * Pagination::limit()
     * get the pagination limit of current state
     * @return int
     */
    public static function limit()
    {
        return self::$limit;
    }
}
?>