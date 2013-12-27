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
 * Pharo SMTP Class .
 *
 * This class enables you to manage and send mails over smtp easily .
 *
 * @package		SMTP
 * @author		Mohammed Alashaal
 */
 
class SMTP
{
    /** -- requirements -- */
    var $username = false;               // auth username if required
    var $password = false;               // auth password if required
    var $server = 'localhost';           // smtp server
    var $port = 587;                   // smtp server port
    var $tls = false;                    // start tls ?
    var $timeout = 3000;               // connection timeout
    var $me = 'localhost';               // current server, domain, ip
    
    /** -- mail fields -- */
    var $to = false;
    var $from = 'admin@localhost.com';
    var $subject = '';
    var $bcc = false;
    var $message = 'this is message from phpharo smtp mailer';
    var $headers = array();
    
    /** -- class configs -- */
    private $handle = false;
    var $states = array();
    var $last_response = '';
    var $new_line = PHP_EOL;
    var $wordwrap = false;
    var $full_request = '';
    
    /**
     * SMTP::connect()
     * start smtp connection
     * @return void
     */
    public function connect()
    {
        /** if not connected, connect, else continue .. */
        if($this->handle == false):
            /** check server addr */
            if(preg_match('~(ssl:\/\/|tls:\/\/)~i', $this->server) || $this->port == '465'):
                // enable tls
                $this->tls = true;
                // re-build server addr
                $this->server = preg_replace('~(ssl:\/\/|tls:\/\/)~i', '', $this->server);
            endif;
            /** start socket connection */
            $h = @fsockopen($this->server, $this->port, $e_n, $e_s, $this->timeout);
            /** if socket not connected die with error */
            if(!$h) die($this->error($e_s));
            /** if connected , set configs */
            else {
                $this->handle = $h;
                stream_set_timeout($this->handle, $this->timeout);
                $this->states['connection started'] = 'yes';
                $this->states['server state'] = fgets($this->handle);
                $this->cmd('hello', 'HELO ' . $this->me);
                if($this->tls !== false):
                    $this->cmd('start tls', 'STARTTLS');
                    $this->cmd('hello 2','HELO' . $this->me);
                endif;
                $this->headers['MIME-Version'] = '1.0';
                $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
                $this->headers['Content-Transfer-Encoding'] = '7bit';
                return true;
            }
        endif;
    }
    
    /**
     * SMTP::disconnect()
     * disconnect from smtp server
     * @return true
     */
    public function disconnect()
    {
        /** close connection */
        fclose($this->handle);
        /** set handle to false */
        $this->handle = false;
        return true;
    }
        
    /**
     * SMTP::cmd()
     * execute smtp command
     * @param mixed $title
     * @param mixed $cmd
     * @return command result
     */
    public function cmd($title, $cmd)
    {
        /** die if we didn\'t connected */
        if(!$this->handle) die($this->error('You didn\'t connect the smtp'));
        /** prepare the cmd */
        $_CMD = $cmd . $this->new_line;
        /** put request */
        @fputs($this->handle, $_CMD);
        /** get result */
        $s = fgets($this->handle);
        /** save result to states array */
        $this->states[strtolower($title)] = $s;
        /** save rsult to last response */
        $this->last_response = $s;
        /** save cmd at full request */
        $this->full_request .= $_CMD;
        /** return result */
        return $s;
    }
    
    /**
     * SMTP::send_mail()
     * send an email
     * @return bool
     */
    public function send_mail()
    {
        /** is to set */
        if(!$this->to) die($this->error('you didn\'t set `to` feild'));
        /** need auth ? */
        if($this->username !== false && $this->password !== false):
            $this->cmd('login', 'AUTH LOGIN');
            $this->cmd('user name', base64_encode($this->username));
            $this->cmd('password', base64_encode($this->password));
        endif;
        /** basic requests */
        $this->cmd('mail-from', 'Mail From: <'.$this->from.'>');
        $this->cmd('rcpt-to', 'RCPT To: <'.$this->to.'>');
        $this->cmd('data', 'DATA');
        /** prepare headers */
        $this->headers['FROM'] = $this->from;
        $this->headers['To'] = $this->to;
        if($this->bcc !== false) $this->headers['Bcc'] = $this->bcc;
        $this->headers['Subject'] = $this->subject;
        if($this->wordwrap !== false) $this->message = wordwrap($this->message, $this->wordwrap, true, false);
        /** merge headers */
        $hdrs = '';
        foreach( $this->headers as $k => $v ) $hdrs .= $this->ucfirst($k) . ': ' . $v . $this->new_line ;
        $hdrs = $hdrs . $this->new_line . $this->message . $this->new_line . '.';
        /** execute & send headers */
        $this->cmd('headers', $hdrs);
        $this->cmd('quit', 'QUIT');
        $this->states['connection closed'] = 'yes';
        $sent = (bool) (substr($this->last_response, 0, 3) == '250' || substr($this->last_response, 0, 3) == '221');
        /** closing connection */
        $this->disconnect();
        return $sent;
    }
    
    /**
     * SMTP::ucfirst()
     * uppercase first char in every words separated by -
     * @param string $txt
     * @return string
     */
    public function ucfirst($txt)
    {
        $x = explode('-', $txt);
        $t = null;
        foreach($x as $v) $t[]= ucfirst($v);
        return implode('-', $t);
    }
    
    /**
     * SMTP::error()
     * error handler
     * @param mixed $str
     * @return string
     */
    public function error($str)
    {
        $css = 'padding:15px;background:#333;color:greenyellow;font-weight:bolder;margin:auto;width:400px;box-shadow:0 0 8px #333';
        return '<div style="'.$css.'"> ' . strtoupper(__CLASS__) . ' error ocurred: ' . $str . '</div>';
    }
}