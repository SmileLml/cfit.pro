set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

alter table zt_modify add issubmit varchar(30) DEFAULT 'submit' comment 'save:保存，submit：提交';
alter table zt_info add issubmit varchar(30) DEFAULT 'submit' comment 'save:保存，submit：提交';
alter table zt_infoqz add issubmit varchar(30) DEFAULT 'submit' comment 'save:保存，submit：提交';
alter table zt_outwarddelivery add issubmit varchar(30) DEFAULT 'submit' comment 'save:保存，submit：提交';


ALTER TABLE `zt_component_release` ADD COLUMN `baseline` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '第三方组件-基线版本';

ALTER TABLE `zt_modify` ADD COLUMN `reviewFailReason`  text  DEFAULT NULL COMMENT '历史审核版本外部失败原因 json';
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `reviewFailReason`  text  DEFAULT NULL COMMENT '历史审核版本外部失败原因 json';
ALTER TABLE `zt_infoqz` ADD COLUMN `reviewFailReason`  text  DEFAULT NULL COMMENT '历史审核版本外部失败原因 json';

-- ----------------------------
-- 迭代26 2023-05-04  shixuyang 二线工单新增字段
-- ----------------------------
ALTER TABLE `zt_secondorder` ADD COLUMN `sourceBackground` varchar(20) DEFAULT NULL COMMENT '任务来源背景；project-项目；second-二线';
ALTER TABLE `zt_secondorder` ADD COLUMN `cbpProject` varchar(255) DEFAULT NULL COMMENT '所属项目：外部项目';
ALTER TABLE `zt_secondorder` ADD COLUMN `contacts` varchar(255) DEFAULT NULL COMMENT '联系人';
ALTER TABLE `zt_secondorder` ADD COLUMN `contactsPhone` varchar(255) DEFAULT NULL COMMENT '联系电话';
ALTER TABLE `zt_secondorder` ADD COLUMN `ifReceived` varchar(2) DEFAULT NULL COMMENT '是否接受：1-否；2-是';
ALTER TABLE `zt_secondorder` ADD COLUMN `notReceiveReason` text DEFAULT NULL COMMENT '未接受原因';
ALTER TABLE `zt_secondorder` ADD COLUMN `acceptanceCondition` text DEFAULT NULL COMMENT '受理情况';
ALTER TABLE `zt_secondorder` ADD COLUMN `completionDescription` text DEFAULT NULL COMMENT '完成情况说明';
ALTER TABLE `zt_secondorder` ADD COLUMN `implementationForm` varchar(20) DEFAULT NULL COMMENT '实现方式：project-项目；second-二线';
ALTER TABLE `zt_secondorder` ADD COLUMN `internalProject` varchar(255) DEFAULT NULL COMMENT '所属内部项目';
ALTER TABLE `zt_secondorder` ADD COLUMN `formType` varchar(20) DEFAULT NULL COMMENT '单子类型：external-清总同步单；internal-内部新建单';
ALTER TABLE `zt_secondorder` ADD COLUMN `rejectUser` varchar(256) DEFAULT NULL COMMENT '退回人';
ALTER TABLE `zt_secondorder` ADD COLUMN `rejectReason` varchar(1024) DEFAULT NULL COMMENT '退回原因';
ALTER TABLE `zt_secondorder` ADD COLUMN `externalCode` varchar(512) DEFAULT NULL COMMENT '外部单号';
ALTER TABLE `zt_secondorder` ADD COLUMN `sourcePlatform` varchar(512) DEFAULT NULL COMMENT '任务来源平台';
ALTER TABLE `zt_secondorder` ADD COLUMN `externalDept` varchar(512) DEFAULT NULL COMMENT '外部所属部门';
ALTER TABLE `zt_secondorder` ADD COLUMN `externalApplicant` varchar(512) DEFAULT NULL COMMENT '外部申请人';
ALTER TABLE `zt_secondorder` ADD COLUMN `externalApplicantTime` date DEFAULT NULL COMMENT '外部申请时间';
ALTER TABLE `zt_secondorder` ADD COLUMN `note` text DEFAULT NULL COMMENT '备注信息';
ALTER TABLE `zt_secondorder` ADD COLUMN `pushStatus` varchar(2) DEFAULT NULL COMMENT '同步清总状态：pending-待发送；success-成功；fail-失败';
ALTER TABLE `zt_secondorder` ADD COLUMN `pushNum` int DEFAULT NULL COMMENT '同步清总次数';
ALTER TABLE `zt_secondorder` ADD COLUMN `taskIdentification` varchar(512) DEFAULT NULL COMMENT '计划性任务标识';
ALTER TABLE `zt_secondorder` ADD COLUMN `notAcceptReason` text DEFAULT NULL COMMENT '未受理原因';
ALTER TABLE `zt_secondorder` ADD COLUMN `externalStatus` varchar(32) DEFAULT NULL COMMENT '外部状态：pass-通过；reject-拒绝;closed-关闭';
ALTER TABLE `zt_secondorder` ADD COLUMN `externalTime` datetime DEFAULT NULL COMMENT '外部反馈时间';
ALTER TABLE `zt_secondorder` ADD COLUMN `completionFeedback` text DEFAULT NULL COMMENT '完成反馈';
ALTER TABLE `zt_secondorder` ADD COLUMN `execution` INT(10) DEFAULT NULL COMMENT '所属阶段';
ALTER TABLE `zt_secondorder` ADD COLUMN `pushDate` datetime DEFAULT NULL COMMENT '发送完成时间';

