<?php
/**
 * [WeYu System] Copyright (c) 2014 BBS.HEIRUI.CN
 * WeYu wechat system,visited http://bbs.heirui.cn/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

class VideoModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('video_reply') . " WHERE `rid`=:rid";
		$item = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($item)) {
			return false;
		}
		return $this->respVideo(array(
			'MediaId' => $item['mediaid'],
			'Title' => $item['title'],
			'Description' => $item['description']
		));
	}
}