<?php namespace  wechat\build;
use wechat\Wx;
/**
 * 专门处理微信消息
 * Class Message
 * @package wechat\bulid
 */
class Message extends Wx {
    #-----------------事件消息类型----------------------
    const EVENT_TYPE_SUBSCRIBE = 'subscribe';//关注
    const EVENT_TYPE_UNSUBSCRIBE = 'unsubscribe';//取消关注
    const EVENT_TYPE_LOCATION = 'LOCATION';//地理位置
    const EVENT_TYPE_PPOA = 'pic_photo_or_album';//地理位置
    const EVENT_TYPE_CLICK = 'CLICK';//点击

    #-----------------用户发送的消息类型----------------
    //消息类型为文本消息
    const MSG_TYPE_TEXT = 'text';
    //图片消息
    const MSG_TYPE_IMAGE = 'image';
    //语音消息
    const MSG_TYPE_VOICE = 'voice';
    //视频
    const MSG_TYPE_VIDEO = 'video';
    //短视频
    const MSG_TYPE_SHORT_VIDEO = 'shortvideo';
    //地理位置
    const MSG_TYPE_LOCATION = 'location';
    //链接
    const MSG_TYPE_LINK = 'link';

    #-----------------事件消息判断---------------------------
    public function isClickEvent(){
        return $this->message->Event == self::EVENT_TYPE_CLICK && $this->message->MsgType == 'event';
    }
    public function isPpoaEvent() {
        return $this->message->Event == self::EVENT_TYPE_PPOA && $this->message->MsgType == 'event';
    }
    public function isSubscribeEvent() {
        return $this->message->Event == self::EVENT_TYPE_SUBSCRIBE && $this->message->MsgType == 'event';
    }
    public function isUnSubscribeEvent() {
        return $this->message->Event == self::EVENT_TYPE_UNSUBSCRIBE && $this->message->MsgType == 'event';
    }
    public function isLocationEvent() {
        return $this->message->Event == self::EVENT_TYPE_LOCATION && $this->message->MsgType == 'event';
    }

    #-----------------普通消息判断-------------------
    public function isTextMsg() {
        return $this->message->MsgType == self::MSG_TYPE_TEXT;
    }
    public function isImageMsg() {
        return $this->message->MsgType == self::MSG_TYPE_IMAGE;
    }
    public function isVoiceMsg() {
        return $this->message->MsgType == self::MSG_TYPE_VOICE;
    }
    public function isVideoMsg() {
        return $this->message->MsgType == self::MSG_TYPE_VIDEO;
    }
    public function isShortVideoMsg() {
        return $this->message->MsgType == self::MSG_TYPE_SHORT_VIDEO;
    }
    public function isLocationMsg() {
        return $this->message->MsgType == self::MSG_TYPE_LOCATION;
    }
    public function isLinkMsg() {
        return $this->message->MsgType == self::MSG_TYPE_LINK;
    }

    //回复文本消息
    public function text($content) {
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        $text = sprintf($textTpl, $this->message->FromUserName, $this->message->ToUserName, time(), $content);
        //header('Content-type:application/xml');//可以不要
        echo $text;
    }

    /**
     * 滴滴厕所，返回附近的厕所位置
     * @param str $jd、$wd 经度、纬度
     * @return str 返回附近的厕所 字符串
     */
     public function toilet($jd, $wd) {
         $api = "http://api.map.baidu.com/geoconv/v1/?coords={$jd},{$wd}&from=3&to=5&ak=d1s6n7mfXKG5Flm3D0z3GG7suiWMaqav";
         $data = json_decode($this->curl($api),true);
         if($data['status'] !== 0) {
             return false;
         }
         $jd = $data['result'][0]['x'];
         $wd = $data['result'][0]['y'];
         $word = urlencode("厕所");
         $api = "api.map.baidu.com/place/v2/search?query={$word}&location={$wd},{$jd}&scope=2&output=json&ak=d1s6n7mfXKG5Flm3D0z3GG7suiWMaqav";
         $data = json_decode($this->curl($api),true);
         if($data['status'] !== 0) {
             return false;
         }
         $res = '';
         for($i = 0; $i < 5; $i++){
             if(!$data['results'][$i]) {
                 break;
             }else {
                 $res .= $i + 1 . '：' . $data['results'][$i]['name'] . ',' . $data['results'][$i]['address'] . ',离你有' .
                     $data['results'][$i]['detail_info']['distance'] . "米\n";
             }
         }
         return $res;
     }

