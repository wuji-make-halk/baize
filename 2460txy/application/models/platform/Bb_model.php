<?php

class Bb_model extends CI_Model
{
    public $platform = 'bb';

    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function login($game_id)
    {
        $uid = $this->input->get('uid');
        $nickname = $this->input->get('nickname');
        $headimgurl = $this->input->get('headimgurl');
        $channel = $this->input->get('channel');
        $token = $this->input->get('token');
        $this->session->set_userdata('channel', $channel);
        $this->session->set_userdata('token', $token);
        $key="F9NSdXLq2KL7jTLx";
        $sign  = md5($game_id.$channel.$uid.$key);
        $url = 'http://m.bbweiyou.com?uid='.$uid.
        '&gameid='.$game_id.
        '&channel='.$channel.
        '&token='.$token.
        '&nickname='.$nickname.
        '&headimgurl='.$headimgurl.
        '&sign='.$sign;
        if (!$uid || !$nickname || !$headimgurl|| !$channel|| !$token|| !$key|| !$sign) {
            log_message('error', 'information error');

            return false;
        }


        $condition = array(
            'p_uid' => $uid,
            'platform' => $this->platform,
        );

        $user = $this->User_model->get_one_by_condition_array($condition);

        if (!$user) {
            $user = array(
                'platform' => $this->platform,
                'p_uid' => $uid,
                'nickname' => $nickname,
                'avatar' => $headimgurl,
                'create_date' => time(),
            );

            $user_id = $this->User_model->add($user);

            if (!$user_id) {
                log_message('error', 'Login error user create fail');

                return false;
            }

            $user['user_id'] = $user_id;
        }

        // generate random token and save it to cache
        // $this->cache->save('user_info', array('_token'=>$token,'_channel'=>$channel));
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
        $openKey = $this->cache->get($openId.'_token');
        $appId = $game_id;
        $serverId = $this->input->get('serverId');
        $server_name = $this->input->get('server_name');
        $pfid = $this->input->get('pfid');
        $noice = time();
        $sign = md5($openId.$noice.$game->app_key);
        $game_url = $game->game_login_url;
        $spe_server_ids = array('2','3','4','5','6','7','8','9','10','11','8000');
        if (in_array($serverId, $spe_server_ids)) {
            $game_url = 'http://lcby.gz.1251208707.clb.myqcloud.com/juhe/login/juhe';
        }

        $url = "$game_url?openId=$openId&openKey=$openKey&noice=$noice&appId=$appId&sign=$sign&serverId=$serverId&server_name=$server_name";
        log_message('debug', "nineg login:$url");

        header("Location: $url");
    }

    // return order and do the sign varification
    public function get_order_id()
    {
        $order_id = $this->input->get_post('attach');
        $condition = array('u_order_id' => $order_id);
        $this->load->model('Game_order_model');
        $game_order = $this->Game_order_model->get_one_by_condition($condition);

        $orderid = $this->input->get_post('orderid');
        $wxorderid = $this->input->get_post('wxorderid');
        $money = $this->input->get_post('money');
        if ($money!=$game_order->money) {
            return;
        }
        $status = $this->input->get_post('status');
        $sign = $this->input->get_post('sign');
        $my_game_id = $game_order->game_id;
        $key = $this->Game_model->get_key($my_game_id, 'key');
        $my_sign = md5($orderid.$money.$status.$key);
        if ($my_sign==$sign) {
            log_message('debug', $this->platform.' check money '.$money);
            return $order_id;
        } else {
            return;
        }
    }

    public function notify_ok()
    {
        echo 'success';
    }

    public function notify_error()
    {
        $info = array(
            'errcode' => 1001,
            'errmsg' => '签名验证失败',
        );
        $error = json_encode($info);

        echo $error;
    }

    public function sign_order($game_id = '')
    {
        $orderid =  $this->input->get('orderid');
        $money = $this->input->get('money');
        $product = $this->input->get('product');
        $channel = $this->input->get('channel');
        $key = $this->Game_model->get_key($game_id, 'key');
        $sign=md5($orderid.$money.$product.$channel.$key);
        $data = array(
            'sign' => $sign,
        );

        return $data;
    }

