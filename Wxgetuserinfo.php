<?php
/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2018-08-01 18:14:29
 * @version $Id$
 */
namespace app\index\controller;
use think\Db;
use think\Exception;
use app\common;
class Wxgetuserinfo extends Base {
    
    public function Wxgetuserinfo($open_id){

    	$access_token = $this->getToken();
    	//echo $access_token;
    	$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$open_id.'&lang=zh_CN';

    	$res = curl_http($url);
    	return $res;
    }

    public function getuserinfo($open_id){
    	$data =$this->Wxgetuserinfo($open_id);
    	
    	if(isset($data['errcode'])){
    		return '获取信息失败！';
    	}
    	if( $data['sex']==1){
    		$sex ='男';
    	}elseif($data['sex']==2){
    		$sex='女';
    	}else{
    		$sex='未知';
    	}
    	
    	$str = '您的信息如下：'.PHP_EOL.'昵称'.$data['nickname'];
    	$str .= PHP_EOL.'性别:'.$sex;
    	//$str .= PHP_EOL.'Open_id:'.PHP_EOL.$data['openid'];
    	$str .= PHP_EOL.'地方:'.$data['country'];
    	$str .= $data['province'];
    	$str .= $data['city'];
    	// $str .= PHP_EOL.'头像<img src="'.$data['headimgurl'].'"/>';
    	return  $str;
    }
}

