<?php namespace wechat;
/**
 * 微信操作基础类
 * Class Wx
 * @package wechat
 */
class Wx extends Error {
    //微信的配置项
    static protected $config = [];
    protected  $apiUrl;
    //粉丝发来的消息内容
    protected $message;
    protected $accessToken;

    public function __construct(array $config = []) {
        if(!empty($config)) {
            self::$config = $config;
        }
        $this->apiUrl = 'https://api.weixin.qq.com';
        $this->message = $this->parsePostRequestData();
    }

    //获取公众号的access_token
    public function getAccessToken() {
        //缓存名
        $cacheName = md5(self::$config['appID'] . self::$config['appsecret']);
        //缓存文件
        $file = __DIR__ .'/cache/' . $cacheName . '.php';
        if(is_file($file) && filemtime($file) + 4000 > time()){
            //缓存有效，微信规定access_token有效期2小时，故设7000比7200小一点
            $data = include $file;
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$config['appID'] . '&secret=' . self::$config['appsecret'];
            $data = $this->curl($url);
            $data = json_decode($data, true);
            //获取失败
            if(isset($data['errcode'])) {
                return false;
            }
            //echo $data['access_token'];
            //缓存access_token
            file_put_contents($file, '<?php return ' . var_export($data, true) . ';');
        }
        return $this->accessToken = $data['access_token'];
    }
    //获取粉丝发来的消息内容
    public function getMessage(){
        return $this->message;
    }

    //获取并解析粉丝发来的消息内容
    private function parsePostRequestData() {
        $postStr = file_get_contents("php://input");
        if(isset($postStr)){
            return simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
    }

    //获取功能实例如消息管理实例
    public function instance($name) {
        $class = '\wechat\build\\' . ucfirst($name);
        return new $class;

    }

    /*发送请求，第二个参数有值时为POST请求
     *
     *@param str $url 请求地址
     *@param array $fields 发送的POST表单
     *
     *@return str
     */
    public function curl($url, $fields = []) {
        $ch = curl_init();
        //设置我们请求的地址
        curl_setopt($ch, CURLOPT_URL, $url);
        //数据返回后不要直接显示
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //禁止证书校验
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if($fields) {
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        $data = '';
        if(curl_exec($ch)){
            //发送成功，获取数据
            $data = curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $data;
    }

    //与微信服务器进行绑定
    public function valid() {
        //有这些参数时，才是微信绑定服务器的行为
        if(isset($_GET['signature']) && isset($_GET['timestamp']) && isset($_GET['nonce']) && isset($_GET['echostr'])) {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];

            $token = self::$config['token'];
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);

            if ($tmpStr == $signature) {
                echo $_GET["echostr"];
            } else {
                return false;
            }
        }
    }

}