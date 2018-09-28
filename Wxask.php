use think\Db;
use think\Exception;
use app\common;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Wxask extends Base
{	
	public function Intchat($msg){
		$map['key']=array('like','%'.$msg.'%');
		$result = Db::name('wx_chat')->field('id,content')->where($map)->select();
		if($result){

			$data = $result[mt_rand(0,(count($result)-1))];
		    $str =  $data['content'];
		}else{
			$str = '你收搜的内容不存在,要获取相关信息,请换一个关键词哟!';
		}
		return $str;
	}
}
