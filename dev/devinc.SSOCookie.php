<?php
/*
 * Copyright (c) 2009 SINA R&D Centre
 * All rights reserved
 *
 * File Name:	SSOCookie.class.php
 * Abstract:	sina sso cookie
 * Author:		lijunjie (junjie2@staff.sina.com.cn)
 * Modify:		2009-4-8
 * Version:		1.0
 */

class SSOCookie {
    const COOKIE_SUE = 'SUE';   //sina user encrypt info
    const COOKIE_SUP = 'SUP';   //sina user plain info
	//const COOKIE_KEY_FILE = '/data1/publish/cookie.conf';

	private $_error;
	private $_errno = 0;
	private $_arrConf; // the infomation in cookie.conf
    private static $_arrKeyMap = array(
        "cv"    => "cookieversion",
        "bt"    => "begintime",
        "et"    => "expiredtime",
        "uid"   => "uniqueid",
        "user"  => "userid",
        "ag"    => "appgroup",
        "nick"  => "displayname",
        "sex"   => "gender",
        "ps"    => "paysign",
    );

	public function __construct($config) {
		if(!$this->_parseConfigFile($config)){
			throw new Exception("parse config file failed");
		}
	}

    public function getCookie(&$arrUserInfo) {
        // ����������cookie������cookie��Ϊ��Ч
        if (!isset($_COOKIE[self::COOKIE_SUE])
            || !isset($_COOKIE[self::COOKIE_SUP])
        ) {
            $this->_setError('');
            //$this->_setError('not all cookie are exists ');

            return false;
        }

		parse_str($_COOKIE[self::COOKIE_SUE], $arrSUE);
		parse_str($_COOKIE[self::COOKIE_SUP], $arrSUP);

		foreach($arrSUP as $key => $val) {
            if(!array_key_exists($key, $this->_arrKeyMap)) {
                $this->_arrKeyMap[$key] = $key;
            }

			$arrUserInfo[$this->_arrKeyMap[$key]] = iconv("UTF-8", "GBk", $val);
		}

		// �ж��Ƿ�ʱ
		if($arrUserInfo['expiredtime'] < time()) {
			$this->_setError("cookie is timeout ");

            return false;
		}

		// ������cookie
        $str = $arrUserInfo['begintime']
            . $arrUserInfo['expiredtime']
            . $arrUserInfo['uniqueid']
            . $arrUserInfo['userid']
            . $arrUserInfo['appgroup']
            . $this->_arrConf[$arrSUE['ev']];

		if($arrSUE['es'] !== md5($str)) {
			$this->_setError("encrypt string error");

			return false;
		}

		unset($arrUserInfo['cookieversion']);

		return true;
	}

	public function getError() {
		return $this->_error;
	}
	public function getErrno() {
		return $this->_errno;
	}

	private function _parseConfigFile($config) {
		$arrConf = @parse_ini_file($config);
		if(!$arrConf) {
			return false;
		}
		$this->_arrConf = $arrConf;
		return true;
	}

	private function _setError($error,$errno=0) {
		$this->_error = $error;
		$this->_errno = $errno;
	}
}

?>

