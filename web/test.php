<?php
use lib\Fun;
//include 'web/lib/Fun.php';
function __autoload($class) {
    echo '---------------'.$class.'---------------'.'<br>';
    echo str_replace('\\','/',$class) . '.php';
    include   str_replace('\\','/',$class) . '.php';
}
$obj = new Fun; // 实例化 foo\Another 对象
echo $obj->a;
/*//header("Content-Type: text/html; charset=UTF8");
$img = 'http://wechat.steviekwok.top/web/upload/img/127288.jpg';
var_dump($img);
//$img = urlencode('https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike116%2C5%2C5%2C116%2C38/sign=9fcd06a78b0a19d8df0e8c575293e9ee/cc11728b4710b9129b8ea9e8c8fdfc039245225c.jpg');
//$img = 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1516812903350&di=224509590431e9ac8efb87fbbcc3b10d&imgtype=0&src=http%3A%2F%2Fimg5.cache.netease.com%2Fphoto%2F0010%2F2015-09-02%2FB2H6JBJE50CB0010.jpg';
$api = "https://api-cn.faceplusplus.com/facepp/v3/detect?api_key=aEWCBGl3Prs83F343TsxIbd1B50xLVts&api_secret=XCWVroKcFwUwY0lfzzbVv7P17J_gkDFH&image_url={$img}&return_attributes=gender,age,smiling,ethnicity,beauty";
$ch = curl_init();
//设置我们请求的地址
curl_setopt($ch, CURLOPT_URL, $api);
//数据返回后不要直接显示
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//禁止证书校验
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//post
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$data = '';
if(curl_exec($ch)){
    //echo 1111;
    //发送成功，获取数据
    $data = curl_multi_getcontent($ch);
}
curl_close($ch);
echo "<pre>";
print_r(json_decode($data,true));*/
//$data = json_decode($data,true)['faces'];
/*echo "检测出" . count($data) . "个人\n";
foreach($data as $d){
    $d = $d['attributes'];
    if($d['gender']['value'] == 'Male') {
        echo '男，';
        echo "颜值" . round($d['beauty']['male_score']) . "，";
    }else {
        echo '女，';
        echo "颜值" . round($d['beauty']['female_score']) . "，";
    }

    if($d['ethnicity']['value'] == 'Asian') {
        echo "黄种人，";
    }elseif($d['ethnicity']['value'] == 'White'){
        echo "白种人，";
    }else{
        echo "黑种人，";
    }

    echo $d['age']['value'] . "岁，笑特征" . round($d['smile']['value']);
    echo "\n";
}*/