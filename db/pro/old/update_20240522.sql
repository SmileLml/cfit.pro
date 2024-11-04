set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 迭代三十四 问题、风险增加抄送 2024-3-20 wangyongliang
-- ----------------------------
ALTER TABLE `zt_issue` ADD COLUMN `mailTo` varchar(255) DEFAULT NULL COMMENT '抄送',
           ADD COLUMN `frameworkUser` varchar(255) DEFAULT NULL COMMENT '架构部人员',
           ADD COLUMN `assignedToFrameWorkBy` varchar(255) DEFAULT NULL COMMENT '指派架构部人员',
           ADD COLUMN `assignedToFrameWorkDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '指派架构部人员时间';

ALTER TABLE `zt_risk` ADD COLUMN `mailTo` varchar(255) DEFAULT NULL COMMENT '抄送',
          ADD COLUMN `frameworkUser` varchar(255) DEFAULT NULL COMMENT '架构部人员',
          ADD COLUMN `assignedToFrameWorkBy` varchar(255) DEFAULT NULL COMMENT '指派架构部人员',
          ADD COLUMN `assignedToFrameWorkDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '指派架构部人员时间';
-- ----------------------------
-- 迭代三十四 征信交付配置 2024-3-28 wangjiurong
-- ----------------------------
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'levelList', '', '', '1', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'levelList', '1', '一级', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'levelList', '2', '二级', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'levelList', '3', '三级', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'changeNodeList', '', '', '1', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'changeNodeList', '1', '上海生产数据中心', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'changeNodeList', '2', '上海同城灾备中心', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'changeSourceList', '', '', '1', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'changeSourceList', '1', '常规需求', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'changeSourceList', '2', '数据修正', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'modeList', '', '', '1', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'modeList', '1', '主版本变更', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'modeList', '2', '副版本变更', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'modeList', '3', '补丁变更', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'modeList', '4', '参数变更', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'typeList', '', '', '1', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'typeList', '1', '应用软件', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'typeList', '2', '数据库脚本', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'typeList', '3', '静态资源', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'credit', 'executeModeList', '', '', '1', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'executeModeList', '1', '人工实施', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'executeModeList', '2', '自动化实施', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'credit', 'executeModeList', '3', '其他', '0', 0);

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'tjCreditKey', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'tjCreditId', '');
-- ----------------------------
-- 迭代三十四 征信交付邮件模板 2024-4-10 wangjiurong
-- ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value)
VALUES('system', 'common', 'global', 'setCreditMail', '{"mailTitle":"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406","variables":["\\u5f81\\u4fe1\\u4ea4\\u4ed8\\u5355"],"mailContent":"<span style=\\"font-weight:700;\\">\\u8bf7\\u8fdb\\u5165\\u3010\\u4ea4\\u4ed8\\u7ba1\\u7406\\u3011<\\/span><span><span style=\\"font-weight:700;\\">-<\\/span><\\/span><span style=\\"font-weight:700;\\">\\u3010\\u5f81\\u4fe1\\u4ea4\\u4ed8\\u3011<span style=\\"font-weight:700;\\"><\\/span>\\u67e5\\u770b<\\/span><span><span style=\\"font-weight:700;\\"><\\/span><\\/span><span style=\\"font-weight:700;\\">\\uff0c\\u6458\\u8981\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/span>"}');

-- ----------------------------
-- 迭代三十四 征信交付表 2024-3-28 wangjiurong
-- ----------------------------
-- cfitpms240130.zt_credit definition

