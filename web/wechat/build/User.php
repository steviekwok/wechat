<?php namespace wechat\build;

use wechat\Wx;

class User extends Wx
{
    /*
     *获取粉丝的基本信息
     * @param str $openid 用户编号（微信服务器）关注时获取的
     * @param str $lang 语言 默认是中文
     *
     * @return array 成功时返回用户信息的数组，出错返回包含错误代码的数组
     */
    public function getUserInfo($openid, $lang = 'zh_CN')
    {
        $url = $this->apiUrl . '/cgi-bin/user/info?access_token=' . $this->getAccessToken() . '&openid=' . $openid . '&lang=' . $lang;
        $res = $this->curl($url);
        return $this->get(json_decode($res, true));
    }

    //批量获取粉丝信息
    public function getUserInfoList(array $data, $lang = 'zh_CN')
    {
        $url = $this->apiUrl . '/cgi-bin/user/info/batchget?access_token=' . $this->getAccessToken();
        $post['user_list'] = [];
        foreach ((array)$data as $openid) {
            $post['user_list'][] = ['openid' => $openid, 'lang' => $lang];
        }
        $res = $this->curl($url, json_encode($post, JSON_UNESCAPED_UNICODE));
        return json_decode($res, true);
    }
}