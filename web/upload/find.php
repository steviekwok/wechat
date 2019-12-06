<?php
/*
 * 上传路上可疑流浪人照片，对比失踪人口照片库，以寻找失踪人
 * Date: 2018/1/26
 *  [faceset_token] => 72c92784b26bb0354d12077bd4b49cb6
 *  [outer_id] => SZRK
 */
use lib\Fun;
spl_autoload_register(function($class) {
    //echo '---------------'.$class.'---------------';
    include  '../' . str_replace('\\','/',$class) . '.php';
});
function delFile($file){
    if (!unlink($file))
    {
        echo ("Error deleting $file\n");
    }
    else
    {
        echo ("Deleted $file\n");
    }
}
function curl($url, $fields = []) {
    $ch = curl_init();
    //设置我们请求的地址
    curl_setopt($ch, CURLOPT_URL, $url);
    //数据返回后不要直接显示
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //禁止证书校验
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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

if(isset($_FILES['pic']) && $_FILES['pic']['error'] == 0 ) {
    $name = mt_rand(100000, 999999);
    $ext = explode('.', $_FILES['pic']['name']);
    $ext = end($ext);
    $full = $name . '.' . $ext;
    $rs = move_uploaded_file($_FILES['pic']['tmp_name'], 'find/' . $full);
    if (!$rs) {
        exit('upload error');
    }

    //upload ok
    $api_key = 'aEWCBGl3Prs83F343TsxIbd1B50xLVts';
    $api_secret = 'XCWVroKcFwUwY0lfzzbVv7P17J_gkDFH';

    $pic = 'find/' . $full;
    $content = file_get_contents($pic);
    $url = 'https://api-cn.faceplusplus.com/facepp/v3/search';
    $outer_id = '失踪人口';
    $data = curl($url, ['api_key' => $api_key, 'api_secret' => $api_secret, 'outer_id' => $outer_id, 'image_file";filename="image' => $content, 'return_result_count' => 5]);
    $rs = json_decode($data, true);
    //print_r($rs);
    if($rs['error_message']) {
        echo $rs['error_message'];
        delFile($pic);
        exit('search faceset fail');
    }
    $ps = [];
    foreach($rs['results'] as $r) {
        if($r['confidence'] > 50 ) {
            $ps[] = ['face_token' => $r['face_token'], 'confidence' => $r['confidence']];
        }
    }
    //print_r($ps);
    //exit;
    if(empty($ps)){
        delFile($pic);
        exit('没有找到相似面孔');
    }
    include_once('../conf/config.php');
    $db = new Fun();
    $rs = [];
    foreach($ps as $p){
        $rs[] = $db->select(['szrk'],['path','name','tel'],['where' => ["face_token = '". $p['face_token'] . "'"]]);
    }
    if(empty($rs[0])){
        delFile($pic);
        exit('table not found');
    }
    for($i = 0; $i < count($rs); $i++) {
        $str .= '相似度：' . $ps[$i]['confidence'] . ' http://wechat.steviekwok.top/web/upload/img/' . $rs[$i][0]['path'] . "\n联系人：".$rs[$i][0]['name']."，电话".$rs[$i][0]['tel']."\n";
    }
    echo $str;

}
/*

$api_key = 'aEWCBGl3Prs83F343TsxIbd1B50xLVts';
$api_secret = 'XCWVroKcFwUwY0lfzzbVv7P17J_gkDFH';
$outer_id = '失踪人口';
$face_tokens = '481c57a16f61d73c31a77d5248f9c978,b1a5603a2c725340a3ce4556bfe3fc30';
$url = 'https://api-cn.faceplusplus.com/facepp/v3/faceset/removeface';
$data = curl($url, ['api_key' => $api_key, 'api_secret' => $api_secret, 'outer_id' => $outer_id, 'face_tokens' => $face_tokens]);
$rs = json_decode($data, true);
print_r($rs);*/