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
 * MyFB
 * simple facebook api wrapper
 * @package PHPharo
 * @author mohammed abdullah alashaal
 * @copyright 2013
 * @version 0.1
 * @access public
 */
class MyFB
{

	protected static $access_token;
	protected static $app_info;
    protected static $logged_in = false;
	
	/**
	 * MyFB::config()
	 * set the class configs
	 * @param string $app_id: application id
	 * @param string $app_secret: application secret key
	 * @return string: access_token
	 */
	public static function config($app_id, $app_secret, $login_redirect = '', $scope = 'email')
	{
		self::$app_info = array('id'=>$app_id, 'secret'=>$app_secret, 'scope' => $scope, 'redirect' => $login_redirect);
		self::$access_token =  self::_get_access_token();
        return self::$access_token;
	}

    /**
     * MyFB::login()
     * log the app into facebook user account
     * @param string $redirect_to
     * @param string $scope
     * @return bool
     */
    public static function login()
    {
        $app_id = self::$app_info['id'];
        $app_secret = self::$app_info['secret'];
        $scope = self::$app_info['scope'];
        $redirect_to = self::$app_info['redirect'];
        $redirect_to = $redirect_to . (((strpos('?',$redirect_to) !== false) ? '&logged_in=true' : '?logged_in=true'));
        // facebook api-auth
        $url = "https://www.facebook.com/dialog/oauth?display=popup&client_id={$app_id}&scope={$scope}&redirect_uri={$redirect_to}";
 
        if(isset($_GET['logged_in']) && $_GET['logged_in'] && isset($_GET['code'])):
            // facebook api-exchange_login_code_with_access_token
            $u2 = "https://graph.facebook.com/oauth/access_token?client_id={$app_id}&redirect_uri={$redirect_to}&client_secret={$app_secret}&code={$_GET['code']}";
            $d = self::_Qfetch($u2);
            if(preg_match('~^(.*?)=(.*?)&(.*?)$~',$d,$m)){
                self::$logged_in = true;
                self::$access_token = $m[2];
                return true;
            } else
                return false;
        endif;
        header('Location: '.$url);
        exit;
    }
    
    /**
     * MyFB::login_link()
     * get login link for permisions
     * @return string
     */
    public static function login_link()
    {
        $app_id = self::$app_info['id'];
        $app_secret = self::$app_info['secret'];
        $scope = self::$app_info['scope'];
        $redirect_to = self::$app_info['redirect'];
        $redirect_to = $redirect_to . (((strpos('?',$redirect_to) !== false) ? '&logged_in=true' : '?logged_in=true'));
        // facebook api-auth
        $url = "https://www.facebook.com/dialog/oauth?display=popup&client_id={$app_id}&scope={$scope}&redirect_uri={$redirect_to}";
        return $url;
    }
    
	/**
	 * MyFB::get()
	 * get an object from facebook graph
	 * @param string $path
	 * @return object / false
	 */
	public static function get($path)
	{
		$access_token = self::$access_token;
		$url = "https://graph.facebook.com/{$path}"
        . ((strpos($path, '?') !== false) ? '&' : '?')
        . "access_token={$access_token}";
        $result = self::_Qfetch($url);
        return (($result !== false) ? json_decode($result) : $result);
	}

    /**
     * MyFB::publish()
     * publish post,like,comment,... etc
     * @param string $path
     * @param string $data
     * @return object / false
     */
    public static function publish($path, array $data)
    {
        $url = "https://graph.facebook.com/$path";
        $data['access_token'] = self::$access_token;
        $result = self::_Qpost($url,$data);
        return (($result !== false) ? json_decode($result) : $result);
    }
    
    /**
     * MyFB::is_logged()
     * check if myfb is logged_in
     * @return bool
     */
    public static function is_logged()
    {
        return self::$logged_in;
    }
    /* ------------------------------------------------ */

	/**
	 * MyFB::_get_access_token()
	 * 
	 * @return
	 */
	protected static function _get_access_token()
	{
		$app_id = self::$app_info['id'];
		$app_secret = self::$app_info['secret'];
		$url = "https://graph.facebook.com/oauth/access_token?client_id={$app_id}&client_secret={$app_secret}&grant_type=client_credentials";
		$r = self::_Qfetch($url);
		$x = preg_match('#^(.*?)=(.*?)$#i', $r, $m);
		if((bool)$x)
			{self::$access_token = $m[2] ;return $m[2];}
		else
			return false;
	}
	
	/**
	 * MyFB::_Qfetch()
	 * 
	 * @param mixed $url
	 * @return
	 */
	protected static function _Qfetch($url)
	{
		$opts = array(CURLOPT_FOLLOWLOCATION => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYHOST => false,
					  CURLOPT_SSL_VERIFYPEER => false);
		$ch = curl_init($url);
		curl_setopt_array($ch, $opts);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}
    
    /**
     * MyFB::_Qpost()
     * 
     * @param mixed $url
     * @param mixed $data
     * @return
     */
    protected static function _Qpost($url, array $data)
    {
		$opts = array(CURLOPT_FOLLOWLOCATION => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYHOST => false,
					  CURLOPT_SSL_VERIFYPEER => false, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $data);
		$ch = curl_init($url);
		curl_setopt_array($ch, $opts);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
    }
}