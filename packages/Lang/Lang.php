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
 * Pharo Language Class
 *
 * This class enables you to register & translate words/phrases .
 *
 * @package		Pharo
 * @subpackage	Lang
 * @author		Mohammed Alashaal
 */
class Lang
{
    protected static $language = null;
    protected static $word_translations;
    protected static $phrase_translations;
    
    /**
     * Lang::set_language()
     * set the working language
     * @param string $language
     * @return void
     */
    public static function set_language($language)
    {
        self::$language = $language;
    }
    
    /**
     * Lang::add_phrase()
     * add phrase(s) and it's translation
     * @param mixed $phrase
     * @param string $translation
     * @return void
     */
    public static function add_phrase($phrase, $translation = null)
    {
        if(empty(self::$language)) trigger_error(__METHOD__ . ' (You must set the working language)');
        if(is_array($phrase))
            foreach($phrase as $p => &$v) self::add_phrase($p, $v);
        else
            if($translation === null) unset(self::$phrase_translations[$phrase]);
            else self::$phrase_translations[self::$language][$phrase] = $translation;
    }
    
    /**
     * Lang::unset_phrase()
     * remove a phrase
     * @param string $phrase
     * @return void
     */
    public static function unset_phrase($phrase)
    {
        unset(self::$phrase_translations[$phrase]);
    }
    
    /**
     * Lang::add_word()
     * add word(s) and it's translation
     * @param mixed $word
     * @param string $translation
     * @return void
     */
    public static function add_word($word, $translation = null)
    {
        if(empty(self::$language)) trigger_error(__METHOD__ . ' (You must set the working language)');
        if(is_array($word))
            foreach($word as $p => &$v) self::add_word($p, $v);
        else
            if($translation === null) unset(self::$word_translations[self::$language][$word]);
            else self::$word_translations[self::$language][$word] = $translation;
    }
    
    /**
     * Lang::unset_word()
     * remove a word
     * @param string $word
     * @return void
     */
    public static function unset_word($word)
    {
        unset(self::$word_translations[$word]);
    }
    
    /**
     * Lang::get_phrase()
     * get a phrase translation
     * @param string $phrase
     * @return string
     */
    public static function get_phrase($phrase)
    {
        if(empty(self::$language)) trigger_error(__METHOD__ . ' (You must set the working language)');
        return isset(self::$phrase_translations[self::$language][$phrase]) ? self::$phrase_translations[self::$language][$phrase] : null;
    }

    /**
     * Lang::get_word()
     * get a word translation
     * @param string $word
     * @return string
     */
    public static function get_word($word)
    {
        if(empty(self::$language)) trigger_error(__METHOD__ . ' (You must set the working language)');
        return isset(self::$word_translations[self::$language][$word]) ? self::$word_translations[self::$language][$word] : null;
    }
    
    /**
     * Lang::translate()
     * translate a subject
     * @param string $subject
     * @return string
     */
    public static function translate($subject)
    {
        if(empty(self::$language)) trigger_error(__METHOD__ . ' (You must set the working language)');
        $subject = str_ireplace(array_keys(self::$phrase_translations[self::$language]), array_values(self::$phrase_translations[self::$language]), $subject);
        return str_ireplace(array_keys(self::$word_translations[self::$language]), array_values(self::$word_translations[self::$language]), $subject);
    }
}