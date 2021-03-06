<?php

class Zeroseven_model extends CI_Model
{
    public $platform = 'zeroseven';
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function login($game_id)
    {
        $user_id = $this->input->get('uid');
        $uname = $this->input->get('uname');
        $time = $this->input->get('time');
        $ext_sdk = $this->input->get('ext_sdk');
        $sign = $this->input->get('sign');
        $sign_type = $this->input->get('sign_type');

        $this->session->set_userdata('uid', $user_id);
        if (!$user_id) {
            return false;
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
        // log_message('debug',$this->platform.' order '.json_encode($_POST).json_encode($HTTP_POST_VARS));
        $data = array();
        $data['orderId'] = $this->input->get_post('game_ordersn');
        $data['order_sn'] = $this->input->get_post('order_sn');
        $data['fee'] = $this->input->get_post('fee');
        $data['ext'] = $this->input->get_post('ext_cp');
        $data['time'] = $this->input->get_post('time');
        $sign = $this->input->get_post('sign');

        $condition = array('u_order_id' => $data['ext']);
        $this->load->model('Game_order_model');
        $game_order = $this->Game_order_model->get_one_by_condition($condition);
        $game_id = $game_order->game_id;
        $this->load->model('Common_model');
        $sign_str=$this->Common_model->sort_params($data);
        // $gameId=$this->Game_model->get_key($game_id, 'gameid');
        $secret=$this->Game_model->get_key($game_id, 'secretkey');
        $my_sign=md5($sign_str.$secret);
        if ($game_order->money == $data['fee']) {
            return $data['ext'];
        } else {
            return false;
        }
    }

    public function notify_ok()
    {
        echo 'success';
    }

    public function notify_error()
    {
        echo 'fail';
        ;
    }
    public function sign_order($game_id = '')
    {
        $sign_data = array();
        $gamekey = $this->Game_model->get_key($game_id, 'gamekey');
        ($this->input->get('ext_sdk'))?$sign_data['ext_sdk'] = $this->input->get('ext_sdk'):'';
        // $sign_data['ext_sdk'] = $this->input->get('ext_sdk');
        $sign_data['ext_cp'] = $this->input->get('ext_cp');
        $sign_data['gamekey']=$gamekey;
        $sign_data['fee'] = $this->input->get('fee');
        $sign_data['game_ordersn'] = $this->input->get('game_ordersn');
        $sign_data['goods_id'] = $this->input->get('goods_id');
        $sign_data['goods_name'] = 'money';
        $sign_data['uid'] = $this->input->get('uid');
        // $sign_data['sign_type'] = 'MD5';
        $time = time();
        $sign_data['time'] = $time;
        $this->load->model('Common_model');
        $sign_str = $this->Common_model->sort_params($sign_data);
        $secretkey = $this->Game_model->get_key($game_id, 'secretkey');
        // echo $sign_str.$secretkey;
        $sign= md5($sign_str.$secretkey);


        $data = array(
            'gamekey'=>$gamekey,
            'sign'=>$sign,
            'time'=>$time,
            'str'=>$sign_str.$secretkey,
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
        $gameid= $this->Game_model->get_key($game_id, 'gamekey');
        return $gameid;
    }
}
