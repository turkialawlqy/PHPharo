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
     * @return string
     */
    function img($attrs)
    {
        return tag(array('img'), $attrs);
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

/* -------------------------------------------------------------- */

if(!function_exists('minify_css')):

    /**
     * minify_css()
     * Minify Css File/Content
     * @param mixed $source
     * @param bool $file
     * @return string
     */
    function minify_css($source, $file = true)
    {
        $c = ($file) ? file_get_contents($source) : $source;
        $c = preg_replace('|\s+|', ' ', $c);
        $c = preg_replace('|;}|', '}', $c);
        $c = preg_replace('~<style>|</style>~i', '', $c);
        $c =  $c ;
        return $c;
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('minify_js')):

    /**
     * minify_js()
     * Minify Javaswcript File
     * @param mixed $source
     * @param bool $file
     * @return string
     */
    function minify_js($source, $file = true)
    {
        $c = ($file) ? file_get_contents($source) : $source;
        $c = preg_replace('|\s+|', '', $c);
        $c = preg_replace('~<script(.*?)>|</script>~i', '', $c);
        $c = $c;
        return $c;
    }

endif;

/* -------------------------{From V1.0.0 RC3}------------------- */

if(!function_exists('html_get_src')):

    /**
     * html_get_src()
     * get array of src of imgs from subject
     * @param mixed $source
     * @return array
     */
    function html_get_src($source)
    {
        $pattren = '<img(.*?)src=["|\'](.*?)["|\'](.*?)>';
        preg_match_all("~{$pattren}~i", $source, $m);
        return (array)@$m[2];
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('html_get_href')):

    /**
     * html_get_href()
     * get array of href of anchors from subject
     * @param mixed $source
     * @return array
     */
    function html_get_href($source)
    {
        $pattren = '<a(.*?)href=["|\'](.*?)["|\'](.*?)>';
        preg_match_all("~{$pattren}~i", $source, $m);
        return (array)@$m[2];
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('html_form_factory')):

    /**
     * html_form_factory()
     * Build a html form easly
     * @param array $configs
     * @param array $inputs
     * @return string
     */
    function html_form_factory(array $configs = array(), array $inputs = array())
    {
        // configs (method, ....)
        if(!isset($configs['method'])) $configs['method'] = 'get';
        // inputs
        if(sizeof($inputs) < 1) die(PHPharo::Error(__FUNCTION__ . ': No Input Set ! .'));
        
        // here assign all attr and it`s value
        $_configs = '';
        foreach($configs as $attr => $val) $_configs .= " {$attr} = '{$val}' ";
        
        // here assign and create all inputs
        $_inputs = '';
        foreach($inputs as $input):
            $_input = '';
            
            // add any thing before starting the tag
            if(isset($input['before'])) {
                $_input .= $input['before'];
                unset($input['before']);
            }
            
            // input tag
            $_input .= ' <input ';
            foreach($input as $attr => $val) 
                $_input .= " {$attr} = '".preg_replace('~(\'|")~','',$val)."' ";
            $_input .= ' /> ' . PHP_EOL;
            
            // add any thing after closing the tag
            if(isset($input['after'])) {
                $_input .= $input['after'];
                unset($input['after']);
            }
            
            // add to inputs
            $_inputs .= "\t" . $_input;
        endforeach;
        
        // here generating final form
        $form = '<form '; // form tag
        $form.= $_configs; // attribiutes
        $form.= ' > ' . PHP_EOL; // end first form tag with new line
        $form.= $_inputs; // the inputs
        $form.= '</form>'; // end form tag
        
        // return the form
        return $form;
    }

endif;

/* ------------------------------------------------------------------- */

if(!function_exists('html_link_tag')):

    /**
     * html_link_tag()
     * Generate <link /> tag
     * @param string $href
     * @param string $rel
     * @param string $others
     * @return string
     */
    function html_link_tag($href, $rel, $others = '')
    {
        return "<link rel='{$rel}' href='{$href}' {$others} />" ;
    }

endif;

/* -------------------------------------------------------------------- */
