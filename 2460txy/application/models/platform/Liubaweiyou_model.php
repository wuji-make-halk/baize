<?php

class Liubaweiyou_model extends CI_Model
{
    public $platform = 'liubaweiyou_model';
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function login($game_id)
    {
        $user_id = $this->input->get('account');
        $appid = $this->input->get('appid');
        $ts = $this->input->get('ts');
        $pf = $this->input->get('pf');
        $follow = $this->input->get('follow');
        $share = $this->input->get('share');
        $sign = $this->input->get('sign');

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
        $orderid = $this->input->get_post('orderid');
        $itemname = $this->input->get_post('itemname');
        $money = $this->input->get_post('money');
        $ext = $this->input->get_post('attach');
        $ts = $this->input->get_post('ts');
        $pf = $this->input->get_post('pf');
        $account = $this->input->get_post('account');
        $sign = $this->input->get_post('sign');
        $condition = array('u_order_id' => $ext);
        $this->load->model('Game_order_model');
        $game_order = $this->Game_order_model->get_one_by_condition($condition);
        $game_id = $game_order->game_id;
        $key=$this->Game_model->get_key($game_id, 'key');
        $sign_data = array(
            'orderid'=>$orderid,
            'money'=>$money,
            'account'=>$account,
            'itemname'=>$itemname,
            'attach'=>$ext,
            // 'serverid'=>$serverid,
            // 'appid'=>$appid,
            'pf'=>$pf,
            'ts'=>$ts,
        );
        $my_sign = $this->makeSign($sign_data, $key);


        if ($sign==$my_sign) {
            if (intval($money)*100==$game_order->money) {
                return $ext;
            }
        } else {
            return false;
        }
    }

    public function notify_ok()
    {
        echo 'ok';
    }

    public function notify_error()
    {
        echo 'fail';
        ;
    }
    public function sign_order($game_id = '')
    {
        $money = $this->input->get('money');
        $account = $this->input->get('account');
        $itemname = $this->input->get('itemname');
        $attach = $this->input->get('attach');
        $serverid = $this->input->get('serverid');
        $appid = $this->input->get('appid');
        $pf = $this->input->get('pf');
        $key=$this->Game_model->get_key($game_id, 'key');
        $sign_data = array(
            'money'=>$money,
            'account'=>$account,
            'itemname'=>$itemname,
            'attach'=>$attach,
            'serverid'=>$serverid,
            'appid'=>$appid,
            'pf'=>$pf,
        );
        $sign = $this->makeSign($sign_data, $key);
        $data = array(
            'sign'=>$sign,
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
    private function makeSign($param, $secureKey)
    {
        ksort($param);
        $res = [];
        foreach ($param as $key => $value) {
            $res[] = $key."=".$value;
        }
        $str = join($res, "&");
        return md5($str . $secureKey);
    }
}
