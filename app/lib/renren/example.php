<?php
/*
 * 调用人人网RESTful API的范例，本类需要继承RESTClient类方可使用
 * 要求最低的PHP版本是5.2.0，并且还要支持以下库：cURL, Libxml 2.6.0
 * This example for invoke RenRen RESTful Webservice
 * It MUST be extends RESTClient
 * The requirement of PHP version is 5.2.0 or above, and support as below:
 * cURL, Libxml 2.6.0
 *
 * @Modified by mike on 17:54 2011/12/21.
 * @Version: 0.0.2 alpha
 * @Created: 0:11:39 2010/11/25
 * @Author:	Edison tsai<dnsing@gmail.com>
 * @Blog:	http://www.timescode.com
 * @Link:	http://www.dianboom.com
 */

require_once 'renrenRestApiService.class.php';

$rrObj = new RenrenRestApiService;
//sessionkey和accesstoken，传任何一个都可以；“测试1”用的是sessionkey，“测试2”用的是accesstoken
$sessionkey='6.c15fbc6fd142dddce6bd98a4d5524286.2592000.1327053600-228487955';//改成测试用户的
$accesstoken='99273|6.c15fbc6fd142dddce6bd98a4d5524286.2592000.1327053600-228487955';//改成测试用户的

//$rrObj->setEncode("GB2312");//如果是utf-8的环境可以不用设，如果当前环境不是utf8编码需要在这里设定


/*@POST暂时有两个参数，第一个是需要调用的方法，具体的方法跟人人网的API一致，注意区分大小写
 *@第二个参数是一维数组，除了api_key,method,v,format,callid之外的其他参数/

/*测试1：获取指定用户的信息
 */
$params = array('uids'=>'346132863,741966903','fields'=>'uid,name,sex,birthday,mainurl,hometown_location,tinyurl,headurl,mainurl','session_key'=>$sessionkey);
$res = $rrObj->rr_post_curl('users.getInfo', $params);//curl函数发送请求
//$res = $rrObj->rr_post_fopen('users.getInfo', $params);//如果你的环境无法支持curl函数，可以用基于fopen函数的该函数发送请求
print_r($res);//输出结果
//echo '<br>'.$res[0]->name;

echo '<br><hr><br>';


/*测试2：分页获取当前用户的好友信息列表
 */
$params = array('page'=>'1','count'=>'2','access_token'=>$accesstoken);
$res = $rrObj->rr_post_curl('friends.getFriends', $params);//curl函数发送请求
//$res = $rrObj->rr_post_fopen('friends.getFriends', $params);//fopen函数发送请求
print_r($res);//输出结果

echo '<br><hr><br>';


/*测试3：上传网络图片和本地图片
 */

//网络图片上传的方式，格式如下，upload是api的参数名不要改，name、tmp_name和type可以根据自己的情况修改，type一定要与图片的后缀类型对应起来
$myfile=array('upload'=>array(
'name'=>'baidu_sylogo1.gif',
'tmp_name'=>'http://www.baidu.com/img/baidu_sylogo1.gif',//如果是服务器本地图片，可以这么写：'tmp_name'=>'c:/pic.jpg'
'type'=>'image/gif'
));

$params = array('caption'=>'description','access_token'=>$accesstoken);
//上传照片的方法，不默认开启，需要测试时，将如下两行代码解除注释，刷新一个该页面，即可将图片传到人人网
//$res = $rrObj->rr_photo_post_fopen('photos.upload',$params,$myfile);//基于fopen函数的发送请求
//print_r($res);//输出结果
?>