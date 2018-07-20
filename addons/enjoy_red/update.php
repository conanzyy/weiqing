<?php 
pdo_query("CREATE TABLE IF NOT EXISTS `ims_enjoy_red_back` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(50) NOT NULL,
  `openid` varchar(100) NOT NULL,
  `money` float(50,2) NOT NULL DEFAULT '0.00',
  `createtime` int(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_enjoy_red_chance` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(20) DEFAULT NULL,
  `openid` varchar(200) DEFAULT NULL,
  `chance` int(20) unsigned DEFAULT NULL,
  `createtime` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_enjoy_red_chance_log` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(20) DEFAULT NULL,
  `openid` varchar(100) DEFAULT NULL,
  `puid` int(20) DEFAULT NULL,
  `chance` int(10) DEFAULT NULL,
  `createtime` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_enjoy_red_fans` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL,
  `openid` varchar(40) NOT NULL DEFAULT '',
  `proxy` varchar(40) NOT NULL DEFAULT '',
  `unionid` varchar(40) NOT NULL DEFAULT '',
  `nickname` varchar(20) NOT NULL DEFAULT '',
  `gender` varchar(2) DEFAULT '',
  `state` varchar(20) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `country` varchar(20) NOT NULL DEFAULT '',
  `avatar` varchar(500) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `puid` int(20) DEFAULT NULL,
  `subscribe` int(2) DEFAULT NULL,
  `subscribe_time` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `uniacid` (`uniacid`),
  KEY `openid` (`openid`),
  KEY `proxy` (`proxy`),
  KEY `nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_enjoy_red_log` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(20) DEFAULT NULL,
  `openid` varchar(200) DEFAULT NULL,
  `money` float(20,2) DEFAULT NULL,
  `createtime` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_enjoy_red_reply` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(20) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `rule` varchar(1000) DEFAULT NULL,
  `adept` varchar(1000) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `apic` varchar(200) DEFAULT NULL,
  `fpic` varchar(200) DEFAULT NULL,
  `bgpic` varchar(200) DEFAULT NULL,
  `redpic` varchar(200) DEFAULT NULL,
  `redpic1` varchar(200) DEFAULT NULL,
  `redpic2` varchar(200) DEFAULT NULL,
  `redpic3` varchar(200) DEFAULT NULL,
  `redpic4` varchar(200) DEFAULT NULL,
  `redpic5` varchar(200) DEFAULT NULL,
  `redpic6` varchar(200) DEFAULT NULL,
  `custom` int(2) NOT NULL DEFAULT '0',
  `sucai` varchar(200) DEFAULT NULL,
  `chance` int(20) DEFAULT NULL,
  `share_chance` int(20) DEFAULT NULL,
  `share_icon` varchar(200) DEFAULT NULL,
  `share_title` varchar(200) DEFAULT NULL,
  `share_content` varchar(200) DEFAULT NULL,
  `vnum` int(200) DEFAULT NULL,
  `vmin` int(50) DEFAULT NULL,
  `vmax` int(50) DEFAULT NULL,
  `subscribe` int(2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `times` int(50) NOT NULL DEFAULT '200',
  `cashgz` int(2) NOT NULL DEFAULT '0',
  `stime` varchar(200) DEFAULT NULL,
  `etime` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_enjoy_red_rule` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(50) unsigned DEFAULT NULL,
  `rmax` int(20) DEFAULT NULL,
  `rmin` int(20) DEFAULT NULL,
  `rchance` int(10) DEFAULT NULL,
  `rcount` int(100) DEFAULT NULL,
  `createtime` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
");
if(pdo_tableexists('enjoy_red_back')) {
	if(!pdo_fieldexists('enjoy_red_back',  'id')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_back')." ADD `id` int(200) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_back')) {
	if(!pdo_fieldexists('enjoy_red_back',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_back')." ADD `uniacid` int(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_back')) {
	if(!pdo_fieldexists('enjoy_red_back',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_back')." ADD `openid` varchar(100) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_back')) {
	if(!pdo_fieldexists('enjoy_red_back',  'money')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_back')." ADD `money` float(50,2) NOT NULL  DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_back')) {
	if(!pdo_fieldexists('enjoy_red_back',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_back')." ADD `createtime` int(30)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance')) {
	if(!pdo_fieldexists('enjoy_red_chance',  'id')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance')." ADD `id` int(255) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance')) {
	if(!pdo_fieldexists('enjoy_red_chance',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance')." ADD `uniacid` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance')) {
	if(!pdo_fieldexists('enjoy_red_chance',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance')." ADD `openid` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance')) {
	if(!pdo_fieldexists('enjoy_red_chance',  'chance')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance')." ADD `chance` int(20) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance')) {
	if(!pdo_fieldexists('enjoy_red_chance',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance')." ADD `createtime` varchar(100)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance_log')) {
	if(!pdo_fieldexists('enjoy_red_chance_log',  'id')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance_log')." ADD `id` int(100) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance_log')) {
	if(!pdo_fieldexists('enjoy_red_chance_log',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance_log')." ADD `uniacid` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance_log')) {
	if(!pdo_fieldexists('enjoy_red_chance_log',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance_log')." ADD `openid` varchar(100)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance_log')) {
	if(!pdo_fieldexists('enjoy_red_chance_log',  'puid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance_log')." ADD `puid` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance_log')) {
	if(!pdo_fieldexists('enjoy_red_chance_log',  'chance')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance_log')." ADD `chance` int(10)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_chance_log')) {
	if(!pdo_fieldexists('enjoy_red_chance_log',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_chance_log')." ADD `createtime` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'uid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `uid` int(11) NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `uniacid` int(11) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `openid` varchar(40) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'proxy')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `proxy` varchar(40) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'unionid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `unionid` varchar(40) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `nickname` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'gender')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `gender` varchar(2)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'state')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `state` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'city')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `city` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'country')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `country` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `avatar` varchar(500) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'status')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `status` tinyint(4) NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'puid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `puid` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'subscribe')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `subscribe` int(2)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_fans')) {
	if(!pdo_fieldexists('enjoy_red_fans',  'subscribe_time')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_fans')." ADD `subscribe_time` varchar(100)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_log')) {
	if(!pdo_fieldexists('enjoy_red_log',  'id')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_log')." ADD `id` int(255) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_log')) {
	if(!pdo_fieldexists('enjoy_red_log',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_log')." ADD `uniacid` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_log')) {
	if(!pdo_fieldexists('enjoy_red_log',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_log')." ADD `openid` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_log')) {
	if(!pdo_fieldexists('enjoy_red_log',  'money')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_log')." ADD `money` float(20,2)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_log')) {
	if(!pdo_fieldexists('enjoy_red_log',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_log')." ADD `createtime` varchar(100)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'id')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `id` int(50) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `uniacid` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'title')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `title` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'rule')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `rule` varchar(1000)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'adept')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `adept` varchar(1000)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'state')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `state` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'city')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `city` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'color')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `color` varchar(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'apic')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `apic` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'fpic')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `fpic` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'bgpic')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `bgpic` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic1')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic1` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic2')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic2` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic3')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic3` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic4')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic4` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic5')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic5` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'redpic6')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `redpic6` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'custom')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `custom` int(2) NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'sucai')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `sucai` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'chance')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `chance` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'share_chance')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `share_chance` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'share_icon')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `share_icon` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'share_title')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `share_title` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'share_content')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `share_content` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'vnum')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `vnum` int(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'vmin')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `vmin` int(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'vmax')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `vmax` int(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'subscribe')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `subscribe` int(2)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'unit')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `unit` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'times')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `times` int(50) NOT NULL  DEFAULT 200 COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'cashgz')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `cashgz` int(2) NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'stime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `stime` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_reply')) {
	if(!pdo_fieldexists('enjoy_red_reply',  'etime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_reply')." ADD `etime` varchar(200)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'id')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `id` int(200) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `uniacid` int(50) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'rmax')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `rmax` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'rmin')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `rmin` int(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'rchance')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `rchance` int(10)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'rcount')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `rcount` int(100)    COMMENT '';");
	}	
}
if(pdo_tableexists('enjoy_red_rule')) {
	if(!pdo_fieldexists('enjoy_red_rule',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('enjoy_red_rule')." ADD `createtime` varchar(200)    COMMENT '';");
	}	
}