-- ----------------------------
-- 迭代26 2023-05-05  shixuyang 对外移交新增字段
-- ----------------------------
create table zt_sectransfer
(
    id                   mediumint unsigned auto_increment
        primary key,
    parent               mediumint unsigned                      null,
    assignedTo           varchar(30)                             null,
    status               varchar(30)                             null,
    createdBy            varchar(30)                             null,
    createdDate          datetime                                null,
    editedBy             varchar(30)                             null,
    editedDate           datetime                                null,
    assignedBy           varchar(30)                             null,
    assignedDate         datetime                                null,
    mailto               text                                    null,
    deleted              enum ('0', '1') default '0'             null,
    dept                 varchar(255)                            null,
    inproject            varchar(255)                            null,
    outproject           varchar(255)                            null,
    app                  text                                    null,
    department           varchar(255)                            null,
    reason               text                                    null,
    iscode               varchar(255)                            null,
    filelist             text                                    null,
    files                varchar(255)                            null,
    warning              text                                    null,
    suggest              text                                    null,
    result               varchar(255)                            null,
    own                  varchar(30)                             null,
    CM                   varchar(30)                             null,
    leader               varchar(30)                             null,
    sec                  varchar(30)                             null,
    maxleader            varchar(30)                             null,
    publish              varchar(255)                            null,
    apply                varchar(30)                             null,
    submitBy             varchar(30)                             null,
    submitDate           datetime                                null,
    approver             varchar(30)                             null,
    cmresult             varchar(255)                            null,
    deptresult           varchar(255)                            null,
    departresult         varchar(255)                            null,
    topresult            varchar(255)                            null,
    secresult            varchar(255)                            null,
    rebackBy             varchar(30)                             null,
    rebackDate           datetime                                null,
    outperson            varchar(255)                            null,
    jftype               varchar(255)                            null,
    deptadvice           text                                    null,
    departadvice         text                                    null,
    cmadvice             text                                    null,
    secadvice            text                                    null,
    topadvice            text                                    null,
    protransferDesc      varchar(256)                           null comment '移交摘要',
    subType              varchar(2)                             null comment '移交子类型',
    secondorderId        int                                    null comment '工单id',
    lastTransfer         varchar(2)                             null comment '是否是最后一次移交：1-否；2-是',
    transferNum          int                                    null comment '本项目第几次移交',
    containsMedia        varchar(2)                             null comment '是否包含介质：1-否；2-是',
    rejectUser           varchar(256)                           null comment '退回人',
    rejectReason         varchar(1024)                          null comment '退回原因',
    pushStatus           varchar(64)                            null comment '发送状态：tosend-待发送；mediaPending-介质发送中；mediaSuccess-介质发送成功；mediaFail-介质发送失败；pending-接口发送中；success-发送成功；fail-发送失败',
    pushTime             datetime                               null comment '发送时间',
    remotePath           varchar(1024)                          null comment '清总介质服务器的文件地址',
    sendFailReason       varchar(1024)                          null comment '发送失败原因',
    externalContactEmail varchar(128)                           null comment '外部接口人邮箱',
    pushMediaTime        datetime                               null comment '发送介质时间',
    mediaMd5             varchar(128)                           null comment '介质md5值',
    pushNum              int             default 0              null comment '发送次数',
    pushMediaNum         int             default 0              null comment '发送介质次数',
    transitionPhase      varchar(32)                            null comment '移交阶段',
    changeVersion        int                                    null comment '审批版本',
    externalId           varchar(128)                           null comment '外部单号',
    externalStatus       varchar(32)                            null comment '外部状态：pass-通过；reject-拒绝',
    externalTime         datetime                               null comment '外部反馈时间',
    reviewStage          varchar(8)                             null comment '当前审批阶段',
    version              varchar(8)                             null comment '当前审批版本'
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT charset = utf8;

ALTER TABLE `zt_sectransfer` ADD `requiredReviewNode` varchar(50) DEFAULT null COMMENT '处理退回时需审批节点';
ALTER TABLE `zt_sectransfer` ADD `sftpPath` varchar(1024) DEFAULT null COMMENT 'sftp路径';
ALTER TABLE `zt_sectransfer` ADD `externalRecipient` varchar(64) DEFAULT null COMMENT '外部接收方';
ALTER TABLE `zt_sectransfer` ADD `remoteFileList` text DEFAULT null COMMENT 'sftp文件列表清单';
ALTER TABLE `zt_sectransfer` ADD `rejectNum` int DEFAULT 0 COMMENT '退回次数';

-- ----------------------------
-- 迭代26 2023-05-05  shixuyang 二线工单后台自定义参数
-- ----------------------------
INSERT INTO `zt_entry` (`name`, `account`, `code`, `key`, `freePasswd`, `ip`, `desc`, `createdBy`, `createdDate`, `calledTime`, `editedBy`, `editedDate`, `deleted`) VALUES ('清总对接', 'admin', 'qingzong', '54dfb493698026bf8f2e5bf3d24d5a7a', '0', '*', '', 'admin', '2023-05-05 17:17:30', 0, 'admin', '2023-05-05 15:05:02', '0');
INSERT INTO `zt_entry` (`name`, `account`, `code`, `key`, `freePasswd`, `ip`, `desc`, `createdBy`, `createdDate`, `calledTime`, `editedBy`, `editedDate`, `deleted`) VALUES ('制品对接', 'admin', 'products', '0fb0834123b89ef5b62befb28490f5be', '0', '*', '', 'admin', '2023-05-05 17:17:30', 0, 'admin', '2023-05-05 15:05:02', '0');

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'typeList', 'support', '现场支持', '0');
update `zt_lang` set `value` = '{"consult":{"a1":"\u95ee\u9898\u54a8\u8be2\u8bc4\u4f30","a2":"\u65b9\u6848\u54a8\u8be2\u8bc4\u4f30","a3":"\u6750\u6599\u786e\u8ba4","a4":"\u5176\u4ed6\u54a8\u8be2\u8bc4\u4f30"},"test":{"b1":"\u517c\u5bb9\u6027\u6d4b\u8bd5","b2":"\u8054\u8c03\u6d4b\u8bd5\uff08\u5185\u90e8\uff09","b3":"\u5176\u4ed6\u6d4b\u8bd5"},"script":{"d1":"\u6570\u636e\u83b7\u53d6\u811a\u672c","d2":"\u5e94\u7528\u76d1\u63a7\u811a\u672c","d3":"\u5176\u4ed6\u811a\u672c"},"plan":{"e1":"\u65b9\u6848\u6587\u6863"},"support":{"f1":"\u5207\u6362\u652f\u6301"},"other":{"c1":"\u5176\u4ed6\u54a8\u8be2\u8bc4\u4f30"}}' where `module` = 'secondorder' and `key` = 'childTypeList';

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','returnedUserList','litianzi','李甜梓','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','apiDealUserList','litianzi','李甜梓','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'sourceList', 'qz', '清总接口同步', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','secondUserList','wanglijiao','王丽姣','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','taskIdentificationList','centoOs','CENTO OS','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalTypeList','consult','consult','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalTypeList','verification','test','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalTypeList','script','script','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalTypeList','programme','plan','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalTypeList','otherType','other','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalTypeList','support','support','1');

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','problem','a1','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','plan','a2','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','consultOther','a3','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','compatibility','b1','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','joint','b2','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','verificationOther','b3','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','data','d1','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','application','d2','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','scriptOther','d3','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','otherSubType','c1','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','programme','e1','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','secondorder','externalSubTypeList','support','f1','1');

