<?php namespace app;

//业务代码，测试微信SDK功能
use wechat\Wx;

class Entry
{
    protected $wx;

    public function __construct()
    {
        $config = [
            'token' => 'gerrGZard47',
            'appID' => 'wx7549d16ad75cf7b1',
            'appsecret' => 'a1114b21270cac67701881744a136431'
        ];
        $this->wx = new Wx($config);
        $this->wx->valid();
    }

    public function handler()
    {
        //$msg = $this->wx->instance('material')->upload('2.jpg');
        //var_dump($msg);
        /*$msg = $this->wx->getMessage();
        file_put_contents('testfile/ee.php',var_export($msg, true),FILE_APPEND);*/
        //$this->wx->instance('message')->text('我收到了你的消息：'.$msg->Content);

        $msg = $this->wx->instance('message');
        if ($msg->isPpoaEvent()) {
            try {
                $memcache = new \Memcache;
                $memcache->connect('localhost', 11211);
                $tmp = json_decode(json_encode($msg->getMessage()), true);//xml对象转成数组，memcache set不能接收xml对象的序列化状态
                $memcache->set($tmp['FromUserName'], $tmp['EventKey'],0,300);//300秒
                //$msg->text($memcache->get($msg->getMessage()->FromUserName));
            }
            catch (\Exception $e){
                file_put_contents('testfile/eee.php',$e->getMessage());
            }
        }elseif($msg->isClickEvent()) {
            if($msg->getMessage()->EventKey == 'V1001_GOOD') {
                $msg->text('谢谢！');
            }
        } else if($msg->isLocationMsg()){
            $data = $this->wx->getMessage();
            $jd = $data->Location_Y;
            $wd = $data->Location_X;
            if($res = $msg->toilet($jd, $wd)) {
                $msg->text($res);
            }
        }else if($msg->isImageMsg()) {
            $tmp = $msg->getMessage();
            $pic = $tmp->PicUrl;
            $memcache = new \Memcache;
            $memcache->connect('localhost', 11211);
            if ($memcache->get($tmp->FromUserName) == 'rselfmenu_0_0') {
                $msg->text($msg->face($pic));
                $memcache->delete($tmp->FromUserName);
            }else if ($memcache->get($tmp->FromUserName) == 'rselfmenu_0_1') {
                $msg->text($msg->find($pic));
                $memcache->delete($tmp->FromUserName);
            }
        }else if($msg->isSubscribeEvent()) {
            $msg->text('终于等到你，还好我没放弃~');
        }

        // $msg = $this->wx->instance('material')->upload('1.jpg');
        //$msg = $this->wx->instance('material');
        //print_r( $msg->batchGet('image','0','10'));
        //print_r($msg->getMaterial('VnsXGkA4lebEStmrwWXXJJ2fG419bqUSc7BzcFLOHxY'));

        /*$msg = $this->wx->instance('message');
        if($msg->isTextMsg()){
            $msg->text('这是文本消息');
        }
        if($msg->isImageMsg()){
            $msg->text('这是图片');
        }
        if($msg->isLinkMsg()){
            $msg->text('这是链接');
        }
        if($msg->isVideoMsg()){
            $msg->text('这是视频');
        }
        if($msg->isShortVideoMsg()){
            $msg->text('这是短视频');
        }
        if($msg->isLocationMsg()){
            $msg->text('这是地理位置');
        }
        if($msg->isVoiceMsg()){
            $msg->text('这是语音消息');
        }
        $json=<<<php
        {
            "button":[
       {
             "name": "菜单",
            "sub_button": [
               {
                    "type": "media_id",
           "name": "帅哥",
           "media_id": "0OqusQLlfAYClPxlrpd_DEbgY1gBCdVzR_qw0GiBh_E",
           "sub_button": [ ]
                },
                {
                    "type": "media_id",
           "name": "美女",
           "media_id": "0OqusQLlfAYClPxlrpd_DFuo-m0Xy6SS0sARNfSc7sI",
                    "sub_button": [ ]
                },
                {
                    "type": "location_select",
                    "name": "找附近厕所",
                    "key":"rselfmenu_1_0",
                    "sub_button": [ ]
                }]
                },
                {
          "name":"重点功能",
           "sub_button":[
           {
                 "type": "pic_photo_or_album",
                    "name": "颜值检测",
                    "key": "rselfmenu_0_0",
                    "sub_button": [ ]
            },
            {
                "type": "pic_photo_or_album",
                    "name": "寻找失踪人口",
                    "key": "rselfmenu_0_1",
                    "sub_button": [ ]
             }]
             },
            {
                "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }
       }]
 }
php;
        $res = $this->wx->instance('button')->create($json);
        var_dump($res);*/
    }
}