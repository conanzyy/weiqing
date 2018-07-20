<?php
global $_W,$_GPC;
// load()->model('account');
// echo $this->account_types;
$uniacid=$_W['uniacid'];
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';


//SELECT sex,sum(age) FROM `msg_info` group by sex
//$userlog=pdo_fetchall("select sum(money) from ".tablename('enjoy_red_log')." group by openid where uniacid=".$uniacid."");
$pindex = max(1, intval($_GPC['page']));
$psize = 10;
if($_GPC['nickname']){
	$where="and nickname LIKE '%".$_GPC['nickname']."%'";
}else{
	$where="";
}

if ($_GPC['op']=='excel') {
$userlist1=pdo_fetchall("select * from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid." and total>0 order by total desc");
// $userlist1=pdo_fetchall("select a.*,abs(SUM(b.money)) as sum,SUM(ABS(b.money)) as txsum from ".tablename('enjoy_red_fans')." as a left join ".tablename('enjoy_red_log')." as b on a.openid=b.openid
// 		where b.uniacid=".$uniacid." and a.uniacid=".$uniacid." ".$where." group by b.openid order by SUM(b.money) desc");
	$title = array(
			'昵称',
			'可提现',
			'已提现',
			'累计'
	);
	$arraydata[] = iconv("UTF-8","GB2312//IGNORE", implode("\t", $title )) ;

	$value['nickname']=empty($value['nickname'])?'匿名':$value['nickname'];
	$value['cashed']=empty($value['cashed'])?0:$value['cashed'];
	foreach ($userlist1 as &$value) {
		$cash=$value['total']-$value['cashed'];
		$tmp_value =  array(
				$value['nickname'],
				$cash,
				$value['cashed'],
				$value['total'],
		);
		$arraydata[] = iconv("UTF-8","GB2312//IGNORE", implode("\t", $tmp_value )) ;
	}
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/vnd.ms-execl");
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".date('Ymd').'.xls');
	header("Content-Transfer-Encoding: binary");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo  implode("\t\n",$arraydata);
	exit();
}else if($_GPC['op']=='delempty'){
	//清空各种表
	pdo_delete('enjoy_red_chance',array('uniacid'=>$uniacid));
	pdo_delete('enjoy_red_chance_log',array('uniacid'=>$uniacid));
	pdo_delete('enjoy_red_log',array('uniacid'=>$uniacid));
	pdo_delete('enjoy_red_fans',array('uniacid'=>$uniacid));
	pdo_delete('enjoy_red_back',array('uniacid'=>$uniacid));
	
	message('清空活动数据成功', $this->createWebUrl('log'), 'success');
}else if($op=='detail'){
	//纪录详情
	$openid=$_GPC['openid'];
	$logs=pdo_fetchall("select * from ".tablename('enjoy_red_log')." where uniacid=".$uniacid." and
			openid='".$openid."'");
}else if($op=='vote'){
//邀请详情
	$openid=$_GPC['openid'];
	$uid=pdo_fetchcolumn("select uid from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid."
			and openid='".$openid."'");
	
	$votes=pdo_fetchall("select * from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid." and
			puid='".$uid."'");
}else if($op=='black'){
	$black=$_GPC['black'];
	$openid=$_GPC['openid'];
	if($black==0){
		$black=1;
	}else{
		$black=0;
	}
	pdo_update('enjoy_red_fans',array('black'=>$black),array('uniacid'=>$uniacid,'openid'=>$openid));

	message('操作成功！', $this->createWebUrl('log'), 'success');


}else{
// 	$totals = pdo_fetchall("SELECT COUNT(*) as count from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid." group by openid");
// 	$total=count($totals);
    $total=pdo_fetchcolumn("SELECT COUNT(*) as count from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid);
// 	$userlist=pdo_fetchall("select * from ".tablename('enjoy_red_fans')." as a left join ".tablename('enjoy_red_chance')." as b
// 		on a.openid=b.openid where a.uniacid=".$uniacid." ".$where." order by
// 		a.total+0 desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$userlist=pdo_fetchall("select * from ".tablename('enjoy_red_fans')."  where uniacid=".$uniacid." ".$where." order by
		total+0 desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

	$pager = pagination($total, $pindex, $psize);
	$countadd=pdo_fetchcolumn("select count(*) from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid."");
	$countsum=pdo_fetchcolumn("select sum(cashed) from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid."");
	
	
	
	
}






















include $this->template('log');