<?php
//微鱼微信管理系统分销-黑-锐微信开发社区
if (!defined('ES_PATH')) {
	exit('Access Denied');
}

class EmptyController extends Controller
{
	public function index()
	{
		global $controller;
		trigger_error(' Controller <b>' . $controller . '</b> Not Found !');
	}
}


?>