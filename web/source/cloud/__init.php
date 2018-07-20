<?php
/**
 * [WeYu System] Copyright (c) 2014 BBS.HEIRUI.CN
 * WeYu wechat system,visited http://bbs.heirui.cn/ for more details.
 */

define('IN_GW', true);

if(in_array($action, array('profile', 'device', 'callback', 'appstore', 'sms'))) {
	$do = $action;
	$action = 'redirect';
}
if($action == 'touch') {
	exit('success');
}
