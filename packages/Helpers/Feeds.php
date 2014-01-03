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
 * simplexml_load_feed()
 * load feed, parse it and return it's nodes .
 * @param string $feed
 * @param bool $path
 * @return object
 */
function simplexml_load_feed($feed, $path = true)
{
    // get the feed data
    if($path) $feed = @file_get_contents($feed);
    // hide most errors, and fetch it only as needed
    libxml_use_internal_errors(true);
    // load the feed string
    $xml = simplexml_load_string($feed,null,LIBXML_NOCDATA+LIBXML_NOBLANKS);
    if(!is_object($xml)) return false;
    // if there is no obect, the feed faild, return false
    $namespaces = (array) $xml->getNamespaces();
    $namespaces[] = 'content';
    foreach($namespaces as &$v) {
        // remove <tagname:string> and </tagname:string>
        $old = array("<{$v}:", "</{$v}:", "&gt;{$v}:", "&gt;/{$v}:");
        $new = array("<{$v}_", "</{$v}_", "&gt;{$v}_", "&gt;/{$v}_");
        $feed = str_replace($old, $new, $feed);
    }
    $feed = $xml;
    unset($xml, $namespaces, $new, $old, $v);
    //die(print_r($feed));
    // return false if not feed
    if(!$feed) return false;
    // set temp array
    $r = array();
    // what is the channel, item(s)
    $channel = false;
    $items = false;
    // check feed type
    if(isset($feed -> channel -> item)) {$r['info']['type'] = 'rss'; $items = $feed -> channel -> item; $channel = $feed -> channel;}
    elseif(isset($feed -> entry)) {$r['info']['type'] = 'atom'; $items = $feed -> entry; $channel = $feed ;}
    elseif(isset($feed -> channel, $feed -> item)) {$r['info']['type'] = 'rdf'; $items = $feed -> item; $channel = $feed -> channel;}
    // end if not feed
    else return false;
    // set version if is set
    if(isset($feed['version'])) $r['info']['version'] = (string)$feed['version'];
    else $r['info']['version'] = false;
    /** --- grab page info --- */
    // set page title
    if(isset($channel -> title)) $r['info']['title'] = (string)$channel -> title;
    else $r['info']['title'] = false;
    // set the page description
    if(isset($channel -> description)) $r['info']['description'] = (string)$channel -> description;
    elseif(isset($channel -> subtitle)) $r['info']['description'] = (string)$channel -> subtitle;
    else $r['info']['description'] = false;
    // set the page url
    if(isset($channel -> link)) {
        foreach($channel -> link as $link) {
            if(isset($link['rel']) && strtolower($link['rel']) === 'alternate')
                {$r['info']['url'] = (string)$link['href']; break;}
            else {$r['info']['url'] = (string)$link;}
        }
    }
    else $r['info']['url'] = false;
    // set the page logo
    if(isset($channel -> image)) $r['info']['logo'] = (string)$channel -> image -> url;
    elseif(isset($channel -> icon)) $r['info']['logo'] = (string)$channel -> icon;
    else $r['info']['logo'] = false;
    // set the page author
    if(isset($channel -> author)) $r['info']['author'] = (string)$channel -> author;
    elseif(isset($channel -> managingEditor)) $r['info']['author'] = (string)$channel -> managingEditor;
    else $r['info']['author'] = false;
    // set the page lastUpdate
    if(isset($channel -> updated)) $r['info']['updated'] = (string)$channel -> updated;
    elseif(isset($channel -> lastBuildDate)) $r['info']['updated'] = (string)$channel -> lastBuildDate;
    elseif(isset($channel -> dc_date)) $r['info']['updated'] = (string)$channel -> dc_date;
    /** --- now grab items --- */
    // auto_increment int
    $id = 0;
    // let's loop !
    foreach($items as $item):
        // set the item id
        $r['items'][$id]['id'] = $id;
        // set item title
        if(isset($item -> title)) $r['items'][$id]['title'] = (string)$item -> title;
        else $r['items'][$id]['title'] = false;
        // set item url
        if(isset($item -> link)) {
            foreach($item -> link as $link):
                // if has rel alternate
                if(isset($link['rel']) && strtolower($link['rel']) === 'alternate') 
                    {$r['items'][$id]['url'] = (string)$link['href']; break;}
                // it is just a link
                else {$r['items'][$id]['url'] = (string)$link;}
            endforeach;
        }
        else $r['items'][$id]['url'] = false;
        // set guid
        if(isset($item -> guid)) $r['items'][$id]['guid'] = (string)$item -> guid;
        else $r['items'][$id]['guid'] = false;
        // set item description
        if(isset($item -> description)) $r['items'][$id]['description'] = (string)$item -> description;
        elseif(isset($item -> summary)) $r['items'][$id]['description'] = (string)$item -> summary;
        else $r['items'][$id]['description'] = false;
        // set content encoded
        if(isset($item -> content_encoded)) $r['items'][$id]['content'] = (string)$item -> content_encoded;
        else $r['items'][$id]['content'] = false;
        // set item lastUpdated
        if(isset($item -> update)) $r['items'][$id]['updated'] = (string)$item -> updated;
        elseif(isset($item -> pubDate)) $r['items'][$id]['updated'] = (string)$item -> pubDate;
        elseif(isset($item -> dc_date)) $r['items'][$id]['updated'] = (string)$item -> dc_date;
        else $r['items'][$id]['updated'] = false;
        // extract images
        $r['items'][$id]['images'] = array();
        if(isset($item -> image)) $r['items'][$id]['images'][] = (string)$item -> image -> url;
        elseif(isset($item -> logo)) $r['items'][$id]['images'][] = (string)$item -> logo;
        $content = $r['items'][$id]['description'] . $r['items'][$id]['content'];
        if(preg_match_all('~(.*?)img(.*?)src=[\'|"](.*?)[\'|"](.*?)~i', $content, $m)):
            $r['items'][$id]['images'] = array_merge($r['items'][$id]['images'], (array)$m[3]);
        endif;
        $r['items'][$id]['images'] = array_flip(array_unique(array_flip($r['items'][$id]['images'])));
        // add 1 to id
        ++ $id;
    endforeach;
    // free memory and return result
    unset($feed, $path, $channel, $content, $id, $item, $items, $m, $link);
    return (object)$r;
}

