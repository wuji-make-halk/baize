<?php

class Wanss_model extends CI_Model
{
    public $platform = 'wanss';
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function login($game_id)
    {
        $user_id = $this->input->get('userid');
        // $username = $this->input->get('username');
        $time = $this->input->get('time');
        $gameid = $this->input->get('gameid');
        $key = $this->Game_model->get_key($game_id, 'key');
        $this->session->set_userdata('userid', $user_id);
        $this->session->set_userdata('gameid', $gameid);
        $this->cache->save('gameid', $gameid, 3600*24*7);


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
        $stat = $this->input->get_post('stat');
        $orderId = $this->input->get_post('trade_no');
        $orderMoney = $this->input->get_post('amount');
        $condition = array('u_order_id' => $orderId);
        $this->load->model('Game_order_model');         //无平台预留字段 orderid与2460 不匹配
        $game_order = $this->Game_order_model->get_one_by_condition($condition);

        if ($game_order&&$stat==1) {
            if (intval($orderMoney) == $game_order->money) {
                log_message('debug', $this->platform.' ordermoney: '.$orderMoney.' game_order :'.$orderId.' stat is '.$stat);
                return $orderId;
            } else {
                log_message('error', $this->platform.' money not match');
                return false;
            }
        } else {
            log_message('error', $this->platform.' game_order is null');
            return false;
        }

        return false;
    }

    public function notify_ok()
    {
        echo '{“status”:”success”}';
    }

    public function notify_error()
    {
        echo '{“status”:”FAILED”}';
        ;
    }
    public function sign_order($game_id = '')
    {
        $user_id= $this->input->get('userId');
        // $user_id= $this->session->userdata('userid');
        // $gameid = $this->session->userdata('gameid');
        $money=$this->input->get('money');
        $ext=$this->input->get('ext');

        $gameid = $this->cache->get('gameid');
        $time = time();
        $key = $this->Game_model->get_key($game_id, 'key');
        $sign_str = $user_id.$gameid.$money.$ext.$key;
        $sign = md5($sign_str);
        log_message('debug', $this->platform.' sign_str is '.$sign_str.' sign '.$sign);
        $data = array(
             'sign' => $sign,
             'gameid' => $gameid,
        );

        return $data;
    }

    public function init($game_id)
    {
        $data = array('usertype' => $this->session->userdata('usertype'));

        return $data;
    }

    public function login_collect($data)
    {
    }
    public function focus($game_id='')
    {
    }
}
