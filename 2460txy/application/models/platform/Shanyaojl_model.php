<?php

class Shanyaojl_model extends CI_Model
{
    public $platform = 'shanyaojl';
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function login($game_id)
    {
        $user_id = $this->input->get('user_id');

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
                log_message('error', $this->platform . " Login error user create $content");

                return false;
            }
            $user['user_id'] = $user_id;
        }

        $this->cache->save($user['user_id'] . '_token', md5($user['user_id'] . $user['platform'] . time()), 86400);

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
        $access_token = md5(substr(time(), 0, 8) . 'aoyouxi');

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

        $openKey = $this->cache->get($openId . '_token');
        $appId = $game_id;
        $serverId = $this->input->get('serverId');
        $noice = time();
        $sign = md5($openId . $noice . $game->app_key);
        $game_url = $game->game_login_url;
        $url = "$game_url?openId=$openId&openKey=$openKey&noice=$noice&appId=$appId&sign=$sign&serverId=$serverId";
        log_message('debug', "allu login:$url");

        header("Location: $url");
    }

    // return order and do the sign varification
    public function get_order_id()
    {
        log_message('debug', $this->platform . ' get_order_id:  json' . json_encode($_POST));
        $res_xml = $this->input->get_post('nt_data');
        $key = $this->Game_model->get_key(1460, 'CallbackKey');
        $res_xml = $this->decode($res_xml,$key);
        libxml_disable_entity_loader(true); // 防止xml跨站攻击
        $ret = json_decode(json_encode(simplexml_load_string($res_xml, 'simpleXMLElement', LIBXML_NOCDATA)), true);
        $data = array();

        $ext = $ret['message']['extras_params'];
        $money = $ret['message']['amount'];
        log_message('debug', $this->platform . ' pay money' . $money);
        if ($money < 1) {
            return false;
        }
        $condition = array('u_order_id' => $ext);
        $this->load->model('Game_order_model');
        $game_order = $this->Game_order_model->get_one_by_condition($condition);
        $game_id = $game_order->game_id;
        $key = $this->Game_model->get_key($game_id, 'key');
        if (intval($money * 100) != $game_order->money) {
            return false;
        }

        return $ext;
    }

    public function notify_ok()
    {
        echo 'SUCCESS';
    }

    public function notify_error()
    {
        echo 'fail';

    }
    public function sign_order($game_id = '')
    {
        $productCode = $this->Game_model->get_key($game_id, 'ProductCode');
        $callbackUrl = "http://" . $_SERVER['HTTP_HOST'] . "/index.php/enter/play/" . $this->platform . "/$game_id";

        $data = array(
            'productCode' => $productCode,
            'callbackUrl' => $callbackUrl,
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
    public function focus($game_id = '')
    {
        $productCode = $this->Game_model->get_key($game_id, 'ProductCode');
        $productKey = $this->Game_model->get_key($game_id, 'ProductKey');

        $data = array(
            'productCode' => $productCode,
            'productKey' => $productKey,
        );
        return $data;
    }
    public function decode($strEncode, $keys) {
		if(empty($strEncode)){
			return $strEncode;
		}
		preg_match_all('(\d+)', $strEncode, $list);
		$list = $list[0];
		if (count($list) > 0) {
			$keys = self::getBytes($keys);
			for ($i = 0; $i < count($list); $i++) {
				$keyVar = $keys[$i % count($keys)];
				$data[$i] =  $list[$i] - (0xff & $keyVar);
			}
			return self::toStr($data);
		} else {
			return $strEncode;
		}
	}

	/**
	 * ������Ϸͬ��ǩ��
	 */
	public static function getSign($params,$callbackkey){

		return md5($params['nt_data'].$params['sign'].$callbackkey);
	}

	/**
	 * MD5ǩ���滻
	 */
	static private function replaceMD5($md5){

		strtolower($md5);
		$bytes = self::getBytes($md5);

		$len = count($bytes);

	 	if ($len >= 23){
			$change = $bytes[1];
           	$bytes[1] = $bytes[13];
            $bytes[13] = $change;

            $change2 = $bytes[5];
            $bytes[5] = $bytes[17];
            $bytes[17] = $change2;

            $change3 = $bytes[7];
            $bytes[7] = $bytes[23];
            $bytes[23] = $change3;
       }else{
			return $md5;
       }

       return self::toStr($bytes);
	}

	/**
	 * ת���ַ�����
	 */
	private static function getBytes($string) {
        $bytes = array();
        for($i = 0; $i < strlen($string); $i++){
             $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    /**
     * ת���ַ���
     */
    private static function toStr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }
   		return $str;
    }
}