// --------------------------------------------------------------------------

/**
 * simplexml_export_feed()
 * generate Rss feed from array .
 * @param array $info
 * @param array $items
 * @return string
 */
function simplexml_export_feed(array $info, array $items)
{
    // title must be exists
    if(!isset($info['title'])) trigger_error(__METHOD__ . ' (You must give me title info)');
    // url must be exists 
    if(!isset($info['url'])) trigger_error(__METHOD__ . ' (You must give me url info)');
    // description must be exists
    if(!isset($info['description'])) $info['description'] = 'This content is generated by PHP Pharaoh';
    // encoding
    if(!isset($info['encoding'])) $encoding = 'UTF-8';
    else $encoding = $info['encoding'];
    $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\" ?>
            <rss version=\"2.0\"> <channel><title>{$info['title']}</title> 
            <link>{$info['url']}</link><description>{$info['description']}</description>";
    // if the logo index exists just create a tag for it .
    if(isset($info['logo'])) 
        $xml.= "<image><url>{$info['logo']}</url>
                <link>{$info['url']}</link>
                <title>{$info['title']}</title></image>";
    // generate item tags
    foreach($items as &$item):
        // item description must be exists
        if(!isset($item['description'])) trigger_error(__METHOD__ . ' (you must provide item description)');
        // item title must be exists
        if(!isset($item['title'])) trigger_error(__METHOD__ . ' (you must provide item title)');
        // item url must be exists
        if(!isset($item['url'])) trigger_error(__METHOD__ . ' (you must provide item url)');
        $xml .= "<item><title>{$item['title']}</title>
                <description><![CDATA[{$item['description']}]]></description>";
        if(isset($item['author'])) 
            $xml .= "<author>{$item['author']}</author>";
        if(isset($item['updated'])) 
            $xml .= '<pubDate>'.date("D, d M Y H:i:s T", strtotime($item['updated'])).'</pubDate>';
        $xml .= "<link>{$item['url']}</link></item>";
    endforeach;
    $xml .= '</channel></rss>';
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->preserveWhiteSpace = false;
    $dom->recover = true;
    $dom->formatOutput = true;
    $dom->loadXML($xml);
    $xml = $dom->saveXML();
    unset($dom, $encoding, $info, $item, $items);
    return $xml;
}