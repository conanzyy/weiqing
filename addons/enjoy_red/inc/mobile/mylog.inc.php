<?php
global $_W, $_GPC;
$modulePublic = '../addons/enjoy_red/public/';
require_once MB_ROOT . '/controller/Fans.class.php';
require_once MB_ROOT . '/controller/Act.class.php';
$puid=intval($_GET['puid']);
$fans = new Fans();
$act = new Act();
	//授权登录，获取粉丝信息
	$user = $this->auth($puid);
// $user['openid']="omWcNs4YdxWrGcUswqMlg2Nwk7nc";
// $user['uniacid']="5";

	//取活动信息
	$actdetail=$act->getact();
	//提现
// 	$county=pdo_fetchcolumn("select ABS(sum(money)) from ".tablename('enjoy_red_log')." where openid='".$user['openid']."' and uniacid=".$user['uniacid']." and money<0");
// 	//累计的钱
// 	$countm=pdo_fetchcolumn("select sum(money) from ".tablename('enjoy_red_log')." where openid='".$user['openid']."' and uniacid=".$user['uniacid']." and money>0");
	$fans=pdo_fetch("select cashed,total from ".tablename('enjoy_red_fans')." where uniacid=".$user['uniacid']." and openid='".$user['openid']."'");
	$county=$fans['cashed'];
	if(empty($county)){
		$county=0;
	}
	//累计的钱
	$countm=$fans['total'];
	if(empty($countm)){
		$countm=0;
	}
	
	$mylog=pdo_fetchall("select * from ".tablename('enjoy_red_log')." where openid='".$user['openid']."' and uniacid=".$user['uniacid']." order by createtime desc");
	$cashed=pdo_fetchall("select * from ".tablename('enjoy_red_back')." where openid='".$user['openid']."' and uniacid=".$user['uniacid']." order by createtime desc");
	












include $this->template('mylog');