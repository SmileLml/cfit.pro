INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','svnList','host','10.128.48.72','1','');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','svnList','port','22','1','');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','svnList','username','monitor','1','');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','svnList','password','1qaz!QAZ','1','');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','svnList','shellfiledirectory','','1','');

-- ----------------------------
-- jenkins配置信息 shixuyang 2024-09-03
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','jenkinsList','host','http://111.1.11.9:8080?username=admin&password=11dc9b5fdabcc88966530d9cb94c073c50','1','');


-- ----------------------------
-- gitlab配置信息 shixuyang 2024-09-03
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','gitlabList','host','10.128.33.57','1','');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','gitlabList','port','9999','1','');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `extendInfo`) VALUES ('zh-cn','api','gitlabList','password','zFTsXR552NoF9aDcSjSb','1','');


-- ----------------------------
-- 迭代36 第三方权限-路径列表表  shixuyang
-- ----------------------------
CREATE TABLE `zt_thirdparty_privilege_url` (
                                           `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                           `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '类型：gitlab、svn、jenkins',
                                           `role` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci  NULL DEFAULT '' COMMENT '角色名称',
                                           `permsission` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '权限',
                                           `projectOrRepository` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所属项目或仓库',
                                           `deleted` enum('0','1') NOT NULL DEFAULT '0',
                                           `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
                                           `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP  COMMENT '更新时间',
                                           PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='第三方权限路径表';

-- ----------------------------
-- 迭代36 数据权限配置表  wangyongliang
-- ----------------------------
CREATE TABLE `zt_data_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `realname` char(80) NOT NULL DEFAULT '' COMMENT '用户名',
  `account` char(30) NOT NULL DEFAULT '' COMMENT '账号',
  `deptName` varchar(100) NOT NULL DEFAULT '' COMMENT '部门',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '类型：gitlab、svn、jenkins、dpmp',
  `typeName` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '类型名称',
  `createdBy` char(30) NOT NULL DEFAULT '' COMMENT '创建人',
  `deletedBy` char(30) NOT NULL DEFAULT '' COMMENT '删除人',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='数据权限配置表';
-- ----------------------------
-- 迭代36 权限申请表  yanzhiyuan
-- ----------------------------
CREATE TABLE `zt_authorityapply` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
                                     `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '编号',
                                     `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '摘要',
                                     `createdBy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '申请人',
                                     `applyDepartment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '申请部门',
                                     `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态',
                                     `dealUser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '处理人',
                                     `processInstanceId` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '审批流id',
                                     `version` tinyint(4) DEFAULT '1' COMMENT '版本',
                                     `approvalDepartment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '审批部门',
                                     `project` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '项目名称',
                                     `application` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '应用系统',
                                     `product` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '产品名称',
                                     `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '申请权限内容',
                                     `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '申请原因',
                                     `createdTime` datetime DEFAULT NULL COMMENT '创建时间',
                                     `deleteTime` datetime DEFAULT NULL COMMENT '删除时间',
                                     `thisDeptLeader` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '申请部门负责人',
                                     `thatDeptLeader` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '其他部门负责人',
                                     `thisDeptChargeLeader` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '分管领导',
                                     `cm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CM管理员',
                                     `realPermission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '权限实际分配',
                                     `noticeList` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '顶部提示信息',
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限申请';

INSERT INTO zt_lang(lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'noticeList', '1', '1、正式/外协员工申请本部门权限时，需经本部门负责人审批通过后由对应CM管理员进行开通。', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES('zh-cn', 'authorityapply', 'noticeList', '2', '2、正式/外协员工申请其他部门权限时，需经本部门负责人审批、其他们部门负责人审批通过后，由对应CM管理员进行开通。', '0', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'noticeList', '3', '3、若开通权限人员包含【实习生/厂商/外单位】时，除满足以上条件外还需分管领导审批通过后，由对应CM管理员进行开通。', '0', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'gitLabPermission', '0', '无权限', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'gitLabPermission', '10', 'Guest', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'gitLabPermission', '20', 'Reporter', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'gitLabPermission', '30', 'Developer', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'gitLabPermission', '40', 'Maintainer', '1', 0, '');
INSERT INTO zt_lang(lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'gitLabPermission', '50', 'Owner', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'projectAlert', '1', '请选择所属项目，二线相关请选择对应的二线空间（如：RD1_二线管理）', '0', 0, '');
INSERT INTO zt_lang(lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'subSystemList', 'gitlab', '代码库', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'subSystemList', 'jenkins', '流水线', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'subSystemList', 'svn', '文档库', '1', 0, '');
INSERT INTO zt_lang( lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES( 'zh-cn', 'authorityapply', 'subSystemList', 'dpmp', '研发过程', '0', 0, '');



-- ----------------------------
-- 安全测试性能测试 2024-8-21 wangjiurong
-- ----------------------------
ALTER TABLE zt_review ADD isSafetyTest TINYINT(1) DEFAULT 1 NOT NULL COMMENT '是否安全测试  1-默认 2-是 3-否';
ALTER TABLE zt_review ADD isPerformanceTest TINYINT(1) DEFAULT 1 NULL COMMENT '是否需要性能测试 1-默认 2-是 3-否';

ALTER TABLE zt_project ADD isSafetyTest TINYINT(1) DEFAULT 1 NOT NULL COMMENT '是否安全测试  1-默认 2-是 3-否';
ALTER TABLE zt_project ADD isPerformanceTest TINYINT(1) DEFAULT 1 NULL COMMENT '是否需要性能测试 1-默认 2-是 3-否';

CREATE TABLE `zt_qualitygate` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '编号',
  `projectId` int(11) NOT NULL DEFAULT '0' COMMENT '项目ID',
  `productId` int(11) NOT NULL DEFAULT '0' COMMENT '产品ID',
  `productCode` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品编号',
  `productVersion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '产品版本',
  `buildId` int(11) NOT NULL DEFAULT '0' COMMENT '制版Id',
  `severityTestUser` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '安全测试工程师',
  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  `dealUser` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '待处理人',
  `workflowId` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '工作流id',
  `deleted` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '是否有效 0-有效 1-无效',
  `createdBy` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '创建人',
  `createdDept` int(11) NOT NULL DEFAULT '0' COMMENT '创建人部门Id',
  `createdTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编辑人',
  `editedtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  `updateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '默认更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `zt_qualitygate_project_product_version_build` (`projectId`,`productId`,`productVersion`,`buildId`,`deleted`) USING BTREE,
  KEY `zt_qualitygate_buildId` (`buildId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='质量门禁';

-- ----------------------------
-- 质量门禁允许部门 2024-8-21 wangjiurong
-- ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES('system', 'qualitygate', '', 'allowQualityGateDeptIds', '6,7,10');

-- ----------------------------
-- wangjiurong 质量门禁添加工作流
-- ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES('system', 'common', 'global', 'qualitygateId', '');
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES('system', 'common', 'global', 'qualitygateKey', 'QUALITGATE');

-- wangjiurong 安全门禁 邮件模板
-- ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES('system', 'common', 'global', 'setQualitygateMail', '{"mailTitle":"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406","variables":["\\u8d28\\u91cf\\u95e8\\u7981"],"mailContent":"<span style=\\"font-weight:700;\\">\\u8bf7\\u8fdb\\u5165\\u3010\\u9879\\u76ee\\u7ba1\\u7406\\u3011<\\/span><span><span style=\\"font-weight:700;\\">-<\\/span><\\/span><span style=\\"font-weight:700;\\">\\u3010\\u6d4b\\u8bd5\\u3011<span style=\\"font-weight:700;\\"><\\/span><span><span style=\\"font-weight:700;\\">-<\\/span><\\/span><span style=\\"font-weight:700;\\">\\u3010\\u8d28\\u91cf\\u95e8\\u7981\\u3011<\\/span><span style=\\"font-weight:700;\\"><\\/span>\\u67e5\\u770b<\\/span><span><span style=\\"font-weight:700;\\"><\\/span><\\/span><span style=\\"font-weight:700;\\">\\uff0c\\u6458\\u8981\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/span>"}');
-- ----------------------------
-- wangjiurong 制版添加安全测试接口人 邮件模板
-- ----------------------------
ALTER TABLE zt_build ADD severityTestUser varchar(50)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''  COMMENT '安全测试接口人' AFTER testUser;
ALTER TABLE zt_build ADD specialPassReason TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '特批制版原因'  AFTER severityTestUser;
-- ----------------------------
-- wangjiurong 制版特批制版通过bug快照
-- ----------------------------

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setAuthorityapplyMail', '{\"mailTitle\":\"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406\",\"variables\":[\"\\u6743\\u9650\\u7533\\u8bf7\"],\"mailContent\":\"<span style=\\\"font-weight:700;\\\">\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011-\\u3010\\u5f85\\u5904\\u7406\\u3011-\\u3010\\u5ba1\\u6279\\u3011-\\u3010\\u6743\\u9650\\u7533\\u8bf7\\u3011\\u5904\\u7406\\u5ba1\\u6279\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/span>\"}');
CREATE TABLE `zt_build_bug_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buildId` int(11) NOT NULL DEFAULT '0',
  `bugId` mediumint(9) NOT NULL DEFAULT '0' COMMENT 'bug的id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `severity` tinyint(4) NOT NULL DEFAULT '0' COMMENT '严重性',
  `pri` tinyint(3) unsigned NOT NULL COMMENT '优先级',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类',
  `childType` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '二级分类',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'bug状态',
  `openedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'bug创建人',
  `openedDate` datetime NOT NULL COMMENT 'bug创建时间',
  `assignedTo` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '指派给',
  `resolution` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '解决方案',
  `isBlackList` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否黑名单 1-否 2-是',
  `createdBy` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '创建人',
  `createdTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `buildId` (`buildId`),
  KEY `bugId` (`bugId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ------------------------------ ----------------------------
-- 需求条目超期考核信息查看设置用户可见 wangjiurong
-- ------------------------------ ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES('system', 'requirement', '', 'overDateInfoVisible', 'maqingyuan,wanglijiao');
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES ('system', 'demand', '', 'overDateInfoVisible', 'wanglijiao');
-- ------------------------------ ----------------------------
-- 项目评审添加未解决问题提出人 wangjiurong  2024-10-10
-- ------------------------------ ----------------------------
ALTER TABLE zt_review ADD unDealIssueRaiseByUsers TEXT NOT NULL COMMENT '未处理的问题的提出人';
-- ------------------------------ ----------------------------
-- 项目评审添加未解决问题提出人邮件模板 wangjiurong  2024-10-10
-- ------------------------------ ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value) VALUES ('system', 'common', 'global', 'setReviewproblemMail', '{"mailTitle":"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u6709\\u95ee\\u9898\\u5f85\\u9a8c\\u8bc1\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406","variables":[" \\u9879\\u76ee\\u8bc4\\u5ba1"],"mailContent":"<p><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u6211\\u7684\\u8bc4\\u5ba1\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u8bc4\\u5ba1\\u95ee\\u9898\\u3011<\\/strong><span><strong><\\/strong><\\/span><strong>\\u6216\\u3010\\u8bc4\\u5ba1\\u7ba1\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u95ee\\u9898\\u5217\\u8868\\u3011<strong>\\u6216\\u3010\\u9879\\u76ee\\u7ba1\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u8bc4\\u5ba1\\u3011<strong><strong><\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u95ee\\u9898\\u5217\\u8868\\u3011<\\/strong><\\/strong><\\/strong>\\u5904\\u7406<span style=\\"color:#E53333;\\">\\u8bc4\\u5ba1\\u95ee\\u9898<\\/span><\\/strong><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<span style=\\"color:#E53333;\\"><br \\/><\\/span><\\/strong><\\/p>\\n<p><strong><span style=\\"color:#E53333;\\">\\u3010\\u91d1\\u79d1\\u5916\\u90e8\\u4e13\\u5bb6\\u8bbf\\u95ee\\u5730\\u5740\\uff1ahttp:\\/\\/dpmp.cfit.cn\\/user-login-Lw==.html\\u3011<\\/span><\\/strong><\\/p>"}');
