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
 * simplexmli_load_string()
 * simplexml(improved) function
 * @param mixed $data
 * @param string $class_name
 * @param integer $options
 * @return false / object
 */
function simplexmli_load_string($data, $class_name = 'SimpleXMLElement', $options = LIBXML_NOCDATA)
{
    $data = str_replace('content:encoded>', 'content>', $data);
	libxml_use_internal_errors(true);
	$dom = new DomDocument;
	$dom->recover = true;
	@$dom->loadXML($data);
	$data = $dom->saveXML();
	$xml = simplexml_load_string($data, $class_name, $options);
	return json_decode(json_encode($xml));
}

/**
 * simplexmli_load_file()
 * simplexml(improved) function
 * @param mixed $filename
 * @param string $class_name
 * @param integer $options
 * @return false / object
 */
function simplexmli_load_file($filename, $class_name = 'SimpleXMLElement', $options = LIBXML_NOCDATA)
{
	libxml_use_internal_errors(true);
	@$xml = simplexml_load_file($filename, $class_name, $options);
	return json_decode(json_encode($xml));
}

/**
 * simplexmli_parse_feed()
 * simplexml(improved) based function
 * to parse any type of feed
 * it will use simplexmli_parse_* to detect
 * and use the right one
 * @param mixed $data
 * @return false / (object/array)
 */
function simplexmli_parse_feed($data, $fetch_type = 'assoc', $class_name = 'SimpleXMLElement', $options = 16640 )
{
    if(($feed = simplexmli_parse_atom($data, $fetch_type, $class_name, $options)) !== false)
        return $feed;
    elseif(($feed = simplexmli_parse_rss($data, $fetch_type, $class_name, $options)) !== false)
        return $feed;
    elseif(($feed = simplexmli_parse_rdf($data, $fetch_type, $class_name, $options)) !== false)
        return $feed;
	
}

/**
 * simplexmli_parse_rss()
 * simplexml(improved) function that will 
 * only parse rss feeds type
 * @param mixed $data
 * @param string $fetch_type
 * @return false / (object/array)
 */
function simplexmli_parse_rss($data, $fetch_type = 'assoc',  $class_name = 'SimpleXMLElement', $options = LIBXML_NOCDATA)
{
	$xml = simplexmli_load_string($data, $class_name, $options);
	// if error return false
		if(!$xml) return false; //var_dump($xml->channel);exit;
	// temp var to save results
		$result = array();
	// Continue if RSS
		if(isset($xml->channel, $xml->channel->item)) {
			// set the site feed type
				$result['info']['type'] = 'rss';
			// set the site title
				if(isset($xml->channel->title)) $result['info']['title'] = $xml->channel->title;
				else $result['info']['title'] = 'Website Title Not Found';
			// set the site description
				if(isset($xml->channel->description)) $result['info']['description'] = $xml->channel->description;
				else $result['info']['description'] = 'Website Description Not Found';
			// set the site image
				if(isset($xml->channel->image, $xml->channel->image->url)) $result['info']['image'] = $xml->channel->image->url;
			// set site link
				if(isset($xml->channel->link)) $result['info']['link'] = $xml->channel->link;
			// posts
				$i=1;
				if(!empty($xml->channel->item)):
					$items = $xml->channel->item;
					foreach($items as $item):
						$item_n =  'post_' . $i;
						// title
							if(isset($item->title)) $result['posts'][$item_n]['title'] = $item->title;
							else $result['posts'][$item_n]['title'] = 'No Title';
						// link
							if(isset($item->guid)) {
								if(isset($link->isPermaLink)) {
									if($link->isPermaLink) {
										if(preg_match('#$http(.*?)://(.*?)$#i', $item->guid)) {
											$result['posts'][$item_n]['link'] = $item->guid;
										} elseif(isset($item->link)) $result['posts'][$item_n]['link'] = $item->link;
									}
									elseif(isset($item->link))  {
										$result['posts'][$item_n]['link'] = $item->link;
									}
								} elseif(preg_match('#$http(.*?)://(.*?)$#i', $item->guid)) {
									 $result['posts'][$item_n]['link'] = $item->guid;
								} elseif(isset($item->link)) $result['posts'][$item_n]['link'] = $item->link;
							}
							elseif(isset($item->link)) $result['posts'][$item_n]['link'] = $item->link;
						// custom id
							$result['posts'][$item_n]['id'] = $i;
						// content
							if(isset($item->content)) $result['posts'][$item_n]['content'] = $item->content;
							elseif(isset($item->description)) $result['posts'][$item_n]['content'] = $item->description;
							elseif(isset($item->summary)) $result['posts'][$item_n]['content'] = $item->summary;
							else $result['posts'][$item_n]['content'] = 'No Content';
						++$i;
					endforeach;
				endif;
			// Finally, Return The Result
				return ($fetch_type == 'assoc' || $fetch_type == 'array') ? $result : json_decode(json_encode($result));
		} 
	// Not Rss
		else
			return false;
}

/**
 * simplexmli_parse_atom()
 * simplexml(improved) function that will 
 * only parse atom feeds type
 * @param mixed $data
 * @param string $fetch_type
 * @return false / (object/array)
 */
