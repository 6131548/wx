<?php
namespace app\index\controller;

use app\common\controller\Common;
use think\Db;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Base extends Common
{
	
	
	protected $AppID = 'wx3a105873dde06d5';
	protected $AppSecret ='a14486eeca602aa1c6e1dc508b5a46f9';
	protected $Token = 'token';
   //获取 $access_token
    public function getToken(){
   		$access_token  =  cache('access_token');
   		if(!$access_token){
   			$url   =  'https://api.weixin.qq.com/cgi-bin/token'  ;
	   		$param  =  [
	   		'grant_type'=>'client_credential',
	   		'appid' =>$this ->AppID,
	   		'secret' =>$this ->AppSecret];
	   		$res  =   getRequest($url,$param);
	   		$access_token = getNewAccess_token($res);
	   		cache('access_token',$access_token,7200);//保存缓存
   		}
   		
   		return $access_token;
   	}
   	//获取服务器IP
   	public function getserverIp(){
   		$api = 'https://api.weixin.qq.com/cgi-bin/getcallbackip';
   		$arr = ['access_token' => $this->getToken()];
   		$res  =   getRequest($api,$arr);
   		$ch  =  curl_init ();
 
	    // 设置URL和相应的选项
	    curl_setopt ( $ch , CURLOPT_URL ,  $res );
	    curl_setopt ( $ch , CURLOPT_HEADER ,  false );
	    curl_setopt ( $ch , CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt ( $ch , CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt ( $ch , CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt ( $ch , CURLOPT_SSLVERSION, 1);
	 
	    // 抓取URL并把它传递给浏览器
	    $str = curl_exec ( $ch );
	 
	    //关闭cURL资源，并且释放系统资源
	    curl_close ( $ch );
	    // echo $str;
	 
	    //将str->数组
	    $arr = json_decode($str , true);
   		return $arr;
   	}

}
