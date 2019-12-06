<?php
/**
 * 上传失踪人照片处理
 * Date: 2018/1/26
 *  [faceset_token] => c0c0558162a1ba45c39d16002be6d990
 *  [outer_id] => 失踪人口
 */
//header("Content-Type: text/html; charset=UTF8");
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
    $name = mt_rand(10000000, 99999999);
    $ext = explode('.', $_FILES['pic']['name']);
    $ext = end($ext);
    $full = $name . '.' . $ext;
    $rs = move_uploaded_file($_FILES['pic']['tmp_name'], 'img/' . $full);
    if (!$rs) {
        exit('upload error');
    }

    //upload ok
    $api_key = 'aEWCBGl3Prs83F343TsxIbd1B50xLVts';
    $api_secret = 'XCWVroKcFwUwY0lfzzbVv7P17J_gkDFH';
    $pic = 'img/' . $full;
    $content = file_get_contents($pic);
    $url = 'https://api-cn.faceplusplus.com/facepp/v3/detect';
    $data = curl($url, ['image_file";filename="image' => $content, 'api_key' => $api_key, 'api_secret' => $api_secret, 'return_attributes' => "gender,age,smiling,ethnicity,beauty"]);
    $rs = json_decode($data, true);
    if (count($rs['faces']) == 0) {
        delFile($pic);
        exit('no faces');
    }

    $face_tokens = $rs['faces'][0]['face_token'];
    $outer_id = '失踪人口';
    $url = 'https://api-cn.faceplusplus.com/facepp/v3/faceset/addface';
    $data = curl($url, ['api_key' => $api_key, 'api_secret' => $api_secret, 'outer_id' => $outer_id, 'face_tokens' => $face_tokens]);
    $rs = json_decode($data, true);
    if (isset($rs['error_message'])) {
        echo $rs['error_message'];
        delFile($pic);
        exit('add to faceset fail');
    }
    echo "add face ok \n";
    $username = $tel = '';
    if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z]{1,20}$/u', trim($_POST['username']))) {
        $username = trim($_POST['username']);
    }
    if (preg_match('/^\d{1,20}$/', trim($_POST['tel']))) {
        $tel = trim($_POST['tel']);
    }
    include_once('../conf/config.php');
    $db = new lib\Fun();
    //print_r($db);
    $rs = $db->add(["szrk"], ['face_token' => $face_tokens, 'path' => $full, 'name' => $username, 'tel' => $tel]);
    if ($rs == 0) {
        delFile($pic);
        exit("add datatable fail");
    }
    echo 'well done';

    //if()
    //$name =
    /*//查询faseset
     $url = 'https://api-cn.faceplusplus.com/facepp/v3/faceset/getdetail';
    $data = curl($url, ['api_key' => $api_key, 'api_secret' => $api_secret, 'outer_id' => $outer_id]);
    print_r(json_decode($data, true));*/
}