<?php
require_once MB_ROOT . '/controller/Act.class.php';
require_once MB_ROOT . '/controller/Fans.class.php';

global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$openid=$_GPC['openid'];
//判断是否关注
$act=new Act();
$actdetail=$act->getact();
$fan=new Fans();
$faninfo=$fan->getOne($openid,true);
$total=$faninfo['total']*100;
//先搜索pid范围
$pid=pdo_fetchcolumn("select id from ".tablename('enjoy_red_pack')." where uniacid=".$uniacid." and cashmin<=".$total." and
			cashmax>=".$total." limit 1");
if(empty($pid)){
	$pid=pdo_fetchcolumn("select id from ".tablename('enjoy_red_pack')." where uniacid=".$uniacid." order by cashmoney desc limit 1");
}
//查询剩余奖品列表
$rulelist=pdo_fetchall("select id,rchance from ".tablename('enjoy_red_rule')." where uniacid=".$uniacid." and pid=".$pid." and rcount>0");
//echo $rulelist[0]['rchance'];
if(!empty($rulelist)){
	foreach ($rulelist as $k=>$v){
		$prize_arr[$v[id]]=$v[rchance];
		$sum+=$v['rchance'];
	}
	$prize_arr['0']=100-$sum;
}else{
	$prize_arr['0']=100;
}

// var_dump($faninfo);
//判断是否有位置限制
// if(!empty($actdetail['state']) && !empty($faninfo['state'])) {
// 	$valid = false;


// 	if(!empty($actdetail['state']) && !empty($actdetail['city'])) {
// 		if($actdetail['state'] == $faninfo['state'] && $actdetail['city'] == $faninfo['city']) {
// 			$valid = true;
// 		}
// 	} elseif (!empty($actdetail['state'])) {
// 		if($actdetail['state'] == $faninfo['state']) {
// 			$valid = true;
// 		}
// 	}


// 	if(!$valid) {
// 		//	return error(-3, "<h4>你的位置是: {$verifyParams['user']['state']}-{$verifyParams['user']['city']}</h4><br><h5>不在本次活动范围. 请期待我们下一次活动</h5>");
// 		$res['user']['state']=$faninfo['state'];
// 		$res['user']['city']=$faninfo['city'];
// 		$res['type']=-6;
// 		echo json_encode($res);
// 		exit();
// 	}
// }
$province=$faninfo['state'];
$city=$faninfo['city'];
$ip=getip();
$res['ip']=$ip;
if ($actdetail["locationtype"]==0){
	require_once MB_ROOT . '/controller/ipfunction.php';
	if (!empty($actdetail["city"])){
		
		$arr=explode(',',$actdetail['city']);
		$result=false;
		foreach($arr as $value){
			if  (iplimit($ip,$value)===false){
				$result=false;
			} else {
				$result=true;
				break;
			}
		}
		if  ($result==false){
		$res['type']=-6;
		
		echo json_encode($res);
			exit();
		}
		 
	}
}elseif ($actdetail["locationtype"]==2){
	//用户资料判断
	if (!empty($actdetail["city"])){
		$arr=explode(',',$actdetail['city']);
		$result=false;
		foreach($arr as $value){
			if  (strpos($province,$value)===false && strpos($city,$value)===false){
				$result=false;
			} else {
				$result=true;
				break;
			}
		}
		if  ($result==false){
				$res['type']=-6;
				echo json_encode($res);
			exit();
		}
	}
}else if ($actdetail["locationtype"]==1){
	//用户gps地址	判断
	if (!empty($actdetail['city'])){
		$message['time']=strtotime("-7 day",time());
		$message['uniacid']=$_W['uniacid'];
		$message['from_user']=$openid;
		$location=$this->getLocation($message,$actdetail);

// 		if  (empty($location)){
// 			if (!empty($actdetail['empty_location'])){
// 				return array("code"=>"1","msg"=>"");
// 			}

// 		}
		$arr=explode(',',$actdetail['city']);
		$result=false;
		foreach($arr as $value){
			$location['addr']= $value;
			$result=($result || $this->getAddr($location));
		}
		 
		if  ($result==false){
			//return array("code"=>"-7","msg"=>"你没有活动资格阿3");
			$res['type']=-6;
			echo json_encode($res);
			exit();
		}
	}
	 
}





if($actdetail['subscribe']==1){
if($faninfo['subscribe']==0){
	$res['type']=-4;
	//还没有关注
	echo json_encode($res);
	exit();
}

	
}

//是否还有兑奖机会
$chance=pdo_fetchcolumn("select chance from ".tablename('enjoy_red_chance')." where uniacid=".$uniacid." and openid='".$openid."'");
if($chance>10000){
	pdo_update('enjoy_red_chance',array('chance'=>0),array('uniacid'=>$uniacid,'openid'=>$openid));
}
if($chance<1){
	//机会用完了
	$res['type']=-1;
	$res['unit']=$actdetail['unit'];
	echo json_encode($res);
	exit();
}else{
	$rid= $this->getrand($prize_arr);
	$res['type']=1;

	//搜索奖品信息
	$res['rule']=pdo_fetch("select * from ".tablename('enjoy_red_rule')." where uniacid=".$uniacid." and id=".$rid." and pid=".$pid."");
	$res['rule']['rpic']=empty($res['rule']['rpic'])?"../addons/enjoy_red/template/mobile/images/break.png":tomedia($res['rule']['rpic']);
	$res['rule']['rname']=empty($res['rule']['rname'])?"空空如也":$res['rule']['rname'];
	//传6个虚拟数回去
	$reply=pdo_fetch("select * from ".tablename('enjoy_red_reply')." where uniacid=".$uniacid."");
	$rand_array = range($reply['vmin'], $reply['vmax']);
	shuffle($rand_array); //调用现成的数组随机排列函数
	$res['vmoney']=array_slice($rand_array, 0, 6); //截取前6个
	
	
	
	if($rid>0){
		//先检查红包间隔秒数
// 		$last_time=pdo_fetchcolumn("select createtime from ".tablename('enjoy_red_log')." where uniacid=".$uniacid." and openid='".$openid."'
// 				order by createtime desc" );
// 		$time=TIMESTAMP-$last_time;
// 		if($time<=22){
// 			//间隔不足20秒
// 			$res['type']=-7;
// 			echo json_encode($res);
// 			exit();
// 		}
		//红包金额
		$fee=rand($res['rule']['rmin'],$res['rule']['rmax']);
		$res['rule']['money']=$fee*0.01;
		$uniacid=$_W['uniacid'];
		

			//纪录到表里里
// 			$data=array(
// 				'uniacid'=>$uniacid,
// 				'openid'=>$openid,
// 				'money'=>$res['rule']['money'],
// 				'createtime'=>TIMESTAMP
// 			);
// 			$resb=pdo_insert('enjoy_red_back',$data);
// 			if($resb==1){
				//机会减一
				//pdo_query("update ".tablename('enjoy_red_chance')." set chance=chance-1 where uniacid=".$uniacid." and openid='".$openid."'");
				//红包个数--
				pdo_query("update ".tablename('enjoy_red_rule')." set rcount=rcount-1 where uniacid=".$uniacid." and id=".$rid."");
				//计数
					
				$insert=array(
						'uniacid'=>$uniacid,
						'openid'=>$openid,
						'money'=>$res['rule']['money'],
						'createtime'=>TIMESTAMP
				);
				$ress=pdo_insert('enjoy_red_log',$insert);
				if($ress==1){
					//机会减一
					pdo_query("update ".tablename('enjoy_red_chance')." set chance=chance-1 where uniacid=".$uniacid." and openid='".$openid."'");
					//我的机会
					$res['chance']=pdo_fetchcolumn("select chance from ".tablename('enjoy_red_chance')." where uniacid=".$uniacid." and openid='".$openid."'");
					//累计的钱++
					pdo_query("update ".tablename('enjoy_red_fans')." set total=total+".$res['rule']['money']." where uniacid=".$uniacid." and openid='".$openid."'");
						
					//我的奖池
//					$res['countm']=pdo_fetchcolumn("select sum(money) from ".tablename('enjoy_red_log')." where uniacid=".$uniacid." and openid='".$openid."'");
					$fans=pdo_fetch("select cashed,total from ".tablename('enjoy_red_fans')." where uniacid=".$uniacid." and openid='".$openid."'");
					$res['countm']=$fans['total']-$fans['cashed'];
					if($res['countm']<=0){
						$res['countm']=0;
					}
					//累计的钱
					//$res['countl']=pdo_fetchcolumn("select sum(money) from ".tablename('enjoy_red_log')." where openid='".$openid."' and uniacid=".$uniacid." and money>0");
					$res['countl']=$fans['total'];
				}
		//	}
			
		//}
	
		
	}else if($rid==0){
		$res['type']=-5;
		//机会减一
		pdo_query("update ".tablename('enjoy_red_chance')." set chance=chance-1 where uniacid=".$uniacid." and openid='".$openid."'");
		//我的机会
		$res['chance']=pdo_fetchcolumn("select chance from ".tablename('enjoy_red_chance')." where uniacid=".$uniacid." and openid='".$openid."'");
			
			
	}	


		
		
}


echo json_encode($res);
exit();