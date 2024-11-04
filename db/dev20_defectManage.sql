--- 迭代20-缺陷管理lzz---
CREATE TABLE `zt_defect` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `code` varchar(50) COMMENT '单号',
                                  `resolution` varchar(30) COMMENT '解决方案',
                                  `resolvedBuild` varchar(30)COMMENT '解决版本',
                                  `resolvedDate` datetime  COMMENT '解决日期',
                                  `dealUser` varchar(50)  COMMENT '指派给',
                                  `analysis` text default NULL COMMENT '问题分析',
                                  `linkProduct` text  COMMENT '涉及产品',
                                  `ifTest` varchar(50) DEFAULT NULL COMMENT '是否集中测试',
                                  `dealSuggest` varchar(30)  COMMENT '处置建议',
                                  `dealComment` mediumtext  COMMENT '处置说明',
                                  `changeDate` datetime  COMMENT '计划变更日期',
                                  `submitChangeDate` datetime  COMMENT '计划提交变更日期',
                                  `EditorImpactscope` mediumtext  COMMENT '影响范围',
                                  `ifHisIssue` char(5) DEFAULT NULL COMMENT '是否历史遗留问题',
                                  `app` int unsigned  COMMENT '所属系统',
                                  `product` int unsigned  COMMENT '所属产品',
                                  `project` int unsigned  COMMENT '所属项目',
                                  `CBPproject` varchar(255) DEFAULT NULL COMMENT '所属CBP项目',
                                  `projectManager` varchar(30)  COMMENT '项目经理',
                                  `title` varchar(255) DEFAULT NULL COMMENT '缺陷标题',
                                  `reportUser` varchar(50) DEFAULT NULL COMMENT '报告人',
                                  `reportDate` datetime DEFAULT NULL COMMENT '报告日期',
                                  `pri` char(5)  COMMENT '问题优先级',
                                  `issues` text  COMMENT '问题描述',
                                  `type` char(30)  COMMENT '缺陷类型',
                                  `childType` char(30)  COMMENT '缺陷子类',
                                  `status` char(30)  COMMENT '流程状态',
                                  `frequency` char(30)  COMMENT '缺陷频次',
                                  `developer` char(30)  COMMENT '开发人员',
                                  `tester` char(30)  COMMENT '测试工程师',
                                  `testOwner` char(30)  COMMENT '测试经理',
                                  `rounds` char(50)  COMMENT '轮次',
                                  `testEnvironment` char(30)  COMMENT '测试环境',
                                  `dept` char(30)  COMMENT '所属部门',
                                  `verification` char(30)  COMMENT '缺陷验证结果',
                                  `testrequestId` char(255)  COMMENT '关联测试申请',
                                  `testrequestCode` char(255)  COMMENT '关联测试申请code编码',
                                  `productenrollId` char(255)  COMMENT '关联产品登记',
                                  `productenrollCode` char(255)  COMMENT '关联产品登记code编码',
                                  `modifycnccId` char(255)  COMMENT '关联生产变更',
                                  `testrequestCreatedBy` char(255)  COMMENT '测试申请单创建人',
                                  `productenrollCreatedBy` char(255)  COMMENT '产品登记单创建人',
                                  `bugId` char(30)  COMMENT '相关bugID',
                                  `testType` char(30)  COMMENT '测试类型',
                                  `severity` int  COMMENT '问题严重性',
                                  `reviewer` mediumtext DEFAULT NULL COMMENT '审批人',
                                  `reviewComment` mediumtext DEFAULT NULL COMMENT '审批意见',
                                  `reviewDate` datetime DEFAULT NULL COMMENT '审批时间',
                                  `feedbackNum` int DEFAULT NULL COMMENT '反馈次数',
                                  `createdBy` char(30) NOT NULL COMMENT '由谁创建',
                                  `createdDate` datetime NOT NULL COMMENT '创建时间',
                                  `confirmedBy` char(30) DEFAULT NULL COMMENT '由谁确认',
                                  `confirmedDate` datetime DEFAULT NULL COMMENT '确认时间',
                                  `dealedBy` char(30) DEFAULT NULL COMMENT '由谁处理',
                                  `dealedDate` datetime DEFAULT NULL COMMENT '处理时间',
                                  `submitedBy` char(30) DEFAULT NULL COMMENT '由谁提交',
                                  `cc` char(30) DEFAULT NULL COMMENT '通知人员',
                                  `submitedDate` datetime DEFAULT NULL COMMENT '提交时间',
                                  `activedBy` char(30) DEFAULT NULL COMMENT '由谁激活',
                                  `activedDate` datetime DEFAULT NULL COMMENT '激活时间',
                                  `deleted` varchar(5) DEFAULT '0',
                                  `source` tinyint(1) DEFAULT NULL COMMENT '数据来源 1:内部bug转缺陷 2：总中心uat同步',
                                  `remark` text DEFAULT NULL COMMENT '本次操作备注',
                                  `testCase` text DEFAULT NULL COMMENT '测试案例',
                                  `testAdvice` text DEFAULT NULL COMMENT '测试建议',
                                  `Dropdown_suspensionreason` text DEFAULT NULL COMMENT '挂起原因',
                                  `uatId` varchar(30) DEFAULT NULL COMMENT '清总UAT同步过来的ID',
                                  `syncStatus` int DEFAULT 0 COMMENT '本次已同步状态 1已同步',
                                  `testrequestGiteeId` char(255)  COMMENT '关联测试申请gitee单号',
                                  `productenrollGiteeId` char(255)  COMMENT '关联产品登记gitee单号',
                                  `modifycnccGiteeId` char(255)  COMMENT '关联生产变更gitee单号',
                                  `outwarddeliveryId` char(255)  COMMENT '关联对外交付',
                                  `changeStatus` char(255)  DEFAULT NULL COMMENT '外部审批结果',
                                  `approverName` char(255)  COMMENT '外部审批人',
                                  `approverComment` char(255)  COMMENT '外部审批意见',
                                  `sampleVersionNumber` char(255)  COMMENT '样品版本号',
                                  `approverDate` datetime  DEFAULT NULL COMMENT '外部审批时间',
                                  `realtedApp` varchar(30)  DEFAULT NULL COMMENT '涉及业务系统',
                                  `progress` text DEFAULT NULL COMMENT '当前进展',
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `zt_testingrequest`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

ALTER TABLE `zt_modifycncc`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

ALTER TABLE `zt_productenroll`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

ALTER TABLE `zt_outwarddelivery`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

// 接口配置
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'defectFeedbackUrl', 'http://plcm.cncc.cn:30080/api/project/app/osc/yinqing_jinke_sync/webhooks/test-defect-receive');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'defectReFeedbackUrl', 'http://plcm.cncc.cn:30080/api/project/app/osc/yinqing_jinke_sync/webhooks/test-defect-receive-feedback');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushDefectAppId', 'jinke');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushDefectAppSecret', '482733936f2e45eaba0cc5768e5541eb');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushDefectEnable', 'enable');





