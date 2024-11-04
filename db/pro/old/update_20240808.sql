set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 厂商代码sql 2024-5-29 jinzhuliang
-- ----------------------------
CREATE TABLE `zt_report_history_bug` (
                                         `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                         `countType` varchar(100) DEFAULT NULL COMMENT '统计的类型，比如activated、closed、totalActivated',
                                         `time` datetime NOT NULL COMMENT '本条记录统计时间',
                                         `timeKey` varchar(100) DEFAULT NULL COMMENT '本条记录的日期标志，比如Y-m-d的格式',
                                         `countTime` datetime DEFAULT NULL COMMENT '统计的哪天的日期',
                                         `project` varchar(100) DEFAULT NULL,
                                         `testtask` varchar(100) DEFAULT NULL,
                                         `countValue` varchar(100) DEFAULT NULL COMMENT '统计的数值',
                                         PRIMARY KEY (`id`),
                                         KEY `zt_report_history_bug_countTime_IDX` (`countTime`) USING BTREE,
                                         KEY `zt_report_history_bug_project_IDX` (`project`) USING BTREE,
                                         KEY `zt_report_history_bug_testtask_IDX` (`testtask`) USING BTREE,
                                         KEY `zt_report_history_bug_countType_IDX` (`countType`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='bug统计历史';

CREATE TABLE `zt_report_task_bug` (
                                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                      `timeKey` varchar(100) DEFAULT NULL COMMENT '本条记录的日期标志，比如Y-m-d的格式',
                                      `project` bigint(20) unsigned NOT NULL,
                                      `status` varchar(10) NOT NULL DEFAULT 'wait' COMMENT 'wait:等待执行 done:执行成功',
                                      PRIMARY KEY (`id`),
                                      KEY `zt_report_task_bug_timeKey_IDX` (`timeKey`) USING BTREE,
                                      KEY `zt_report_task_bug_project_IDX` (`project`) USING BTREE,
                                      KEY `zt_report_task_bug_status_IDX` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='bug统计执行计划';

ALTER TABLE zt_bug ADD linkPlan varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NULL COMMENT '所属产品版本,逗号分隔';

-- 迭代三十五 现场支持  wangjiurong

CREATE TABLE `zt_localesupport` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '单号',
  `startDate` datetime NOT NULL COMMENT '开始日期',
  `endDate` datetime NOT NULL COMMENT '结束日期',
  `area` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支持地点',
  `appIds` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '系统名称',
  `stype` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支持属性',
  `deptIds` varchar(30) NOT NULL DEFAULT '0' COMMENT '支持部门多选',
  `owndept` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '承建单位',
  `reason` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支持事由',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
  `version` tinyint(4) NOT NULL DEFAULT '1' COMMENT '版本',
  `deptManagers` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '部门负责人',
  `deptManagersGroup` text NOT NULL COMMENT '部门负责人按照部门分组存储',
  `supportUsers` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支持人员',
  `isUserSelfReportWork` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许人员自己填报工时 1-是 2-否',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  `work` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现场支持总工作量（人天）',
  `mailto` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '通知人',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否有效 0-有效 1-删除',
  `sj` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '业务司局',
  `jxdepart` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '成方金信部门',
  `sysper` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '运维人员',
  `secper` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '二线专员',
  `manufacturer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '厂商人员',
  `dealUsers` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '待处理人',
  `workflowId` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '工作流标识',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '创建人',
  `createdDept` int(11) NOT NULL DEFAULT '0' COMMENT '创建人部门id',
  `createdTime` datetime NOT NULL COMMENT '创建时间',
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编辑人',
  `editedtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '默认更新时间',
  `isOld` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否旧的现场支持数据 1-否 2-是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='现场支持表';

CREATE TABLE `zt_localesupport_workreport` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supportId` int(11) unsigned NOT NULL COMMENT '现场支持id',
  `supportDate` date NOT NULL COMMENT '现场支持日期',
  `deptId` int(10) NOT NULL DEFAULT '0' COMMENT '用户部门id',
  `supportUser` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户',
  `consumed` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '工时',
  `syncStatus` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否同步到正式报工 1-未同步 2已同步',
  `execution` int(11) NOT NULL DEFAULT '0' COMMENT '任务阶段id',
  `taskId` int(11) NOT NULL DEFAULT '0' COMMENT '任务id',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否有效 0-有效 1-删除',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '创建人',
  `createdDate` datetime NOT NULL COMMENT '创建时间',
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编辑人',
  `editedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '默认更新时间',
   PRIMARY KEY (`id`),
   KEY `supportId` (`supportId`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='现场支持报工表';
-- ----------------------------
-- 迭代35 现场支持更新报工涉及表comment wangyongliang
-- ----------------------------
alter table zt_task modify taskType mediumint(9) comment '任务类型（0：默认 1：制版 2：现场支持）';
alter table zt_effort modify source mediumint(9) comment '数据来源（0：用户创建 1：制版创建 2：现场支持）';
alter table zt_effort modify buildID mediumint(9) comment '制版id 默认0 source是2，则存现场支持id';
alter table `zt_project` ADD COLUMN `isLocaleSupport` int(4) DEFAULT '1' COMMENT '是否现场支持数据 1：否 2：是';

-- ----------------------------
-- 迭代35 现场支持工作流 wangjiurong
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'localesupportKey', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'localesupportId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'localesupportTempId', '');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES('zh-cn', 'localesupport', 'areaList', '', '', '1', 0, '');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES('zh-cn', 'localesupport', 'areaList', '1', '成方金信', '0', 0, '');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'areaList', '5', '业务司局', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'areaList', '4', '其他', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '', '', '1', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '1', '问题排查', '0', 0, '');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '2', '二线应用变更', '0', 0, '');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '3', '二线系统变更', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '4', '项目应用变更', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '5', '应急演练', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '6', '现场巡检', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '7', '数据处理', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES('zh-cn', 'localesupport', 'stypeList', '8', '重保支持', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '9', '新系统投产', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES('zh-cn', 'localesupport', 'stypeList', '10', '外部借调', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '11', '测试支持', '0', 0, '');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'localesupport', 'stypeList', '12', '其他支持', '0', 0, '');

-- ----------------------------
-- 迭代35 任务类型 wangyongliang
-- ----------------------------
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'task', 'typeList', '12', '现场支持', '0', 0, '');

-- 记录邮件发送日志 songdi
CREATE TABLE `zt_mail_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext COMMENT '邮件标题',
  `content` text COMMENT '邮件内容',
  `action` varchar(50) DEFAULT NULL COMMENT '动作名称',
  `objectType` varchar(30) DEFAULT NULL COMMENT '模块类型 ',
  `objectId` int(11) DEFAULT NULL COMMENT '单子id',
  `createdBy` varchar(30) DEFAULT NULL COMMENT '发件人',
  `createdDate` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updatedDate` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `toList` varchar(255) DEFAULT NULL COMMENT '收件人',
  `ccList` varchar(255) DEFAULT NULL COMMENT '抄送人',
  `status` enum('1','2') DEFAULT '1' COMMENT '1 成功 2 失败',
  `error` text COMMENT '错误信息',
  `emails` text COMMENT '用户信息',
  `deleted` enum('0','1') DEFAULT '0' COMMENT '1 已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pcurl', 'http://dpmp.cfit.cn');
