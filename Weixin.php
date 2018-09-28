<?php

namespace app\index\controller;
use think\Db;
use think\Exception;
use app\common;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Weixin extends Base
{	


	//公共属性

	public $user_id ;
	public $server_id;
	public $msg_type;
	public function test(){
		return $this->user_id;
	}
    public function index()
    {

    	// echo $this->AppID;
    	//include_once "wxBizMsgCrypt.php";
    	if(input('echostr')){
    		$this->_access();
    		exit();
    	}

    	$str_xml = file_get_contents("php://input");
    	$this->saveXml($str_xml);	
    	if($str_xml!=''){
    		$obj_xml = simplexml_load_string($str_xml);
    		$arr_xml = $this->_toArray($obj_xml);
    		$this->arr_xml = $arr_xml;
    		$this->server_id = $arr_xml['ToUserName'];
    		$this->user_id   = $arr_xml['FromUserName'] ;//微信用户ID
    		$this->msg_type = $arr_xml['MsgType'];
    	}

    	switch($this->msg_type){
    		case 'event':
    		$event_type = $arr_xml['Event'];
    		if($event_type=='subscribe'){
    		    		$this->_msgReplay($this->user_id ,$this->server_id,'欢迎关注'.PHP_EOL.'SANKOZ服装定制！'.PHP_EOL."回复 ".PHP_EOL."1：获取SANKOZ网站资源".PHP_EOL."2：查询天气".PHP_EOL."3：智能聊天".PHP_EOL.'h/H:帮助');
    		    		$this->addOpenid($this->user_id);
    		    		exit();
    	    }elseif($event_type=='CLICK'){
    	    	$key = $arr_xml['EventKey'];
    	    	if($key =='news'){
    	    		$this->_actNews();
    	    	}else{
    	    		$userinfo = new Wxgetuserinfo();
    	    		$this->_textReplay($userinfo->getuserinfo($this->user_id));
    	    	}
    	    	
    	    }elseif($event_type=='unsubscribe'){
    	    		$this->unsubscribe($this->user_id);
    	    	}
    	    break;
    	    case 'text':
    	    if($arr_xml['Content']=='图文'){
    	    	$this->_actNews();
    	    	
    	    }elseif(strtolower($arr_xml['Content'])=='h'){//帮助
    	    	$this->_msgReplay($this->user_id ,$this->server_id,'欢迎关注'.PHP_EOL.'SANKOZ服装定制！'.PHP_EOL."回复 ".PHP_EOL."1：获取SANKOZ网站资源".PHP_EOL."2：查询天气".PHP_EOL."3：智能聊天".PHP_EOL.'h/H:帮助');
    	    	    exit();
    	    }
    	    elseif($arr_xml['Content']=='1'){
    	    	$this->_actNews();exit();
    	    }elseif($arr_xml['Content']=='2'){
    	    	$this->_textReplay('查询天气：回复格式    '.'城市/天气'.PHP_EOL.'如'.PHP_EOL."北京/天气");
    	    	exit();
    	    }elseif($arr_xml['Content']=='3'){
    	    	$this->_textReplay('提示：智能聊天格式'.PHP_EOL.'@关键词'.PHP_EOL.'如下:'.PHP_EOL.'@sankoz');
    	    	exit();
    	    }
    	    else{ //天气查询
    	    		if(strpos($arr_xml['Content'],'/天气') !==false)	{
    	    			$air = explode('/', $arr_xml['Content']);
	    	    	
	    	    	if($air[1]=='天气'){

	    	    		$a = new Wxweather;
	    	    		// $this->_textReplay($air[0]);exit();
			            // $data= $weather ->index('$weather[0]');
	    	    		$this->_textReplay($a ->index($air[0]));
	    	    	}else{
						$this->_textReplay($arr_xml['Content']);
	    	    	}

	    	    }elseif(strpos($arr_xml['Content'],'@') !==false){ //智能聊天

	    	    	$msg = explode('@', $arr_xml['Content']);

	    	    	if($msg[1]!=''&&$msg[0]==''){
	    	    		$wxask = new Wxask;	
    	    			$this->_textReplay($wxask->Intchat($msg[1]));
	    	    	}elseif($msg[1]==''&&$msg[0]==''){
	    	    		$this->_textReplay('提示：智能聊天格式'.PHP_EOL.'@关键词'.PHP_EOL.'如下:'.PHP_EOL.'@sankoz');
	    	    	}else{
	    	    		$this->_textReplay($arr_xml['Content']);
	    	    	}
    	    		exit();
    	    	
	    	    }else{
	    	    	$this->_textReplay($arr_xml['Content']);
	    	    }
	    	    exit();
    	    }
    	    // $arr_xml['Content'] 
    	    
    	    break;
    	    case 'image':
    	    
    	    //$this->_textReplay('您发了一张图片！');
    	    $image = $arr_xml['MediaId'];
    	    $this->_imgReplay();
    	    break;
    	}


    }

// <xml><ToUserName><![CDATA[gh_6bfa7e855caa]]></ToUserName>
// <FromUserName><![CDATA[oj54Tv13ujvXgIMsVrvBOJ_7hdBk]]></FromUserName>
// <CreateTime>1533104914</CreateTime>
// <MsgType><![CDATA[event]]></MsgType>
// <Event><![CDATA[CLICK]]></Event>
// <EventKey><![CDATA[news]]></EventKey>
// </xml>
//获取access_token
     	
    private function _access()
    {
	 		//$encodingAesKey = $this->EncodingAESKey;
				
				$token = $this->Token;
				
				$timestamp = $_GET['timestamp'];

				$nonce = $_GET['nonce'];
				$signature = $_GET['signature'];
				$echostr = $_GET['echostr'];
				$appId = $this->AppID;
				$array = array($token,$timestamp,$nonce);
				sort($array,SORT_STRING);
				$str =implode($array);
				$my_signature =sha1($str);
				if($my_signature==$signature){
					echo $echostr;
		}
	}

	//图文消息介绍处理
    public function _actNews(){
  
		$str = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>%s</ArticleCount>
					<Articles>
					%s
					</Articles>
					</xml>";
					
		$str_one = "<item>
				
					<Title><![CDATA[%s]]></Title> 
					
					<Description><![CDATA[%s]]></Description>
					
					<PicUrl><![CDATA[%s]]></PicUrl>
					
					<Url><![CDATA[%s]]></Url>
			
				</item>";
				if(!cache('wx_newsreplay_data')){
					$data = Db::name('wx_newsreplay')->field('Title,Description,PicUrl,Url')->where('status',1)->select();
					if(!$data){
						exit();
					}
					cache('wx_newsreplay_data',$data,7200);
				}else{
					$data = cache('wx_newsreplay_data');
				}
		
		$news  =  '';			
		foreach ($data as $k => $v) {
			$news.= sprintf($str_one,$v['Title'],$v['Description'],$v['PicUrl'],$v['Url']);
		}
		$arr_xml = $this->arr_xml;
		$ToUserName = $arr_xml['ToUserName'];
		$FromUserName = $arr_xml['FromUserName'];
							
		echo sprintf($str,$FromUserName,$ToUserName,time(),count($data),$news);
    }

//将获取XML添加到数据库
	public function saveXml($xml){
			if($xml){
			Db::name('data_xml')->insert(['xml'=>$xml,'create_time'=>time()]);
		}
   }
   //将XML变成数组
   private function _toArray($obj_xml){
   	$arr_xml =  (array)$obj_xml;
   	foreach($arr_xml as $k =>$v){
   		if(is_object($v)){
   			$arr_xml[$k] = (string)$v;
   		}
   	}
   	return $arr_xml;
   }


//关注
   private function _msgReplay($to,$from,$msg,$type='text'){
   			switch ($type) {
   				case 'text':
   					$arr_xml = $this->arr_xml;
   					$textTpl = "<xml>
		 				<ToUserName><![CDATA[%s]]></ToUserName>
		 				<FromUserName><![CDATA[%s]]></FromUserName>
		 				<CreateTime>%s</CreateTime>
		 				<MsgType><![CDATA[%s]]></MsgType>
		 				<Content><![CDATA[%s]]></Content>
		 				<FuncFlag>0</FuncFlag>
		 				</xml>"; //发送的模板 
		 				$resultStr = sprintf($textTpl, $arr_xml['FromUserName'], $arr_xml['ToUserName'], time(), 'text',$msg);
		 				echo $resultStr;

   					break;
   				
   				default:
   					# code...
   					break;
   			}
   		}
 //文本回复
  private function _textReplay($msg='你好帅帅！'){
  	$arr_xml = $this->arr_xml;
   					$textTpl = "<xml>
		 				<ToUserName><![CDATA[%s]]></ToUserName>
		 				<FromUserName><![CDATA[%s]]></FromUserName>
		 				<CreateTime>%s</CreateTime>
		 				<MsgType><![CDATA[%s]]></MsgType>
		 				<Content><![CDATA[%s]]></Content>
		 				<FuncFlag>0</FuncFlag>
		 				</xml>"; //发送的模板 
	$resultStr = sprintf($textTpl, $arr_xml['FromUserName'], $arr_xml['ToUserName'], time(), 'text',$msg);
	echo $resultStr;
  } 

  private function _imgReplay(){
  	$arr_xml = $this->arr_xml;
   					$textTpl = "<xml>
		 		<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%d</CreateTime>
				<MsgType><![CDATA[image]]></MsgType>
				<Image>
					<MediaId><![CDATA[%s]]></MediaId>
				</Image>
		 				</xml>"; //发送的模板 
	$resultStr = sprintf($textTpl, $arr_xml['FromUserName'], $arr_xml['ToUserName'], time(),'ueLx2GrjfaFi-ci-UcBl_K4AIfK3XJqnmDV4-Azuhi2n8IOaFFYnXLpBazGSR6k2');
	echo $resultStr;
  } 	

//关注添加粉丝
  private function addOpenid($open_id){
  	//查看数据库是否有此粉丝
    	$res = Db::name('wx_fans')->field('id,status')->where('open_id',$open_id)->find();
    	if($res){

    		Db::name('wx_fans')->update(['id'=>$res['id'],'status'=>1,'update_time'=>time()]);
    		
    	}else{
    		$user = ['open_id'=>$open_id,'status'=>1,'create_time'=>time(),'update_time'=>time()];
    		Db::name('wx_fans')->insert($user);
    	}
    	exit();
  }	
  //粉丝取消关注
  private function unsubscribe($open_id){
  	$res = Db::name('wx_fans')->field('id')->where('open_id',$open_id)->find();
  	if($res){
  		Db::name('wx_fans')->update(['id'=>$res['id'],'status'=>0,'update_time'=>time()]);
  	}else{
  		$user = ['open_id'=>$open_id,'status'=>0,'create_time'=>time(),'update_time'=>time()];
  		Db::name('wx_fans')->insert($user);
  	}
  	exit();
  }
//群发$msg,$type='text'
  public function allTRepaly(){
   			$msg = 'ssss';
   				$to='gh_6bfa7e855caa';
   				
   					$textTpl = "<xml>
		 				<ToUserName><![CDATA[%s]]></ToUserName>
		 				<FromUserName><![CDATA[%s]]></FromUserName>
		 				<CreateTime>%s</CreateTime>
		 				<MsgType><![CDATA[%s]]></MsgType>
		 				<Content><![CDATA[%s]]></Content>
		 				<FuncFlag>0</FuncFlag>
		 				</xml>"; //发送的模板 
		 				//查询数据库
		 				$res = Db::name('wx_fans')->field('open_id')->where('status',1)->select();

		 				for($i=0;$i<=count($res)-1;$i++){
		 					echo $res[$i]['open_id'];
		 					$resultStr = sprintf($textTpl, $res[$i]['open_id'],$to, time(), 'text',$msg);
		 				    //echo $resultStr;
		 				}
		 					
		 				// $resultStr = sprintf($textTpl, $arr_xml['FromUserName'],$to, time(), 'text',$msg);
		 				// echo $resultStr;

   			
   		}
}    	
