<?php
/**
 * [WeYu System] Copyright (c) 2014 BBS.HEIRUI.CN
 * WeYu wechat system,visited http://bbs.heirui.cn/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

class DefaultModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W, $engine;
		if ($this->message['type'] == 'trace') {
			return $this->respText('');
		}
		$setting = uni_setting($_W['uniacid'], array('default'));
		if(!empty($setting['default'])) {
			$flag = array('image' => 'url', 'link' => 'url', 'text' => 'content');
			$message = $this->message;
			$message['type'] = 'text';
			$message['content'] = $setting['default'];
			$message['redirection'] = true;
			$message['source'] = 'default';
			$message['original'] = $this->message[$flag[$this->message['type']]];
			$pars = $engine->analyzeText($message);
			if(is_array($pars)) {
				return array('params' => $pars);
			}
		}
	}
}