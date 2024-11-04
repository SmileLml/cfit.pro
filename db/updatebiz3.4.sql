-- DROP TABLE IF EXISTS `zt_workflow`;
CREATE TABLE IF NOT EXISTS `zt_workflow` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent` varchar(30) NOT NULL,
  `child` varchar(30) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'flow',
  `app` varchar(20) NOT NULL,
  `position` varchar(30) NOT NULL,
  `module` varchar(30) NOT NULL,
  `table` varchar(50) NOT NULL,
  `name` varchar(30) NOT NULL,
  `flowchart` text NOT NULL,
  `js` text NOT NULL,
  `css` text NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `administrator` text NOT NULL,
  `desc` text NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '1.0',
  `status` varchar(10) NOT NULL DEFAULT 'wait',
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `type` (`type`),
  KEY `app` (`app`),
  KEY `module` (`module`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`app`, `module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowaction`;
CREATE TABLE IF NOT EXISTS `zt_workflowaction` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` enum('single', 'batch') NOT NULL DEFAULT 'single',
  `batchMode` enum('same', 'different') NOT NULL DEFAULT 'different',
  `extensionType` varchar(10) NOT NULL DEFAULT 'override' COMMENT 'none | extend | override',
  `open` varchar(20) NOT NULL,
  `position` enum('menu', 'browseandview', 'browse', 'view') NOT NULL DEFAULT 'browseandview',
  `layout` char(20) NOT NULL,
  `show` enum('dropdownlist', 'direct') NOT NULL DEFAULT 'dropdownlist',
  `order` smallint(5) unsigned NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `conditions` text NOT NULL,
  `verifications` text NOT NULL,
  `hooks` text NOT NULL,
  `linkages` text NOT NULL,
  `js` text NOT NULL,
  `css` text NOT NULL,
  `toList` char(255) NOT NULL,
  `desc` text NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `action` (`action`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`module`, `action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowdatasource`;
CREATE TABLE IF NOT EXISTS `zt_workflowdatasource` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('system', 'sql', 'func', 'option', 'lang') NOT NULL DEFAULT 'option',
  `name` varchar(30) NOT NULL,
  `datasource` text NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowfield`;
CREATE TABLE IF NOT EXISTS `zt_workflowfield` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `field`  varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'varchar',
  `length` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `control` varchar(20) NOT NULL,
  `options` text NOT NULL,
  `default` varchar(100) NOT NULL,
  `rules` varchar(255) NOT NULL,
  `placeholder` varchar(100) NOT NULL,
  `canExport` enum('0', '1') NOT NULL DEFAULT '0',
  `canSearch` enum('0', '1') NOT NULL DEFAULT '0',
  `isForeignKey` enum('0', '1') NOT NULL DEFAULT '0',
  `isKey` enum('0', '1') NOT NULL DEFAULT '0',
  `isValue` enum('0', '1') NOT NULL DEFAULT '0',
  `order` smallint(5) unsigned NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `desc` text NOT NULL,
  `readonly` enum('0', '1') NOT NULL DEFAULT '0',
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `field` (`field`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`module`, `field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowlayout`;
CREATE TABLE IF NOT EXISTS `zt_workflowlayout` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `field`  varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `width` smallint(5) NOT NULL,
  `position` text NOT NULL,
  `readonly` enum('0', '1') NOT NULL DEFAULT '0',
  `mobileShow` enum('0', '1') NOT NULL DEFAULT '1',
  `totalShow` enum('0', '1') NOT NULL DEFAULT '0',
  `defaultValue` text NOT NULL,
  `layoutRules` varchar(255) NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `action` (`action`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`module`, `action`, `field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowlabel`;
CREATE TABLE IF NOT EXISTS `zt_workflowlabel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `code` varchar(30) NOT NULL,
  `label` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `order` tinyint(3) NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowrule`;
CREATE TABLE IF NOT EXISTS `zt_workflowrule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('system', 'regex', 'func') NOT NULL DEFAULT 'regex',
  `name` varchar(30) NOT NULL,
  `rule` text NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowsql`;
CREATE TABLE IF NOT EXISTS `zt_workflowsql` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `field` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `sql` text NOT NULL,
  `vars` text NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `field` (`field`),
  KEY `action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowversion`;
CREATE TABLE IF NOT EXISTS `zt_workflowversion` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `version` varchar(10) NOT NULL,
  `fields` text NOT NULL,
  `actions` text NOT NULL,
  `layouts` text NOT NULL,
  `sqls` text NOT NULL,
  `labels` text NOT NULL,
  `table` text NOT NULL,
  `datas` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `moduleversion` (`module`, `version`),
  KEY `module` (`module`),
  KEY `version` (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_action` CHANGE `action` `action` varchar(80) NOT NULL DEFAULT '';
ALTER TABLE `zt_action` CHANGE `extra` `extra` text;
ALTER TABLE `zt_file`   CHANGE `objectType` `objectType` char(30) NOT NULL;

INSERT INTO `zt_workflowrule` VALUES (1,'system','必填','notempty','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00'),(2,'system','唯一','unique','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00'),(3,'system','日期','date','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00'),(4,'system','email','email','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00'),(5,'system','数字','float','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00'),(6,'system','电话','phone','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00'),(7,'system','IP','ip','admin','2020-07-09 21:05:50','','0000-00-00 00:00:00');

REPLACE INTO `zt_grouppriv` VALUES
(1,'apppriv','flow'),
(1,'workflow','activate'),
(1,'workflow','backup'),
(1,'workflow','browseDB'),
(1,'workflow','browseFlow'),
(1,'workflow','copy'),
(1,'workflow','create'),
(1,'workflow','css'),
(1,'workflow','deactivate'),
(1,'workflow','delete'),
(1,'workflow','edit'),
(1,'workflow','flowchart'),
(1,'workflow','js'),
(1,'workflow','release'),
(1,'workflow','ui'),
(1,'workflow','upgrade'),
(1,'workflow','view'),
(1,'workflowaction','browse'),
(1,'workflowaction','create'),
(1,'workflowaction','css'),
(1,'workflowaction','delete'),
(1,'workflowaction','edit'),
(1,'workflowaction','js'),
(1,'workflowaction','setNotice'),
(1,'workflowaction','setVerification'),
(1,'workflowaction','view'),
(1,'workflowcondition','browse'),
(1,'workflowcondition','create'),
(1,'workflowcondition','delete'),
(1,'workflowcondition','edit'),
(1,'workflowdatasource','browse'),
(1,'workflowdatasource','create'),
(1,'workflowdatasource','edit'),
(1,'workflowdatasource','delete'),
(1,'workflowfield','browse'),
(1,'workflowfield','create'),
(1,'workflowfield','edit'),
(1,'workflowfield','delete'),
(1,'workflowfield','setExport'),
(1,'workflowfield','setSearch'),
(1,'workflowfield','sort'),
(1,'workflowhook','browse'),
(1,'workflowhook','create'),
(1,'workflowhook','delete'),
(1,'workflowhook','edit'),
(1,'workflowlabel','browse'),
(1,'workflowlabel','create'),
(1,'workflowlabel','edit'),
(1,'workflowlabel','delete'),
(1,'workflowlabel','sort'),
(1,'workflowlayout','admin'),
(1,'workflowlinkage','browse'),
(1,'workflowlinkage','create'),
(1,'workflowlinkage','delete'),
(1,'workflowlinkage','edit'),
(1,'workflowrelation','admin'),
(1,'workflowrule','browse'),
(1,'workflowrule','create'),
(1,'workflowrule','edit'),
(1,'workflowrule','view'),
(1,'workflowrule','delete');

ALTER TABLE `zt_workflow` ADD `navigator` varchar(10) NOT NULL AFTER `type`;

INSERT INTO `zt_workflowdatasource` (`id`, `type`, `name`, `datasource`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES
(1,'system','产品','{\"app\":\"system\",\"module\":\"product\",\"method\":\"getPairs\",\"methodDesc\":\"Get product pairs.\",\"params\":[{\"name\":\"mode\",\"type\":\"string\",\"desc\":\"\",\"value\":\"noclosed\"}]}','admin','2019-07-15 10:49:53','','0000-00-00 00:00:00'),
(2,'system','项目','{\"app\":\"system\",\"module\":\"project\",\"method\":\"getPairs\",\"methodDesc\":\"Get project pairs.\",\"params\":[{\"name\":\"mode\",\"type\":\"string\",\"desc\":\"all|noclosed or empty\",\"value\":\"noclosed\"}]}','admin','2019-07-15 10:50:07','','0000-00-00 00:00:00'),
(3,'system','产品线','{\"app\":\"system\",\"module\":\"tree\",\"method\":\"getLinePairs\",\"methodDesc\":\"Get line pairs.\",\"params\":[{\"name\":\"useShort\",\"type\":\"bool\",\"desc\":\"\",\"value\":\"\"}]}','admin','2019-07-15 10:51:15','','0000-00-00 00:00:00'),
(4,'sql','需求','select id,title from zt_story','admin','2019-07-15 10:53:15','','0000-00-00 00:00:00'),
(5,'sql','任务','select id,name from zt_task','admin','2019-07-15 10:53:46','','0000-00-00 00:00:00'),
(6,'sql','Bug','select id,title from zt_bug','admin','2019-07-15 10:54:01','','0000-00-00 00:00:00'),
(7,'system','权限分组','{\"app\":\"system\",\"module\":\"group\",\"method\":\"getPairs\",\"methodDesc\":\"\",\"params\":[]}','admin','2019-07-15 10:54:37','','0000-00-00 00:00:00'),
(8,'system','用户','{\"app\":\"system\",\"module\":\"user\",\"method\":\"getPairs\",\"methodDesc\":\"\",\"params\":[{\"name\":\"params\",\"type\":\"\",\"desc\":\"\",\"value\":\"noclosed|noletter\"},{\"name\":\"usersToAppended\",\"type\":\"\",\"desc\":\"\",\"value\":\"\"}]}','admin','2019-07-15 10:55:00','','0000-00-00 00:00:00'),
(9,'system','产品分支','{\"app\":\"system\",\"module\":\"branch\",\"method\":\"getPairs\",\"methodDesc\":\"Get pairs.\",\"params\":[{\"name\":\"productID\",\"type\":\"int\",\"desc\":\"\",\"value\":\"\"},{\"name\":\"params\",\"type\":\"string\",\"desc\":\"\",\"value\":\"\"}]}','admin','2019-07-15 10:56:56','','0000-00-00 00:00:00'),
(10,'sql','版本','select id,name from zt_build','admin','2019-07-15 10:58:01','','0000-00-00 00:00:00'),
(11,'sql','模块','select id,name from zt_module','admin','2019-07-15 11:06:25','','0000-00-00 00:00:00'),
(12,'sql','计划','select id,title from zt_productplan','admin','2019-07-15 11:06:44','','0000-00-00 00:00:00'),
(13,'lang','产品类型','productType','admin','2019-07-15 15:40:50','','0000-00-00 00:00:00'),
(14,'lang','产品状态','productStatus','admin','2019-07-15 15:41:14','','0000-00-00 00:00:00'),
(15,'lang','产品访问控制','productAcl','admin','2019-07-15 15:45:44','','0000-00-00 00:00:00'),
(16,'lang','项目类型','projectType','admin','2019-07-15 15:46:33','','0000-00-00 00:00:00'),
(17,'lang','项目状态','projectStatus','admin','2019-07-15 15:46:47','','0000-00-00 00:00:00'),
(18,'lang','项目访问控制','projectAcl','admin','2019-07-15 15:46:58','','0000-00-00 00:00:00'),
(19,'lang','发布状态','releaseStatus','admin','2019-07-15 15:50:42','','0000-00-00 00:00:00'),
(20,'lang','需求来源','storySource','admin','2019-07-15 15:51:06','','0000-00-00 00:00:00'),
(21,'lang','需求优先级','storyPri','admin','2019-07-15 15:55:04','','0000-00-00 00:00:00'),
(22,'lang','需求状态','storyStatus','admin','2019-07-15 15:55:25','','0000-00-00 00:00:00'),
(23,'lang','需求阶段','storyStage','admin','2019-07-15 15:55:40','','0000-00-00 00:00:00'),
(24,'lang','Bug严重程度','bugSeverity','admin','2019-07-15 15:55:57','','0000-00-00 00:00:00'),
(25,'lang','Bug优先级','bugPri','admin','2019-07-15 15:56:13','','0000-00-00 00:00:00'),
(26,'lang','Bug类型','bugType','admin','2019-07-15 15:56:28','','0000-00-00 00:00:00'),
(27,'lang','Bug操作系统','bugOs','admin','2019-07-15 15:56:43','','0000-00-00 00:00:00'),
(28,'lang','Bug浏览器','bugBrowser','admin','2019-07-15 15:57:03','','0000-00-00 00:00:00'),
(29,'lang','Bug状态','bugStatus','admin','2019-07-15 15:57:48','','0000-00-00 00:00:00'),
(30,'lang','任务类型','taskType','admin','2019-07-15 15:59:17','','0000-00-00 00:00:00'),
(31,'lang','任务优先级','taskPri','admin','2019-07-15 15:59:32','','0000-00-00 00:00:00'),
(32,'lang','任务状态','taskStatus','admin','2019-07-15 15:59:51','','0000-00-00 00:00:00'),
(33,'lang','测试用例优先级','testcasePri','admin','2019-07-15 16:00:32','','0000-00-00 00:00:00'),
(34,'lang','测试用例类型','testcaseType','admin','2019-07-15 16:06:52','','0000-00-00 00:00:00'),
(35,'lang','测试用例阶段','testcaseStage','admin','2019-07-15 16:07:18','','0000-00-00 00:00:00'),
(36,'lang','测试用例状态','testcaseStatus','admin','2019-07-15 16:07:47','','0000-00-00 00:00:00'),
(37,'lang','测试单优先级','testtaskPri','admin','2019-07-15 16:18:52','','0000-00-00 00:00:00'),
(38,'lang','测试单状态','testtaskStatus','admin','2019-07-15 16:19:25','','0000-00-00 00:00:00'),
(39,'lang','反馈状态','feedbackStatus','admin','2019-07-15 16:23:19','','0000-00-00 00:00:00');
