<?php
global $_W,$_GPC;
load()->func('tpl');
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($op == 'display') {
	$list = pdo_fetchall("SELECT * FROM " . tablename('enjoy_red_pack') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id asc");
	//	$count = pdo_fetch("SELECT sum(rcount) as sumcount,sum(rchance) as sumchance FROM " . tablename('enjoy_red_pack') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id asc");
} elseif ($op == 'post') {
	$id = intval($_GPC['id']);
	if (checksubmit('submit')) {
		$data = array(
				'uniacid' => $_W['uniacid'],
				'cashmin'=>$_GPC['cashmin'],
				'cashmax'=>$_GPC['cashmax'],
				'cashmoney'=>$_GPC['cashmoney'],
				'createtime'=>TIMESTAMP
		);
		if (!empty($id)) {
			pdo_update('enjoy_red_pack', $data, array('id' => $id));
			$message="更新红包套餐成功！";
		} else {
			pdo_insert('enjoy_red_pack', $data);
			$id = pdo_insertid();
			$message="新增红包套餐成功！";
		}
		message($message, $this->createWebUrl('redpack', array('op' => 'display')), 'success');
	}
	//修改
	$redpack = pdo_fetch("SELECT * FROM " . tablename('enjoy_red_pack') . " WHERE id = '$id' and uniacid = '{$_W['uniacid']}'");
} elseif ($op == 'delete') {
	$id = intval($_GPC['id']);
	$redpack = pdo_fetch("SELECT id FROM " . tablename('enjoy_red_pack') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
	if (empty($redpack)) {
		message('抱歉，红包套餐不存在或是已经被删除！', $this->createWebUrl('redpack', array('op' => 'display')), 'error');
	}
	pdo_delete('enjoy_red_pack', array('id' => $id));
	pdo_delete('enjoy_red_rule', array('pid' => $id));
	message('红包套餐删除成功！', $this->createWebUrl('redpack', array('op' => 'display')), 'success');
} else {
	message('请求方式不存在');
}






include $this->template('redpack');