-- ----------------------------
-- 迭代26 2023-05-05  shixuyang 二线工单发送参数信息
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'secondorderFeedbackUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'secondorderAppId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'secondorderAppSecret', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'secondorderEnable', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'secondorderFileIP', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'secondorderSftpServerIP', '');

-- ----------------------------
-- 迭代26 2023-05-05  shixuyang 对外移交发送参数信息
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sectransferFeedbackUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sectransferAppId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sectransferAppSecret', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sectransferEnable', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sectransferFileIP', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sectransferSftpServerIP', '');

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setSectransferMail', '{"mailTitle":"\u3010\u5f85\u529e\u3011\u60a8\u6709\u4e00\u4e2a\u3010%s\u3011\u5f85\u5904\u7406\uff0c\u8bf7\u8fdb\u5165\u7814\u53d1\u8fc7\u7a0b\u7ba1\u7406\u5e73\u53f0\u5904\u7406","variables":["\u5bf9\u5916\u79fb\u4ea4"],"mailContent":"<span style=\"font-weight:700;\">\u8bf7\u8fdb\u5165\u3010\u5730\u76d8\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5f85\u5904\u7406\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5ba1\u6279\u3011\u6216\u3010\u4ea4\u4ed8\u7ba1\u7406\u3011\u5904\u7406<\/span><span style=\"color:#E53333;\"><span style=\"font-weight:700;\">\u5bf9\u5916\u79fb\u4ea4<\/span><\/span><span style=\"font-weight:700;\">\uff0c\u5177\u4f53\u4fe1\u606f\u5982\u4e0b\uff1a<\/span>"}');

