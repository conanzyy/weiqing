<?php

//bbs.heirui.cn
defined('IN_IA') || die('Access Denied');
define('MB_ROOT', IA_ROOT . '/addons/enjoy_red');
class Enjoy_redModuleSite extends WeModuleSite
{
	public function tpl_form_field_imagepic($name, $value = '', $default = '', $options = array())
	{
		global $_W;
		if (empty($default)) {
			$default = './resource/images/nopic.jpg';
		}
		$val = $default;
		if (!empty($value)) {
			$val = tomedia($value);
		}
		if (empty($options['tabs'])) {
			$options['tabs'] = array('upload' => 'active', 'browser' => '', 'crawler' => '');
		}
		if (!empty($options['global'])) {
			$options['global'] = true;
		} else {
			$options['global'] = false;
		}
		if (empty($options['class_extra'])) {
			$options['class_extra'] = '';
		}
		if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
			if (!preg_match('/^\\w+([\\/]\\w+)?$/i', $options['dest_dir'])) {
				die('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
			}
		}
		$options['direct'] = true;
		$options['multi'] = false;
		if (isset($options['thumb'])) {
			$options['thumb'] = !empty($options['thumb']);
		}
		$s = '';
		if (!defined('TPL_INIT_IMAGE')) {
			$s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showImageDialog(elm, opts, options) {\r\n\t\t\t\trequire([\"util\"], function(util){\r\n\t\t\t\t\tvar btn = \$(elm);\r\n\t\t\t\t\tvar ipt = btn.parent().prev();\r\n\t\t\t\t\tvar val = ipt.val();\r\n\t\t\t\t\tvar img = ipt.parent().next().children();\r\n\t\r\n\t\t\t\t\tutil.image(val, function(url){\r\n\t\t\t\t\t\tif(url.url){\r\n\t\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\t\timg.get(0).src = url.url;\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\tipt.val(url.filename);\r\n\t\t\t\t\t\t\tipt.attr(\"filename\",url.filename);\r\n\t\t\t\t\t\t\tipt.attr(\"url\",url.url);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\tif(url.media_id){\r\n\t\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\t\timg.get(0).src = \"\";\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\tipt.val(url.media_id);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}, opts, options);\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t\tfunction deleteImage(elm){\r\n\t\t\t\trequire([\"jquery\"], function(\$){\r\n\t\t\t\t\t\$(elm).prev().attr(\"src\", \"./resource/images/nopic.jpg\");\r\n\t\t\t\t\t\$(elm).parent().prev().find(\"input\").val(\"\");\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
			define('TPL_INIT_IMAGE', true);
		}
		$s .= '<div class=\'input-group ' . $options['class_extra'] . '\'><input type=\'text\' name=\'' . $name . '\' value=\'' . $value . '\'' . ($options['extras']['text'] ? $options['extras']['text'] : '') . ' class=\'form-control\' autocomplete=\'off\'><span class=\'input-group-btn\'><button class=\'btn btn-default\' type=\'button\' onclick=\'showImageDialog(this, \\\'' . base64_encode(iserializer($options)) . '\\\', ' . str_replace('"', '\'', json_encode($options)) . ');\'>选择图片</button></span></div>';
		if (!empty($options['tabs']['browser']) || !empty($options['tabs']['upload'])) {
			$s .= '<div class=\'input-group ' . $options['class_extra'] . '\' style=\'margin-top:.5em;\'><img src=\'' . $val . '\' onerror=\'this.src=\\\'' . $default . '\\\'; this.title=\\\'图片未找到.\\\'\' class=\'img-responsive img-thumbnail\' ' . ($options['extras']['image'] ? $options['extras']['image'] : '') . ' width=\'150\' /><em class=\'close\' style=\'position:absolute; top: 0px; right: -14px;\' title=\'删除这张图片\' onclick=\'deleteImage(this)\'>×</em></div>';
		}
		return $s;
	}
	public function doMobilecash()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_GPC['openid'];
		require_once MB_ROOT . '/controller/Act.class.php';
		$act = new Act();
		$actdetail = $act->getact();
		$puid = intval($_GET['puid']);
		$user = pdo_fetch('select * from ' . tablename('enjoy_red_fans') . ' where uniacid=' . $uniacid . ' and openid=\'' . $openid . '\'');
		$total = $user['total'] * 100;
		$cashmoney = pdo_fetchcolumn('select cashmoney from ' . tablename('enjoy_red_pack') . ' where uniacid=' . $uniacid . ' and cashmin<=' . $total . " and\r\n\t\t\tcashmax>=" . $total . ' limit 1');
		if (empty($cashmoney)) {
			$cashmoney = pdo_fetchcolumn('select cashmoney from ' . tablename('enjoy_red_pack') . ' where uniacid=' . $uniacid . ' order by cashmoney desc limit 1');
		}
		$cashmoney = $cashmoney * 0.01;
		$fans = pdo_fetch('select cashed,total from ' . tablename('enjoy_red_fans') . ' where uniacid=' . $user['uniacid'] . ' and openid=\'' . $user['openid'] . '\'');
		$money = $fans['total'] - $fans['cashed'];
		if ($actdetail['cashgz'] == 1 && $user['subscribe'] == 0) {
			$res['type'] = -4;
		} else {
			if ($cashmoney <= $money) {
				$fee = $money * 100;
				$api = $this->module['config']['api'];
				if (empty($api)) {
					$res['type'] = -2;
					die;
				}
				$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
				$pars = array();
				$pars['mch_appid'] = $api['appid'];
				$pars['mchid'] = $api['mchid'];
				$pars['nonce_str'] = random(32);
				$pars['partner_trade_no'] = time() . random(3, 1);
				$pars['openid'] = $openid;
				$pars['check_name'] = 'NO_CHECK';
				$pars['amount'] = $fee;
				$pars['desc'] = '分享好友后获得' . $actdetail['share_chance'] . $actdetail['unit'] . ',' . $_W['account']['name'] . '(' . $_W['account']['account'] . ')现金小游戏';
				$pars['spbill_create_ip'] = $api['ip'];
				ksort($pars, SORT_STRING);
				$string1 = '';
				foreach ($pars as $k => $v) {
					$string1 .= $k . '=' . $v . '&';
				}
				$string1 .= 'key=' . $api['password'];
				$pars['sign'] = strtoupper(md5($string1));
				$xml = array2xml($pars);
				$extras = array();
				$extras['CURLOPT_CAINFO'] = MB_ROOT . '/cert/rootca.pem.' . $uniacid;
				$extras['CURLOPT_SSLCERT'] = MB_ROOT . '/cert/apiclient_cert.pem.' . $uniacid;
				$extras['CURLOPT_SSLKEY'] = MB_ROOT . '/cert/apiclient_key.pem.' . $uniacid;
				$procResult = NULL;
				load()->func('communication');
				$resp = ihttp_request($url, $xml, $extras);
				if (is_error($resp)) {
					$procResult = $resp;
				} else {
					$arr = json_decode(json_encode((array) simplexml_load_string($resp['content'])), true);
					$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
					$dom = new DOMDocument();
					if ($dom->loadXML($xml)) {
						$xpath = new DOMXPath($dom);
						$code = $xpath->evaluate('string(//xml/return_code)');
						$ret = $xpath->evaluate('string(//xml/result_code)');
						if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
							$procResult = array('errno' => 0, 'error' => 'success');
						} else {
							$error = $xpath->evaluate('string(//xml/err_code_des)');
							$procResult = array('errno' => -2, 'error' => $error);
						}
					} else {
						$procResult = array('errno' => -1, 'error' => '未知错误');
					}
				}
				if ($procResult['errno'] != 0) {
					$res['type'] = -3;
					$res['error'] = $procResult['error'];
				} else {
					$insert = array('uniacid' => $uniacid, 'openid' => $openid, 'money' => $money, 'createtime' => TIMESTAMP);
					$ress = pdo_insert('enjoy_red_back', $insert);
					$res['type'] = 1;
					if ($ress == 1) {
						pdo_query('update ' . tablename('enjoy_red_fans') . ' set cashed=cashed+' . $money . ' where uniacid=' . $uniacid . ' and openid=\'' . $openid . '\'');
						require_once MB_ROOT . '/controller/weixin.class.php';
						$url = $_W['siteroot'] . $this->createMobileUrl('mylog');
						$template = array('touser' => $openid, 'template_id' => $api['mid'], 'url' => $url, 'topcolor' => '#743a3a', 'data' => array('first' => array('value' => urlencode('恭喜您，提现成功，请实时查看微信到账通知'), 'color' => '#2F1B58'), 'money' => array('value' => urlencode($money . '元'), 'color' => '#2F1B58'), 'timet' => array('value' => urlencode(date('y-m-d h:i:s', time())), 'color' => '#2F1B58'), 'remark' => array('value' => urlencode('分享好友后获得' . $actdetail['share_chance'] . $actdetail['unit'] . ',' . $_W['account']['name'] . '(' . $_W['account']['account'] . ')现金直接到账游戏,点击查看提现记录哦'), 'color' => '#2F1B58')));
						$weixin = new class_weixin($api['appid'], $api['secret']);
						$weixin->send_template_message(urldecode(json_encode($template)));
					}
				}
			} else {
				$res['type'] = -1;
				$res['cashmoney'] = $cashmoney;
			}
		}
		$res['unit'] = $actdetail['unit'];
		if ($money < 0.01) {
			$res['money'] = 0;
		} else {
			$res['money'] = $money;
		}
		echo json_encode($res);
		die;
	}
	public function getLocation($message, $setting)
	{
		$sql = 'select message from ' . tablename('stat_msg_history') . ' where uniacid=\'' . $message['uniacid'] . '\'  and from_user=\'' . $message['from_user'] . '\'' . ' and createtime>' . $message['time'] . ' and type=\'trace\' order by createtime desc';
		$stat_msg_history = pdo_fetch($sql);
		if (!empty($stat_msg_history) && !empty($stat_msg_history['message'])) {
			$ret = iunserializer($stat_msg_history['message']);
			return $ret;
		}
		WeUtility::logging('getLocation2', $sql . '==>' . json_encode($message));
		return '';
	}
	public function getAddr($location)
	{
		if (empty($location)) {
			return false;
		}
		if (empty($location['x']) && empty($location['location_x'])) {
			return false;
		}
		$loc = '';
		if (!empty($location['location_x']) && !empty($location['location_y'])) {
			$loc = $location['location_x'] . ',' . $location['location_y'];
		}
		if (!empty($location['x']) && !empty($location['y'])) {
			$loc = $location['x'] . ',' . $location['y'];
		}
		if (empty($loc)) {
			return false;
		}
		$url = 'http://api.map.baidu.com/geocoder/v2/?ak=qen1OGw9ddzoDQrTX35gote2&location=' . $loc . '&output=json';
		$ret = json_decode(file_get_contents($url), true);
		if ($ret['status'] != 0) {
			WeUtility::logging('getAddr', $url . '==>' . json_encode($ret));
			return false;
		}
		if (strpos($ret['result']['formatted_address'], $location['addr']) === false) {
			WeUtility::logging('getAddr', 'error==>' . json_encode($location));
			return false;
		}
		return true;
	}
	protected function auth($puid)
	{
		global $_W;
		session_start();
		$openid = $_SESSION['__:proxy:openid'];
		require_once MB_ROOT . '/controller/Fans.class.php';
		$f = new Fans();
		if (!empty($openid)) {
			$exists = $f->getOne($openid, true);
			if (!empty($exists)) {
				if ($exists['black'] == 1) {
					message('抱歉，您没有参与资格', '', 'error');
					die;
				}
				return $exists;
			}
		}
		$api = $this->module['config']['api'];
		if (empty($api)) {
			message('系统还未开放');
		}
		$callback = $_W['siteroot'] . 'app' . substr($this->createMobileUrl('auth', array('puid' => $puid)), 1);
		$callback = urlencode($callback);
		$state = $_SERVER['REQUEST_URI'];
		$stateKey = substr(md5($state), 0, 8);
		$_SESSION['__:proxy:forward'] = $state;
		$forward = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $api['appid'] . '&redirect_uri=' . $callback . '&response_type=code&scope=snsapi_userinfo&state=' . $stateKey . '#wechat_redirect';
		header('Location: ' . $forward);
		die;
	}
	public function doMobileAuth()
	{
		global $_GPC;
		global $_W;
		$puid = $_GPC['puid'];
		session_start();
		$api = $this->module['config']['api'];
		if (empty($api)) {
			message('系统还未开放');
		}
		$code = $_GPC['code'];
		load()->func('communication');
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $api['appid'] . '&secret=' . $api['secret'] . '&code=' . $code . '&grant_type=authorization_code';
		$resp = ihttp_get($url);
		if (is_error($resp)) {
			message('系统错误, 详情: ' . $resp['message']);
		}
		$auth = @json_decode($resp['content'], true);
		if (is_array($auth) && !empty($auth['openid'])) {
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $auth['access_token'] . '&openid=' . $auth['openid'] . '&lang=zh_CN';
			$resp = ihttp_get($url);
			if (is_error($resp)) {
				message('系统错误');
			}
			$info = @json_decode($resp['content'], true);
			$account = account_fetch($_W['uniacid']);
			$level = $account['level'];
			if ($level < 4) {
				if (empty($puid)) {
					$subscribe = 1;
				} else {
					$subscribe = 0;
				}
			} else {
				$accObj = WeiXinAccount::create($_W['account']);
				$userinfo = $accObj->fansQueryInfo($info['openid']);
				$subscribe = $userinfo['subscribe'];
			}
			if (is_array($info) && !empty($info['openid'])) {
				$user = array();
				$user['uniacid'] = $_W['uniacid'];
				$user['openid'] = $info['openid'];
				$user['unionid'] = $info['unionid'];
				$user['nickname'] = $info['nickname'];
				$user['gender'] = $info['sex'];
				$user['city'] = $info['city'];
				$user['state'] = $info['province'];
				$user['avatar'] = $info['headimgurl'];
				$user['country'] = $info['country'];
				$user['subscribe'] = $subscribe;
				$user['subscribe_time'] = TIMESTAMP;
				$user['puid'] = $puid;
				if (!empty($user['avatar'])) {
					$user['avatar'] = rtrim($user['avatar'], '0');
					$user['avatar'] .= '132';
				}
				require_once MB_ROOT . '/controller/Fans.class.php';
				$f = new Fans();
				$f->save($user);
				$_SESSION['__:proxy:openid'] = $user['openid'];
				$forward = $_SESSION['__:proxy:forward'];
				header('Location: ' . $forward);
				die;
			}
		}
		message('系统错误');
	}
	public function getrand($proArr)
	{
		$result = '';
		$proSum = array_sum($proArr);
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum);
			if ($randNum <= $proCur) {
				$result = $key;
				break;
			}
			$proSum -= $proCur;
		}
		unset($proArr);
		return $result;
	}
	public function inject_check($sql_str)
	{
		return eregi('select|insert|and|or|update|delete|\'|\\/\\*|\\*|\\.\\.\\/|\\.\\/|union|into|load_file|outfile', $sql_str);
	}
	public function verify_id($id = NULL)
	{
		if (!$id) {
			die('没有提交参数！');
		} else {
			if ($this->inject_check($id)) {
				die('提交的参数非法！');
			} else {
				if (!is_numeric($id)) {
					die('提交的参数非法！');
				}
			}
		}
		$id = intval($id);
		return $id;
	}
}