    public function create_role_report()
    {
        $date = $this->input->get('date');
        if (!$date) {
            $today_date_str = date('Y-m-d', time());
        } else {
            $today_date_str = $date;
        }
        $from_date = strtotime($today_date_str);

        $page = $this->input->get('page');
        if (!$page) {
            $page = 1;
        }

        $limit = $this->input->get('limit');
        if (!$limit) {
            $limit = 100;
        }

        $condition = array(
            'platform' => $this->platform,
            'create_date >= ' => $from_date,
        );
        $this->load->model('Create_role_report_model');
        $reports = $this->Create_role_report_model->get_report($this->platform, $from_date, ($page - 1) * $limit, $limit);

        if ($reports) {
            $all = $this->Create_role_report_model->get_report($this->platform, $from_date);
            $response = array(
                'success' => true,
                'message' => '成功',
            );
            $data = array(
                'total' => count($reports),
                'totalPage' => ceil(count($all) / $limit),
            );
            $userList = array();

            foreach ($reports as $one) {
                $user = array();
                $user['roleName'] = $one->nickname;
                $user['roleLevel'] = 1;
                $user['createTime'] = date('Y-m-d H:i:s', $one->create_date);
                $user['serverId'] = $one->server_id;
                $userList[$one->p_uid.''] = $user;
            }
            $data['userList'] = $userList;
            $response['data'] = $data;
        } else {
            $response = array(
                'success' => true ,
                'message' => '成功',
            );
        }
        echo json_encode($response);
    }

    public function login_report($value = '')
    {
        $date = $this->input->get('date');
        if (!$date) {
            $today_date_str = date('Y-m-d', time());
        } else {
            $today_date_str = $date;
        }
        $from_date = strtotime($today_date_str);

        $page = $this->input->get('page');
        if (!$page) {
            $page = 1;
        }

        $limit = $this->input->get('limit');
        if (!$limit) {
            $limit = 100;
        }

        $this->load->model('Login_report_model');
        $reports = $this->Login_report_model->get_report($this->platform, $from_date, ($page - 1) * $limit, $limit);

        if ($reports) {
            $all = $this->Login_report_model->get_report($this->platform, $from_date);
            $response = array(
                'success' => true,
                'message' => '成功',
            );
            $data = array(
                'total' => count($reports),
                'totalPage' => ceil(count($all) / $limit),
            );
            $userList = array();

            foreach ($reports as $one) {
                $user = array();
                $user['roleName'] = $one->nickname;
                $user['roleLevel'] = $one->level;
                $user['createTime'] = date('Y-m-d H:i:s', $one->create_date);
                $user['serverId'] = $one->server_id;
                $userList[$one->p_uid.''] = $user;
            }
            $data['userList'] = $userList;
            $response['data'] = $data;
        } else {
            $response = array(
                'success' => true ,
                'message' => '成功',
            );
        }
        echo json_encode($response);
    }

    public function login_collect($data)
    {
    }
    public function create_role_collect($data)
    {
    }

    public function server_name()
    {
        $server_id = $this->input->get('server_id');
        $openId = $this->input->get('openId');
        if (!$openId || !$server_id) {
            $this->Output_model->json_print(1, 'error');

            return;
        }

        $url = 'http://lcby.gz.1251208707.clb.myqcloud.com/juhe/api/?m=player&fn=getserverlist&openId='.$openId;
        $content = $this->Curl_model->curl_get($url);
        if ($content) {
            $servers = json_decode($content);
            foreach ($servers->server_list as $one) {
                if ($one->id == $server_id) {
                    $this->Output_model->json_print(0, 'ok', $one->name);

                    return;
                }
            }
        }

        $url = 'http://lcby.gz.1251208707.clb.myqcloud.com/9g/api/?m=player&fn=getserverlist&openId='.$openId;
        $content = $this->Curl_model->curl_get($url);
        if ($content) {
            $servers = json_decode($content);
            foreach ($servers->server_list as $one) {
                if ($one->id == $server_id) {
                    $this->Output_model->json_print(0, 'ok', $one->name);

                    return;
                }
            }
        }
    }

    public function init($game_id)
    {
        $gameid = $this->Game_model->get_key($game_id, 'gameid');

        // $this->cache->save($user['user_id'].'_info', array('_token'=>$token,'_channel'=>$channel));
        $channel = $this->input->get('channel');
        $token = $this->input->get('token');
        $data = array(
            'gameid' => $gameid,
            'channel' => $channel,
            'token' => $token,
        );

        return $data;
    }
}
