<?php namespace wechat\build;

use wechat\Wx;

class Material extends Wx
{
    /**
     * 资源类素材上传
     *
     * @param $file 文件
     * @param string $type 素材类型
     * @param int $materialType 永久0 临时素材1
     *
     * @return array|bool
     */
    public function upload($file, $type = 'image', $materialType = 0)
    {
        switch ($materialType) {
            case 0:
                //永久
                $url = $this->apiUrl . '/cgi-bin/material/add_material?access_token=' . $this->getAccessToken() . '&type=' . $type;
                break;
            default:
                //临时
                $url = $this->apiUrl . '/cgi-bin/media/upload?access_token=' . $this->getAccessToken() . '&type=' . $type;
        }
        //curl   get post   php 5.5> CURLFile类     @
        $file = realpath($file);//将文件转为绝对路径
        if (class_exists('CURLFile', false)) {//不要\也可以
            $data = [
                'media' => new \CURLFile($file)
            ];
        } else {
            $data = [
                'media' => '@' . $file
            ];
        }
        $res = $this->curl($url, $data);

        return $this->get(json_decode($res, true));
    }

    /**
     * 获取素材列表
     *
     * @param str $type 素材类型 图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param str $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     * @param str $count  返回素材的数量，取值在1到20之间
     *
     * @return array|bool
     */
    public function batchGet($type = 'image', $offset = '0', $count = '10')
    {
        $url = $this->apiUrl . '/cgi-bin/material/batchget_material?access_token=' . $this->getAccessToken();
        $data = ['type' => $type, 'offset' => $offset, 'count' => $count];
        $data = json_encode($data);//这里需要传Json数据
        $res = $this->curl($url, $data);
        return $this->get(json_decode($res, true));

    }

    public function getMaterial($media)
    {
        $url = $this->apiUrl . '/cgi-bin/material/get_material?access_token=' . $this->getAccessToken();
        $data = ['media' => $media];
        $res = $this->curl($url, $data);
        return $this->get(json_decode($res, true));

    }
}