-- ----------------------------
-- 迭代26 2023-05-05  shixuyang 对外移交后台自定义参数
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','sectransfer','transitionPhase','design','设计阶段移交','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','sectransfer','transitionPhase','test','测试阶段移交','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','sectransfer','transitionPhase','operation','投产阶段移交','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','sectransfer','transitionPhase','transition','过渡阶段移交','1');
-- ----------------------------
-- 迭代26 2023-05-18  wangshusen 拆分内外部需求池
-- ----------------------------
ALTER TABLE `zt_requirement` ADD COLUMN `CBPProject`  varchar(10) DEFAULT '' COMMENT '所属CBP项目';
ALTER TABLE `zt_requirement` ADD COLUMN `sourceRequirement`  varchar(10) DEFAULT '1' COMMENT '数据来源 1：外部  2：内部';
ALTER TABLE `zt_demand` ADD COLUMN `sourceDemand`  varchar(10) DEFAULT '1' COMMENT '数据来源 1：外部  2：内部';
ALTER TABLE `zt_opinion` ADD COLUMN `sourceOpinion`  varchar(10) DEFAULT '1' COMMENT '数据来源 1：外部  2：内部';
ALTER TABLE `zt_opinion` ADD COLUMN `isOutsideProject`  varchar(10) DEFAULT '' COMMENT '是否属于外部项目范围 1：是  2：否';

update zt_lang set value = '否' where module = 'opinion' and section = 'synUnionList' and `key` = 1;
update zt_lang set value = '是' where module = 'opinion' and section = 'synUnionList' and `key` = 2;


-- ----------------------------
-- 迭代26 2023-05-19  shxiuyang 看板管理855需求
-- ----------------------------
ALTER TABLE zt_demandcollection ADD commConfirmRecord MEDIUMTEXT default NULL comment '沟通确认记录';
ALTER TABLE zt_demandcollection ADD commConfirmBy VARCHAR(30) default NULL comment '沟通确认人';

ALTER TABLE `zt_problem`
    ADD COLUMN `problemCause` varchar(255)  DEFAULT '' COMMENT '问题引起原因' AFTER `type`,
    ADD COLUMN `secureStatusLinkage` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '解除状态联动' AFTER `problemCause`;

update zt_problem set problemCause ='programmeCause'  where `type` ='designdefect';
update zt_problem set problemCause ='softCause'  where (`type` ='codeerror' or `type` ='apperror' or `type` ='softerror');
update zt_problem set problemCause ='configurationCause'  where `type` ='config';
update zt_problem set problemCause ='networkCause'  where `type` ='networkerror';
update zt_problem set problemCause ='hardwareCause'  where (`type` ='labfacterror' or `type` ='hardwareerror');
update zt_problem set problemCause ='otherCause'  where (`type` ='others' or `type` ='install' or `type` ='security');

