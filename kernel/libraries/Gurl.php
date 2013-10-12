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

if(!function_exists('Gurl')):

    /**
     * Gurl()
     * Gurl is a powerful cURL based function
     * that considers better that cURL it self !
     * @param string $url
     * @param array $curl_opts
     * @param internal $tmp
     * @author <http://fb.me/alash3al>|<http://twitter.com/PHPharo>
     * @return array of header, content, info
     */
    function Gurl($url, array $curl_opts = array(), $tmp = null)
    {
        // prepare tmp
        $tmp['curl_opts'] = $curl_opts;
        if(!isset($tmp['count']))
            $tmp['count'] = 0;
        if(!isset($tmp['last_url']))
            $tmp['last_url'] = '';
        /** ------------------------------------------------------------ */
        // prepare cURL Options
        if(isset($curl_opts[CURLOPT_FOLLOWLOCATION])) {
            if($curl_opts[CURLOPT_FOLLOWLOCATION])
                $follow_location = true;
            else 
                $follow_location = false;
            unset($curl_opts[CURLOPT_FOLLOWLOCATION]);
        } else
            $follow_location = false;
        if(isset($curl_opts[CURLOPT_RETURNTRANSFER]))
            unset($curl_opts[CURLOPT_RETURNTRANSFER]);
        if(isset($curl_opts[CURLOPT_HEADER]))
            unset($curl_opts[CURLOPT_HEADER]);
        if(isset($curl_opts[CURLOPT_MAXREDIRS])) {
            $max_redirs = $curl_opts[CURLOPT_MAXREDIRS];
            unset($curl_opts[CURLOPT_MAXREDIRS]);
        } else
            $max_redirs = 20;
        /** ------------------------------------------------------------ */
        // cURL
        $ch = curl_init($url);
        $opts = array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; GurlBot)',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => true);
        $curl_opts = array_filter($curl_opts);
        // merge curl_opts with opts
        // note: array_merge will edit keys to 1,2,3,4 ... 
        // and change the options_keys
        if(!empty($curl_opts)):
            foreach($curl_opts as $k => $v):
                $opts[$k] = $v;
            endforeach;
        endif;
        curl_setopt_array($ch, $opts);
        $result['content'] = curl_exec($ch);
        $x = explode(PHP_EOL . PHP_EOL, $result['content'], 2);
        @$result['content'] = $x[1];
        @$result['header'] = $x[0];
        $result['info'] = curl_getinfo($ch);
        curl_close($ch);
        /** ------------------------------------------------------------ */
        // if redirecting, set reirect_url
        if (!isset($result['info']['redirect_url']) && $result['info']['http_code'] !== '200'):
            // http redirect
            if (preg_match("#^Location:(\S*)$#i", $result['header'], $m)):
                $result['info']['redirect_url'] = $m[1];
            endif;
            // html redirect
            if (preg_match("/meta(.*?)http-equiv=['|\"]refresh['|\"](.*?)content=[\"|'](.*?);(.*?)[\"|'](.*?)/i",
                $result['content'], $m)):
                $result['info']['redirect_url'] = preg_replace('#url=#i', '', $m[4], 1);
            endif;
            // javascript redirect
            if (preg_match('#(.*?)location(.*?)=(.*?)[\'|"](.*?)[\'|"]#i', $result['content'], $m)):
                $result['info']['redirect_url'] = $m[4];
            endif;
        endif;
        /** ------------------------------------------------------------ */
        // prepare redirect_url
        if (!isset($result['info']['redirect_url'])):
            $result['info']['redirect_url'] = '';
        endif;
        $result['info']['redirect_url'] = preg_replace("#\s#", '', $result['info']['redirect_url']);
        $result['info']['redirect_count'] = $tmp['count'];
        /** ------------------------------------------------------------ */
        // redirects
        if ($result['info']['redirect_url'] !== ''):
            if ($follow_location):
                if($tmp['count'] <= $max_redirs):
                    ++$tmp['count'];
                    $tmp['last_url'] = $result['info']['redirect_url'];
                    $func = __FUNCTION__ ;
                    $result = $func($result['info']['redirect_url'], $tmp['curl_opts'], $tmp);
                endif;
            endif;
        endif;
        /** ------------------------------------------------------------ */
        // the last visted url
        $result['info']['last_url'] = $tmp['last_url'];
        // result
        //$x = explode(PHP_EOL . PHP_EOL, $result['content'], 2);
        //@$result['content'] = $x[1];
        //@$result['header'] = $x[0];
        return $result;
    }

endif;