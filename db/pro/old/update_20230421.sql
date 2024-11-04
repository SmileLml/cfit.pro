ALTER TABLE `zt_application`
    ADD `isSyncQz` varchar(8) DEFAULT NULL COMMENT '是否同步清总';

// 需求收集新增所属模块字段
ALTER TABLE zt_demandcollection ADD `belongModel` varchar(255) DEFAULT '' COMMENT '所属模块';
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'project', 'setWhiteList', '0', '', '1');

//新建部门工单表
CREATE TABLE `zt_deptorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT '单号',
  `summary` varchar(255) NOT NULL COMMENT '摘要',
  `type` varchar(20) NOT NULL COMMENT '类型',
  `subtype` varchar(20) NOT NULL COMMENT '子类型',
  `source` varchar(20) NOT NULL COMMENT '来源方式',
  `team` varchar(255) NOT NULL COMMENT '任务发起人',
  `union` varchar(50) DEFAULT NULL COMMENT '任务发起方',
  `exceptDoneDate` date DEFAULT NULL COMMENT '期望完成日期',
  `dealUser` varchar(50) NOT NULL COMMENT '下节点处理人',
  `ccList` varchar(255) DEFAULT NULL COMMENT '编辑抄送人',
  `acceptUser` varchar(50) DEFAULT NULL COMMENT '受理人',
  `acceptDept` mediumint(9) DEFAULT NULL COMMENT '受理部门',
  `createdDept` mediumint(9) NOT NULL COMMENT '发起部门',
  `status` varchar(20) NOT NULL COMMENT '流程状态',
  `app` varchar(255) DEFAULT NULL COMMENT '应用系统',
  `closeReason` mediumtext COMMENT '关闭原因',
  `ifAccept` char(5) DEFAULT NULL COMMENT '是否受理',
  `progress` mediumtext COMMENT '当前进展',
  `completeStatus` char(5) DEFAULT NULL COMMENT '完成情况',
  `createdBy` char(30) NOT NULL COMMENT '由谁创建',
  `createdDate` datetime NOT NULL COMMENT '创建日期',
  `closedBy` char(30) DEFAULT NULL COMMENT '由谁关闭',
  `closedDate` datetime DEFAULT NULL COMMENT '关闭时间',
  `editedBy` char(30) DEFAULT NULL COMMENT '由谁编辑',
  `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
  `startDate` date DEFAULT NULL COMMENT '实际开始',
  `overDate` date DEFAULT NULL COMMENT '实际结束',
  `planstartDate` date DEFAULT NULL COMMENT '计划开始',
  `planoverDate` date DEFAULT NULL COMMENT '计划结束',
  `deleted` varchar(5) DEFAULT '0',
  `desc` text COMMENT '详细描述',
  `consultRes` mediumtext COMMENT '咨询评估结果',
  `testRes` mediumtext COMMENT '测试验证结果',
  `dealRes` mediumtext COMMENT '处理结果',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE zt_demandcollection ADD `belongPlatform` varchar(255) DEFAULT '' COMMENT '所属平台';

ALTER TABLE `zt_component` ADD COLUMN `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联组件id';
ALTER TABLE `zt_component` ADD COLUMN `isattach` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否纳入现有组件 0否,1是';
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','delaystopped','incorporate') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','delaystopped','incorporate') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `reviewer`;

ALTER TABLE `zt_component` ADD COLUMN `gitlab` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'gitlab库';
ALTER TABLE `zt_component_release` ADD COLUMN `gitlab` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'gitlab库';

ALTER TABLE `zt_release`
     ADD COLUMN `alreadyMergeCode` enum('0','1','2') NOT NULL DEFAULT '0'  COMMENT '已合并代码,0:'',1:是,2:否',
     ADD COLUMN `alreadyBaseLine` enum('0','1','2') NOT NULL DEFAULT '0'  COMMENT '已合并代码,0:'',1:是,2:否';

ALTER TABLE `zt_release` ADD `syncStateTimes` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '二线状态联动同步次数';