update zt_problem set `type` ='apperror'  where (`type` ='config' or `type` ='codeerror' or `type` ='designdefect');
update zt_problem set `type` ='infosecurityerror'  where (`type` ='install' or `type` ='security');
update zt_problem set `type` ='systemerror'  where `type` ='performance' ;
update zt_problem set `type` ='others'  where (`type` ='standard' or `type` ='automation');


-- ----------------------------
-- 迭代26 2023-05-24  shxiuyang 内部项目邮件配置
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setWeeklyreportinMail', '{"mailTitle":"\u3010\u5f85\u529e\u3011\u60a8\u6709\u4e00\u4e2a\u3010%s\u3011\u5f85\u5904\u7406\uff0c\u8bf7\u8fdb\u5165\u7814\u53d1\u8fc7\u7a0b\u7ba1\u7406\u5e73\u53f0\u5904\u7406","variables":["\u5185\u90e8\u9879\u76ee\u5468\u62a5"],"mailContent":"<span style=\"font-weight:700;\">\u8bf7\u8fdb\u5165\u3010\u5730\u76d8\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5f85\u5904\u7406\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5ba1\u6279\u3011\u6216\u3010\u5e74\u5ea6\u8ba1\u5212\u3011\u5904\u7406<\/span><span style=\"color:#E53333;\"><span style=\"font-weight:700;\">\u5185\u90e8\u9879\u76ee\u5468\u62a5<\/span><\/span>"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setWeeklyreportoutMail', '{"mailTitle":"\u3010\u5f85\u529e\u3011\u60a8\u6709\u4e00\u4e2a\u3010%s\u3011\u5f85\u5904\u7406\uff0c\u8bf7\u8fdb\u5165\u7814\u53d1\u8fc7\u7a0b\u7ba1\u7406\u5e73\u53f0\u5904\u7406","variables":["\u5185\u90e8\u9879\u76ee\u5468\u62a5"],"mailContent":"<span style=\"font-weight:700;\">\u8bf7\u8fdb\u5165\u3010\u5730\u76d8\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5f85\u5904\u7406\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5ba1\u6279\u3011\u6216\u3010\u5e74\u5ea6\u8ba1\u5212\u3011\u5904\u7406<\/span><span style=\"color:#E53333;\"><span style=\"font-weight:700;\">\u5185\u90e8\u9879\u76ee\u5468\u62a5<\/span><\/span>"}');


CREATE TABLE `zt_closingitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL COMMENT '项目ID',
  `projectType` varchar(30) NOT NULL COMMENT '项目类型',
  `isAssembly` tinyint(2) NOT NULL COMMENT '是否形成公共技术组件 1-是 2-否',
  `assemblyNum` mediumint(9) NOT NULL COMMENT '形成公共组件个数',
  `assemblyAdvise` tinyint(2) NOT NULL COMMENT '公共技术组件改进意见 1-有 2-无',
  `toolsUsage` tinyint(2) NOT NULL COMMENT '测试工具使用情况 1-有 2-无',
  `toolsAdvise` tinyint(2) NOT NULL COMMENT '测试工具改进意见 1-有 2-无',
  `osspAdvise` tinyint(2) NOT NULL COMMENT '对OSSP过程改进建议 1-有 2-无',
  `platformAdvise` tinyint(2) NOT NULL COMMENT '对研发过程平台改进建议 1-有 2-无',
  `adviseChecklist` mediumint(9) DEFAULT NULL COMMENT '建议提交组织样例库清单  1-有 2-无',
  `realPoints` mediumint(9) NOT NULL COMMENT '项目实际功能点数',
  `demandAdvise` char(30) NOT NULL COMMENT '需求单位反馈意见',
  `files` varchar(255) DEFAULT '' COMMENT '附件',
  `constructionAdvise` char(30) NOT NULL COMMENT '承建单位或科技司反馈意见',
  `achievementNum` mediumint(9) DEFAULT 0 COMMENT '实际成果个数',
  `planNum` mediumint(9) DEFAULT 0 COMMENT '基准计划成果个数',
  `outPlanNum` mediumint(9) DEFAULT 0 COMMENT '计划外成果个数',
  `assemblyInfo` text COMMENT '公共组件 json',
  `toolsInfo` text COMMENT '测试工具使用情况 json',
  `status` varchar(50) DEFAULT '' COMMENT '状态',
  `dealuser` varchar(128) DEFAULT '' COMMENT '待处理人',
  `createdBy` char(30) NOT NULL COMMENT '由谁创建',
  `createdDate` datetime NOT NULL COMMENT '创建日期',
  `deleted` varchar(5) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `zt_closingitem` ADD `reviewStage` varchar(8) DEFAULT '' COMMENT '当前审批阶段';
ALTER TABLE `zt_closingitem` ADD `version` varchar(8) DEFAULT '' COMMENT '当前审批版本';
ALTER TABLE `zt_closingitem` ADD `knowledgeInfo` text COMMENT '知识库组织样例库清单 json';

CREATE TABLE `zt_closingadvise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL COMMENT '项目ID',
  `dept` int(5) NOT NULL COMMENT '部门ID',
  `dealuser` varchar(128) DEFAULT '' COMMENT '待处理人',
  `advise` text COMMENT '建议',
  `submitFileName` varchar(100) DEFAULT '' COMMENT '提交文档名称',
  `submitReason` varchar(100) DEFAULT '' COMMENT '提交理由',
  `versionCodeOSSP` varchar(100) DEFAULT '' COMMENT '对应OSSP版本号',
  `createdBy` char(30) NOT NULL COMMENT '由谁创建',
  `createdDate` datetime NOT NULL COMMENT '创建日期',
  `deleted` varchar(5) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `zt_closingadvise` ADD `itemId` int(11) NOT NULL COMMENT '项目结项ID';
ALTER TABLE `zt_closingadvise` ADD `status` varchar(50) DEFAULT '' COMMENT '状态';
ALTER TABLE `zt_closingadvise` ADD `comment` text COMMENT '处理意见备注';

ALTER TABLE `zt_closingadvise` ADD `source` int(5) NOT NULL COMMENT '来源';


-- ----------------------------
-- 迭代26 2023-05-30  jinzhuliang 周报数据库改造
-- ----------------------------
ALTER TABLE `zt_projectweeklyreport`
    MODIFY COLUMN `deleted` tinyint(4) UNSIGNED NULL DEFAULT NULL,
    ADD COLUMN `isImportant` varchar(5) NOT NULL DEFAULT '' COMMENT '是否重点项目' ,
    ADD COLUMN `relationRequirement` varchar(1000) NOT NULL DEFAULT '' COMMENT '关联需求任务',
    ADD COLUMN `weeknum` int(10) NOT NULL COMMENT '周序号',
    ADD COLUMN `projectStage` varchar(30) NOT NULL DEFAULT '' COMMENT '项目状态',
    ADD COLUMN `mileDelayNum` int(10) NOT NULL DEFAULT 0 COMMENT '项目里程碑延期次数',
    ADD COLUMN `mileDelayMark` text NULL COMMENT '里程碑延期说明',
    ADD COLUMN `projectProgressMark` text NULL COMMENT '项目进展描述',
    ADD COLUMN `projectTransDesc` text NULL COMMENT '项目移交情况说明',
    ADD COLUMN `projectAbnormalDesc` text NULL COMMENT '项目异常情况',
    ADD COLUMN `nextWeekplan` text NULL COMMENT '下周工作计划',
    ADD COLUMN `projectplanYear` varchar(20) NOT NULL DEFAULT '' COMMENT '对应年度计划',
    ADD COLUMN `produceStatus` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '内部周报状态 0未确认，1组织级QA确认',
    ADD COLUMN `isGenerate` int(11) NOT NULL DEFAULT 0 COMMENT '周报是否生成',
    ADD COLUMN `outPlanId` varchar(255) NOT NULL DEFAULT '' COMMENT '外部年度计划id串',
    ADD COLUMN `qa` varchar(255) NOT NULL DEFAULT '' COMMENT 'QA';

CREATE TABLE `zt_projectweeklyreport_insidemile` (
     `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
     `weekreportID` int(10) unsigned NOT NULL COMMENT '周报id',
     `projectID` int(10) unsigned NOT NULL COMMENT '项目id',
     `insideMileStage` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '里程碑所属阶段',
     `insideMileName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '里程碑名称',
     `insideMilePreDate` date DEFAULT NULL COMMENT '计划完成时间',
     `insideMileRealDate` date DEFAULT NULL COMMENT '实际完成时间',
     `insideMileMark` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '完成情况说明',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='周报内部里程碑表';

CREATE TABLE `zt_projectweeklyreport_medium` (
     `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
     `weekreportID` int(10) unsigned NOT NULL COMMENT '周报id',
     `projectID` int(10) unsigned NOT NULL COMMENT '项目id',
     `mediumName` varchar(100) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '介质名称',
     `preMediumPublishDate` date DEFAULT NULL COMMENT '拟发布时间',
     `preMediumOnlineDate` date DEFAULT NULL COMMENT '拟上线时间',
     `realMediumPublishDate` date DEFAULT NULL COMMENT '实际发布时间',
     `realMediumOnlineDate` date DEFAULT NULL COMMENT '实际上线时间',
     `mediumOutsideplanTask` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '所属外部建设任务',
     `mediumRequirement` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '产品实现需求任务',
     `mediumMark` varchar(1000) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '产品实现需求补充',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='周报介质信息表';

CREATE TABLE `zt_projectweeklyreport_outmile` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `weekreportID` int(10) unsigned NOT NULL COMMENT '周报id',
      `projectID` int(10) unsigned NOT NULL COMMENT '项目id',
      `outMileStageName` varchar(100) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '外部阶段名称',
      `outMileName` varchar(100) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '里程碑名称',
      `outMilePreDate` date DEFAULT NULL COMMENT '计划完成时间',
      `outMileRealDate` date DEFAULT NULL COMMENT '实际完成时间',
      `outMileMark` varchar(1000) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '完成情况说明',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='周报外部里程碑表';

CREATE TABLE `zt_projectweeklyreport_risk` (
   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
   `weekreportID` int(10) unsigned NOT NULL COMMENT '周报id',
   `projectID` int(10) unsigned NOT NULL COMMENT '项目id',
   `reportRiskMark` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '风险描述',
   `reportRiskStatus` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '风险状态',
   `reportRiskStrategy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '应对策略',
   `reportRiskPrevention` text COLLATE utf8mb4_bin COMMENT '预防措施',
   `reportRiskRemedy` text COLLATE utf8mb4_bin COMMENT '应急措施',
   `reportRiskResolution` text COLLATE utf8mb4_bin COMMENT '解决措施',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='周报项目风险信息';

CREATE TABLE `zt_outsideprojectweeklyreport` (
     `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
     `outProjectID` int(10) unsigned NOT NULL COMMENT '外部计划项目id',
     `outsideProjectName` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT 'CBP项目名称',
     `outsideProjectCode` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT 'CBP项目编号',
     `outsideProjectSubProject` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '包含研发子项',
     `relationInsideProject` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '关联内部项目',
     `outweeknum` int(11) NOT NULL COMMENT '周序号',
     `outreportStartDate` date NOT NULL COMMENT '周报时间开始',
     `outreportEndDate` date NOT NULL COMMENT '周报时间结束',
     `outprojectStatus` varchar(20) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '外部项目状态',
     `outOverallProgress` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '整体进展',
     `outProjectTransferMark` text COLLATE utf8mb4_bin COMMENT '项目移交情况说明',
     `outProjectAbnormal` text COLLATE utf8mb4_bin COMMENT '项目异常情况',
     `outNextWeekplan` text COLLATE utf8mb4_bin COMMENT '下周工作计划',
     `outSyncStatus` int(11) NOT NULL DEFAULT '0' COMMENT '同步状态0未同步,1同步成功,2同步失败',
     `outSyncDesc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '外部同步提示',
     `outSyncTime` datetime DEFAULT NULL COMMENT '同步时间',
     `outFeedbackTime` datetime DEFAULT NULL COMMENT '外部反馈时间',
     `outFeedbackUser` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '外部反馈人',
     `outFeedbackView` text COLLATE utf8mb4_bin COMMENT '外部反馈意见',
     `outFeedbackMark` text COLLATE utf8mb4_bin COMMENT '反馈说明',
     `outOperatingRemarks` text COLLATE utf8mb4_bin COMMENT '操作备注',
     `createTime` datetime DEFAULT NULL COMMENT '创建时间',
     `updateTime` datetime DEFAULT NULL COMMENT '更新时间',
     `createdBy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '创建人',
     `editedBy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '最后编辑人',
     `innerReportId` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内部周报id',
     `outmediuListInfo` text COLLATE utf8mb4_bin COMMENT '介质信息',
     `outmileListInfo` text COLLATE utf8mb4_bin COMMENT '里程碑信息',
     `deleted` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除 0 正常,1删除',
     `outriskListInfo` text COLLATE utf8mb4_bin COMMENT '风险信息',
     `insideProjectStage` text COLLATE utf8mb4_bin COMMENT '内部项目的项目阶段',
     `waitpush` tinyint(3) NOT NULL DEFAULT '0' COMMENT '等待推送 0 否 1是',
     `innerReportBaseInfo` text COLLATE utf8mb4_bin COMMENT '内部周报基本信息',
     `iscbp` tinyint(3) unsigned NOT NULL COMMENT '是否cbp项目 0否，1 是',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `zt_outsideprojectweeklyreport_queue` (
   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
   `outplanID` int(11) NOT NULL COMMENT '外部年度计划id,即外部项目',
   `weeknum` int(10) unsigned NOT NULL COMMENT '周序号',
   `outreportStartDate` datetime NOT NULL COMMENT '周报开始时间',
   `outreportEndDate` datetime NOT NULL COMMENT '周报结束时间',
   `isgeneration` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否等待生成 0 等待生成;1已生成',
   `createTime` datetime DEFAULT NULL COMMENT '创建时间',
   `updateTime` datetime DEFAULT NULL COMMENT '更新时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='外部周报待生成队列';


ALTER TABLE `zt_projectweeklyreport` ADD COLUMN `planID` int(0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '年度计划id';

ALTER TABLE `zt_demand`
    ADD COLUMN `secureStatusLinkage` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '解除状态联动' AFTER `application`;

-- ----------------------------
-- 迭代26 2023-06-01  师旭阳 历史数据处理
-- ----------------------------
update `zt_secondorder` set `status` = 'closed' where `status` = 'solved';

-- ----------------------------
-- 迭代26 2023-06-02  wangshusen 清总同步失败流转状态添加标识
-- ----------------------------
update `zt_consumed` set `extra` = 'requirementFeedBack' where `objectType` = 'requirement' and `after`='syncfail';

-- ----------------------------

-- 迭代26 2023-06-06  刘胜杰 记录用户行为ip
-- ----------------------------
ALTER TABLE `zt_action` ADD COLUMN `ip` char(15) DEFAULT '' COMMENT '用户IP';

-- 需求收集2338 shixuyang 推送生产变更单内部状态
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'modifycnccstateUrl', '');


-- ----------------------------
-- 迭代26 2023-06-06  项样 项目结项 实际功能点数字段整数改浮点型
-- ----------------------------
ALTER TABLE `zt_closingitem` MODIFY `realPoints` FLOAT;

-- ----------------------------
-- 迭代26 2023-06-08  wangshusen 需求意向、需求任务、需求条目内部数据
-- ----------------------------
update `zt_opinion` set `sourceOpinion` = '2' where `id` in (235,369,397,392,100,488,449,491,512,425,467);
update `zt_requirement` set `sourceRequirement` = '2' where `id` in (854,1160,1214,1215,1216,1217,1196,1197,699,700,701,702,703,1223,1224,1367,1312,1368,1369,1402,1403,1250,1334);
update `zt_demand` set `sourceDemand` = '2' where `id` in (818,1311,1452,1453,1454,1455,1399,1400,1467,1468,1469,1470,1471,1542,1543,1544,1545,1546,1658,1735,1736,1839,1923,1924,1925,1926,1927,1928,2082,2083,2084,2085,2086);
ALTER TABLE `zt_requirement` ADD COLUMN `feekBackStartTime` datetime DEFAULT NULL COMMENT '反馈开始时间';
ALTER TABLE `zt_requirement` ADD COLUMN `feekBackEndTimeInside` datetime DEFAULT NULL COMMENT '内部反馈截止时间';
ALTER TABLE `zt_requirement` ADD COLUMN `feekBackEndTimeOutSide` datetime DEFAULT NULL COMMENT '外部反馈截止时间';

-- ----------------------------
-- 迭代26 2023-06-12 刘胜杰 需求意向、需求任务、需求条目增加索引
-- ----------------------------
CREATE INDEX idx_requirementID_opinionID ON `zt_demand` (`requirementID`,`opinionID`);
CREATE INDEX idx_createdDate ON `zt_opinion` (`createdDate`);
CREATE INDEX idx_opinion ON `zt_requirement` (`opinion`);

update zt_problem set ifOverDate  = (case when unix_timestamp(now()) > unix_timestamp(feedbackExpireTime) then '1' else '0' end)  where IssueId  !='';

ALTER TABLE `zt_sectransfer` ADD `openFile` varchar(32) DEFAULT NULL COMMENT '是否需要查询文件列表：true-是；false-否';