function simplexmli_parse_atom($data, $fetch_type = 'assoc', $class_name = 'SimpleXMLElement', $options = LIBXML_NOCDATA)
{
	$xml = simplexmli_load_string($data, $class_name, $options);
	// if error return false
		if(!$xml) return false;
	// temp var to save results
		$result = array();
	// Continue if atom
		if(isset($xml->entry)) {
			// set the site feed type
				$result['info']['type'] = 'atom';
			// set the site title
				if(isset($xml->title)) $result['info']['title'] = $xml->title;
				else $result['info']['title'] = 'Website Title Not Found';
			// set the site description
				if(isset($xml->description)) $result['info']['description'] = $xml->description;
				elseif(isset($xml->subtitle)) $result['info']['description'] = $xml->subtitle;
				else $result['info']['description'] = 'Website Description Not Found';
			// set the site image
				if(isset($xml->image, $xml->image->url)) $result['info']['image'] = $xml->image->url;
			// set site link
				if(isset($xml->link))
					foreach($xml->link as $link):
						if(isset($link->{'@attributes'})) $link = $link->{'@attributes'};
						if(isset($link->rel) && $link->rel == 'alternate')
							$result['info']['link'] = $link->href;
						elseif(!isset($link->rel)) 
							$result['info']['link'] = $link->href;
					endforeach;
			// posts
				$i=1;
				if(!empty($xml->entry)):
					$items = $xml->entry;
					foreach($items as $item):
						$item_n =  'post_' . $i;
						// title
							if(isset($item->title)) $result['posts'][$item_n]['title'] = $item->title;
							else $result['posts'][$item_n]['title'] = 'No Title';
						// link
							if(isset($item->guid)) $result['posts'][$item_n]['link'] = $item->guid;
							elseif(isset($item->link))
							foreach($item->link as $link):
								if(isset($link->{'@attributes'})) $link = $link->{'@attributes'};
								if(isset($link->rel) && $link->rel == 'alternate')
									$result['posts'][$item_n]['link'] = $link->href;
								elseif(!isset($link->rel))
									$result['post'][$item_n]['link'] = $link->href;
							endforeach;
						// custom id
							$result['posts'][$item_n]['id'] = $i;
						// content
							if(isset($item->content)) $result['posts'][$item_n]['content'] = $item->content;
							elseif(isset($item->description)) $result['posts'][$item_n]['content'] = $item->description;
							elseif(isset($item->summary)) $result['posts'][$item_n]['content'] = $item->summary;
							else $result['posts'][$item_n]['content'] = 'No Content';
						++$i;
					endforeach;
				endif;
			// Finally, Return The Result
				return ($fetch_type == 'assoc' || $fetch_type == 'array') ? $result : json_decode(json_encode($result));
		} 
	// Not Atom
		else
			return false;
}

/**
 * simplexmli_parse_rdf()
 * simplexml(improved) function that will 
 * only parse rdf feeds type
 * @param mixed $data
 * @param string $fetch_type
 * @return false / (object/array)
 */
function simplexmli_parse_rdf($data, $fetch_type = 'assoc', $class_name = 'SimpleXMLElement', $options = LIBXML_NOCDATA)
{
	$xml = simplexmli_load_string($data, $class_name, $options);
	// if error return false
		if(!$xml) return false;
	// temp var to save results
		$result = array();
	// Continue if RDF
		if(isset($xml->channel, $xml->item)) {
			// set the site feed type
				$result['info']['type'] = 'rss';
			// set the site title
				if(isset($xml->channel->title)) $result['info']['title'] = $xml->channel->title;
				else $result['info']['title'] = 'Website Title Not Found';
			// set the site description
				if(isset($xml->channel->description)) $result['info']['description'] = $xml->channel->description;
				else $result['info']['description'] = 'Website Description Not Found';
			// set the site image
				if(isset($xml->channel->image, $xml->channel->image->url)) $result['info']['image'] = $xml->channel->image->url;
			// set site link
				if(isset($xml->channel->link)) $result['info']['link'] = $xml->channel->link;
			// posts
				$i=1;
				if(!empty($xml->item)):
					$items = $xml->item;
					foreach($items as $item):
						$item_n =  'post_' . $i;
						// title
							if(isset($item->title)) $result['posts'][$item_n]['title'] = $item->title;
							else $result['posts'][$item_n]['title'] = 'No Title';
						// link
							if(isset($item->guid)) {
								if(isset($link->isPermaLink)) {
									if($link->isPermaLink) {
										if(preg_match('#$http(.*?)://(.*?)$#i', $item->guid)) {
											$result['posts'][$item_n]['link'] = $item->guid;
										} elseif(isset($item->link)) $result['posts'][$item_n]['link'] = $item->link;
									}
									elseif(isset($item->link))  {
										$result['posts'][$item_n]['link'] = $item->link;
									}
								} elseif(preg_match('#$http(.*?)://(.*?)$#i', $item->guid)) {
									 $result['posts'][$item_n]['link'] = $item->guid;
								} elseif(isset($item->link)) $result['posts'][$item_n]['link'] = $item->link;
							}
							elseif(isset($item->link)) $result['posts'][$item_n]['link'] = $item->link;
						// custom id
							$result['posts'][$item_n]['id'] = $i;
						// content
							if(isset($item->content)) $result['posts'][$item_n]['content'] = $item->content;
							elseif(isset($item->description)) $result['posts'][$item_n]['content'] = $item->description;
							elseif(isset($item->summary)) $result['posts'][$item_n]['content'] = $item->summary;
							else $result['posts'][$item_n]['content'] = 'No Content';
						++$i;
					endforeach;
				endif;
			// Finally, Return The Result
				return ($fetch_type == 'assoc' || $fetch_type == 'array') ? $result : json_decode(json_encode($result));
		} 
	// Not Rss
		else
			return false;
}

/**
 * simplexmli_is_feed()
 * Check if a string is feed or not
 * @param mixed $content
 * @return bool
 */
function simplexmli_is_feed($content)
{
    return (bool)(!(simplexmli_parse_atom($content) == false 
            && simplexmli_parse_rss($content) == false 
            && simplexmli_parse_rdf($content) == false));
}