CREATE TABLE `zt_credit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '单号',
  `appIds` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '系统id',
  `productIds` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品id',
  `implementationForm` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所属内部项目实现方式',
  `projectPlanId` int(11) NOT NULL DEFAULT '0' COMMENT '所属项目id',
  `secondorderIds` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '工单id',
  `problemIds` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '问题单id',
  `demandIds` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '需求条目id',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '变更级别',
  `changeNode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更级别',
  `changeSource` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更来源',
  `mode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更类型',
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更分类',
  `executeMode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '实施方式',
  `emergencyType` tinyint(1) NOT NULL DEFAULT '1' COMMENT '紧急程度 1-紧急 2-不紧急',
  `isBusinessAffect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '实施期间是否有业务影响 1-否 2-是',
  `planBeginTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '预计开始时间',
  `planEndTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '预计结束时间',
  `actualBeginTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际开始时间',
  `actualEndTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际结束时间',
  `summary` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变更摘要',
  `desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变更描述',
  `techniqueCheck` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '技术验证',
  `feasibilityAnalysis` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变更可行性分析',
  `riskAnalysisEmergencyHandle` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '风险分析与应急处置',
  `productAffect` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '给生产系统带来的影响',
  `businessAffect` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '给业务功能带来的影响',
  `svnUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品交付SVN路径',
  `onLineFile` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '上线材料清单',
  `version` smallint(6) NOT NULL DEFAULT '1',
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dealUsers` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '待处理人',
  `reviewerInfo` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '审核人信息',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDept` int(11) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `editedDate` datetime NOT NULL,
  `lastDealDate` date NOT NULL,
  `issubmit` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'submit' COMMENT 'save:submit',
  `deleted` tinyint(1) NOT NULL,
  `workflowId` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '工作流id',
  `abnormalId` int(10) NOT NULL DEFAULT '0' COMMENT '异常变更单id',
  `deliveryTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '交付时间',
  `onlineTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上线时间',
  `cancelBy` varchar(30) NOT NULL DEFAULT '' COMMENT '取消人',
  `cancelDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '取消时间',
  `lastStatus` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '上一状态',
  `cancelReason` text NOT NULL COMMENT '取消原因',
  `demandCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '需求条目是否取消状态联动：0:正常 1：取消状态联动',
  `problemCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '问题池是否取消状态联动：0正常 1：取消状态联动',
  `secondorderCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '工单是否取消状态联动：0:正常 1：取消状态联动',
  `demandLinked` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已经联动需求条目：0未联动 1：已联动',
  PRIMARY KEY (`id`),
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='征信交付表';



-- 金信系统新增字段
ALTER TABLE `zt_modify`
     ADD COLUMN `materialIsReview` enum('0','1','2') NOT NULL DEFAULT '0'  COMMENT '材料是否评审,0:'',1:是,2:否',
     ADD COLUMN `materialReviewUser` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评审人',
     ADD COLUMN `materialReviewResult` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评审结果';



-- ----------------------------
-- 迭代三十四 内部需求池已发布状态无待处理人补充创建人 2024-4-3 wangshusen
-- ----------------------------
UPDATE zt_demand SET `dealUser` = 'tianmingfei' WHERE id in (1546,1544,1543,1542);
INSERT INTO zt_action(objectType,objectID,product,project,execution,actor,action,`date`,`comment`) VALUES ('demand','1546','0','0','0','guestjk','edited',NOW(),'历史数据无待处理人，补充为创建人');
INSERT INTO zt_action(objectType,objectID,product,project,execution,actor,action,`date`,`comment`) VALUES ('demand','1544','0','0','0','guestjk','edited',NOW(),'历史数据无待处理人，补充为创建人');
INSERT INTO zt_action(objectType,objectID,product,project,execution,actor,action,`date`,`comment`) VALUES ('demand','1543','0','0','0','guestjk','edited',NOW(),'历史数据无待处理人，补充为创建人');
INSERT INTO zt_action(objectType,objectID,product,project,execution,actor,action,`date`,`comment`) VALUES ('demand','1542','0','0','0','guestjk','edited',NOW(),'历史数据无待处理人，补充为创建人');
UPDATE zt_demand SET `dealUser` = 'chentaiji' WHERE id = 1455;
INSERT INTO zt_action(objectType,objectID,product,project,execution,actor,action,`date`,`comment`) VALUES ('demand','1455','0','0','0','guestjk','edited',NOW(),'历史数据无待处理人，补充为创建人');

-- -----------------------
-- 迭代34 项目变更 联动年度计划 jinzhuliang
-- -----------------------
ALTER TABLE zt_change ADD projectplantext TEXT NULL COMMENT '一级变更年度计划修改内容';

-- ----------------------------
-- 2024-03-06  leiyong 修复用例执行结果查询错误
-- ----------------------------
UPDATE zt_testresult SET run = 0 WHERE run = `case`;

-- -----------------------
-- 迭代34 需求条目增加交付是否超期字段 wangshusen
-- -----------------------
ALTER TABLE `zt_demand` ADD COLUMN `deliveryOver` tinyint(4) NOT NULL DEFAULT '0' COMMENT '交付是否超期  1：否 ；2：是';

-- -----------------------
-- 迭代34 需求收集模块增加数据修正原因 wangshusen
-- -----------------------
ALTER TABLE `zt_demandcollection` ADD COLUMN `correctionReason` varchar(60) NOT NULL DEFAULT '' COMMENT '数据修正原因，保存自定义枚举值';

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demandcollection', 'correctionReasonList', '', '', '1');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demandcollection', 'correctionReasonList', '1', '人工操作问题', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demandcollection', 'correctionReasonList', '2', '内部系统原因', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demandcollection', 'correctionReasonList', '3', '外部系统原因', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demandcollection', 'correctionReasonList', '4', '其他', '0');

ALTER TABLE `zt_demandcollection` MODIFY COLUMN `commConfirmBy` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';




-- -----------------------
-- 迭代34 问题单 二线月报统计增加字段
-- -----------------------
ALTER TABLE `zt_problem` ADD COLUMN `isExceedByTime` enum('否','是') NOT NULL DEFAULT '否'  COMMENT '交付是否超期（只比较时间）';

-- -----------------------
-- 迭代34 需求人无变更表增加计划完成时间字段
-- -----------------------
ALTER TABLE `zt_requirementchangeoutside` ADD COLUMN `requirementEnd` datetime DEFAULT NULL COMMENT '原计划完成时间' AFTER `changeDeadline`;
ALTER TABLE `zt_requirementchangeoutside` ADD COLUMN `changePlanEnd` datetime DEFAULT NULL COMMENT '变更后计划完成时间' AFTER `requirementEnd`;

-- -----------------------
-- 迭代34 问题单 外部问题单，内部不能关闭 wangyongliang
-- -----------------------
update zt_problem set dealUser ='' where status ='toclose'  and createdBy in ('guestcn','guestjx');

-- -----------------------
-- 迭代34 工单 新增 是否最终移交 wangyongliang
-- -----------------------
ALTER TABLE `zt_secondorder` ADD COLUMN `finallyHandOver` tinyint(4) NOT NULL DEFAULT '0'  COMMENT '是否最终移交 1：是 2：否';
ALTER TABLE `zt_sectransfer` ADD COLUMN `finallyHandOver` tinyint(4) NOT NULL DEFAULT '0'  COMMENT '是否最终移交 1：是 2：否';
-- -----------------------
-- 迭代34 数据收集关联产品 wangjiurong
-- -----------------------
ALTER table zt_demandcollection MODIFY COLUMN product varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '关联产品';

-- -----------------------
-- 迭代34 清总生产变更新增紧急来源、紧急原因字段
-- -----------------------
ALTER TABLE `zt_modifycncc`
     ADD COLUMN `urgentSource` tinyint(4) NOT NULL DEFAULT '0'  COMMENT '紧急来源',
     ADD COLUMN `urgentReason` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '紧急原因';

INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'modifycncc', 'urgentSourceList', '1', '生产事件', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'modifycncc', 'urgentSourceList', '2', '风险隐患', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'modifycncc', 'urgentSourceList', '3', '紧急需求', '0', 0);

-- -----------------------
-- 迭代34 需求任务增加用户考核的计划完成时间 wangjiurong
-- -----------------------
ALTER TABLE zt_requirement ADD planEnd DATE NOT NULL COMMENT '用户考核的计划完成时间';

-- -----------------------
-- 迭代34 需求任务新增计划完成时间历史数据处理
-- -----------------------
update  zt_requirement set planEnd = `end`  where 1 and createdBy = 'guestcn' and feedbackStatus = 'feedbacksuccess';
update zt_requirement set planEnd = `deadLine` where 1 and ((createdBy = 'guestcn' and feedbackStatus != 'feedbacksuccess') or createdBy != 'guestcn') and deadLine != '0000-00-00';

update zt_requirement zr left join zt_opinion zo on zr.opinion = zo.id set zr.planEnd = zo.deadline
where 1
and  zr.createdBy != 'guestcn'
and zr.deadLine = '0000-00-00'
and zr.opinion = zo.id;

-- ---------------------------------
--内部自建系统投产变更 wangshusen
-- ---------------------------------
CREATE TABLE `zt_productionchange` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `type` tinyint(1) NOT NULL COMMENT '类型 1：投产 2变更',
                                       `code` varchar(32) DEFAULT NULL COMMENT '编号',
                                       `applicant` varchar(255) DEFAULT NULL COMMENT '申请人',
                                       `applicantDept` varchar(30) DEFAULT NULL COMMENT '申请人所属部门',
                                       `onlineType` varchar(30) DEFAULT NULL COMMENT '上线类型',
                                       `status` varchar(30) DEFAULT NULL COMMENT '状态',
                                       `dealUser` varchar(255) DEFAULT NULL COMMENT '待处理人',
                                       `createdBy` varchar(30) DEFAULT NULL COMMENT '创建人',
                                       `createdDate` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                       `updatedDate` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                       `application` varchar(30) DEFAULT NULL COMMENT '应用系统名称',
                                       `onlineStart` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '上线计划实施开始时间',
                                       `onlineEnd` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '上线计划实施结束时间',
                                       `abstract` text COMMENT '上线摘要',
                                       `effect` text COMMENT '上线影响说明',
                                       `ifEffectSystem` varchar(30) DEFAULT NULL COMMENT '是否影响关联系统:后台可配置',
                                       `effectSystemExplain` text COMMENT '影响关联系统说明',
                                       `materialExplain` text COMMENT '上线材料说明',
                                       `correlationPublish` varchar(255) DEFAULT NULL COMMENT '关联发布',
                                       `space` varchar(255) DEFAULT NULL COMMENT '空间',
                                       `releaseRecord` text COMMENT '发布记录',
                                       `correlationDemand` varchar(255) DEFAULT NULL COMMENT '关联需求条目',
                                       `correlationProblem` varchar(255) DEFAULT NULL COMMENT '关联问题单',
                                       `correlationSecondorder` varchar(255) DEFAULT NULL COMMENT '关联工单',
                                       `ifReport` tinyint(1) DEFAULT NULL COMMENT '是否上报 1：否 2：是',
                                       `deptConfirmPerson` varchar(255) DEFAULT NULL COMMENT '部门确认责任人',
                                       `interfacePerson` varchar(255) DEFAULT NULL COMMENT '业务方接口人',
                                       `interfaceDeptPerson` varchar(255) DEFAULT NULL COMMENT '业务方部门责任人',
                                       `mediaPackage` varchar(255) DEFAULT NULL COMMENT '介质包获取地址',
                                       `mailto` varchar(255) DEFAULT NULL COMMENT '抄送人',
                                       `defaultMailto` varchar(255) DEFAULT NULL COMMENT '默认抄送人',
                                       `deleted` enum('0','1') DEFAULT '0' COMMENT '1 已删除',
                                       `implementContent` text COMMENT '上线实施内容',
                                       `operationPerson` varchar(255) DEFAULT NULL COMMENT '运维方接口人',
                                       `version` tinyint(2) DEFAULT NULL COMMENT '版本号',
                                       `returnTimes` tinyint(2) DEFAULT 0 COMMENT '退回次数',
                                       `processInstanceId` varchar(255) DEFAULT NULL COMMENT '之前审批流程id',
                                       `executanter` varchar(255) DEFAULT NULL COMMENT '实施人员',
                                       `reviewPerson` varchar(255) DEFAULT NULL COMMENT '复核人员',
                                       `operationDept` varchar(255) DEFAULT NULL COMMENT '运维方部门',
                                       `actualOnlineTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际上线时间',
                                       `record` text COMMENT '实施记录',
                                       `remark` text COMMENT '备注说明',
                                       `waitValidate` varchar(255) DEFAULT NULL COMMENT '验证人员',
                                       PRIMARY KEY (`id`),
                                       KEY `correlationDemand` (`correlationDemand`),
                                       KEY `correlationProblem` (`correlationProblem`),
                                       KEY `correlationSecondorder` (`correlationSecondorder`),
                                       KEY `dealUser` (`dealUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='内部自建投产/变更';
-- -----------------------
-- 迭代34 工作流引擎
-- -----------------------
alter table zt_iwfp add nodeInfoList text null comment '节点信息';
alter table zt_iwfp modify processTaskId text null comment '任务id';

-- -----------------------
-- 迭代34 内部自建投产/变更邮件模板配置
-- -----------------------
INSERT INTO zt_config
(owner, module, `section`, `key`, value)
VALUES('system', 'common', 'global', 'setProductionchangeMail', '{"mailTitle":"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406","variables":["\\u6295\\u4ea7\\/\\u53d8\\u66f4"],"mailContent":"<span style=\\"font-weight:700;\\">\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/span><span><span style=\\"font-weight:700;\\">-<\\/span><\\/span><span style=\\"font-weight:700;\\">\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/span><span><span style=\\"font-weight:700;\\">-<\\/span><\\/span><span style=\\"font-weight:700;\\">\\u3010\\u5185\\u90e8\\u81ea\\u5efa\\u6295\\u4ea7\\/\\u53d8\\u66f4\\u3011\\u5904\\u7406<\\/span><span style=\\"color:#E53333;\\"><span style=\\"font-weight:700;\\">\\u95ee\\u9898<\\/span><\\/span><span style=\\"font-weight:700;\\">\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/span>"}');

alter table zt_iwfp add nodeInfoList text null comment '节点信息';

-- -----------------------
-- 迭代34 征信交付填写变更结果账户
-- -----------------------
INSERT INTO zt_config (owner,module,`section`,`key`,value) VALUES
	 ('system','credit','','confirmResultUsers','liujite');

-- -----------------------
-- 迭代34 外部问题单关闭人员清空 wangyongliang
-- -----------------------
update zt_lang set `value` ='' where module ='problem' and `section` ='closePersonList' limit 2;
-- -----------------------
-- 迭代34 天津分公司二线专员配置 wangjiurong
-- -----------------------
UPDATE zt_dept
SET  executive=',t_zhangyajie'
WHERE id=26;

