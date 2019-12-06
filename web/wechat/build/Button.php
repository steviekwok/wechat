<?php namespace wechat\build;

use wechat\Wx;
/*
 * 微信按钮管理
 * Class Button
 * @package wechat\build
 */
class Button extends Wx {
    /*创建按钮
     * @param str $data 创建按钮的josn格式的数据
     * @return arr 数组格式的创建返回结果
     */
    public function create($data) {
        $url = $this->apiUrl . '/cgi-bin/menu/create?access_token=' . $this->getAccessToken();

        $result = $this->curl($url, $data);
        return $this->get(json_decode($result,true));
    }

    //删除按钮
    public function flush() {
        $url = $this->apiUrl . '/cgi-bin/menu/delete?access_token=' . $this->getAccessToken();

        $result = $this->curl($url);
        return $this->get(json_decode($result,true));
    }
}