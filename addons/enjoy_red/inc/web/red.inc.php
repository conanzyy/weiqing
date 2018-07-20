<?php
global $_W,$_GPC;
load()->func('tpl');
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$pid=intval($_GPC['pid']);
if ($op == 'display') {
	$list = pdo_fetchall("SELECT * FROM " . tablename('enjoy_red_rule') . " WHERE uniacid = '{$_W['uniacid']}' and pid=".$pid." ORDER BY id asc");
//	$count = pdo_fetch("SELECT sum(rcount) as sumcount,sum(rchance) as sumchance FROM " . tablename('enjoy_red_rule') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id asc");
} elseif ($op == 'post') {
	$id = intval($_GPC['id']);
	if (checksubmit('submit')) {
		$data = array(
				'uniacid' => $_W['uniacid'],
				'rmin'=>$_GPC['rmin'],
				'rmax'=>$_GPC['rmax'],
				'rchance'=>$_GPC['rchance'],
				'rcount'=>$_GPC['rcount'],
				'pid'=>$pid,
				'createtime'=>TIMESTAMP
		);
		if (!empty($id)) {
			pdo_update('enjoy_red_rule', $data, array('id' => $id));
			$message="更新红包成功！";
		} else {
			pdo_insert('enjoy_red_rule', $data);
			$id = pdo_insertid();
			$message="新增红包成功！";
		}
		message($message, $this->createWebUrl('red', array('op' => 'display','pid'=>$pid)), 'success');
	}
	//修改
	$rule = pdo_fetch("SELECT * FROM " . tablename('enjoy_red_rule') . " WHERE id = '$id' and uniacid = '{$_W['uniacid']}' and pid=".$pid."");
} elseif ($op == 'delete') {
	$id = intval($_GPC['id']);
	$rule = pdo_fetch("SELECT id FROM " . tablename('enjoy_red_rule') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . " and pid=".$pid."");
	if (empty($rule)) {
		message('抱歉，红包不存在或是已经被删除！', $this->createWebUrl('red', array('op' => 'display','pid'=>$pid)), 'error');
	}
	pdo_delete('enjoy_red_rule', array('id' => $id));
	message('红包删除成功！', $this->createWebUrl('red', array('op' => 'display','pid'=>$pid)), 'success');
} else {
	message('请求方式不存在');
}






include $this->template('red');