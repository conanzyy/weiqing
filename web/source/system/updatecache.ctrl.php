<?php
/**
 * [WeYu System] Copyright (c) 2014 BBS.HEIRUI.CN
 * WeYu wechat system,visited http://bbs.heirui.cn/ for more details.
 */
$_W['page']['title'] = '更新缓存 - 系统管理';
load()->model('cache');
load()->model('setting');
if (checksubmit('submit')) {
	cache_clean();
	
	cache_build_template();
	cache_build_users_struct();
	cache_build_setting();
	cache_build_frame_menu();
	cache_build_module_subscribe_type();
	cache_build_cloud_ad();
	message('缓存更新成功！', url('system/updatecache'));
} else {
	template('system/updatecache');
}



















