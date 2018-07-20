<?php
//微鱼微信-黑锐微信开发社区bbs.heirui.cn
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once IA_ROOT . '/addons/ewei_shopv2/version.php';
require_once IA_ROOT . '/addons/ewei_shopv2/defines.php';
require_once EWEI_SHOPV2_INC . 'functions.php';
class Ewei_shopv2Module extends WeModule
{
	public function welcomeDisplay()
	{
		header('location: ' . webUrl());
		exit();
	}
}

?>
