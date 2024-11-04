ALTER TABLE `zt_effort` DROP INDEX `user`;
ALTER TABLE `zt_effort` DROP `user`;
ALTER TABLE `zt_effort` DROP `todo`;
ALTER TABLE `zt_effort` DROP `type`;
ALTER TABLE `zt_effort` DROP `idvalue`;
ALTER TABLE `zt_effort` DROP `name`;
ALTER TABLE `zt_effort` DROP `desc`;
ALTER TABLE `zt_effort` DROP `status`;

ALTER TABLE `zt_effort` ADD `objectType` varchar(30) NOT NULL AFTER `id`;
ALTER TABLE `zt_effort` ADD `objectID` mediumint(8) unsigned NOT NULL AFTER `objectType`;
ALTER TABLE `zt_effort` ADD `product` varchar(255) NOT NULL AFTER `objectID`;
ALTER TABLE `zt_effort` ADD `project` mediumint(8) unsigned NOT NULL AFTER `product`;
ALTER TABLE `zt_effort` ADD `execution` mediumint(8) unsigned NOT NULL AFTER `project`;
ALTER TABLE `zt_effort` ADD `account` varchar(30) NOT NULL AFTER `execution`;
ALTER TABLE `zt_effort` ADD `work` text COLLATE 'utf8_general_ci' NULL AFTER `account`;
ALTER TABLE `zt_effort` ADD `left` float NOT NULL AFTER `date`;
ALTER TABLE `zt_effort` ADD `consumed` float NOT NULL AFTER `left`;
ALTER TABLE `zt_effort` CHANGE `begin` `begin` smallint(4) unsigned zerofill NOT NULL AFTER `consumed`;
ALTER TABLE `zt_effort` CHANGE `end` `end` smallint(4) unsigned zerofill NOT NULL AFTER `begin`;
ALTER TABLE `zt_effort` ADD `deleted` enum('0','1') NOT NULL DEFAULT '0' AFTER `end`;
ALTER TABLE `zt_effort` ADD INDEX `execution` (`execution`);
ALTER TABLE `zt_effort` ADD INDEX `objectID` (`objectID`);
ALTER TABLE `zt_effort` ADD INDEX `date` (`date`);
ALTER TABLE `zt_effort` ADD INDEX `account` (`account`);

ALTER TABLE `zt_action` ADD  `efforted` BOOL NOT NULL DEFAULT  '0';
INSERT INTO `zt_cron` (`m`, `h`, `dom`, `mon`, `dow`, `command`, `remark`, `type`, `buildin`, `status`, `lastTime`) VALUES ('30', '23', '*', '*', '*', 'moduleName=execution&methodName=computeTaskEffort', '计算任务剩余工时', 'zentao', '1', 'normal', '0000-00-00 00:00:00');
INSERT INTO `zt_cron` (`m`, `h`, `dom`, `mon`, `dow`, `command`, `remark`, `type`, `buildin`, `status`, `lastTime`) VALUES ('30', '7', '*', '*', '*', 'moduleName=effort&methodName=remindNotRecord', '提醒录入日志', 'zentao', '1', 'stop', '0000-00-00 00:00:00');
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'company', 'effort'),
(2, 'company', 'effort'),
(3, 'company', 'effort'),
(4, 'company', 'effort'),
(5, 'company', 'effort'),
(6, 'company', 'effort'),
(7, 'company', 'effort'),
(8, 'company', 'effort'),
(9, 'company', 'effort'),
(10, 'company', 'effort'),
(1, 'effort', 'export'),
(2, 'effort', 'export'),
(3, 'effort', 'export'),
(4, 'effort', 'export'),
(5, 'effort', 'export'),
(6, 'effort', 'export'),
(7, 'effort', 'export'),
(8, 'effort', 'export'),
(9, 'effort', 'export'),
(10, 'effort', 'export'),
(1, 'effort', 'view'),
(2, 'effort', 'view'),
(3, 'effort', 'view'),
(4, 'effort', 'view'),
(5, 'effort', 'view'),
(6, 'effort', 'view'),
(7, 'effort', 'view'),
(8, 'effort', 'view'),
(9, 'effort', 'view'),
(10, 'effort', 'view'),
(1, 'effort', 'edit'),
(2, 'effort', 'edit'),
(3, 'effort', 'edit'),
(4, 'effort', 'edit'),
(5, 'effort', 'edit'),
(6, 'effort', 'edit'),
(7, 'effort', 'edit'),
(8, 'effort', 'edit'),
(9, 'effort', 'edit'),
(10, 'effort', 'edit'),
(1, 'effort', 'batchCreate'),
(2, 'effort', 'batchCreate'),
(3, 'effort', 'batchCreate'),
(4, 'effort', 'batchCreate'),
(5, 'effort', 'batchCreate'),
(6, 'effort', 'batchCreate'),
(7, 'effort', 'batchCreate'),
(8, 'effort', 'batchCreate'),
(9, 'effort', 'batchCreate'),
(10, 'effort', 'batchCreate'),
(1, 'effort', 'delete'),
(2, 'effort', 'delete'),
(3, 'effort', 'delete'),
(4, 'effort', 'delete'),
(5, 'effort', 'delete'),
(6, 'effort', 'delete'),
(7, 'effort', 'delete'),
(8, 'effort', 'delete'),
(9, 'effort', 'delete'),
(10, 'effort', 'delete'),
(1, 'effort', 'createForObject'),
(2, 'effort', 'createForObject'),
(3, 'effort', 'createForObject'),
(4, 'effort', 'createForObject'),
(5, 'effort', 'createForObject'),
(6, 'effort', 'createForObject'),
(7, 'effort', 'createForObject'),
(8, 'effort', 'createForObject'),
(9, 'effort', 'createForObject'),
(10, 'effort', 'createForObject'),
(1, 'my', 'effort'),
(2, 'my', 'effort'),
(3, 'my', 'effort'),
(4, 'my', 'effort'),
(5, 'my', 'effort'),
(6, 'my', 'effort'),
(7, 'my', 'effort'),
(8, 'my', 'effort'),
(9, 'my', 'effort'),
(10, 'my', 'effort'),
(1, 'execution', 'effort'),
(2, 'execution', 'effort'),
(3, 'execution', 'effort'),
(4, 'execution', 'effort'),
(5, 'execution', 'effort'),
(6, 'execution', 'effort'),
(7, 'execution', 'effort'),
(8, 'execution', 'effort'),
(9, 'execution', 'effort'),
(10, 'execution', 'effort'),
(1, 'user', 'effort');
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'todo', 'calendar'),
(2, 'todo', 'calendar'),
(3, 'todo', 'calendar'),
(4, 'todo', 'calendar'),
(5, 'todo', 'calendar'),
(6, 'todo', 'calendar'),
(7, 'todo', 'calendar'),
(8, 'todo', 'calendar'),
(9, 'todo', 'calendar'),
(10, 'todo', 'calendar'),
(1, 'effort', 'calendar'),
(2, 'effort', 'calendar'),
(3, 'effort', 'calendar'),
(4, 'effort', 'calendar'),
(5, 'effort', 'calendar'),
(6, 'effort', 'calendar'),
(7, 'effort', 'calendar'),
(8, 'effort', 'calendar'),
(9, 'effort', 'calendar'),
(10, 'effort', 'calendar'),
(1, 'execution', 'calendar'),
(2, 'execution', 'calendar'),
(3, 'execution', 'calendar'),
(4, 'execution', 'calendar'),
(5, 'execution', 'calendar'),
(6, 'execution', 'calendar'),
(7, 'execution', 'calendar'),
(8, 'execution', 'calendar'),
(9, 'execution', 'calendar'),
(10, 'execution', 'calendar'),
(1, 'user', 'effortcalendar'),
(2, 'user', 'effortcalendar'),
(3, 'user', 'effortcalendar'),
(4, 'user', 'effortcalendar'),
(5, 'user', 'effortcalendar'),
(6, 'user', 'effortcalendar'),
(7, 'user', 'effortcalendar'),
(8, 'user', 'effortcalendar'),
(9, 'user', 'effortcalendar'),
(10, 'user', 'effortcalendar'),
(1, 'user', 'todocalendar'),
(2, 'user', 'todocalendar'),
(3, 'user', 'todocalendar'),
(4, 'user', 'todocalendar'),
(5, 'user', 'todocalendar'),
(6, 'user', 'todocalendar'),
(7, 'user', 'todocalendar'),
(8, 'user', 'todocalendar'),
(9, 'user', 'todocalendar'),
(10, 'user', 'todocalendar');
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'bug', 'exportTemplet'),
(4, 'bug', 'exportTemplet'),
(1, 'bug', 'import'),
(4, 'bug', 'import'),
(1, 'bug', 'showImport'),
(4, 'bug', 'showImport'),
(1, 'story', 'exportTemplet'),
(4, 'story', 'exportTemplet'),
(1, 'story', 'import'),
(4, 'story', 'import'),
(1, 'story', 'showImport'),
(4, 'story', 'showImport'),
(1, 'task', 'exportTemplet'),
(4, 'task', 'exportTemplet'),
(1, 'task', 'import'),
(4, 'task', 'import'),
(1, 'task', 'showImport'),
(4, 'task', 'showImport');
 -- DROP TABLE IF EXISTS `zt_relationoftasks`;
