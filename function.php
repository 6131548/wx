<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

// 为方便系统核心升级，二次开发中需要用到的公共函数请写在这个文件，不要去修改common.php文件
// 追踪并携程日志
function logInfo($info,$fileName='log'){
	$debugInfo =  debug_backtrace();//跟踪函数调用信息
	$message = date('Y-m-d H:i:s').PHP_EOL.$info.PHP_EOL;
	$message .='['. $debugInfo[0]['file'].'] .line'.$debugInfo[0]['line'].PHP_EOL;
	file_put_contents($fileName.'-'.date('Y-m-d').'.log' ,$message,FILE_APPEND);
}
/**
XML转成数组
*/ 
function xmlToArray($data){
	return (array)simplexml_load_string($data,'SimpleXMLElement',LIBXML_NOCDATA);
}
//生成签名
function generrateSgin($params,$key,$encrypt='md5'){
	//1.将集合M内费空值的参数按照参数命名从大到小排
	ksort($params);

	$params['key']= $key;

	$str= http_build_query($params);
	//3 进行加密
	return strtoupper(md5($str));
}
function getRequest($str_apiurl,$arr_param=array(),$str_returnType='array'){
        if(!$str_apiurl){
            exit('request url is empty 请求地址不正确');
        }

        //url拼装
        if(is_array($arr_param) && count($arr_param)>0){

            $tmp_param = http_build_query($arr_param);
            if(strpos($str_apiurl, '?') !== false){
                $str_apiurl .= "&".$tmp_param;

            }else{
                $str_apiurl .= "?" . $tmp_param; //?hope&c=index&m=news

            }
            
       }elseif (is_string($arr_param)){

            if(strpos($str_apiurl, '?') !== false){
                $str_apiurl .= "&".$arr_param;
            }else{
                $str_apiurl .= "?" . $arr_param;
            }
        }

        return $str_apiurl;
    }    
function setToken($url)
{
    $ch   = curl_init($url);              //初始化curl
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回数据不直接输出
    curl_setopt($ch, CURLOPT_POST, 1);                  //发送POST类型数据
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //SSL 报错时使用
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //SSL 报错时使用
    $contents = curl_exec($ch);                              //执行并存储结果
    // var_dump(curl_error($ch));                       //获取失败是使用（采集错误提示）
    curl_close($ch);
     $res = json_decode( $contents, true );
    if( isset( $res['errcode'] ) ){
        return ['code' => $res['errcode'], 'token' => 'invalid appid'];
    }
     $token = $res['access_token'];

    return ['code' => 1, 'token' => $token];
}    


function getNewAccess_token($url){
    
    $ch  =  curl_init ();
 
    // 设置URL和相应的选项
    curl_setopt ( $ch , CURLOPT_URL ,  $url );
    curl_setopt ( $ch , CURLOPT_HEADER ,  false );
    curl_setopt ( $ch , CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ( $ch , CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ( $ch , CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt ( $ch , CURLOPT_SSLVERSION, 1);
 
    // 抓取URL并把它传递给浏览器
    $str = curl_exec ( $ch );
 
    //关闭cURL资源，并且释放系统资源
    curl_close ( $ch );
    
    $arr = json_decode($str , true);
    if( isset( $arr['errcode'] ) ){
        return ['code' => $res['errcode'], 'token' => 'invalid appid'];
    }
    //添加新元素
    $arr['create_time'] = time();
 
    //将数组->json
    $str = json_encode($arr);
 
    return $arr['access_token'];
}

function curl_http($url){
  
    $ch  =  curl_init ();
 
    // 设置URL和相应的选项
    curl_setopt ( $ch , CURLOPT_URL ,  $url );
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
    
    //远程POST请求
    function postRequest($str_apiurl,$arr_param=array(),$str_returnType='array'){
        if(!$str_apiurl){
            exit('request url is empty 请求地址不正确');
        }


        $ch = curl_init();  //初始curl
        curl_setopt($ch,CURLOPT_URL,$str_apiurl);   //需要获取的 URL 地址
        curl_setopt($ch,CURLOPT_HEADER,0);          //启用时会将头文件的信息作为数据流输出, 此处禁止输出头信息
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  //获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30); //连接超时时间
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);  //头信息
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); 
        curl_setopt($ch, CURLOPT_POST, 1);          //post请求
        //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); //  PHP 5.6.0 后必须开启
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr_param);
        $res = curl_exec($ch);                      //执行curl请求
        $response_code = curl_getinfo($ch);

        //请求出错
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch)."<br>";
            //echo $res;
            var_dump($response_code);
        }
         //$ac='dssd';
            // var_dump($response_code['http_code']);
        //请求成功
        if($response_code['http_code'] == 200){

            if($str_returnType == 'array'){
                return json_decode($res,true);
            }else{
                return $res;
            }
        }else{
            $code = $response_code['http_code'];
            switch ($code) {
                case '404':
                    exit('请求的页面不存在');
                    break;
                
                default:
                     exit('请求失败!');
                    break;
            }
        }
    }
