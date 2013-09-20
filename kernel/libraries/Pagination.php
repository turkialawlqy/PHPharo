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
 * Paginate
 * 
 * @package Pagination Class
 * @author GNU - GPL
 * @copyright 2013
 * @access public
 */
class Paginate
{

    public  $start = null;
    public  $limit = null; // number to show
    public  $pagination = null; // The links
    public  $pages = null; // the number of pages

    /**
     * Paginate::config()
     * 
     * @param string $adjacents
     * @param string $total_pages
     * @param string $targetpage
     * @param string $limit
     * @param string $separator
     * @return
     */
    public function config($adjacents = 2, $total_pages, $limit, $targetpage,
        $page_name = 'page', $current_page = "get[page]", $separator = '&', $equal = '=')
    {
        $page = $current_page;
        $page = addslashes(strip_tags($page));
        $page = preg_replace('/[^0-9]/i', '', $page);
        $page = trim($page);
        $this->limit = $limit;
        if ($page) {
            $start = ($page - 1) * $limit;
        } //first item to display on this page
        else {
            $start = 0;
        } //if no page var is given, set start to 0
        $this->start = $start;
        if ($page == 0)
            $page = 1; //if no page var is given, default to 1.
        $prev = $page - 1; //previous page is page - 1
        $next = $page + 1; //next page is page + 1
        $lastpage = ceil($total_pages / $limit); //lastpage is = total pages / items per page, rounded up.
        $this->pages = $lastpage;
        $lpm1 = $lastpage - 1; //last page minus 1
        $pagination = $this->pagination;
        if ($lastpage > 1) {
            $this->pagination .= "<div class=\"pagination\">";
            //previous button
            if ($page > 1)
                $this->pagination .= "<a href=\"{$targetpage}" . $separator . "{$page_name}{$equal}{$prev}\">« previous</a>";
            else
                $this->pagination .= "<span class=\"disabled\">« previous</span>";
            //pages
            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $this->pagination .= "<span class=\"current\">{$counter}</span>";
                    else
                        $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                //close to beginning; only hide later pages
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page)
                            $this->pagination .= "<span class=\"current\">$counter</span>";
                        else
                            $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                    }
                    $this->pagination .= "...";
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lpm1\">$lpm1</a>";
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lastpage\">$lastpage</a>";
                }
                //in middle; hide some front and some back
                elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}1\">1</a>";
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}2\">2</a>";
                    $this->pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page)
                            $this->pagination .= "<span class=\"current\">$counter</span>";
                        else
                            $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                    }
                    $this->pagination .= "...";
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lpm1\">$lpm1</a>";
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$lastpage\">$lastpage</a>";
                }
                //close to end; only hide early pages
                else {
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}1\">1</a>";
                    $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}2\">2</a>";
                    $this->pagination .= "...";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page)
                            $this->pagination .= "<span class=\"current\">$counter</span>";
                        else
                            $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$counter\">$counter</a>";
                    }
                }
            }
            //next button
            if ($page < $counter - 1)
                $this->pagination .= "<a href=\"$targetpage" . $separator . "{$page_name}{$equal}$next\">next »</a>";
            else
                $this->pagination .= "<span class=\"disabled\">next »</span>";
            $this->pagination .= "</div>\n";
        }
    }
    
    /**
     * Paginate::css()
     * 
     * @return void
     */
    public  function css()
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
     * Paginate::showLinks()
     * 
     * @return void
     */
    public  function showLinks()
    {
        echo $this->pagination;
    }
}
?>