CREATE TABLE IF NOT EXISTS `zt_relationoftasks` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `execution` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `pretask` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `condition` ENUM( 'begin', 'end' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
  `task` MEDIUMINT( 8 ) UNSIGNED NOT NULL ,
  `action` ENUM( 'begin', 'end' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `relationoftasks` (`execution`,`task`)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'project', 'deleterelation'),
(1, 'project', 'gantt'),
(2, 'project', 'gantt'),
(3, 'project', 'gantt'),
(4, 'project', 'gantt'),
(5, 'project', 'gantt'),
(6, 'project', 'gantt'),
(7, 'project', 'gantt'),
(8, 'project', 'gantt'),
(9, 'project', 'gantt'),
(10, 'project', 'gantt'),
(1, 'project', 'relation'),
(2, 'project', 'relation'),
(3, 'project', 'relation'),
(4, 'project', 'relation'),
(5, 'project', 'relation'),
(6, 'project', 'relation'),
(7, 'project', 'relation'),
(8, 'project', 'relation'),
(9, 'project', 'relation'),
(10, 'project', 'relation');
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'report', 'build'),
(2, 'report', 'build'),
(3, 'report', 'build'),
(4, 'report', 'build'),
(5, 'report', 'build'),
(6, 'report', 'build'),
(7, 'report', 'build'),
(8, 'report', 'build'),
(9, 'report', 'build'),
(10, 'report', 'build'),
(1, 'report', 'testcase'),
(2, 'report', 'testcase'),
(3, 'report', 'testcase'),
(4, 'report', 'testcase'),
(5, 'report', 'testcase'),
(6, 'report', 'testcase'),
(7, 'report', 'testcase'),
(8, 'report', 'testcase'),
(9, 'report', 'testcase'),
(10, 'report', 'testcase'),
(1, 'report', 'workSummary'),
(2, 'report', 'workSummary'),
(3, 'report', 'workSummary'),
(4, 'report', 'workSummary'),
(5, 'report', 'workSummary'),
(6, 'report', 'workSummary'),
(7, 'report', 'workSummary'),
(8, 'report', 'workSummary'),
(9, 'report', 'workSummary'),
(10, 'report', 'workSummary');
ALTER TABLE `zt_user` ADD `ldap` CHAR(30) NOT NULL AFTER `ranzhi`;

