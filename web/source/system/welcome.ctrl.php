<?php
/**
 * [WeYu System] Copyright (c) 2014 BBS.HEIRUI.CN
 * WeYu wechat system,visited http://bbs.heirui.cn/ for more details.
 */
$_W['page']['title'] = '系统';

load()->model('cloud');

$cloud_registered = cloud_prepare();
$cloud_registered = $cloud_registered === true ? true : false;

template('system/welcome');
