<?php 
/**
 * 小程序入口
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class Ewei_hotelModuleWxapp extends WeModuleWxapp {
	public $_search_key = '__hotel2_search';
	public $_set_info = array();
	public $_user_info = array();
	public $_from_user = '';
	function __construct(){
		global $_W;
		include "model.php";
		$this->_set_info = get_hotel_set();
	}
	//i=281&c=entry&a=wxapp&do=hotellists&m=ewei_hotel&   i 公众号id
	//获取该公众号下的所有酒店信息
	public function doPageHotelLists(){
		global $_GPC, $_W;
		$weid = $_GPC['i'];
		$show_data = array();
		$show_data['hotels'] = pdo_getall('hotel2', array('weid' => $weid), array('id', 'weid', 'title', 'thumb'));
		if(!empty($show_data['hotels'])){
			foreach($show_data['hotels'] as &$hotel_info){
				if(!empty($hotel_info['thumb'])){
					$hotel_info['thumb'] = tomedia($hotel_info['thumb']);
				}
			}
		}
		return $this->result(0, '获取酒店成功', $show_data);
	}
	//检查登录
	public function check_login(){
		global $_GPC, $_W;
		$info = array();
		if(empty($_SESSION['openid'])){
			$info['code'] = -1;
			$info['message'] = '请重新登录';
		}else{
			load()->model('mc');
			$_W['member'] = mc_fetch($_SESSION['openid']);
			$info['code'] = 0;
			$info['message'] = '登录状态不变';
			$weid = $_GPC['i'];
			$this->_from_user = $_SESSION['openid'];
			$user_info = pdo_fetch("SELECT * FROM " . tablename('hotel2_member') . " WHERE from_user = :from_user AND weid = :weid limit 1", array(':from_user' => $this->_from_user, ':weid' => $weid));
			if(empty($user_info)){
				$member = array();
				$member['weid'] = $weid;
				$member['from_user'] = $_SESSION['openid'];
				
				$member['createtime'] = time();
				$member['isauto'] = 1;
				$member['status'] = 1;
				pdo_insert('hotel2_member', $member);
				$member['id'] = pdo_insertid();
				$member['user_set'] = $this->_set_info['user'];
				$this->_user_info = $member;
			}else{
				$this->_user_info = $user_info;
			}
		}
		return $info;
	}
	//i=281&c=entry&a=wxapp&hid=4&do=HotelRoomLists&m=ewei_hotel&
	//page=1&order_title=&bdate=&day=&sid= 标识id
	//获取酒店列表
	public function doPageHotelRoomLists(){
		global $_GPC, $_W;
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$weid = $_GPC['i'];
		$hid = $_GPC['hid'];
		$show_data = array();
		$show_data['search_array'] = $this->setdate();
		$params = array(
				":weid"=>$weid,
				":hotelid"=>$hid,
				":status"=>1
		);
		if(empty($_GPC['page']) && empty($_GPC['order_title'])){
			//获取该酒店的评论
			$show_data['comments'] = pdo_getall('hotel2_comment', array('uniacid' => $weid, 'hotelid' => $hid));
			if (!empty($show_data['comments'])) {
				foreach($show_data['comments'] as &$comment) {
					$user = mc_fetch($comment['uid']);
					$comment['username'] = $user['nickname'];
				}
			}unset($comment);
			//酒店的信息
			$show_data['reply'] = pdo_fetch("SELECT * FROM " . tablename('hotel2') . " WHERE id = :id AND weid = :weid", array(':id' => $hid, ':weid' => $weid));
			if(empty($show_data['reply'])){
				return $this->result(-1, '酒店未找到, 请联系管理员!', $show_data);
			}
			if(!empty($show_data['reply']['thumb'])){
				$show_data['reply']['thumb'] = tomedia($show_data['reply']['thumb']);
			}
			//图片
			if(unserialize($show_data['reply']['thumbs'])){
				$thumbs = unserialize($show_data['reply']['thumbs']);
				foreach ($thumbs as $k=>$url){
					$thumbs[$k] = tomedia($url);
				}
				$show_data['thumbs'] = $thumbs;
			}
			$show_data['thumbs'][] = $show_data['reply']['thumb'];
			$show_data['thumbcount'] = count($show_data['thumbs']);
			if ($this->_set_info['is_unify'] == 1) {
				$show_data['tel'] = $this->_set_info['tel'];
			} else {
				$show_data['tel'] = $show_data['reply']['phone'];
			}
			//得到房间的所有类型
			$room_list = pdo_getall('hotel2_room', array('hotelid' => $hid, 'weid' => $weid, 'status' => 1));
			$show_data['room_type'] = array();
			$show_data['room_type'][] = '房间类型';
			foreach ($room_list as $detail){
				if(!in_array($detail['title'], $show_data['room_type'])){
					$show_data['room_type'][] = $detail['title'];
				}
			}
			unset($room_list);
			$device = unserialize($show_data['reply']['device']);
			if ($device) {
				$show_data['reply']['device'] = '';
				foreach ($device as $key => $value) {
					if ($value['isshow'] == 1) {
						$show_data['reply']['device'].= $value['value'] . ',';
					}
				}
			}
			$this->getDetailByCondition($show_data, $params, 1);
		}else{
			$page = empty($_GPC['page'])? 1 : $_GPC['page'];
			$this->getDetailByCondition($show_data, $params, $page, $_GPC['order_title']);
		}
		//支持的支付方式
		$setting = uni_setting($weid, array('payment'));
		if(!empty($setting)){
			$pay_type = array('alipay'=>'支付宝支付','wechat'=>'微信支付','baifubao'=>'百付宝');
			foreach ($pay_type as $type => $info){
				if(!empty($setting['payment'][$type]) && $setting['payment'][$type]['switch'] == 1){
					$show_data['payment'][] = $info;
				}
			}
		}
		return $this->result(0, '访问成功', $show_data);
	}
	public  function isMember() {
		global $_W;
		//判断公众号是否卡其会员卡功能
		$card_setting = pdo_fetch("SELECT * FROM ".tablename('mc_card')." WHERE uniacid = '{$_W['uniacid']}'");
		$card_status =  $card_setting['status'];
		//查看会员是否开启会员卡功能
		$membercard_setting  = pdo_get('mc_card_members', array('uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));
		$membercard_status = $membercard_setting['status'];
		$pricefield = !empty($membercard_status) && $card_status == 1?"mprice":"cprice";
		if (!empty($card_status) && !empty($membercard_status)) {
			return true;
		} else {
			return false;
		}
	}
	public function getDetailByCondition(&$show_data, $params ,$page = 1, $order_title = ''){
		$pindex = max(1, intval($page));//第几页
		$psize = 2;//每页大小
		//判断会员价还是普通价
		$pricefield = $this->isMember() ? 'mprice' : 'cprice';
		//入住
		$bdate = $show_data['search_array']['bdate'];
		$btime = $show_data['search_array']['btime'];
		//住几天
		$day =intval($show_data['search_array']['day']);
		//离店
		$edate = $show_data['search_array']['edate'];
		$etime = $show_data['search_array']['etime'];
		$sql = "SELECT id, hotelid, id as roomid, title, breakfast, thumb, thumbs, cprice, oprice, " . $pricefield . " as m_price";
		$sql .= " FROM " .tablename('hotel2_room');
		$sql .= " WHERE 1 = 1";
		$sql .= " AND hotelid = :hotelid";
		$sql .= " AND weid = :weid";
		$sql .= " AND status = :status";
		$room_params = $params;
		unset($room_params[':status']);
		if($order_title){
			$params[":order_title"]=$order_title;
			$sql .= " AND title = :order_title";
		}
		$sql .= " ORDER BY displayorder, sortid DESC";
		$room_list = pdo_fetchall($sql, $params);
		foreach($room_list as $key => $value) {
			$room_list[$key]['thumbs'] = unserialize($value['thumbs']);
			$r_sql = "SELECT roomdate, num, status, oprice, cprice, " . $pricefield . " as m_price FROM " . tablename('hotel2_room_price');
			$r_sql .= " WHERE 1 = 1";
			$r_sql .= " AND roomid = " . $value['roomid'];
			$r_sql .= " AND weid = :weid";
			$r_sql .= " AND hotelid = :hotelid";
			$r_sql .= " AND roomdate >=" . $btime ." AND roomdate <" .$etime;
			$r_sql .= " order by roomdate desc";
			$price_list = pdo_fetchall($r_sql, $room_params);
			//补全图片地址
			if($value['thumb']){
				$room_list[$key]['thumb'] = tomedia($value['thumb']);
			}
			if ($price_list) {
				//价格表中存在
				$has = 1;
				$avg = 0;
				//如果hotel2_room_price 中存在这一天的修改的价格，那么手机端显示的旧价格和新价格都是hotel2_room_price表里的价格
				//而会员显示的价格是 hotel2_room_price 表里的 优惠价*会员卡的折扣率，普通用户就直接显示优惠价
				foreach($price_list as $k => $v) {
					if ($pricefield == 'mprice') {
						$member_p = unserialize($value['m_price']);
						$room_list[$key]['oprice'] = $v['oprice'];
						if(!empty($_W['member'])){
							$room_list[$key]['cprice'] = $v['cprice'] * $member_p[$_W['member']['groupid']];
						}else{
							$room_list[$key]['cprice'] = $v['cprice'] * 1;
						}
					}else{
						$room_list[$key]['oprice'] = $v['oprice'];
						$room_list[$key]['cprice'] = $v['cprice'];
					}
					if ($v['status'] == 0 || $v['num'] == 0 ) {
						$has = 0;
					}
				}
				$totalprice =  $room_list[$key]['cprice'] * $day;
				$room_list[$key]['has'] = $has;
				$room_list[$key]['price'] = round( $totalprice / $day);
				$room_list[$key]['total_price'] = $totalprice;
				if($day == 1) {
					$avg = 0;
				}
				$room_list[$key]['avg'] = $avg;
			} else {
				//价格表中不存在
				$room_list[$key]['has'] = 1;
				if ($pricefield == 'mprice') {
					$member_p = unserialize($value['m_price']);
					if(!empty($_W['member'])){
						$room_list[$key]['price'] =  $value['cprice'] * $member_p[$_W['member']['groupid']];//会员显示的价格是（优惠价*会员的折扣率）
					}else{
						$room_list[$key]['price'] =  $value['cprice'] * 1;
					}
					if ($room_list[$key]['price'] == 0) {
						$room_list[$key]['price'] =  $value['oprice'];
					}
				} else {
					$room_list[$key]['price'] = $value['m_price'];
				}
				$room_list[$key]['total_price'] = $room_list[$key]['price'] * $day;
				$room_list[$key]['avg'] = 0;
			}
		}
		if ($show_data['search_array']['price_type'] == 1) {
			$price_value = $show_data['search_array']['price_value'];
			if (!empty($price_value)) {
				foreach($room_list as $key => $value) {
					$new_price = $value['price'];
					$price_flag = 1;
					if (strstr($price_value, '-') !== false) {
						$price_array = explode("-", $price_value);
						if ($new_price >= intval($price_array[0]) && $new_price <= intval($price_array[1])) {
							$price_flag = 1;
						} else {
							$price_flag = 0;
						}
					} else {
						if ($price_value == 150) {
							if ($new_price <= 150) {
								$price_flag = 1;
							} else {
								$price_flag = 0;
							}
						}else if ($price_value == 1000) {
							if ($new_price >= 1000) {
								$price_flag = 1;
							} else {
								$price_flag = 0;
							}
						}
					}
					if ($price_flag == 0) {
						unset($room_list[$key]);
					}
				}
			}
		}
		$total = count($room_list);
		if ($total <= $psize) {
			$list = $room_list;
		} else {
			// 需要分页
			if($pindex > 0) {
				$list_array = array_chunk($room_list, $psize, true);
				$list = $list_array[($pindex-1)];
			} else {
				$list = $room_list;
			}
		}
		$list_value = array_values($list);
		if(!empty($list_value)){
			foreach($list_value as &$infos){
				if(!empty($infos['thumbs'])){
					foreach($infos['thumbs'] as &$url){
						$url = tomedia($url);
					}
					$infos['thumbs_num'] = count($infos['thumbs']);
				}else{
					$infos['thumbs_num'] = 0;
				}
			}
		}
		$show_data['room_list'] = $list_value;
		$data = array();
		$data['result'] = 1;
		$show_data['page_array'] = get_page_array($total, $pindex, $psize);
		ob_start();
		$data['code'] = ob_get_contents();
		ob_clean();
		$data['total'] = $total;
		$data['isshow'] = $page_array['isshow'];
		if ($page_array['isshow'] == 1) {
			$data['nindex'] = $page_array['nindex'];
		}
		$show_data['data'] = $data;
	}
	function getSearchArray(){
		$search_array = get_cookie($this->_search_key);
		if (empty($search_array)) {
			//默认搜索参数
			$search_array['order_type'] = 1;
			$search_array['order_name'] = 2;
			$search_array['location_p'] = $this->_set_info['location_p'];
			$search_array['location_c'] = $this->_set_info['location_c'];
			if (strpos($search_array['location_p'], '市') > -1) {
				//直辖市
				$search_array['municipality'] = 1;
				$search_array['city_name'] = $search_array['location_p'];
			} else {
				$search_array['municipality'] = 0;
				$search_array['city_name'] = $search_array['location_c'];
			}
			$search_array['business_id'] = 0;
			$search_array['business_title'] = '';
			$search_array['brand_id'] = 0;
			$search_array['brand_title'] = '';
	
			$weekarray = array("日", "一", "二", "三", "四", "五", "六");
	
			$date = date('Y-m-d');
			$time = strtotime($date);
			$search_array['btime'] = $time;
			$search_array['etime'] = $time + 86400;
			$search_array['bdate'] = $date;
			$search_array['edate'] = date('Y-m-d', $search_array['etime']);
			$search_array['bweek'] = '星期' . $weekarray[date("w", $time)];
			$search_array['eweek'] = '星期' . $weekarray[date("w", $search_array['etime'])];
			$search_array['day'] = 1;
			insert_cookie($this->_search_key, $search_array);
		}
		return $search_array;
	}
	//设置入住的开始时间和入住几晚
	public function setdate(){
		global $_GPC;
		$data = $this->getSearchArray();
		$bdate = $_GPC['bdate'];
		$day = $_GPC['day'];
		if (!empty($bdate) && !empty($day)) {
			$btime = strtotime($bdate);
			$etime = $btime + $day * 86400;
			$weekarray = array("日", "一", "二", "三", "四", "五", "六");
			$data['btime'] = $btime;
			$data['etime'] = $etime;
			$data['bdate'] = $bdate;
			$data['edate'] = date('Y-m-d', $etime);
			$data['bweek'] = '星期' . $weekarray[date("w", $btime)];
			$data['eweek'] = '星期' . $weekarray[date("w", $etime)];
			$data['day'] = $day;
		}
		return $data;
	}
	//i=281&c=entry&a=wxapp&hid=4&id=26&price=100.00&total_price=200&do=order&m=ewei_hotel
	//&bdate=2017-01-14&day=&uname=&contact_name=&mobile=&remark=&nums=  //提交时的参数
	//在线预定酒店和提交订单
	public function doPageOrder(){
		global $_GPC, $_W;
// 		检查登录
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$weid = $_GPC['i'];
		$hid = intval($_GPC['hid']);
		$id = intval($_GPC['id']);
		$price = $_GPC['price'];
		$total_price = $_GPC['total_price'];
		$show_data = array();
		$show_data['search_array'] = $this->setdate();
	
		//$paysetting = uni_setting($weid, array('payment', 'creditbehaviors'));
		//$_W['account'] = array_merge($_W['account'], $paysetting);
		if(empty($hid) || empty($id)){
			return $this->result(-1, '预定失败!', $show_data);
		}
		if (!$show_data['search_array'] || empty($show_data['search_array']['btime']) || empty($show_data['search_array']['day'])) {
			return $this->result(-1, '缺少房间入住信息!', $show_data);
		}
	
		$is_submit = $_GPC['submit'];
		$sql = 'SELECT `title`, `mail`, `phone`, `thumb`, `description` FROM ' . tablename('hotel2') . ' WHERE `id` = :id';
		$reply = pdo_fetch($sql, array(':id' => $hid));
		if (empty($reply)) {
			return $this->result(-1, '酒店未找到, 请联系管理员!', $show_data);
		}
		//给前端酒店的名称
		$show_data['hotel_title'] = $reply['title'];
		// 设置分享信息
		$shareTitle = $reply['title'];
		$shareDesc = $reply['description'];
		$shareThumb = tomedia($reply['thumb']);
	
		if ($this->_set_info['is_unify'] == 1) {
			$tel = $this->_set_info['tel'];
		} else {
			$tel = $reply['phone'];
		}
		//判断是不是会员
		$pricefield = $this->isMember() ? 'mprice' : 'cprice';
		$sql = "SELECT * , $pricefield AS roomprice FROM " . tablename('hotel2_room') . " WHERE `id` = :id AND `hotelid` = :hotelid ";
		$room = pdo_fetch($sql, array(':id' => $id, ':hotelid' => $hid));
		if (empty($room)) {
			return $this->result(-1, '房型未找到, 请联系管理员!', $show_data);
		}
		//给前端房型的名称
		$show_data['room_title'] = $room['title'];
		// 入住
		$btime = $show_data['search_array']['btime'];
		$bdate = $show_data['search_array']['bdate'];
		if(strtotime($bdate) < strtotime(date('Y-m-d' ,time()))){
			$this->result(-1, '预定的开始日期不能小于当日的日期!', $show_data);
		}
		// 住几天
		$days =intval($show_data['search_array']['day']);
		// 离店
		$etime = $show_data['search_array']['etime'];
		$edate = $show_data['search_array']['edate'];
		$date_array = array();
		$date_array[0]['date'] = $bdate;
		$date_array[0]['day'] = date('j', $btime);
		$date_array[0]['time'] = $btime;
		$date_array[0]['month'] = date('m',$btime);
	
		if ($days > 1) {
			for($i = 1; $i < $days; $i++) {
				$date_array[$i]['time'] = $date_array[$i-1]['time'] + 86400;
				$date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
				$date_array[$i]['day'] = date('j', $date_array[$i]['time']);
				$date_array[$i]['month'] = date('m', $date_array[$i]['time']);
			}
		}
		//酒店信息
		$sql = 'SELECT `id`, `roomdate`, `num`, `status` FROM ' . tablename('hotel2_room_price') . ' WHERE `roomid` = :roomid
				AND `roomdate` >= :btime AND `roomdate` < :etime AND `status` = :status';
		$setInfo = pdo_fetch('SELECT email,template,templateid,smscode FROM ' . tablename('hotel2_set') . ' WHERE weid = :weid', array(':weid' => $_W['uniacid']));
	
		$params = array(':roomid' => $id, ':btime' => $btime, ':etime' => $etime, ':status' => '1');
		$room_date_list = pdo_fetchall($sql, $params);
		$flag = intval($room_date_list);
		$list = array();
		$max_room = 8;
		$show_data['max_room'] = 8;
		$is_order = 1;
		
		if ($flag == 1) {
			for($i = 0; $i < $days; $i++) {
				$k = $date_array[$i]['time'];
				foreach ($room_date_list as $p_key => $p_value) {
					// 判断价格表中是否有当天的数据
					if($p_value['roomdate'] == $k) {
						$room_num = $p_value['num'];
						if (empty($room_num)) {
							$is_order = 0;
							$show_data['max_room'] = 0;
							$list['num'] = 0;
							$list['date'] =  $date_array[$i]['date'];
						} else if ($room_num > 0 && $room_num < $show_data['max_room']) {
							$show_data['max_room'] = $room_num;
							$list['num'] =  $room_num;
							$list['date'] =  $date_array[$i]['date'];
						}
						break;
					}
				}
			}
		}
		if ($show_data['max_room'] == 0) {
			$msg = $list['date'] . '当天没有空房间了,请选择其他房型。';
			return $this->result(-1, $msg, $show_data);
		}
	
		$memberid = $this->_user_info['id'];
		$r_sql = 'SELECT `roomdate`, `num`, `oprice`, `cprice`, `status`, ' . $pricefield . ' AS `m_price` FROM ' . tablename('hotel2_room_price') .
		' WHERE `roomid` = :roomid AND `weid` = :weid AND `hotelid` = :hotelid AND `roomdate` >= :btime AND ' .
		' `roomdate` < :etime  order by roomdate desc';
		$params = array(':roomid' => $id, ':weid' => $weid, ':hotelid' => $hid, ':btime' => $btime, ':etime' => $etime);
		$price_list = pdo_fetchall($r_sql, $params);
		$member_p = unserialize($room['mprice']);
	
		if ($price_list) {
			//价格表中存在
			foreach($price_list as $k => $v) {
				$room['oprice'] = $v['oprice'];
				$room['cprice'] = $v['cprice'];
				if ($pricefield == 'mprice') {
					if(!empty($_W['member'])){
						$this_price = $v['cprice'] * $member_p[$_W['member']['groupid']];
					}else{
						$this_price = $v['cprice'] * 1;
					}
				}else{
					$this_price = $v['cprice'];
				}
				if ($v['status'] == 0 || $v['num'] == 0 ) {
					$has = 0;
				}
			}
			$totalprice =  $this_price * $day;
			$totalprice =  ($this_price + $room['service']) * $days;
			$service = $room['service'] * $days;
		}else{
			//会员的价格mprice=现价*会员卡折扣率
			if(!empty($_W['member'])){
				$this_price =  $pricefield == 'mprice' ? $room['cprice']*$member_p[$_W['member']['groupid']] : $room['cprice'];
			}else{
				$this_price =  $pricefield == 'mprice' ? $room['cprice']*1 : $room['cprice'];
			}
			if ($this_price == 0) {
				$this_price = $room['oprice'] ;
			}
			$totalprice =  ($this_price + $room['service']) * $days;
			$service = $room['service'] * $days;
		}
		if($totalprice == 0){
			return $this->result(-1, '房间价格不能是0，请联系管理员修改！', $show_data);
		}
		//提交订单，存表
		if ($is_submit) {
			$name = $_GPC['uname'];
			$contact_name = $_GPC['contact_name'];
			$mobile = $_GPC['mobile'];
			$remark = trim($_GPC['remark']);
			$mobilecode=$_GPC['mobilecode'];
			
			if (empty($name)) {
				return $this->result(-1, '入住人不能为空!', $show_data);
			}
	
			if (empty($contact_name)) {
				return $this->result(-1, '联系人不能为空!', $show_data);
			}
	
			if (empty($mobile)) {
				return $this->result(-1, '手机号不能为空!', $show_data);
			}
	
			if ($_GPC['nums'] > $max_room) {
				return $this->result(-1, '您的预定数量超过最大限制!', $show_data);
			}
			$sql = 'SELECT smscode, templateid, template, email FROM ' . tablename('hotel2_set') . ' WHERE weid = :weid';
			$setInfo = pdo_fetch($sql, array(':weid' => $weid));
			if ($setInfo['smscode'] == 1) {
				$sql="SELECT code from".tablename('hotel12_code').'WHERE `mobile`= :mobile AND `weid`= :weid';
				$code=pdo_fetch($sql,array(':mobile'=>$mobile,':weid'=>$_W['uniacid']));
				if ($mobilecode != $code['code']) {
					return $this->result(-1, '您的验证码错误，请重新输入!', $show_data);
				}
			}
			
			$insert = array(
					'weid' => $weid,
					//fans id 不存在，所以订单号是不正确的
					'ordersn' => date('md') . sprintf("%04d", $_W['fans']['fanid']) . random(4, 1),
					'hotelid' => $hid,
					'openid' => $_SESSION['openid'],
					'roomid' => $id,
					'memberid' => $memberid,
					'name' => $name,
					'remark' => $remark,
					'contact_name' => $contact_name,
					'mobile' => $mobile,
					'btime' => $show_data['search_array']['btime'],
					'etime' => $show_data['search_array']['etime'],
					'day' => $show_data['search_array']['day'],
					'style' => $room['title'],
					'nums' => intval($_GPC['nums']),
					'oprice' => $room['oprice'],
					'cprice' => $room['cprice'],
					'mprice' => $room['mprice'],
					'time' => TIMESTAMP,
					'paytype' => $_GPC['paytype']
			);
			$insert[$pricefield] = $this_price;
			$insert['sum_price'] = $totalprice * $insert['nums'];
			//"SELECT * FROM ".tablename('hotel2_order'). " WHERE 
			//time < :time AND weid = '{$_W['uniacid']}' AND paystatus = '0' 
			//AND status <> '1' AND status <> '3'", array(':time' => (time() - 86400)))"
			pdo_query('UPDATE '. tablename('hotel2_order'). " SET status = '-1' WHERE time < :time AND weid = '{$_W['uniacid']}' AND paystatus = '0' AND status <> '1' AND status <> '3'", array(':time' => (time() - 86400)));
			$order_exist = pdo_fetch("SELECT * FROM ". tablename('hotel2_order'). " WHERE hotelid = :hotelid AND roomid = :roomid AND openid = :openid AND status = '0'", array(':hotelid' => $insert['hotelid'], ':roomid' => $insert['roomid'], ':openid' => $insert['openid']));
			if ($order_exist) {
				//return $this->result(-1, '您有未完成订单,不能重复下单', $show_data);
			}
			pdo_insert('hotel2_order', $insert);
			$order_id = pdo_insertid();
	
			//如果有接受订单的邮件,
			if (!empty($reply['mail'])) {
				$subject = "微信公共帐号 [" . $_W['account']['name'] . "] 微酒店订单提醒.";
				$body = "您后台有一个预定订单: <br/><br/>";
				$body .= "预定酒店: " . $reply['title'] . "<br/>";
				$body .= "预定房型: " . $room['title'] . "<br/>";
				$body .= "预定数量: " . $insert['nums'] . "<br/>";
				$body .= "预定价格: " . $insert['sum_price'] . "<br/>";
				$body .= "预定人: " . $insert['name'] . "<br/>";
				$body .= "预定电话: " . $insert['mobile'] . "<br/>";
				$body .= "到店时间: " . $bdate . "<br/>";
				$body .= "离店时间: " . $edate . "<br/><br/>";
				$body .= "请您到管理后台仔细查看. <a href='" .$_W['siteroot'] .create_url('member/login') . "' target='_blank'>立即登录后台</a>";
				load()->func('communication');
				ihttp_email($reply['mail'], $subject, $body);
			}
			$sql = 'SELECT * FROM ' . tablename('hotel2_order') . ' WHERE id = :id AND weid = :weid';
			$order = pdo_fetch($sql, array(':id' => $order_id, ':weid' => $weid));
			if($insert['paytype'] == '3') {
				//到店付款减库存
				$starttime = $insert['btime'];
				for ($i = 0; $i < $insert['day']; $i++) {
					$sql = 'SELECT * FROM '. tablename('hotel2_room_price'). ' WHERE weid = :weid AND roomid = :roomid AND roomdate = :roomdate';
					$day = pdo_fetch($sql, array(':weid' => $weid, ':roomid' => $insert['roomid'], ':roomdate' => $starttime));
					pdo_update('hotel2_room_price', array('num' => $day['num'] - $insert['nums']), array('id' => $day['id']));
					$starttime += 86400;
				}
			}
			pdo_update('hotel2_member', array('mobile' => $mobile, 'realname' => $contact_name), array('weid' => $_W['uniacid'], 'from_user' => $_SESSION['openid']));
			$show_data = array();
			$show_data['order_id'] = $order_id;
			return $this->result(0, '提交成功!', $show_data);
		} else {
			$show_data['price'] = $totalprice;
			$member = array();
			$member['from_user'] = $login_info['record']['openid'];
			$record = hotel_member_single($member);
			
			if ($record) {
				$show_data['realname'] = $record['realname'];
				$show_data['mobile'] = $record['mobile'];
			} else {
				$fans = pdo_fetch("SELECT realname, mobile FROM " . tablename('hotel2_member') . " WHERE from_user = :from_user limit 1", array(':from_user' => $this->_from_user));
				if (!empty($fans)) {
					$show_data['realname'] = $fans['realname'];
					$show_data['mobile'] = $fans['mobile'];
				}
			}
			$sql = 'SELECT email,template,templateid,smscode FROM ' . tablename('hotel2_set') . ' WHERE weid = :weid';
			$setInfo = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
			$show_data['totalprice'] = $totalprice;
			$show_data['service'] = $service;
			return $this->result(0, '可以在线预定!', $show_data);
		}
	}
	public function getOrderDetail(&$show_data, $order_id){
		global $_GPC, $_W;
		$weid = $_GPC['i'];
		$memberid = $this->_user_info['id'];
		//订单id是空
		if (empty($order_id) || empty($memberid)) {
			return ;
		}
		
		$sql = "SELECT o.*, h.title, h.address, h.phone, h.thumb";
		$sql .= " FROM " .tablename('hotel2_order') ." AS o";
		$sql .= " LEFT JOIN " .tablename('hotel2') ." AS h ON o.hotelid = h.id";
		$sql .= " WHERE 1 = 1";
		$sql .= " AND o.id = :id";
		$sql .= " AND o.memberid = :memberid";
		$sql .= " AND o.weid = :weid";
		//$sql .="  LEFT JOIN ".tablename('hotel2_room')."AS r ON r.id =o.roomid ";
		//$sql .= " AND r.wid = o.weid";
		$params = array();
		$params[':memberid'] = $memberid;
		$params[':weid'] = $weid;
		$params[':id'] = $order_id;
		$sql .= " LIMIT 1";
		$item = pdo_fetch($sql, $params);
		if (empty($item)) {
			$show_data['item'] = '';
			return;
		}
		$roomid = $item['roomid'];
		$room_weid = $item['weid'];
		$SQL ="SELECT * FROM " .tablename('hotel2_room')."where id = $roomid";
		$PARAMS = array();
		$ITEM = pdo_fetch($SQL,$PARAMS);
		//svar_dump($ITEM);
		if(!empty($ITEM['score']))
		{
			pdo_fetch("UPDATE " . tablename('hotel2_member') . " SET score = (score + " .$ITEM['score'] . ") WHERE weid = '" . $room_weid . "' ");
		}
	
		if ($this->_set_info['is_unify'] == 1) {
			$tel = $this->_set_info['tel'];
		} else {
			$tel = $item['phone'];
		}
		if(!empty($_W['member']['uid'])) {
			$member = mc_fetch($_W['member']['uid'], array('credit1', 'credit2'));
		}
		$params['module'] = "ewei_hotel";
		$params['ordersn'] = $item['ordersn'];
		$params['tid'] = $item['id'];
		$params['user'] = $this->_user_info['from_user'];
		$params['fee'] = $item['sum_price'];
		$params['delivery']['title'] = '到店支付';
		$params['title'] = $_W['account']['name'] . "酒店订单{$item['ordersn']}";
		// 设置分享信息
		$shareDesc = $item['address'];
		$shareThumb = tomedia($item['thumb']);
	
		$setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
		//print_r($setting['payment']['card']['switch']);die;
		if($setting['payment']['card']['switch'] == 3 && $_W['member']['uid']) {
			//获取微擎卡券
			$cards = array();
			if(!empty($params['module'])) {
				$cards = pdo_fetchall('SELECT a.id,a.couponid,b.type,b.title,b.discount,b.condition,b.starttime,b.endtime FROM ' . tablename('activity_coupon_modules') . ' AS a LEFT JOIN ' . tablename('activity_coupon') . ' AS b ON a.couponid = b.couponid WHERE a.uniacid = :uniacid AND a.module = :modu AND b.condition <= :condition AND b.starttime <= :time AND b.endtime >= :time  ORDER BY a.id DESC', array(':uniacid' => $_W['uniacid'], ':modu' => $params['module'], ':time' => TIMESTAMP, ':condition' => $params['fee']), 'couponid');
				if(!empty($cards)) {
					foreach($cards as $key => &$card) {
						$has = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' WHERE uid = :uid AND uniacid = :aid AND couponid = :cid AND status = 1' . $condition, array(':uid' => $_W['member']['uid'], ':aid' => $_W['uniacid'], ':cid' => $card['couponid']));
						if($has > 0){
							if($card['type'] == '1') {
								$card['fee'] = sprintf("%.2f", ($params['fee'] * $card['discount']));
								$card['discount_cn'] = sprintf("%.2f", $params['fee'] * (1 - $card['discount']));
							} elseif($card['type'] == '2') {
								$card['fee'] = sprintf("%.2f", ($params['fee'] -  $card['discount']));
								$card['discount_cn'] = $card['discount'];
							}
						} else {
							unset($cards[$key]);
						}
					}
				}
			}
		}
		$show_data['params'] = $params;
		$show_data['item'] = $item;
	}
	//支付
	public function doPageOrderPay(){
		global $_GPC, $_W;
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$show_data = array();
		$this->getOrderDetail($show_data, intval($_GPC['order_id']));
		if(!empty($show_data['item'])){
			$params = array(
					'ordersn' => $show_data['item']['ordersn'],
					'tid' => $show_data['item']['id'],//支付订单编号, 应保证在同一模块内部唯一
					'title' => $_W['account']['name'] . "酒店订单{$show_data['item']['ordersn']}",//商家名称
					'fee' => $show_data['item']['sum_price'],//总费用, 只能大于 0
					'user' => $_SESSION['openid'],//付款用户, 付款的用户名(选填项)
			);
			$show_data['pay_info'] = $this->pay($params);
			return $this->result(0, '可以支付', $show_data);
		}else{
			return $this->result(-1, '获取订单信息失败', array());
		}
	}
	public function payResult($params) {
		global $_GPC, $_W;
		if($params['type']=='credit'){
			$paytype=1;
		}elseif($params['type']=='wechat'){
			$paytype=21;
		}elseif($params['type']=='alipay'){
			$paytype=22;
		}elseif($params['type']=='delivery'){
			$paytype=3;
		}
		$sql = 'SELECT * FROM ' . tablename('hotel2_order') . ' WHERE `id` = :id AND `weid` = :weid';
		$order = pdo_fetch($sql, array(':id' => $params['tid'], ':weid' => $_W['uniacid']));
		pdo_update('hotel2_order', array('paystatus' => 1,'paytype'=>$paytype), array('id' => $params['tid']));
		$sql = 'SELECT `email`, `mobile`,`template`, `confirm_templateid`,`templateid` FROM ' . tablename('hotel2_set') . ' WHERE `weid` = :weid';
		$setInfo = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
		$starttime = $order['btime'];
		if ($setInfo['email']) {
			$body = "<h3>酒店订单</h3> <br />";
			$body .= '订单编号：' . $order['ordersn'] . '<br />';
			$body .= '姓名：' . $order['name'] . '<br />';
			$body .= '手机：' . $order['mobile'] . '<br />';
			$body .= '房型：' . $order['style'] . '<br />';
			$body .= '订购数量' . $order['nums'] . '<br />';
			$body .= '原价：' . $order['oprice']  . '<br />';
			$body .= '会员价：' . $order['mprice']  . '<br />';
			$body .= '入住日期：' . date('Y-m-d',$order['btime'])  . '<br />';
			$body .= '退房日期：' . date('Y-m-d',$order['etime']) . '<br />';
			$body .= '总价:' . $order['sum_price'];
	
			// 发送邮件提醒
			if (!empty($setInfo['email'])) {
				load()->func('communication');
				ihttp_email($setInfo['email'], '微酒店订单提醒', $body);
			}
		}
		if ($setInfo['mobile']) {
			// 发送短信提醒
			if (!empty($setInfo['mobile'])) {
				load()->model('cloud');
				cloud_prepare();
				$body = 'df';
				$body = '用户' . $order['name'] . ',电话:' . $order['mobile'] . '于' . date('m月d日H:i') . '成功支付微酒店订单' . $order['ordersn']
				. ',总金额' . $order['sum_price'] . '元' . '.' . random(3);
				cloud_sms_send($setInfo['mobile'], $body);
	
			}
		}
	
		if ($params['from'] == 'return') {
			$roomid = $order['roomid'];
			$room = pdo_fetch("SELECT * FROM " . tablename('hotel2_room') . " WHERE id = {$roomid} AND weid = {$_W['uniacid']} LIMIT 1");
			$score = intval($room['score']);
			$acc = WeAccount::create($_W['acid']);
			if ($params['result'] == 'success' && $_SESSION['ewei_hotel_pay_result'] != $params['tid']) {
				//发送模板消息提醒
				if (!empty($setInfo['template']) && !empty($setInfo['confirm_templateid'])) {
					// $acc = WeAccount::create($_W['acid']);
					$time = '';
					$time.= date('Y年m月d日',$order['btime']);
					$time.='-';
					$time.= date('Y年m月d日',$order['etime']);
					$data = array(
							'first' => array('value' =>'你好，你已成功提交订房订单'),
							'keyword1' => array('value' => $order['style']),
							'keyword2' => array('value' => $time),
							'keyword3' => array('value' => $order['name']),
							'keyword4' => array('value' => $order['sum_price']),
							'keyword5' => array('value' => $order['ordersn']),
							'remark' => array('value' => '如有疑问，请咨询酒店前台'),
					);
					$acc->sendTplNotice($this->_from_user, $setInfo['confirm_templateid'],$data);
	
				} else {
					$info = '您在'.$hotel['title'].'预订的'.$room['title']."已预订成功";
					$custom = array(
							'msgtype' => 'text',
							'text' => array('content' => urlencode($info)),
							'touser' => $item['openid'],
					);
					$status = $acc->sendCustomNotice($custom);
				}
	
				//TM00217
				$clerks = pdo_getall('hotel2_member', array('clerk' => 1, 'weid' => $_W['uniacid'],'status'=>1));
				if (!empty($setInfo['template']) && !empty($setInfo['templateid'])) {
					$tplnotice = array(
							'first' => array('value' => '您好，酒店有新的订单等待处理'),
							'order' => array('value' => $order['ordersn']),
							'Name' => array('value' => $order['name']),
							'datein' => array('value' => date('Y-m-d', $order['btime'])),
							'dateout' => array('value' => date('Y-m-d', $order['etime'])),
							'number' => array('value' => $order['nums']),
							'room type' => array('value' => $order['style']),
							'pay' => array('value' => $order['sum_price']),
							'remark' => array('value' => '为保证客人入住正常，请及时处理！')
					);
					foreach ($clerks as $clerk) {
						$acc->sendTplNotice($clerk['from_user'],$setInfo['templateid'],$tplnotice);
					}
	
				} else {
					foreach ($clerks as $clerk) {
						$info = '酒店有新的订单,为保证客人入住正常，请及时处理!';
						$custom = array(
								'msgtype' => 'text',
								'text' => array('content' => urlencode($info)),
								'touser' => $clerk['from_user'],
						);
						$status = $acc->sendCustomNotice($custom);
					}
	
				}
	
	
				for ($i = 0; $i < $order['day']; $i++) {
					$sql = 'SELECT * FROM '. tablename('hotel2_room_price'). ' WHERE weid = :weid AND roomid = :roomid AND roomdate = :roomdate';
					$day = pdo_fetch($sql, array(':weid' => $weid, ':roomid' => $order['roomid'], ':roomdate' => $starttime));
					pdo_update('hotel2_room_price', array('num' => $day['num'] - $order['nums']), array('id' => $day['id']));
					$starttime += 86400;
				}
				if ($score) {
					$from_user = $this->_from_user;
					pdo_fetch("UPDATE " . tablename('hotel2_member') . " SET score = (score + " . $score . ") WHERE from_user = '" . $from_user . "' AND weid = " . $weid . "");
					//会员送积分
					$_SESSION['ewei_hotel_pay_result'] = $params['tid'];
					//判断公众号是否卡其会员卡功能
					$card_setting = pdo_fetch("SELECT * FROM " . tablename('mc_card') . " WHERE uniacid = '{$_W['uniacid']}'");
					$card_status = $card_setting['status'];
					//查看会员是否开启会员卡功能
					$membercard_setting = pdo_get('mc_card_members', array('uniacid' => $_W['uniacid'], 'uid' => $params['user']));
					$membercard_status = $membercard_setting['status'];
					if ($membercard_status && $card_status) {
						$room_credit = pdo_get('hotel2_room', array('weid' => $_W['uniacid'], 'id' => $order['roomid']));
						$room_credit = $room_credit['score'];
						$member_info = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $params['user']));
						pdo_update('mc_members', array('credit1' => $member_info['credit1'] + $room_credit), array('uniacid' => $_W['uniacid'], 'uid' => $params['user']));
					}
				}
			}
			if($paytype == 3){
				message('提交成功！', '../../app/' . $this->createMobileUrl('detail', array('hid' => $room['hotelid'])), 'success');
			}else{
				message('支付成功！', '../../app/' . $this->createMobileUrl('detail', array('hid' => $room['hotelid'])), 'success');
			}
		}
	}
	
	//订单详情
	public function doPageOrderDetail(){
		global $_GPC;
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$memberid = $this->_user_info['id'];
		if (empty($memberid)) {
			return $this->result(-1, '请登录!', array());
		}
		$show_data = array();
		$this->getOrderDetail($show_data, intval($_GPC['order_id']));
		if(empty($show_data['item'])){
			return $this->result(-1, '获取失败!', $show_data);
		}
		$this->check_status($show_data['item']);
		return $this->result(0, '获取成功!', $show_data);
	}
	//i,page
	//订单列表
	public function doPageOrderLists(){
		global $_GPC, $_W;
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$weid = $_GPC['i'];
		$memberid = $this->_user_info['id'];
		if (empty($memberid)) {
			return $this->result(-1, '请登录!', array());
		}
		$show_data = array();
		$set = pdo_get('hotel2_set', array('weid' => $weid));
		$show_data['hotel'] = pdo_getall('hotel2', array('weid' => $weid), array(), 'id');
		pdo_query('UPDATE '. tablename('hotel2_order'). " SET status = '-1' WHERE time <  :time AND weid = '{$_W['uniacid']}' AND paystatus = '0' AND status <> '1' AND status <> '3'", array(':time' => time() -1800));
	
		$pindex = max(1, intval($_GPC['page']));
		$psize = 3;
		$sql = "SELECT o.*, h.title ";
		$where = " FROM " .tablename('hotel2_order') ." AS o";
		$where .= " LEFT JOIN " .tablename('hotel2') ." AS h ON o.hotelid = h.id";
		$where .= " WHERE 1 = 1";
		$where .= " AND o.memberid = $memberid";
		$where .= " AND o.weid = $weid";
		$count_sql = "SELECT COUNT(o.id) " . $where;
		$sql .= $where;
		$sql .= " ORDER BY o.id DESC";
		if($pindex > 0) {
			// 需要分页
			$start = ($pindex - 1) * $psize;
			$sql .= " LIMIT {$start},{$psize}";
		}
		$show_data['list'] = pdo_fetchall($sql);
		if(!empty($show_data['list'])){
			foreach ($show_data['list'] as $k => &$order_info){
				$this->check_status($order_info);
			}
		}
		$total = pdo_fetchcolumn($count_sql);
		$page_array = get_page_array($total, $pindex, $psize);
		$data = array();
		$data['result'] = 1;
		$data['total'] = $total;
		$data['isshow'] = $page_array['isshow'];
		if ($page_array['isshow'] == 1) {
			$data['nindex'] = $page_array['nindex'];
		}
		$show_data['page_array'] = $page_array;
		$show_data['data'] = $data;
		return $this->result(0, '获取订单列表成功!', $show_data);
	}
	public function check_status(&$order_info){
		if(!empty($order_info)){
			$order_info['btime'] = date('Y-m-d', $order_info['btime']);
			$order_info['etime'] = date('Y-m-d', $order_info['etime']);
			$order_info['time'] = date('Y-m-d', $order_info['time']);
			$order_info['status_text'] = '';
			$order_info['status_pay'] = '';
			$order_info['status_cancel'] = '';
			if($order_info['status'] == 0){
				if($order_info['paystatus']== 0){
					$order_info['status_text'] = '待付款';
					$order_info['status_pay'] = 1;
					$order_info['status_cancel'] = 1;
				}else{
					$order_info['status_text'] = '等待酒店确认';
					$order_info['status_cancel'] = 1;
				}
			}else if($order_info['status'] == -1){
				if($order_info['paystatus']== 0){
					$order_info['status_text'] = '订单已取消';
				}else{
					if($order_info['paytype'] == 3){
						$order_info['status_text'] = '订单已取消';
					}else{
						$order_info['status_text'] = '正在退款中';
					}
				}
			}else if($order_info['status'] == 1){
				if($order_info['paystatus']== 0){
					$order_info['status_text'] = '待入住';
				}else{
					$order_info['status_text'] = '待入住';
				}
			}else if($order_info['status'] == 2){
				if($order_info['paystatus']== 0){
					$order_info['status_text'] = '酒店已拒绝';
				}else{
					$order_info['status_text'] = '已退款';
				}
			}else if($order_info['status'] == 4){
				$order_info['status_text'] = '已入住';
			}else if($order_info['status'] == 3){
				$order_info['status_text'] = '已完成';
			}
		}
	}
	//取消订单 i,order_id
	public function doPageCancelOrder(){
		global $_GPC, $_W;
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$id = $_GPC['order_id'];
		if (!empty($id)) {
			$show_data = array();
			pdo_update('hotel2_order', array('status' => -1), array('id' => $id, 'weid' => $_W['uniacid']));
			$this->getOrderDetail($show_data, $id);
			$this->check_status($show_data['item']);
			return $this->result(0, '取消订单成功!', $show_data);
		}else{
			return $this->result(-1, '取消订单失败!', array());
		}
	}
	//评论订单 i,id,common,order_id
	public function doPageCommentOrder(){
		global $_GPC, $_W;
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$id = $_GPC['id'];
		$comment = $_GPC['comment'];
		$id = $_GPC['id'];
		$orderid = $_GPC['order_id'];
		$post = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $this->_user_info['id'],
			'createtime' => time(),
			'comment' => $comment,
			'hotelid' => $id
		);
		pdo_insert('hotel2_comment', $post);
		pdo_update('hotel2_order', array('comment' => 1), array('weid' => $_W['uniacid'], 'id' => $orderid));
		return $this->result(0, '评论成功!', array());
	}
	//店员评价 i,op,order_id,hotelid
	public function doPageClerkCommon(){
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$weid = $_GPC['i'];
		$login_info = $this->check_login();
		if($login_info['code'] == -1){
			return $this->result($login_info['code'], $login_info['message'], array());
		}
		$show_data = array();
		if($op=='list') {
			$sql = 'SELECT * FROM ' . tablename('hotel2_member') . ' WHERE `clerk` = :clerk AND `weid` = :weid AND `status` = :status';
			$show_data['members'] = pdo_fetchall($sql, array(':clerk' => 1, ':weid' => $weid,'status'=>1));
			if(empty($members)) {
				$sign = 0;
			}else {
				$sign =1;
			}
			$show_data['orderid'] = intval($_GPC['order_id']);
			$show_data['hotelid'] =intval($_GPC['hotelid']);
			return $this->result(0, '获取店员成功!', $show_data);
		}
		
		if($op=='post') {
			$id = intval($_GPC['id']);
			$orderid = intval($_GPC['order_id']);
			$hotelid =intval($_GPC['hotelid']);
			$sql = 'SELECT * FROM ' . tablename('hotel2_member') . ' WHERE `id` = :id AND `weid` = :weid';
			$clerk = pdo_fetch($sql, array(':id' => $id, ':weid' => $weid));
			
			if ($_GPC['submit']) {
				$comment = trim($_GPC['comment']) ? trim($_GPC['comment']) : message('请填写评论内容！');
				$realname = trim($_GPC['realname']);
				$grade = intval($_GPC['grade']);
				$insert = array(
						'comment' => $comment,
						'clerkid' => $id,
						'realname' => $realname,
						'grade' => $grade,
						'orderid' =>$orderid,
						'hotelid' => $hotelid,
						'uniacid' => $_W['uniacid'],
						'createtime' => TIMESTAMP
				);
				$result = pdo_insert('hotel2_comment_clerk', $insert);
				if (empty($result)) {
					return $this->result(-1, '保存店员评分数据失败, 请稍后重试!', $show_data);
				}
				$clerkid = pdo_insertid();
				if(empty($clerkid)) {
					return $this->result(-1, '保存店员评分数据失败, 请稍后重试!', $show_data);
				}
				$temp = pdo_update('hotel2_order', array('clerkcomment' => $clerkid), array('id' => $orderid));
				if ($temp == false) {
					return $this->result(-1, '抱歉，刚才操作数据失败！', $show_data);
				}
				return $this->result(0, '店员评分成功！', $show_data);
			}
		}
	}
}