-- DROP TABLE IF EXISTS `zt_report`;
CREATE TABLE IF NOT EXISTS `zt_report` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `module` varchar(100) NOT NULL,
  `sql` text NOT NULL,
  `vars` text NOT NULL,
  `langs` text NOT NULL,
  `params` text NOT NULL,
  `step` tinyint(1) NOT NULL DEFAULT '2',
  `desc` text NOT NULL,
  `addedBy` char(30) NOT NULL,
  `addedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE OR REPLACE VIEW `ztv_executionsummary` AS select `zt_task`.`execution` AS `execution`,sum(if((`zt_task`.`parent` >= '0'),`zt_task`.`estimate`,0)) AS `estimate`,sum(if((`zt_task`.`parent` >= '0'),`zt_task`.`consumed`,0)) AS `consumed`,sum(if(((`zt_task`.`status` <> 'cancel') and (`zt_task`.`status` <> 'closed') and (`zt_task`.`parent` >= '0')),`zt_task`.`left`,0)) AS `left`,count(0) AS `number`,sum(if(((`zt_task`.`status` <> 'done') and (`zt_task`.`status` <> 'closed')),1,0)) AS `undone`,sum((if((`zt_task`.`parent` >= '0'),`zt_task`.`consumed`,0) + if(((`zt_task`.`status` <> 'cancel') and (`zt_task`.`status` <> 'closed') and (`zt_task`.`parent` >= '0')),`zt_task`.`left`,0))) AS `totalReal` from `zt_task` where (`zt_task`.`deleted` = '0') group by `zt_task`.`execution`;
CREATE OR REPLACE VIEW `ztv_projectsummary` AS select `zt_task`.`project` AS `project`,sum(if((`zt_task`.`parent` >= '0'),`zt_task`.`estimate`,0)) AS `estimate`,sum(if((`zt_task`.`parent` >= '0'),`zt_task`.`consumed`,0)) AS `consumed`,sum(if(((`zt_task`.`status` <> 'cancel') and (`zt_task`.`status` <> 'closed') and (`zt_task`.`parent` >= '0')),`zt_task`.`left`,0)) AS `left`,count(0) AS `number`,sum(if(((`zt_task`.`status` <> 'done') and (`zt_task`.`status` <> 'closed')),1,0)) AS `undone`,sum((if((`zt_task`.`parent` >= '0'),`zt_task`.`consumed`,0) + if(((`zt_task`.`status` <> 'cancel') and (`zt_task`.`status` <> 'closed') and (`zt_task`.`parent` >= '0')),`zt_task`.`left`,0))) AS `totalReal` from `zt_task` where (`zt_task`.`deleted` = '0') group by `zt_task`.`project`;
CREATE OR REPLACE VIEW `ztv_projectstories` AS select `t1`.`project` AS `execution`,count('*') AS `stories`,sum(if((`t2`.`status` = 'closed'),0,1)) AS `undone` from ((`zt_projectstory` `t1` left join `zt_story` `t2` on((`t1`.`story` = `t2`.`id`))) left join `zt_project` `t3` on((`t1`.`project` = `t3`.`id`))) where ((`t2`.`deleted` = '0') and (`t3`.`type` in ('sprint','stage'))) group by `t1`.`project`;
CREATE OR REPLACE VIEW `ztv_projectteams` AS select `zt_team`.`root` AS `execution`,count('*') AS `teams` from `zt_team` where (`zt_team`.`type` = 'execution') group by `zt_team`.`root`;
CREATE OR REPLACE VIEW `ztv_projectbugs` AS select `zt_bug`.`execution` AS `execution`,count(0) AS `bugs`,sum(if((`zt_bug`.`resolution` = ''),0,1)) AS `resolutions`,sum(if((`zt_bug`.`severity` <= 2),1,0)) AS `seriousBugs` from `zt_bug` where (`zt_bug`.`deleted` = '0') group by `zt_bug`.`execution`;
CREATE OR REPLACE VIEW `ztv_productbugs` AS select `zt_bug`.`product` AS `product`,count(0) AS `bugs`,sum(if((`zt_bug`.`resolution` = ''),0,1)) AS `resolutions`,sum(if((`zt_bug`.`severity` <= 2),1,0)) AS `seriousBugs` from `zt_bug` where (`zt_bug`.`deleted` = '0') group by `zt_bug`.`product`;
CREATE OR REPLACE VIEW `ztv_productstories` AS select `zt_story`.`product` AS `product`,count('*') AS `stories`,sum(if((`zt_story`.`status` = 'closed'),0,1)) AS `undone` from `zt_story` where (`zt_story`.`deleted` = '0') group by `zt_story`.`product`;
CREATE OR REPLACE VIEW `ztv_dayuserlogin` AS select count(*) AS `userlogin`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'user') and (`zt_action`.`action` = 'login')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_dayeffort` AS select round(sum(`zt_effort`.`consumed`),1) AS `consumed`,`zt_effort`.`date` AS `date` from `zt_effort` group by `zt_effort`.`date`;
CREATE OR REPLACE VIEW `ztv_daystoryopen` AS select count(*) AS `storyopen`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'story') and (`zt_action`.`action` = 'opened')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_daystoryclose` AS select count(*) AS `storyclose`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'story') and (`zt_action`.`action` = 'closed')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_daytaskopen` AS select count(*) AS `taskopen`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'task') and (`zt_action`.`action` = 'opened')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_daytaskfinish` AS select count(*) AS `taskfinish`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'task') and (`zt_action`.`action` = 'finished')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_daybugopen` AS select count(*) AS `bugopen`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'bug') and (`zt_action`.`action` = 'opened')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_daybugresolve` AS select count(*) AS `bugresolve`,left(`zt_action`.`date`,10) AS `day` from `zt_action` where ((`zt_action`.`objectType` = 'bug') and (`zt_action`.`action` = 'resolved')) group by left(`zt_action`.`date`,10);
CREATE OR REPLACE VIEW `ztv_dayactions` AS select count(*) AS `actions`,left(`zt_action`.`date`,10) AS `day` from `zt_action` group by left(`zt_action`.`date`,10);
REPLACE INTO `zt_report` (`code`, `name`, `module`, `sql`, `vars`, `langs`, `params`, `step`, `desc`, `addedBy`, `addedDate`) VALUES
('product-invest',       '{\"zh-cn\":\"\\u4ea7\\u54c1\\u6295\\u5165\\u8868\",\"zh-tw\":\"\\u7522\\u54c1\\u6295\\u5165\\u8868\",\"en\":\"Product Investment\"}',  ',product',     'select t1.id,t1.name,1 as projects, round(t3.consumed,2) as consumed from TABLE_PRODUCT as t1\r\n left join TABLE_PROJECTPRODUCT as t2 on t1.id=t2.product\r\n left join ztv_projectsummary as t3 on t2.project=t3.project\r\n left join TABLE_PROJECT as t4 on t2.project=t4.id\r\n left join TABLE_PROGRAM as t5 on t1.program=t5.id\r\n where t1.deleted=\'0\' and t4.deleted=\'0\' and t4.type=\'project\'\r\norder by t5.`order` asc, t1.line desc, t1.`order` asc',      '',     '{\"projects\":{\"zh-cn\":\"\\u9879\\u76ee\\u6570\",\"zh-tw\":\"\\u9879\\u76ee\\u6570\",\"en\":\"Projects\"},\"consumed\":{\"zh-cn\":\"\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\",\"zh-tw\":\"\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\",\"en\":\"Cost(h)\"}}',   '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"projects\",\"consumed\"],\"reportType\":[\"sum\",\"sum\"],\"sumAppend\":[\"projects\",\"consumed\"]}', 2,      '{\"zh-cn\":\"\\u5217\\u51fa\\u6bcf\\u4e2a\\u4ea7\\u54c1\\u7684\\u9879\\u76ee\\u603b\\u6570\\uff0c\\u5df2\\u7ecf\\u6d88\\u8017\\u7684\\u5de5\\u65f6\\u3002\",\"zh-tw\":\"\\u5217\\u51fa\\u6bcf\\u500b\\u7522\\u54c1\\u7684\\u9805\\u76ee\\u7e3d\\u6578\\uff0c\\u5df2\\u7d93\\u6d88\\u8017\\u7684\\u5de5\\u6642\\u3002 \",\"en\":\"Number of projects and consumed hours.\"}',   'admin',        '2015-07-20 14:21:30'),
('product-progress',     '{\"zh-cn\":\"\\u4ea7\\u54c1\\u5b8c\\u6210\\u5ea6\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\\u7522\\u54c1\\u5b8c\\u6210\\u5ea6\\u7d71\\u8a08\\u8868\",\"en\":\"Product Progress\"}',  ',product',     'select t1.*,t2.name, (case when t1.status = \'closed\' or t1.stage = \'released\' then 1 else 0 end) as done, 1 as count from TABLE_STORY as t1 \r\nleft join TABLE_PRODUCT as t2 on t1.product=t2.id \r\nleft join TABLE_PROGRAM as t3 on t2.program=t3.id \r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\'\r\norder by t3.`order` asc, t2.line desc, t2.`order` asc', '',     '{\"count\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"done\":{\"zh-cn\":\"\\u5b8c\\u6210\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u6570\",\"en\":\"Done\"}}',  '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"count\",\"done\"],\"reportType\":[\"sum\",\"sum\"],\"sumAppend\":[\"count\",\"done\"],\"percent\":{\"1\":\"1\"},\"contrast\":{\"1\":\"count\"},\"showAlone\":{\"1\":\"1\"}}',  2,      '{\"zh-cn\":\"\\u6309\\u7167\\u4ea7\\u54c1\\u5217\\u51fa\\u9700\\u6c42\\u603b\\u6570\\uff0c\\u5b8c\\u6210\\u7684\\u603b\\u6570(\\u72b6\\u6001\\u662f\\u5173\\u95ed\\uff0c\\u6216\\u8005\\u7814\\u53d1\\u9636\\u6bb5\\u662f\\u53d1\\u5e03)\\uff0c\\u5b8c\\u6210\\u7684\\u767e\\u5206\\u6bd4\\u3002\",\"zh-tw\":\"\\u6309\\u7167\\u7522\\u54c1\\u5217\\u51fa\\u9700\\u6c42\\u7e3d\\u6578\\uff0c\\u5b8c\\u6210\\u7684\\u7e3d\\u6578\\uff08\\u72c0\\u614b\\u662f\\u95dc\\u9589\\uff0c\\u6216\\u8005\\u7814\\u767c\\u968e\\u6bb5\\u662f\\u767c\\u4f48\\uff09\\uff0c\\u5b8c\\u6210\\u7684\\u767e\\u5206\\u6bd4\\u3002\",\"en\":\"Number of total stories,done stories(state is closed, or stage is released), percent of completion.\"}',     'admin',        '2015-07-21 15:07:48'),
('story-status', '{\"zh-cn\":\"\\u4ea7\\u54c1\\u9700\\u6c42\\u72b6\\u6001\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\\u7522\\u54c1\\u9700\\u6c42\\u72c0\\u614b\\u5206\\u4f48\\u8868\",\"en\":\"Story Status\"}',        ',product',     'select t1.*,t2.name from TABLE_STORY as t1\r\n left join TABLE_PRODUCT as t2 on t1.product=t2.id \r\nleft join TABLE_PROGRAM as t3 on t2.program=t3.id \r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\'\r\norder by t3.`order` asc, t2.line desc, t2.`order` asc',      '',     '',     '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"status\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',   2,      '{\"zh-cn\":\"\\u6309\\u7167\\u4ea7\\u54c1\\u5217\\u51fa\\u9700\\u6c42\\u603b\\u6570\\uff0c\\u72b6\\u6001\\u7684\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\\u6309\\u7167\\u7522\\u54c1\\u5217\\u51fa\\u9700\\u6c42\\u7e3d\\u6578\\uff0c\\u72c0\\u614b\\u7684\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"en\":\"Total number and status distribution of stories.\"}',        'admin',        '2015-07-21 15:35:38'),
('story-stage',  '{\"zh-cn\":\"\\u4ea7\\u54c1\\u9700\\u6c42\\u9636\\u6bb5\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\\u7522\\u54c1\\u9700\\u6c42\\u968e\\u6bb5\\u5206\\u4f48\\u8868\",\"en\":\"Story Stage\"}', ',product',     'select t1.*,t2.name from TABLE_STORY as t1\r\n left join TABLE_PRODUCT as t2 on t1.product=t2.id \r\nleft join TABLE_PROGRAM as t3 on t2.program=t3.id \r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\'\r\norder by t3.`order` asc, t2.line desc, t2.`order` asc',      '',     '',     '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"stage\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',    2,      '{\"zh-cn\":\"\\u6309\\u7167\\u4ea7\\u54c1\\u5217\\u51fa\\u9700\\u6c42\\u603b\\u6570\\uff0c\\u7814\\u53d1\\u9636\\u6bb5\\u7684\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\\u6309\\u7167\\u7522\\u54c1\\u5217\\u51fa\\u9700\\u6c42\\u7e3d\\u6578\\uff0c\\u7814\\u767c\\u968e\\u6bb5\\u7684\\u5206\\u5e03\\u60c5\\u51b5\\u3002 \",\"en\":\"Total number and stage distribution of stories \"}',    'admin',        '2015-07-21 15:38:34'),
('product-release',      '{\"zh-cn\":\"\\u4ea7\\u54c1\\u53d1\\u5e03\\u6570\\u91cf\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\\u7522\\u54c1\\u767c\\u4f48\\u6578\\u91cf\\u7d71\\u8a08\\u8868\",\"en\":\"Product Release\"}',     ',product',     'select t2.name, 1 as releases from TABLE_RELEASE as t1 \r\nleft join TABLE_PRODUCT as t2 on t1.product=t2.id \r\nleft join TABLE_PROGRAM as t3 on t2.program=t3.id \r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\'\r\norder by t3.`order` asc, t2.line desc, t2.`order` asc',  '',     '{\"count\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"done\":{\"zh-cn\":\"\\u5b8c\\u6210\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u6570\",\"en\":\"Done\"}}',  '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"releases\"],\"reportType\":[\"sum\"],\"sumAppend\":[\"releases\"]}',   2,      '{\"zh-cn\":\"\\u6309\\u7167\\u4ea7\\u54c1\\u5217\\u51fa\\u53d1\\u5e03\\u7684\\u6570\\u91cf\\u3002\",\"zh-tw\":\"\\u6309\\u7167\\u7522\\u54c1\\u5217\\u51fa\\u767c\\u4f48\\u7684\\u6578\\u91cf\\u3002\",\"en\":\"Product Release.\"}',  'admin',        '2015-07-21 16:00:52'),
('task-status',  '{\"zh-cn\":\"\\u4efb\\u52a1\\u72b6\\u6001\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\",\"en\":\"Task Status Report\",\"de\":\"Task Status Report\",\"fr\":\"Task Status Report\",\"vi\":\"Task Status Report\",\"ja\":\"Task Status Report\"}', ',project',     'select t1.id,t3.name as project,t1.name,t2.status,t1.name as execution,t2.id as taskID,  t1.status as projectstatus, (case when t2.deadline < CURDATE() and t2.deadline != \'0000-00-00\' and t2.status != \'closed\' and t2.status != \'done\' and t2.status != \'cancel\' then 1 else 0 end) as timeout from TABLE_EXECUTION as t1\r\n left join TABLE_TASK as t2 on t1.id=t2.execution\r\n left join TABLE_PROJECT as t3 on t3.id=t1.project\r\n where t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and t2.deleted=\'0\' and if($project=\'\',1,t3.id=$project) and if($status=\'\',1,t1.status=$status) and if($beginDate=\'\',1,t1.begin>=$beginDate) and if($endDate=\'\',1,t1.end<=$endDate)',        '{\"varName\":[\"project\",\"status\",\"beginDate\",\"endDate\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\",\"\\u6267\\u884c\\u8d77\\u59cb\\u65e5\\u671f\",\"\\u6267\\u884c\\u7ed3\\u675f\\u65e5\\u671f\"],\"requestType\":[\"select\",\"select\",\"date\",\"date\"],\"selectList\":[\"project\",\"project.status\",\"user\",\"user\"],\"default\":[\"\",\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}',    '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"status\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',       2,      '{\"zh-cn\":\"\\u6309\\u7167\\u6267\\u884c\\u7edf\\u8ba1\\u4efb\\u52a1\\u7684\\u72b6\\u6001\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',  'admin',        '2015-07-22 11:28:33'),
('task-type',    '{\"zh-cn\":\"\\u4efb\\u52a1\\u7c7b\\u578b\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\",\"en\":\"Task Type Report\",\"de\":\"Task Type Report\",\"fr\":\"Task Type Report\",\"vi\":\"Task Type Report\",\"ja\":\"Task Type Report\"}', ',project',     'select t1.id,t3.name as project,t1.name as execution,t2.type,t2.id as taskID, t1.status as projectstatus from TABLE_EXECUTION as t1 \r\nleft join TABLE_TASK as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\nwhere t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and t2.deleted=\'0\' and if($project=\'\',1,t3.id=$project) and if($status=\'\',1,t1.status=$status) and if($beginDate=\'\',1,t1.begin>=$beginDate) and if($endDate=\'\',1,t1.end<=$endDate)',   '{\"varName\":[\"project\",\"status\",\"beginDate\",\"endDate\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\",\"\\u6267\\u884c\\u8d77\\u59cb\\u65e5\\u671f\",\"\\u6267\\u884c\\u7ed3\\u675f\\u65e5\\u671f\"],\"requestType\":[\"select\",\"select\",\"date\",\"date\"],\"selectList\":[\"project\",\"project.status\",\"user\",\"user\"],\"default\":[\"\",\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}',    '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"type\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}', 2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1\\u4efb\\u52a1\\u7684\\u7c7b\\u578b\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',  'admin',        '2015-07-22 13:06:46'),
('task-assign',  '{\"zh-cn\":\"\\u9879\\u76ee\\u4efb\\u52a1\\u6307\\u6d3e\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\",\"en\":\"Task Assign Report\",\"de\":\"Task Assign Report\",\"fr\":\"Task Assign Report\",\"vi\":\"Task Assign Report\",\"ja\":\"Task Assign Report\"}',   ',project',     'select t1.id,t4.name as project,t1.name as execution,if(t3.account is not null, t3.account,t2.assignedTo) as assignedTo,t2.id as taskID, t1.status as projectstatus from TABLE_EXECUTION as t1\r\n left join TABLE_TASK as t2 on t1.id=t2.execution\r\n left join TABLE_TEAM as t3 on t3.type=\'task\' && t3.root=t2.id \r\nleft join TABLE_PROJECT as t4 on t1.project=t4.id\r\nwhere t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and t2.deleted=\'0\' and if($project=\'\',1,t4.id=$project) and if($status=\'\',1,t1.status=$status) and if($beginDate=\'\',1,t1.begin>=$beginDate) and if($endDate=\'\',1,t1.end<=$endDate)',   '{\"varName\":[\"project\",\"status\",\"beginDate\",\"endDate\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\",\"\\u6267\\u884c\\u8d77\\u59cb\\u65e5\\u671f\",\"\\u6267\\u884c\\u7ed3\\u675f\\u65e5\\u671f\"],\"requestType\":[\"select\",\"select\",\"date\",\"date\"],\"selectList\":[\"project\",\"project.status\",\"user\",\"user\"],\"default\":[\"\",\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}',    '{\"assignedTo\":{\"zh-cn\":\"\\u6307\\u6d3e\\u7ed9\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',    '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"assignedTo\"],\"isUser\":{\"reportField\":[[\"1\"]]},\"reportType\":[\"count\"],\"sumAppend\":[\"\"]}',    2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1\\u4efb\\u52a1\\u7684\\u6307\\u6d3e\\u7ed9\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',   'admin',        '2015-07-22 13:13:28'),
('task-finish',  '{\"zh-cn\":\"\\u9879\\u76ee\\u4efb\\u52a1\\u5b8c\\u6210\\u8005\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\",\"en\":\"Task Finish Report\",\"de\":\"Task Finish Report\",\"fr\":\"Task Finish Report\",\"vi\":\"Task Finish Report\",\"ja\":\"Task Finish Report\"}',    ',project',     'select t1.id,t3.name as project,t1.name as execution,t2.finishedBy,t2.id as taskID, t1.status as projectstatus from TABLE_EXECUTION as t1 \r\nleft join TABLE_TASK as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t1.project=t3.id \r\nwhere t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and t2.deleted=\'0\' and t2.finishedBy!=\'\' and if($project=\'\',1,t3.id=$project) and if($status=\'\',1,t1.status=$status) and if($beginDate=\'\',1,t1.begin>=$beginDate) and if($endDate=\'\',1,t1.end<=$endDate)',    '{\"varName\":[\"project\",\"status\",\"beginDate\",\"endDate\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\",\"\\u6267\\u884c\\u8d77\\u59cb\\u65e5\\u671f\",\"\\u6267\\u884c\\u7ed3\\u675f\\u65e5\\u671f\"],\"requestType\":[\"select\",\"select\",\"date\",\"date\"],\"selectList\":[\"project\",\"project.status\",\"user\",\"user\"],\"default\":[\"\",\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}',    '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"finishedBy\"],\"isUser\":{\"reportField\":[[\"1\"]]},\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',    2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1\\u4efb\\u52a1\\u7684\\u5b8c\\u6210\\u8005\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',   'admin',        '2015-07-22 13:16:21'),
('project-invest',       '{\"zh-cn\":\"\\u9879\\u76ee\\u6295\\u5165\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Invest Report\",\"de\":\"Project Invest Report\",\"fr\":\"Project Invest Report\",\"vi\":\"Project Invest Report\",\"ja\":\"Project Invest Report\"}', ',project',     'select t1.id,t5.name as project,t1.name as execution,CONCAT(t1.begin,\' ~ \',t1.end) as timeLimit,t2.teams,t3.stories,round(t4.consumed,1) as consumed,t4.number, t1.status as projectstatus \r\nfrom TABLE_EXECUTION as t1\r\n left join ztv_projectteams as t2 on t1.id=t2.execution\r\nleft join ztv_projectstories as t3 on t1.id=t3.execution\r\n left join ztv_executionsummary as t4 on t1.id=t4.execution \r\nleft join TABLE_PROJECT as t5 on t1.project=t5.id \r\nwhere t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and if($project=\'\',1,t5.id=$project) and if($status=\'\',1,t1.status=$status) and if($beginDate=\'\',1,t1.begin>=$beginDate) and if($endDate=\'\',1,t1.end<=$endDate)',     '{\"varName\":[\"project\",\"status\",\"beginDate\",\"endDate\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\",\"\\u6267\\u884c\\u8d77\\u59cb\\u65e5\\u671f\",\"\\u6267\\u884c\\u7ed3\\u675f\\u65e5\\u671f\"],\"requestType\":[\"select\",\"select\",\"date\",\"date\"],\"selectList\":[\"project\",\"project.status\",\"user\",\"user\"],\"default\":[\"\",\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}',    '{\"timeLimit\":{\"zh-cn\":\"\\u5de5\\u671f\"},\"teams\":{\"zh-cn\":\"\\u4eba\\u6570\"},\"stories\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\"},\"consumed\":{\"zh-cn\":\"\\u603b\\u6d88\\u8017\"},\"number\":{\"zh-cn\":\"\\u4efb\\u52a1\\u6570\"},\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',    '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"number\",\"stories\",\"teams\",\"consumed\"],\"reportType\":[\"sum\",\"sum\",\"sum\",\"sum\"],\"sumAppend\":[\"number\",\"stories\",\"teams\",\"consumed\"]}',     2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u5217\\u51fa\\uff1a\\u4efb\\u52a1\\u6570\\uff0c\\u9700\\u6c42\\u6570\\uff0c\\u4eba\\u6570\\uff0c\\u603b\\u6d88\\u8017\\u5de5\\u65f6\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',  'admin',        '2015-07-22 16:37:38'),
('projectstory-status',  '{\"zh-cn\":\"\\u9879\\u76ee\\u9700\\u6c42\\u72b6\\u6001\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Story Status\",\"de\":\"Project Story Status\",\"fr\":\"Project Story Status\",\"vi\":\"Project Story Status\",\"ja\":\"Project Story Status\"}',   ',project',     'select t2.id, t4.name as project,t2.name as execution,t3.status from TABLE_PROJECTSTORY as t1 \r\nleft join TABLE_EXECUTION as t2 on t1.project=t2.id \r\nleft join TABLE_STORY as t3 on t1.story=t3.id \r\nleft join TABLE_PROJECT as t4 on t4.id=t2.project\r\nwhere t2.deleted=\'0\' and t2.type in(\'sprint\', \'stage\') and if($project=\'\',1,t4.id=$project) and if($execution=\'\',1,t2.id=$execution) and if($status=\'\',1,t2.status=$status)',     '{\"varName\":[\"project\",\"execution\",\"status\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\"],\"requestType\":[\"select\",\"select\",\"select\"],\"selectList\":[\"project\",\"execution\",\"project.status\"],\"default\":[\"\",\"\",\"\"]}', '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"status\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',       2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1\\u9700\\u6c42\\u7684\\u72b6\\u6001\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',  'admin',        '2015-07-23 15:35:08'),
('project-stage',        '{\"zh-cn\":\"\\u9879\\u76ee\\u9700\\u6c42\\u9636\\u6bb5\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Stage Report\",\"de\":\"Project Stage Report\",\"fr\":\"Project Stage Report\",\"vi\":\"Project Stage Report\",\"ja\":\"Project Stage Report\"}',   ',project',     'select t2.id, t4.name as project,t2.name as execution,t3.stage from TABLE_PROJECTSTORY as t1 \r\nleft join TABLE_EXECUTION as t2 on t1.project=t2.id \r\nleft join TABLE_STORY as t3 on t1.story=t3.id \r\nleft join TABLE_PROJECT as t4 on t4.id=t2.project\r\nwhere t2.deleted=\'0\' and t2.type in(\'sprint\', \'stage\') and if($project=\'\',1,t4.id=$project) and if($execution=\'\',1,t2.id=$execution) and if($status=\'\',1,t2.status=$status)',      '{\"varName\":[\"project\",\"execution\",\"status\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\"],\"requestType\":[\"select\",\"select\",\"select\"],\"selectList\":[\"project\",\"execution\",\"project.status\"],\"default\":[\"\",\"\",\"\"]}', '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"stage\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',        2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1\\u9700\\u6c42\\u9636\\u6bb5\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}', 'admin',        '2015-07-23 15:38:18'),
('projectbug-resolution',        '{\"zh-cn\":\"\\u9879\\u76eeBug\\u89e3\\u51b3\\u65b9\\u6848\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Bug Resolution\",\"de\":\"Project Bug Resolution\",\"fr\":\"Project Bug Resolution\",\"vi\":\"Project Bug Resolution\",\"ja\":\"Project Bug Resolution\"}',        ',project,test',        'select t1.id,t3.name as project,t3.id,t1.name as execution,t1.id as bugID,t2.resolution from TABLE_EXECUTION as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\n where t1.deleted=\'0\' and t2.deleted=\'0\' and t2.resolution!=\'\' having bugID!=\'\' and if($project=\'\',1,t3.id=$project) and if($execution=\'\',1,t1.id=$execution)',     '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"resolution\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',   2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1Bug\\u7684\\u89e3\\u51b3\\u65b9\\u6848\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',       'admin',        '2015-07-23 16:04:46'),
('projectbug-status',    '{\"zh-cn\":\"\\u9879\\u76eeBug\\u72b6\\u6001\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Bug Status\",\"de\":\"Project Bug Status\",\"fr\":\"Project Bug Status\",\"vi\":\"Project Bug Status\",\"ja\":\"Project Bug Status\"}',      ',project,test',        'select t1.id,t3.name as project,t3.id,t1.name as execution,t1.id as bugID,t2.status from TABLE_EXECUTION as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\' having bugID!=\' \' and if($project=\'\',1,t3.id=$project) and if($execution=\'\',1,t1.id=$execution)', '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"status\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"]}',       2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1Bug\\u7684\\u72b6\\u6001\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',     'admin',        '2015-07-23 15:48:03'),
('projectbug-opened',    '{\"zh-cn\":\"\\u9879\\u76eeBug\\u521b\\u5efa\\u8005\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Bug Opened\",\"de\":\"Project Bug Opened\",\"fr\":\"Project Bug Opened\",\"vi\":\"Project Bug Opened\",\"ja\":\"Project Bug Opened\"}',       ',project,test',        'select t1.id,t3.name as project,t3.id,t1.name as execution,t1.id as bugID,t2.openedBy from TABLE_EXECUTION as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\' having bugID!=\'\' and if($project=\'\',1,t3.id=$project) and if($execution=\'\',1,t1.id=$execution)',        '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"openedBy\"],\"isUser\":{\"reportField\":[[\"1\"]]},\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',      2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1Bug\\u7684\\u521b\\u5efa\\u8005\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',      'admin',        '2015-07-23 16:08:10'),
('projectbug-resolve',   '{\"zh-cn\":\"\\u9879\\u76eeBug\\u89e3\\u51b3\\u8005\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Bug Resolve\",\"de\":\"Project Bug Resolve\",\"fr\":\"Project Bug Resolve\",\"vi\":\"Project Bug Resolve\",\"ja\":\"Project Bug Resolve\"}',       ',project,test',        'select t1.id,t3.name as project,t3.id,t1.name as execution,t1.id as bugID,t2.resolvedBy from TABLE_EXECUTION as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\' and t2.status!=\'active\' and t2.resolvedBy!=\'\' having bugID!=\'\' and if($project=\'\',1,t3.id=$project) and if($execution=\'\',1,t1.id=$execution)',    '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"resolvedBy\"],\"isUser\":{\"reportField\":[[\"1\"]]},\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',    2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1Bug\\u7684\\u89e3\\u51b3\\u8005\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',      'admin',        '2015-07-23 16:13:16'),
('projectbug-assign',    '{\"zh-cn\":\"\\u9879\\u76eeBug\\u6307\\u6d3e\\u7ed9\\u5206\\u5e03\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Bug Assign\",\"de\":\"Project Bug Assign\",\"fr\":\"Project Bug Assign\",\"vi\":\"Project Bug Assign\",\"ja\":\"Project Bug Assign\"}',       ',project,test',        'select t1.id,t3.name as project,t3.id,t1.name as execution,t1.id as bugID,t2.assignedTo from TABLE_EXECUTION as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.execution \r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\' having bugID!=\'\' and if($project=\'\',1,t3.id=$project) and if($execution=\'\',1,t1.id=$execution)',     '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"assignedTo\"],\"isUser\":{\"reportField\":[[\"1\"]]},\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',    2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1Bug\\u7684\\u6307\\u6d3e\\u7ed9\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',      'admin',        '2015-07-23 16:29:10'),
('project-quality',      '{\"zh-cn\":\"\\u9879\\u76ee\\u8d28\\u91cf\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Quality Report\",\"de\":\"Project Quality Report\",\"fr\":\"Project Quality Report\",\"vi\":\"Project Quality Report\",\"ja\":\"Project Quality Report\"}',       ',project',     'select t1.id, t5.name as project,t5.id,t1.name as execution,t2.stories,(t2.stories-t2.undone) as doneStory,t3.number,(t3.number-t3.undone) as doneTask,t4.bugs,t4.resolutions, round(t4.bugs/(t2.stories-t2.undone),2) as bugthanstory,round(t4.bugs/(t3.number-t3.undone),2) as bugthantask,t4.seriousBugs from TABLE_EXECUTION as t1 \r\nleft join ztv_projectstories as t2 on t1.id=t2.execution\r\nleft join ztv_executionsummary as t3 on t1.id=t3.execution\r\nleft join ztv_projectbugs as t4 on t1.id=t4.execution\r\nleft join TABLE_PROJECT as t5 on t5.id=t1.project\r\nwhere t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and t1.grade=\'1\' and if($project=\'\',1,t5.id=$project) and if($execution=\'\',1,t1.id=$execution)', '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"stories\":{\"zh-cn\":\"\\u9700\\u6c42\\u603b\\u6570\"},\"doneStory\":{\"zh-cn\":\"\\u5b8c\\u6210\\u9700\\u6c42\\u6570\"},\"number\":{\"zh-cn\":\"\\u4efb\\u52a1\\u603b\\u6570\"},\"doneTask\":{\"zh-cn\":\"\\u5b8c\\u6210\\u4efb\\u52a1\\u6570\"},\"bugs\":{\"zh-cn\":\"Bug\\u6570\"},\"resolutions\":{\"zh-cn\":\"\\u89e3\\u51b3Bug\\u6570\"},\"bugthanstory\":{\"zh-cn\":\"Bug\\/\\u5b8c\\u6210\\u9700\\u6c42\"},\"bugthantask\":{\"zh-cn\":\"Bug\\/\\u5b8c\\u6210\\u4efb\\u52a1\"},\"seriousBugs\":{\"zh-cn\":\"\\u91cd\\u8981Bug\\u6570\"},\"seriousBugsPercent\":{\"zh-cn\":\"\\u4e25\\u91cdBug\\u6bd4\\u7387\"},\"project\":{\"zh-cn\":\"\\u9879\\u76ee\\u540d\\u79f0\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',        '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"stories\",\"doneStory\",\"number\",\"doneTask\",\"bugs\",\"resolutions\",\"bugthanstory\",\"bugthantask\",\"seriousBugs\"],\"reportType\":[\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\"],\"sumAppend\":[\"stories\",\"doneStory\",\"number\",\"doneTask\",\"bugs\",\"resolutions\",\"bugthanstory\",\"bugthantask\",\"seriousBugs\"]}', 2,      '{\"zh-cn\":\"\\u5217\\u51fa\\u9879\\u76ee\\u7684\\u9700\\u6c42\\u603b\\u6570\\uff0c\\u5b8c\\u6210\\u9700\\u6c42\\u6570\\uff0c\\u4efb\\u52a1\\u603b\\u6570\\uff0c\\u5b8c\\u6210\\u7684\\u4efb\\u52a1\\u6570\\uff0cBug\\u6570\\uff0c\\u89e3\\u51b3\\u7684Bug\\u6570\\uff0cBug\\/\\u9700\\u6c42\\uff0cBug\\/\\u4efb\\u52a1\\uff0c\\u91cd\\u8981Bug\\u6570\\u91cf(\\u4e25\\u91cd\\u7a0b\\u5ea6\\u4e0d\\u5927\\u4e8e3\\uff09\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}', 'admin',        '2015-07-23 17:03:10'),
('productbug-type',      '{\"zh-cn\":\"\\u4ea7\\u54c1Bug\\u7c7b\\u578b\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\\u7522\\u54c1Bug\\u985e\\u578b\\u7d71\\u8a08\\u8868\",\"en\":\"Bug Type of Product\"}',       ',product,test',        'select t1.id,t1.name,t2.id as bugID,t2.type from TABLE_PRODUCT as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.product \r\nleft join TABLE_PROGRAM as t3 on t1.program=t3.id \r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\'\r\norder by t3.`order` asc, t1.line desc, t1.`order` asc',        '',     '{\"count\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"done\":{\"zh-cn\":\"\\u5b8c\\u6210\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u6570\",\"en\":\"Done\"}}',  '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"type\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',     2,      '{\"zh-cn\":\"\\u6309\\u7167\\u4ea7\\u54c1\\u7edf\\u8ba1Bug\\u7684\\u7c7b\\u578b\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\\u6309\\u7167\\u7522\\u54c1\\u7d71\\u8a08Bug\\u7684\\u985e\\u578b\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"en\":\"Type distribution of Bugs.\"}',      'admin',        '2015-07-24 13:48:22'),
('product-quality',      '{\"zh-cn\":\"\\u4ea7\\u54c1\\u8d28\\u91cf\\u8868\",\"zh-tw\":\"\\u7522\\u54c1\\u8cea\\u91cf\\u8868\",\"en\":\"Product Quality\"}',     ',product',     'select t1.id,t1.name,t2.stories,(t2.stories-t2.undone) as doneStory,t3.bugs,t3.resolutions,round(t3.bugs/(t2.stories-t2.undone),2) as bugthanstory,t3.seriousBugs from TABLE_PRODUCT as t1 \r\nleft join ztv_productstories as t2 on t1.id=t2.product \r\nleft join ztv_productbugs as t3 on t1.id=t3.product \r\nleft join TABLE_PROGRAM as t4 on t1.program=t4.id \r\nwhere t1.deleted=\'0\'\r\norder by t4.`order` asc, t1.line desc, t1.`order` asc',      '',     '{\"stories\":{\"zh-cn\":\"\\u9700\\u6c42\\u603b\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u603b\\u6570\",\"en\":\"Stories\"},\"doneStory\":{\"zh-cn\":\"\\u5b8c\\u6210\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u9700\\u6c42\\u6570\",\"en\":\"Finished Stories\"},\"bugs\":{\"zh-cn\":\"Bug\\u6570\",\"zh-tw\":\"Bug\\u6570\",\"en\":\"Bugs\"},\"resolutions\":{\"zh-cn\":\"\\u89e3\\u51b3Bug\\u6570\",\"zh-tw\":\"\\u89e3\\u51b3Bug\\u6570\",\"en\":\"Solved Bugs\"},\"bugthanstory\":{\"zh-cn\":\"Bug\\/\\u5b8c\\u6210\\u9700\\u6c42\",\"zh-tw\":\"Bug\\/\\u5b8c\\u6210\\u9700\\u6c42\",\"en\":\"Bug\\/Finished Story\"},\"seriousBugs\":{\"zh-cn\":\"\\u91cd\\u8981Bug\\u6570\",\"zh-tw\":\"\\u91cd\\u8981Bug\\u6570\",\"en\":\"Serious Bugs\"},\"seriousBugsPercent\":{\"zh-cn\":\"\\u4e25\\u91cdbug\\u6bd4\\u7387\",\"zh-tw\":\"\\u4e25\\u91cdbug\\u6bd4\\u7387\",\"en\":\"Serious Bugs %\"}}',     '{\"group1\":\"name\",\"group2\":\"\",\"reportField\":[\"stories\",\"doneStory\",\"bugs\",\"resolutions\",\"bugthanstory\",\"seriousBugs\"],\"reportType\":[\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\"],\"sumAppend\":[\"stories\",\"doneStory\",\"bugs\",\"resolutions\",\"bugthanstory\",\"seriousBugs\"],\"percent\":{\"5\":\"1\"},\"contrast\":{\"5\":\"bugs\"},\"showAlone\":{\"5\":\"1\"}}', 2,      '{\"zh-cn\":\"\\u5217\\u51fa\\u4ea7\\u54c1\\u7684\\u9700\\u6c42\\u6570\\uff0c\\u5b8c\\u6210\\u7684\\u9700\\u6c42\\u603b\\u6570\\uff0cBug\\u6570\\uff0c\\u89e3\\u51b3\\u7684Bug\\u603b\\u6570\\uff0cBug\\/\\u9700\\u6c42\\uff0c\\u91cd\\u8981Bug\\u6570\\u91cf(\\u4e25\\u91cd\\u7a0b\\u5ea6\\u4e0d\\u5927\\u4e8e3)\\u3002\",\"zh-tw\":\"\\u5217\\u51fa\\u7522\\u54c1\\u7684\\u9700\\u6c42\\u6578\\uff0c\\u5b8c\\u6210\\u7684\\u9700\\u6c42\\u7e3d\\u6578\\uff0cBug\\u6578\\uff0c\\u89e3\\u51b3\\u7684Bug\\u7e3d\\u6578\\uff0cBug\\/\\u9700\\u6c42\\uff0c\\u91cd\\u8981Bug\\u6578\\u91cf\\uff08\\u56b4\\u91cd\\u7a0b\\u5ea6\\u4e0d\\u5927\\u65bc3\\uff09\\u3002\",\"en\":\"Serious Bug (severity is less than 3).\"}',    'admin',        '2015-07-23 17:17:40'),
('user-login',   '{\"zh-cn\":\"\\u5458\\u5de5\\u767b\\u5f55\\u6b21\\u6570\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\\u54e1\\u5de5\\u767b\\u9304\\u6b21\\u6578\\u7d71\\u8a08\\u8868\",\"en\":\"Login Times\"}', ',staff',       'select actor,LEFT(`date`,10) as `day` from TABLE_ACTION where `action`=\'login\' and if($startDate=\'\',1,`date`>=$startDate) and if($endDate=\'\',1,`date`<=$endDate) order by `date` asc, actor asc',        '{\"varName\":[\"startDate\",\"endDate\"],\"showName\":[\"\\u8d77\\u59cb\\u65f6\\u95f4\",\"\\u7ed3\\u675f\\u65f6\\u95f4\"],\"requestType\":[\"date\",\"date\"],\"selectList\":[\"user\",\"user\"],\"default\":[\"$MONTHBEGIN\",\"$MONTHEND\"]}',        '{\"count\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"done\":{\"zh-cn\":\"\\u5b8c\\u6210\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u6570\",\"en\":\"Done\"}}',  '{\"group1\":\"actor\",\"isUser\":{\"group1\":[\"1\"]},\"group2\":\"\",\"reportField\":[\"day\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"]}',     2,      '{\"zh-cn\":\"\\u5b9e\\u73b0\\u5458\\u5de5\\u767b\\u5f55\\u6b21\\u6570\\u7edf\\u8ba1\\u62a5\\u8868\\uff0c\\u6309\\u7167\\u5929\\u7edf\\u8ba1\\u6bcf\\u5929\\u6bcf\\u4e2a\\u4eba\\u7684\\u767b\\u5f55\\u6b21\\u6570\\uff0c\\u4ee5\\u53ca\\u603b\\u6570\\u3002\",\"zh-tw\":\"\\u5be6\\u73fe\\u54e1\\u5de5\\u767b\\u9304\\u6b21\\u6578\\u7d71\\u8a08\\u5831\\u8868\\uff0c\\u6309\\u5929\\u7d71\\u8a08\\u6bcf\\u5929\\u6bcf\\u500b\\u4eba\\u7684\\u767b\\u9304\\u6b21\\u6578\\uff0c\\u4ee5\\u53ca\\u7e3d\\u6578\\u3002 \",\"en\":\"The summary of user login times.\"}',    'admin',        '2015-07-24 14:28:11'),
('effort',       '{\"zh-cn\":\"\\u65e5\\u5fd7\\u6c47\\u603b\\u8868\",\"zh-tw\":\"\\u65e5\\u8a8c\\u5f59\\u7e3d\\u8868\",\"en\":\"Effort Summary\"}',      ',staff',       'select t1.account,t1.consumed,t1.`date`,if($dept=\'0\',0,t2.dept) as dept from TABLE_EFFORT as t1 left join TABLE_USER as t2 on t1.account=t2.account where t1.`deleted`=\'0\' and if($startDate=\'\',1,t1.`date`>=$startDate) and if($endDate=\'\',1,t1.`date`<=$endDate) having dept=$dept order by `date` asc',     '{\"varName\":[\"dept\",\"startDate\",\"endDate\"],\"showName\":[\"\\u90e8\\u95e8\",\"\\u8d77\\u59cb\\u65f6\\u95f4\",\"\\u7ed3\\u675f\\u65f6\\u95f4\"],\"requestType\":[\"select\",\"date\",\"date\"],\"selectList\":[\"dept\",\"user\",\"user\"],\"default\":[\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}',   '{\"date\":{\"zh-cn\":\"\\u65e5\\u671f\",\"zh-tw\":\"\\u65e5\\u671f\",\"en\":\"Date\"},\"consumed\":{\"zh-cn\":\"\\u6d88\\u8017\\u5de5\\u65f6\",\"zh-tw\":\"\\u6d88\\u8017\\u5de5\\u65f6\",\"en\":\"Cost\"}}',  '{\"group1\":\"account\",\"isUser\":{\"group1\":[\"1\"]},\"group2\":\"\",\"reportField\":[\"date\"],\"reportType\":[\"sum\"],\"sumAppend\":[\"consumed\"]}',    2,      '{\"zh-cn\":\"\\u67e5\\u770b\\u67d0\\u4e2a\\u65f6\\u95f4\\u6bb5\\u5185\\u7684\\u65e5\\u5fd7\\u60c5\\u51b5\\uff0c\\u53ef\\u4ee5\\u6309\\u7167\\u90e8\\u95e8\\u9009\\u62e9\\u3002\",\"zh-tw\":\" \\u67e5\\u770b\\u67d0\\u500b\\u6642\\u9593\\u6bb5\\u5167\\u7684\\u65e5\\u8a8c\\u60c5\\u51b5\\uff0c\\u53ef\\u4ee5\\u6309\\u7167\\u90e8\\u9580\\u9078\\u64c7\\u3002 \",\"en\":\"Effort summary of users.\"}',      'admin',        '2015-07-27 13:53:32'),
('company-dynamic',      '{\"zh-cn\":\"\\u516c\\u53f8\\u52a8\\u6001\\u6c47\\u603b\\u8868\",\"zh-tw\":\"\\u516c\\u53f8\\u52d5\\u614b\\u5f59\\u7e3d\\u8868\",\"en\":\"Company Dynamics\"}',        ',staff',       'select t1.day,t2.userlogin,t3.consumed,t4.storyopen,t5.storyclose,t6.taskopen,t7.taskfinish,t8.bugopen,t9.bugresolve,t1.actions from ztv_dayactions as t1 left join ztv_dayuserlogin as t2 on t1.day=t2.day left join ztv_dayeffort as t3 on t1.day=t3.date left join ztv_daystoryopen as t4 on t1.day=t4.day left join ztv_daystoryclose as t5 on t1.day=t5.day left join ztv_daytaskopen as t6 on t1.day=t6.day left join ztv_daytaskfinish as t7 on t1.day=t7.day left join ztv_daybugopen as t8 on t1.day=t8.day left join ztv_daybugresolve as t9 on t1.day=t9.day where if($startDate=\'\',1,t1.day>=$startDate) and if($endDate=\'\',1,t1.day<=$endDate)',      '{\"varName\":[\"startDate\",\"endDate\"],\"showName\":[\"\\u8d77\\u59cb\\u65f6\\u95f4\",\"\\u7ed3\\u675f\\u65f6\\u95f4\"],\"requestType\":[\"date\",\"date\"],\"selectList\":[\"user\",\"user\"],\"default\":[\"$MONTHBEGIN\",\"$MONTHEND\"]}',        '{\"day\":{\"zh-cn\":\"\\u65e5\\u671f\",\"zh-tw\":\"\\u65e5\\u671f\",\"en\":\"Date\"},\"userlogin\":{\"zh-cn\":\"\\u767b\\u5f55\\u6b21\\u6570\",\"zh-tw\":\"\\u767b\\u9304\\u6b21\\u6578\",\"en\":\"Login\"},\"consumed\":{\"zh-cn\":\"\\u65e5\\u5fd7\\u5de5\\u65f6\",\"zh-tw\":\"\\u65e5\\u8a8c\\u5de5\\u6642\",\"en\":\"Cost(h)\"},\"storyopen\":{\"zh-cn\":\"\\u65b0\\u589e\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u65b0\\u589e\\u9700\\u6c42\\u6578\",\"en\":\"Open Story\"},\"storyclose\":{\"zh-cn\":\"\\u5173\\u95ed\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u95dc\\u9589\\u9700\\u6c42\\u6578\",\"en\":\"Closed Story\"},\"taskopen\":{\"zh-cn\":\"\\u65b0\\u589e\\u4efb\\u52a1\\u6570\",\"zh-tw\":\"\\u65b0\\u589e\\u4efb\\u52d9\\u6578\",\"en\":\"Open Task\"},\"taskfinish\":{\"zh-cn\":\"\\u5b8c\\u6210\\u4efb\\u52a1\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u4efb\\u52d9\\u6578\",\"en\":\"Finished Task\"},\"bugopen\":{\"zh-cn\":\"\\u65b0\\u589eBug\\u6570\",\"zh-tw\":\"\\u65b0\\u589eBug\\u6578\",\"en\":\"Open Bug\"},\"bugresolve\":{\"zh-cn\":\"\\u89e3\\u51b3Bug\\u6570\",\"zh-tw\":\"\\u89e3\\u51b3Bug\\u6578\",\"en\":\"Resolved bug\"},\"actions\":{\"zh-cn\":\"\\u52a8\\u6001\\u6570\",\"zh-tw\":\"\\u52d5\\u614b\\u6578\",\"en\":\"Dynamics\"}}',   '{\"group1\":\"day\",\"isUser\":{\"group1\":[\"1\"]},\"group2\":\"\",\"reportField\":[\"userlogin\",\"consumed\",\"storyopen\",\"storyclose\",\"taskopen\",\"taskfinish\",\"bugopen\",\"bugresolve\",\"actions\"],\"reportType\":[\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\"],\"sumAppend\":[\"userlogin\",\"consumed\",\"storyopen\",\"storyclose\",\"taskopen\",\"taskfinish\",\"bugopen\",\"bugresolve\",\"actions\"]}',        2,      '{\"zh-cn\":\"\\u53ef\\u4ee5\\u6307\\u5b9a\\u4e00\\u4e2a\\u65f6\\u671f\\uff0c\\u5217\\u51fa\\u76f8\\u5e94\\u7684\\u6570\\u636e\\uff1a1. \\u6bcf\\u5929\\u7684\\u767b\\u5f55\\u6b21\\u6570\\u30022. \\u6bcf\\u5929\\u7684\\u65e5\\u5fd7\\u5de5\\u65f6\\u91cf\\u30023. \\u6bcf\\u5929\\u65b0\\u589e\\u7684\\u9700\\u6c42\\u6570\\u30024. \\u6bcf\\u5929\\u5173\\u95ed\\u7684\\u9700\\u6c42\\u6570\\u30025. \\u6bcf\\u5929\\u65b0\\u589e\\u7684\\u4efb\\u52a1\\u6570\\u30026. \\u6bcf\\u5929\\u5b8c\\u6210\\u7684\\u4efb\\u52a1\\u6570\\u30027. \\u6bcf\\u5929\\u65b0\\u589e\\u7684Bug\\u6570\\u30028. \\u6bcf\\u5929\\u89e3\\u51b3\\u7684Bug\\u6570\\u30029. \\u6bcf\\u5929\\u7684\\u52a8\\u6001\\u6570\\u3002\",\"zh-tw\":\"\\u53ef\\u4ee5\\u6307\\u5b9a\\u4e00\\u500b\\u6642\\u671f\\uff0c\\u5217\\u51fa\\u76f8\\u61c9\\u7684\\u6578\\u64da\\uff1a1.\\u6bcf\\u5929\\u7684\\u767b\\u9304\\u6b21\\u6578\\u30022.\\u6bcf\\u5929\\u7684\\u65e5\\u8a8c\\u5de5\\u6642\\u91cf\\u30023.\\u6bcf\\u5929\\u65b0\\u589e\\u7684\\u9700\\u6c42\\u6578\\u30024.\\u6bcf\\u5929\\u95dc\\u9589\\u7684\\u9700\\u6c42\\u6578\\u30025.\\u6bcf\\u5929\\u65b0\\u589e\\u7684\\u4efb\\u52d9\\u6578\\u30026.\\u6bcf\\u5929\\u5b8c\\u6210\\u7684\\u4efb\\u52d9\\u6578\\u30027.\\u6bcf\\u5929\\u65b0\\u589e\\u7684Bug\\u6578\\u30028.\\u6bcf\\u5929\\u89e3\\u51b3\\u7684Bug\\u6578\\u30029.\\u6bcf\\u5929\\u7684\\u52d5\\u614b\\u6578\\u3002\",\"en\":\"The summary of company dynamics\"}',        'admin',        '2015-07-27 15:09:42'),
('bug-resolve',  '{\"zh-cn\":\"Bug\\u89e3\\u51b3\\u8868\",\"zh-tw\":\"Bug\\u89e3\\u6c7a\\u8868\",\"en\":\"Solved Bugs\"}',       ',test',        'select *,if($product=\'\',0,product) as customproduct from TABLE_BUG where deleted=\'0\' and resolution!=\'\' and if($startDate=\'\',1,resolvedDate>=$startDate) and if($endDate=\'\',1,resolvedDate<=$endDate) having customproduct=$product',        '{\"varName\":[\"product\",\"startDate\",\"endDate\"],\"showName\":[\"\\u4ea7\\u54c1\",\"\\u89e3\\u51b3\\u65e5\\u671f\\u5f00\\u59cb\",\"\\u89e3\\u51b3\\u65e5\\u671f\\u7ed3\\u675f\"],\"requestType\":[\"select\",\"date\",\"date\"],\"selectList\":[\"product\",\"user\",\"user\"],\"default\":[\"\",\"$MONTHBEGIN\",\"$MONTHEND\"]}', '{\"count\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"done\":{\"zh-cn\":\"\\u5b8c\\u6210\\u6570\",\"zh-tw\":\"\\u5b8c\\u6210\\u6570\",\"en\":\"Done\"}}',  '{\"group1\":\"resolvedBy\",\"isUser\":{\"group1\":[\"1\"]},\"group2\":\"\",\"reportField\":[\"resolution\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"],\"reportTotal\":[\"1\"],\"percent\":[\"1\"],\"contrast\":[\"crystalTotal\"],\"showAlone\":[\"1\"]}',       2,      '{\"zh-cn\":\"\\u5217\\u51fa\\u89e3\\u51b3\\u7684Bug\\u603b\\u6570\\uff0c\\u89e3\\u51b3\\u65b9\\u6848\\u7684\\u5206\\u5e03\\uff0c\\u5360\\u7684\\u6bd4\\u4f8b\\uff08\\u8be5\\u7528\\u6237\\u89e3\\u51b3\\u7684Bug\\u7684\\u6570\\u91cf\\u5360\\u6240\\u6709\\u7684\\u89e3\\u51b3\\u7684Bug\\u7684\\u6570\\u91cf)\\u3002\",\"zh-tw\":\"\\u5217\\u51fa\\u89e3\\u51b3\\u7684Bug\\u7e3d\\u6578\\uff0c\\u89e3\\u6c7a\\u65b9\\u6848\\u7684\\u5206\\u4f48\\uff0c\\u5360\\u7684\\u6bd4\\u4f8b\\uff08\\u8a72\\u7528\\u6236\\u89e3\\u51b3\\u7684Bug\\u7684\\u6578\\u91cf\\u5360\\u6240\\u6709\\u7684\\u89e3\\u51b3\\u7684Bug\\u7684\\u6578\\u91cf\\uff09\\u3002\",\"en\":\"percentage:self resolved / all resolved\"}',   'admin',        '2015-07-24 13:44:25'),
('project-progress',     '{\"zh-cn\":\"\\u9879\\u76ee\\u8fdb\\u5c55\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Progress Report\",\"de\":\"Project Progress Report\",\"fr\":\"Project Progress Report\",\"vi\":\"Project Progress Report\",\"ja\":\"Project Progress Report\"}',       ',project',     'select t1.id,t4.name as project,t4.id,t1.name as execution,t1.status,t2.number as tasks,round(t2.consumed,2) as consumed,round(t2.`left`,2) as `left`,t3.stories,t2.undone as undoneTask,t3.undone as undoneStory,t2.totalReal from TABLE_EXECUTION as t1 \r\nleft join ztv_executionsummary as t2 on t1.id=t2.execution\r\nleft join ztv_projectstories as t3 on t1.id=t3.execution\r\nleft join TABLE_PROJECT as t4 on t4.id=t1.project\r\nwhere t1.deleted=\'0\' and t1.type in (\'sprint\',\'stage\') and if($project=\'\',1,t4.id=$project) and if($execution=\'\',1,t1.id=$execution) and if($status=\'\',1,t1.status=$status)', '{\"varName\":[\"project\",\"execution\",\"status\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\",\"\\u6267\\u884c\\u72b6\\u6001\"],\"requestType\":[\"select\",\"select\",\"select\"],\"selectList\":[\"project\",\"execution\",\"project.status\"],\"default\":[\"\",\"\",\"\"]}', '{\"stories\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"tasks\":{\"zh-cn\":\"\\u4efb\\u52a1\\u6570\",\"zh-tw\":\"\\u4efb\\u52a1\\u6570\",\"en\":\"Tasks\"},\"undoneStory\":{\"zh-cn\":\"\\u5269\\u4f59\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u5269\\u4f59\\u9700\\u6c42\\u6570\",\"en\":\"Undone Story\"},\"undoneTask\":{\"zh-cn\":\"\\u5269\\u4f59\\u4efb\\u52a1\\u6570\",\"zh-tw\":\"\\u5269\\u4f59\\u4efb\\u52a1\\u6570\",\"en\":\"Undone Task\"},\"consumed\":{\"zh-cn\":\"\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\",\"zh-tw\":\"\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\",\"en\":\"Cost(h)\"},\"left\":{\"zh-cn\":\"\\u5269\\u4f59\\u5de5\\u65f6\",\"zh-tw\":\"\\u5269\\u4f59\\u5de5\\u65f6\",\"en\":\"Left(h)\"},\"consumedPercent\":{\"zh-cn\":\"\\u8fdb\\u5ea6\",\"zh-tw\":\"\\u8fdb\\u5ea6\",\"en\":\"Process\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',    '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"stories\",\"undoneStory\",\"tasks\",\"undoneTask\",\"left\",\"consumed\"],\"reportType\":[\"sum\",\"sum\",\"sum\",\"sum\",\"sum\",\"sum\"],\"sumAppend\":[\"stories\",\"undoneStory\",\"tasks\",\"undoneTask\",\"left\",\"consumed\"],\"percent\":{\"5\":\"1\"},\"contrast\":{\"5\":\"totalReal\"},\"showAlone\":{\"5\":\"1\"}}',  2,      '{\"zh-cn\":\"\\u9879\\u76ee\\u7684\\u9700\\u6c42\\u6570\\uff0c\\u4efb\\u52a1\\u6570\\uff0c\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\\uff0c\\u5269\\u4f59\\u5de5\\u65f6\\uff0c\\u5269\\u4f59\\u9700\\u6c42\\u6570\\uff0c\\u5269\\u4f59\\u4efb\\u52a1\\u6570\\uff0c\\u8fdb\\u5ea6\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',       'admin',        '2015-07-23 14:03:06'),
('projectbug-type',      '{\"zh-cn\":\"\\u9879\\u76eeBug\\u7c7b\\u578b\\u7edf\\u8ba1\\u8868\",\"zh-tw\":\"\",\"en\":\"Project Bug Type\",\"de\":\"Project Bug Type\",\"fr\":\"Project Bug Type\",\"vi\":\"Project Bug Type\",\"ja\":\"Project Bug Type\"}',      ',project,test',        'select t1.id,t3.name as project,t3.id,t1.name as execution,t2.id as bugID,t2.type from TABLE_EXECUTION as t1 \r\nleft join TABLE_BUG as t2 on t1.id=t2.execution\r\nleft join TABLE_PROJECT as t3 on t3.id=t1.project\r\nwhere t1.deleted=\'0\' and t2.deleted=\'0\' and if($project=\'\',1,t3.id=$project) and if($execution=\'\',1,t1.id=$execution)',       '{\"varName\":[\"project\",\"execution\"],\"showName\":[\"\\u9879\\u76ee\\u5217\\u8868\",\"\\u6267\\u884c\\u5217\\u8868\"],\"requestType\":[\"select\",\"select\"],\"selectList\":[\"project\",\"execution\"],\"default\":[\"\",\"\"]}',        '{\"stories\":{\"zh-cn\":\"\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u9700\\u6c42\\u6570\",\"en\":\"Stories\"},\"tasks\":{\"zh-cn\":\"\\u4efb\\u52a1\\u6570\",\"zh-tw\":\"\\u4efb\\u52a1\\u6570\",\"en\":\"Tasks\"},\"undoneStory\":{\"zh-cn\":\"\\u5269\\u4f59\\u9700\\u6c42\\u6570\",\"zh-tw\":\"\\u5269\\u4f59\\u9700\\u6c42\\u6570\",\"en\":\"Undone Story\"},\"undoneTask\":{\"zh-cn\":\"\\u5269\\u4f59\\u4efb\\u52a1\\u6570\",\"zh-tw\":\"\\u5269\\u4f59\\u4efb\\u52a1\\u6570\",\"en\":\"Undone Task\"},\"consumed\":{\"zh-cn\":\"\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\",\"zh-tw\":\"\\u5df2\\u6d88\\u8017\\u5de5\\u65f6\",\"en\":\"Cost(h)\"},\"left\":{\"zh-cn\":\"\\u5269\\u4f59\\u5de5\\u65f6\",\"zh-tw\":\"\\u5269\\u4f59\\u5de5\\u65f6\",\"en\":\"Left(h)\"},\"consumedPercent\":{\"zh-cn\":\"\\u8fdb\\u5ea6\",\"zh-tw\":\"\\u8fdb\\u5ea6\",\"en\":\"Process\"},\"execution\":{\"zh-cn\":\"\\u6267\\u884c\\u540d\\u79f0\"}}',    '{\"group1\":\"project\",\"group2\":\"execution\",\"reportField\":[\"type\"],\"reportType\":[\"count\"],\"sumAppend\":[\"\"]}', 2,      '{\"zh-cn\":\"\\u6309\\u7167\\u9879\\u76ee\\u7edf\\u8ba1Bug\\u7684\\u7c7b\\u578b\\u5206\\u5e03\\u60c5\\u51b5\\u3002\",\"zh-tw\":\"\",\"en\":\"\",\"de\":\"\",\"fr\":\"\",\"vi\":\"\",\"ja\":\"\"}',     'admin',        '2015-08-04 13:54:22');
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'report', 'export'),
(4, 'report', 'export'),
(5, 'report', 'export'),
(6, 'report', 'export'),
(7, 'report', 'export'),
(8, 'report', 'export'),
(9, 'report', 'export');