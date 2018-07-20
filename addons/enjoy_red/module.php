<?php

//bbs.heirui.cn
defined('IN_IA') || die('Access Denied');
define('MB_ROOT', IA_ROOT . '/addons/enjoy_red');
class Enjoy_redModule extends WeModule
{
	public function fieldsFormDisplay($rid = 0)
	{
	}
	public function fieldsFormValidate($rid = 0)
	{
		return '';
	}
	public function fieldsFormSubmit($rid)
	{
	}
	public function ruleDeleted($rid)
	{
	}
	public function settingsDisplay($settings)
	{
		global $_W;
		global $_GPC;
		global $_W;
		global $_GPC;
		if (checksubmit()) {
			load()->func('file');
			mkdirs(MB_ROOT . '/cert');
			$r = true;
			if (!empty($_GPC['cert'])) {
				$ret = file_put_contents(MB_ROOT . '/cert/apiclient_cert.pem.' . $_W['uniacid'], trim($_GPC['cert']));
				$r = $r && $ret;
			}
			if (!empty($_GPC['key'])) {
				$ret = file_put_contents(MB_ROOT . '/cert/apiclient_key.pem.' . $_W['uniacid'], trim($_GPC['key']));
				$r = $r && $ret;
			}
			if (!empty($_GPC['ca'])) {
				$ret = file_put_contents(MB_ROOT . '/cert/rootca.pem.' . $_W['uniacid'], trim($_GPC['ca']));
				$r = $r && $ret;
			}
			if (!$r) {
				message('证书保存失败, 请保证 /addons/enjoy_red/cert/ 目录可写');
			}
			$input = array_elements(array('appid', 'secret', 'mchid', 'password', 'ip', 'mid'), $_GPC);
			$input['appid'] = trim($input['appid']);
			$input['secret'] = trim($input['secret']);
			$input['mchid'] = trim($input['mchid']);
			$input['mid'] = trim($input['mid']);
			$input['password'] = trim($input['password']);
			$input['ip'] = trim($input['ip']);
			$setting = $this->module['config'];
			$setting['api'] = $input;
			if ($this->saveSettings($setting)) {
				message('保存参数成功', 'refresh');
			}
		}
		$config = $this->module['config']['api'];
		if (empty($config['ip'])) {
			$config['ip'] = $_SERVER['SERVER_ADDR'];
		}
		include $this->template('setting');
	}
}