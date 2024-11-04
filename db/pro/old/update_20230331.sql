ALTER TABLE `zt_projectplan`
    ADD COLUMN `problem` varchar(255) COMMENT '关联问题' AFTER `demand`;
ALTER TABLE `zt_problem`
    ADD COLUMN `ifOverDateInside`  varchar(10) DEFAULT 0 COMMENT '内部反馈是否超时' AFTER `ifOverDate`;
ALTER TABLE `zt_requirement`
     ADD COLUMN `isImprovementServices` enum('0','1') NOT NULL DEFAULT '0'  COMMENT '是否纳入MA需求完善服务,0:否,1:是' after `nextDealuser`,
     ADD COLUMN `estimateWorkload` varchar(100)  COMMENT '预计工作量' after `isImprovementServices`;
CREATE TABLE `zt_kanbantype` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '分类名称',
  `key` varchar(60) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '对应key值',
  `kanban` mediumint(8) DEFAULT NULL COMMENT '看板ID',
  `createdBy` char(30) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '由谁创建',
  `createdDate` datetime DEFAULT NULL,
  `deleted` enum('1','0') COLLATE utf8mb4_bin DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- 2023-03-20 xiangyang 新增清总评审表
-- ----------------------------
CREATE TABLE `zt_reviewqz` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `qzReviewId` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '外部评审ID',
  `title` varchar(255) NOT NULL COMMENT '评审标题',
  `reviewCenter` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评审中心',
  `type` char(30) NOT NULL COMMENT '评审类型',
  `isProject` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否属于项目 1-是  0-否',
  `project` varchar(255) NOT NULL DEFAULT '' COMMENT '如果属于项目，项名称',
  `status` char(30) NOT NULL COMMENT '评审状态',
  `owner` varchar(50) NOT NULL COMMENT '评审主席',
  `dealUser` varchar(255) NOT NULL DEFAULT '' COMMENT '待处理人',
  `version` mediumint(9) NOT NULL DEFAULT '0' COMMENT '版本',
  `planReviewMeetingTime` timestamp NOT NULL COMMENT '建议评审会议召开时间',
  `planJinkeExports` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '建议金科参会专家',
  `planExternalExports` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '建议外部专家',
  `review_method` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '评审方式 online:在线评审 meeting:会议评审',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `liasisonOfficer` varchar(50) DEFAULT NULL COMMENT '金科评审接口人',
  `content` text NOT NULL COMMENT '评审信息',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '备注',
  `applicant` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '申请人',
  `applicationDept` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '申请人部门id',
  `deptManager` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '部门经理',
  `applicationTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '申请时间',
  `planFeedbackTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '线上评审反馈建议截止时间',
  `verifier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '验证人',
  `verifierTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '验证时间',
  `confirmJoinDeadLine` timestamp NOT NULL COMMENT '确认参会截止时间',
  `relationFiles` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '附件json',
  `createBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '创建人',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `reason` text NOT NULL COMMENT '打回原因',
  `num` mediumint(5) NOT NULL DEFAULT '0' COMMENT '清总反馈次数',
  PRIMARY KEY (`id`),
  KEY `qzReviewId` (`qzReviewId`) USING BTREE,
  KEY `project` (`project`) USING BTREE,
  KEY `dealUser` (`dealUser`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='清总评审表';

-- ----------------------------
-- 迭代24 2023-03-02  wangjiurong 项目评审添加自动处理时间
-- ----------------------------
ALTER TABLE zt_review ADD autoDealTime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '自动处理时间';

CREATE TABLE `zt_projectplanedit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `planID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年度计划id',
  `email` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '邮箱接收人',
  `vsersion` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '年度计划version',
  `createtime` bigint(20) unsigned NOT NULL COMMENT '添加时间',
  `editmark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '修改备注',
  PRIMARY KEY (`id`),
  KEY `planid` (`planID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='年度计划审批中编辑通知人表';

ALTER TABLE `zt_copyrightqz`
	MODIFY COLUMN `techFeature` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '软件的技术特点（文本）',
	MODIFY COLUMN `others` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '其他'

 update zt_consumed set after ='build' where objectType = 'demand' and account ='guestjk' and `after` ='testing' and deleted ='0' and createdDate >= '2023-03-10' ;
-- ----------------------------
-- 2023-03-20 xiangyang 新增清总评审问题表
-- ----------------------------
CREATE TABLE `zt_reviewissueqz` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `reviewId` int(10) NOT NULL DEFAULT '0' COMMENT '清总评审表ID',
  `qzReviewId` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '清总评审ID',
  `qzIssueId` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '问题ID',
  `type` char(30) NOT NULL COMMENT '提出阶段',
  `title` varchar(255) NOT NULL COMMENT '文件名位置',
  `desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '问题描述',
  `createBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '提出人',
  `status` char(30) NOT NULL COMMENT '流程状态',
  `dealUser` varchar(255) NOT NULL DEFAULT '' COMMENT '待处理人',
  `resolutionBy` char(30) NOT NULL COMMENT '解决人员',
  `resolutionDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '解决时间',
  `validation` char(30) NOT NULL COMMENT '验证人员',
  `verifyDate` date NOT NULL COMMENT '验证日期',
  `content` text NOT NULL COMMENT '修改说明',
  `accept` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否采纳 1-是  0-否',
  `proposalType` char(30) NOT NULL COMMENT '意见类型',
  `verifyContent` text NOT NULL COMMENT '验证情况说明',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `raiseBy` char(30) NOT NULL COMMENT '提出人',
  `raiseDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '提出日期',
  `delDesc` text NOT NULL COMMENT '删除备注',
  `editBy` varchar(50) NOT NULL COMMENT '由谁编辑',
  PRIMARY KEY (`id`),
  KEY `qzIssueId` (`qzIssueId`) USING BTREE,
  KEY `qzReviewId` (`reviewId`) USING BTREE,
  KEY `dealUser` (`dealUser`) USING BTREE,
  KEY `reviewId` (`reviewId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='清总问题表';

-- ----------------------------
-- 迭代24 2023-03-02  wangjiurong 项目评审添加自动处理时间
-- ----------------------------
ALTER TABLE zt_review ADD autoDealTime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '自动处理时间';

-- ----------------------------
-- 迭代24 2023-03-23  xiangyang 是否超时字段
-- ----------------------------
ALTER TABLE zt_reviewqz ADD overtime enum('0','1') NOT NULL DEFAULT '0'  COMMENT '是否超时需要接口人处理,1:已超时,0:未超时';

-- ----------------------------
-- 迭代24 2023-03-23  xiangyang 接口地址a
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'feedbackExpertsUrl', 'http://plcm.cncc.cn:30080/api/project/apps/api/v1/osc/apps/yinqing_jinke_sync/environments/production/webtriggers/sync-review-feedbackExperts');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'feedbackUpDataExpertsUrl', 'http://plcm.cncc.cn:30080/api/project/apps/api/v1/osc/apps/yinqing_jinke_sync/environments/production/webtriggers/sync-review-feedbackUpDataExperts');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'feedbackQzIssuesUrl', 'http://plcm.cncc.cn:30080/api/project/apps/api/v1/osc/apps/yinqing_jinke_sync/environments/production/webtriggers/sync-receive-audit-opinion');

-- ----------------------------
-- 迭代24 2023-03-02  wangjiurong 审核节点添加备注信息
-- ----------------------------

ALTER table zt_reviewnode ADD extra TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '扩展信息';

alter table zt_kanbancard add type varchar(60) null comment '类型key值';