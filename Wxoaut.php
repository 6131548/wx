<?php
/**
 * 
 * @authors 网页授权 (you@example.org)
 * @date    2018-08-02 10:12:29
 * @version $Id$
 */
namespace app\index\controller;
use think\Db;
use think\Exception;
use app\common;
class Wxoauth extends Mp {
    //引导授权
    //当前url $_SERVER['HTTP_HOST'].SERVER['REQUEST_URL']
    public function wx_login(){
    	$userinfo = session('wx_userinfo');
    	if(!$userinfo){
    		$userinfo = $this->goOauth();
    		
    	}

    	
    	return $userinfo;
    }
    public function goOauth($scope = 'snsapi_userinfo'){
    	$code  = input('get.code');
    	//1引导授权

    	if(!input('get.code')){
    		//$cur_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];//回调地址
    		//$cur_url = 'http://sankoz.com/index.php/index/test/index';
    		$cur_url='http://www.sankoz.com';
    		$cur_url = urlencode($cur_url);

    		//return $cur_url;
    		
    		$api =  'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->AppID.'&redirect_uri='.$cur_url.'&response_type=code&scope='.$scope.'&state=sankoz#wechat_redirect';
    		// return $api;
    		return $this->redirect($api);
    		// echo $api;exit();
    		//  return redirect($api);
    		//return $data =curl_http($api);
    		// return $data;
    		//return $data;
    	}else{
    		
    		$state = input('get.state');
    		if($state!='sankoz'){
    			return '验证失败';
    			// exit('验证失败！');
    		}else{
    			$access_token = $this->_getOauthAccessToken($code);
    			if(isset($access_token['errcode'])){
    				exit($access_token['errmsg']);
    			}else{
    				//第三部，拉去用户信息
    				$userinfo = $this->_getuserinfo($access_token);

    				return $userinfo;
    			}
    		}

    	}
    	

    }


    	//取得网页授权token
    private function _getOauthAccessToken($code){
    	$api = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->AppID.'&secret='.$this->AppSecret.'&code='.$code.'&grant_type=authorization_code';
    	$res = curl_http($api);
    	return $res;
    }
    //取得用户信息
    private function _getuserinfo($access_token){
    	$api = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['access_token'].'&lang=zh_CN';
    	$userinfo = curl_http($api);
    	if(isset($userinfo['errcode'])){
    		exit($userinfo['errmsg']);
    	}else{
    		session('wx_userinfo',$userinfo);
    		return $userinfo;	
    	}

    }
}
