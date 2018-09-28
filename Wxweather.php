<?php
/**
 * 
 * @authors 获取天气信息 (you@example.org)
 * @date    2018-08-01 15:27:42
 * @version $Id$
 */
namespace app\index\controller;
use think\Db;
use think\Exception;
use app\common;
//查询天气
class Wxweather extends Base {
	public function index ($city){
		 $api = 'https://www.sojson.com/open/api/weather/json.shtml';
		 $param  =  [
	   			'city'=>$city
	   			   		];

    	$res  =   getRequest($api,$param);
    	$data = curl_http($res);
    	if($data['message']=='Success !'){
			$data = $data['data'];
    		$today = $data['forecast'][0];
    		$content = '您查询天气的城市：'.PHP_EOL;
    		$content .= $city.PHP_EOL;
    		$content .='湿度:'.$data['shidu'].PHP_EOL;
    		$content .= '环境情况:'.$data['quality'].PHP_EOL;
    		$content .= '适宜:'.$data['ganmao'].PHP_EOL;
    		$content .= '最高温:'.$today['high'].PHP_EOL;
    		$content .= '最低温:'.$today['low'].PHP_EOL;
    		$content .= '日升/日落:'.$today['sunrise'].'~'.$today['sunset'].PHP_EOL;
    		$content .= '风向/大小/雨水:'.$today['fx'].'/'.$today['fl'].$today['type'].PHP_EOL;
    		$content .= '注意事项'.$today['notice'];
    		return $content;
    		
    	}elseif($data['message']=='Check the parameters.'){
    		 return '无效的地址!';
    	}else{
    		return 1;
    	}
	}
   
}
