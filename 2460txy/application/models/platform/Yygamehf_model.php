<?php

class Yygamehf_model extends CI_Model
{
    public $platform = 'yygamehf';
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function login($game_id)
    {
        $sid = $this->input->get('sid');

        $gameid = $this->Game_model->get_key($game_id, 'appid');
        $iipp=$_SERVER["REMOTE_ADDR"];
        $time = (int)(microtime(true)*1000);
        $appkey = $this->Game_model->get_key($game_id, 'appkey');
        $sign_str = "clientIp=$iipp&game=$gameid&sid=$sid&time=$time$appkey";
        $sign=strtolower(md5($sign_str));
        $requery_url = "http://api.sylogin.game.yy.com/access.do?sid=$sid&game=$gameid&clientIp=$iipp&time=$time&sign=$sign";
        $response = $this->Curl_model->curl_get($requery_url);
        $response = json_decode($response);
        if ($response&&$response->status==200) {
            $user_id=$response->data->guid;
        } else {
            exit();
        }



        $condition = array(
                            'p_uid' => $user_id,
                            'platform' => $this->platform,
                        );
        $user = $this->User_model->get_one_by_condition_array($condition);
        if (!$user) {
            $user = array(
                            'platform' => $this->platform,
                            'p_uid' => $user_id,
                            'create_date' => time(),
                        );
            $user_id = $this->User_model->add($user);
            if (!$user_id) {
                log_message('error', $this->platform." Login error user create $content");

                return false;
            }
            $user['user_id'] = $user_id;
        }

        $this->cache->save($user['user_id'].'_token', md5($user['user_id'].$user['platform'].time()), 86400);

        return $user['user_id'];
    }

    public function game($platform, $game_id)
    {
        $openId = $this->input->get('openId');
        // $frameHeight = $this->input->get('frameHeight');
        // $frameWidth = $this->input->get('frameWidth');

        $servers = array();

        $server1 = array(
                    'server_id' => 8003,
                    'server_name' => '1服',
                );
        $servers[] = $server1;

        $game = $this->Game_model->get_by_game_id($game_id);
        if (!$game) {
            $this->Output_model->json_print(-2, '');

            return;
        }

        $game_name = $game->game_name;

        $url = "/index.php/enter/trun_to_game/$platform/$game_id?openId=$openId";

        $data = array(
                      'servers' => $servers,
                    'game_name' => $game_name,
                    'url' => $url,
                );

        $this->load->view('game_login/allu_lc_login', $data);
    }

    public function trun_to_game($game_id)
    {
        $game = $this->Game_model->get_by_game_id($game_id);
        if (!$game) {
            $this->Output_model->json_print(-2, '');

            return;
        }

        $this->cache->get('user_id');

        $openId = $this->input->get('openId');
        if (!$openId) {
            echo 'error';

            return;
        }

        //执行统计请求
        $access_token = md5(substr(time(), 0, 8).'aoyouxi');

        $condition = array('user_id' => $openId);
        $user = $this->User_model->get_one_by_condition($condition);
        if (!$user) {
            echo 'error';

            return;
        }

        $user_id = $user->p_uid;
        //定义统计请求的地址：
        // $url = "http://h5.xileyougame.com/tongji/tongji_game_login/{$user_id}/{$game_id}/{$access_token}";
        // $this->Curl_model->curl_get($url);

        //如果这块功能实现完成， 需要把 allugame.com 的 controllers/game.php 的第153行注释掉，这块是现在使用的游戏登录统计。

        $openKey = $this->cache->get($openId.'_token');
        $appId = $game_id;
        $serverId = $this->input->get('serverId');
        $noice = time();
        $sign = md5($openId.$noice.$game->app_key);
        $game_url = $game->game_login_url;
        $url = "$game_url?openId=$openId&openKey=$openKey&noice=$noice&appId=$appId&sign=$sign&serverId=$serverId";
        log_message('debug', "allu login:$url");

        header("Location: $url");
    }

    // return order and do the sign varification
    public function get_order_id()
    {
        $game = $this->input->get('game');
        $guid = $this->input->get('guid');
        $sdkOrderIdsdkOrderId = $this->input->get('sdkOrderId');
        $payId = $this->input->get('payId');
        $orderTime = $this->input->get('orderTime');
        $amount = $this->input->get('amount');
        $gameCoin = $this->input->get('gameCoin');
        $roleId = $this->input->get('roleId');
        $server = $this->input->get('server');
        $extData = $this->input->get('extData');
        $sign = $this->input->get('sign');


        $ext = $extData;

        $condition = array('u_order_id' => $ext);
        $this->load->model('Game_order_model');
        $game_order = $this->Game_order_model->get_one_by_condition($condition);
        $game_id = $game_order->game_id;

        if (intval($amount*100)==$game_order->money) {
            return $ext;
        } else {
            return false;
        }
    }

    public function notify_ok()
    {
        echo '{"status": 200,"message": "成功"}';
    }

    public function notify_error()
    {
        echo '{"status": 2,"message": "重复回调"}';
    }
    public function sign_order($game_id = '')
    {
        $order_id = $this->input->get('order_id');
        $money = $this->input->get('money');
        $openId = $this->input->get('openId');
        $userId = $this->input->get('userId');
        $goodsName = $this->input->get('goodsName');
        $CpID=$this->Game_model->get_key($game_id, 'CpID');
        $AppID=$this->Game_model->get_key($game_id, 'AppID');
        $AppKey = $this->Game_model->get_key($game_id, 'Appkey');
        $time = date('YmdHis', time());
        $md5_AppKey = strtolower(md5($AppKey));
        $notify = "http://h5sdk.zytxgame.com/index.php/api/notify/$this->platform/$game_id";
        $version ="1.0";
        $sign=strtolower(md5("appId=$AppID&cpId=$CpID&cpOrderNumber=$order_id&extInfo=$order_id&notifyUrl=$notify&orderAmount=$money&orderDesc=$goodsName&orderTime=$time&orderTitle=$goodsName&version=$version&$md5_AppKey"));


        $person = array(
            'notifyUrl'=>"$notify",
            'orderAmount'=>"$money",
            'orderDesc'=>urlencode($goodsName),
            'orderTitle'=>urlencode($goodsName),
            'orderTime'=>"$time",
            'cpId'=>"$CpID",
            'appId'=>"$AppID",
            'cpOrderNumber'=>"$order_id",
            'version'=>"$version",
            'extInfo'=>"$order_id",
            'signature'=>"$sign",
            'signMethod'=>"MD5"
        );
        $post_url = "https://pay.vivo.com.cn/vcoin/trade";
        $requery = $this->Curl_model->curl_post($post_url, $person);
        $data = array(
            "params"=>$requery,
            "appid"=>$AppID,
        );

        return $data;
    }

    public function init($game_id)
    {
        $data = array('usertype' => $this->session->userdata('usertype'));

        return $data;
    }
    public function create_role_collect($data)
    {
    }

    public function login_collect($data)
    {
    }
    public function focus($game_id='')
    {
        $gameid= $this->Game_model->get_key($game_id, 'gameid');
        return $gameid;
    }
}