    /**
     * 人脸检测，返回图片中人的性别、年龄、种族等
     * @param str $pic 要检测的图片
     * @return str 返回检测的结果描述
     */
    public function face($pic) {
        $content = file_get_contents($pic);

        $api_key = 'aEWCBGl3Prs83F343TsxIbd1B50xLVts';
        $api_secret = 'XCWVroKcFwUwY0lfzzbVv7P17J_gkDFH';
        $url = 'https://api-cn.faceplusplus.com/facepp/v3/detect';
        $data = $this->curl($url, ['api_key' => $api_key, 'api_secret' => $api_secret, 'image_file";filename="image' => $content, 'return_attributes' => 'gender,age,smiling,ethnicity,beauty,skinstatus']);
        $data = json_decode($data, true);
        if($data['error_message']) {
            return $data['error_message'] . "search faceset fail\nsorry，网络繁忙，请再发一次。";
        }

        $data = $data['faces'];
        if(count($data) == 0) {
            $res = '什么破图，脸都不要了';
        }else {
            $res = "检测出" . count($data) . "个人\n";
            foreach ($data as $d) {
                $d = $d['attributes'];
                if ($d['gender']['value'] == 'Male') {
                    $res .= '男，颜值' . round($d['beauty']['male_score']) . "，";
                } else {
                    $res .= '女，颜值' . round($d['beauty']['female_score']) . "，";
                }

                if ($d['ethnicity']['value'] == 'ASIAN') {
                    $res .= "黄种人，";
                } elseif ($d['ethnicity']['value'] == 'WHITE') {
                    $res .= "白种人，";
                } else {
                    $res .= "黑种人，";
                }

                $res .= $d['age']['value'] . "岁，笑特征" . round($d['smile']['value']) . "\n";
                $res .= "健康：" . round($d['skinstatus']['health']);
                $res .= "，黑眼圈：" . round($d['skinstatus']['dark_circle']);
                $res .= "，青春痘：" . round($d['skinstatus']['acne']);
                $res .= "，色斑：" . round($d['skinstatus']['stain']) ."\n\n";
            }
        }
        return $res;
    }

    /**
     * 失踪人口寻找 在faceplus利用人脸集（faceset）寻找，和上传（街上可疑、流浪儿童）相似的检
     * @param str $pic 要检测的图片
     * @return str 如找到相似度50以上的失踪人口库照片，返回库中图片链接、相似度、失踪人的亲属。找不到返回找不到原因string
     */
    public function find($pic) {
        $content = file_get_contents($pic);

        $api_key = 'aEWCBGl3Prs83F343TsxIbd1B50xLVts';
        $api_secret = 'XCWVroKcFwUwY0lfzzbVv7P17J_gkDFH';
        $url = 'https://api-cn.faceplusplus.com/facepp/v3/search';
        $outer_id = '失踪人口';
        $data = $this->curl($url, ['api_key' => $api_key, 'api_secret' => $api_secret, 'outer_id' => $outer_id, 'image_file";filename="image' => $content, 'return_result_count' => 3]);
        $rs = json_decode($data, true);
        //print_r($rs);
        if($rs['error_message']) {
            return $rs['error_message'] . "search faceset fail\nsorry，网络繁忙，请再发一次。";
        }
        $ps = [];
        foreach($rs['results'] as $r) {
            if($r['confidence'] > 50 ) {
                $ps[] = ['face_token' => $r['face_token'], 'confidence' => $r['confidence']];
            }
        }
        if(empty($ps)){
            return '没有找到样貌相似的失踪人，好人一生平安！';
        }
        include_once('conf/config.php');
        $db = new \lib\Fun();
        $rs = [];
        foreach($ps as $p){
            $rs[] = $db->select(['szrk'],['name', 'tel', 'path'],['where' => ["face_token = '". $p['face_token'] . "'"]]);
        }
        if(empty($rs)){
            return 'table not found';
        }
        $str = "从失踪人口库找到如下：\n";
        for($i = 0; $i < count($rs); $i++) {
            $str .= $i+1 . '，相似度：' . $ps[$i]['confidence'] . '%，ta的照片：http://wechat.steviekwok.top/web/upload/img/'
                . $rs[$i][0]['path'] . "\n联系人：" . $rs[$i][0]['name'] . "，电话：".$rs[$i][0]['tel'] .
                "\n-------------------------------------------------------\n\n";
        }
        $str .= "相似度最高100%，大于50%可能是同一人，大于60很可能是同一人，以此类推。\n将爱心进行到底，电话告诉ta的亲属。谢谢！";
        return $str;
    }
     public function show() {
        echo __METHOD__;
    }
}