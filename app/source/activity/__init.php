<?php
/**
 * [WeYu System] Copyright (c) 2014 BBS.HEIRUI.CN
 * WeYu wechat system,visited http://bbs.heirui.cn/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

if ($controller == 'activity') {
	header('Location: ' . murl('entry', array('m' => 'we7_coupon', 'do' => 'activity')));
	exit;
}
