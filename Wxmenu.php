<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
/**
 * 
 * @authors 删除或者添加、修改菜单 (you@example.org)
 * @date    2018-08-01 15:27:42
 * @version $Id$
 */

class   Wxmenu extends Base
{	
	public function delMenu(){
		$access_token  =  $this->getToken();
    		$url    =  'https://api.weixin.qq.com/cgi-bin/menu/delete';
    		$param  =  [
	   			'access_token'=>$access_token
	   			   		];
	   		$res  =   getRequest($url,$param);
    		
    		$res = curl_http($res);
    		//var_dump($res);exit();
    		
    		if($res['errcode'] == 0){
    			exit('删除成功!'); 
    		}else{
    			exit('删除失败!'); 
    		}
	}
	//创建菜单
	public function createMenu(){
		$access_token = $this->getToken(); //获取缓存
		//var_dump(cache('access_token'));
		$url   =    'https://api.weixin.qq.com/cgi-bin/menu/create';
		$param  =  [
	   			'access_token'=>$access_token
	   			   		];
	   	$res  =   getRequest($url,$param);		   		
		$menu_config =array(
			'button' => array(
				array('type' => 'view' , 'name' => '我要定制' , 'url' => "http://sankoz.com/"),
				array('type' => 'view' , 'name' => '申请代理' , 'url' => "http://sankoz.com/"),
				array('type' => 'view' , 'name' => '个人中心' , 'url' => "http://sankoz.com/"),

				// array('type' => 'click' , 'name' => '图文' , 'key' => '图文'),
				//array('type' => 'view' , 'name' => '申请代理' , 'url' => "http://sankoz.com/"),
				//array(
				// 	'name' => '官网',
				// 	'sub_button' => array(
				// 		array('type' => 'view' , 'name' => 'SANKOZ' , 'url' => 'http://sankoz.com/'),
				// 		//array('type' => 'view' , 'name' => 'cs-tee' , 'url' => 'http://cs-tee.com/'),
				// 		//array('type' => 'view' , 'name' => '测试' , 'url' => "http://sankoz.com/index.php/index/test/index")
						
				// 	)
				// ),
				// array(
				// 	'name' => '菜单',
				// 	'sub_button' => array(
				// 		array('type' => 'view' , 'name' => 'SANKOZ' , 'url' => 'http://sankoz.com/'),
				// 		//array('type' => 'view' , 'name' => 'cs-tee' , 'url' => 'http://cs-tee.com/'),
				// 		//array('type' => 'view' , 'name' => '测试' , 'url' => "http://sankoz.com/index.php/index/test/index")
						
				// 	)
				// ),
				//array('type' => 'click' , 'name' => '用户信息' , 'key' => 'userinfo')
			)
			);
		$post_json  = json_encode($menu_config,JSON_UNESCAPED_UNICODE);
		
		$res   =  postRequest($res,$post_json);
		
		if($res['errcode'] == 0){
    			exit('创建成功!'); 
    		}else{
    			exit('创建失败!'); 
    		}

	} 



    }
