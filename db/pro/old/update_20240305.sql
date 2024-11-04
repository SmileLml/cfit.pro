-- ----------------------------
-- 投产移交 迭代三十三 自定义配置 2023-12-27 wangjiurong
-- ----------------------------
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'levelList', '', '', '1', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'levelList', '1', '重大投产', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'levelList', '2', '一般投产', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '', '', '1', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '1', '新建系统', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '2', '信创改造', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '3', '应用功能升级', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '4', '架构改造（优化）', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '5', '系统整合', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'propertyList', '6', '上云改造', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'stageList', '1', '第一阶段', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'stageList', '2', '第二阶段', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'dataCenterList', '', '', '1', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'dataCenterList', '1', '北京德胜', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'dataCenterList', '2', '北京稻香湖', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'dataCenterList', '3', '北京右安门', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'dataCenterList', '4', '上海张江', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'putproduction', 'dataCenterList', '5', '无锡', '0', 0);

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'putproduction', '', 'syncFailList', 'admin');

-- ----------------------------
-- 迭代三十三 需求任务、意向增加变更前状态 2023-12-27 wangshusen
-- ----------------------------
ALTER TABLE `zt_requirement` ADD COLUMN `beforeStatus`  varchar(30)  DEFAULT '' COMMENT '变更前状态';
ALTER TABLE `zt_opinion`     ADD COLUMN `beforeStatus`  varchar(30)  DEFAULT '' COMMENT '变更前状态';

-- ----------------------------
-- 迭代三十三 shixuyang 2024-1-4 工作流和业务的关联表
-- ----------------------------
CREATE TABLE `zt_iwfp` (
                                    `id`                 bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                    `objectType`         varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '业务类型',
                                    `objectID`           varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '业务数据ID',
                                    `objectCode`           varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '业务数据编号',
                                    `processDefinitionKey`       varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '工作流流程key',
                                    `processDefinitionId`       varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '工作流流程Id',
                                    `processInstanceId`  varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '工作流实例Id',
                                    `status`             varchar(31) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '工作流实例Id的状态：running-正运行；closed-正常结束；pending-挂起',
                                    `nodeDealUser`       text COLLATE utf8mb4_bin DEFAULT NULL COMMENT '各个节点待处理人',
                                    `createdDate`        datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '创建时间',
                                    `createdBy`          varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '发起人',
                                    `updatedDate`        datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '变更时间',
                                    `updatedBy`          varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更人',
                                    `delete`             tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1 COMMENT '删除 1：未删除 2：已删除',
                                    PRIMARY KEY (`id`),
                                    KEY `objectType` (`objectType`) USING BTREE,
                                    KEY `objectID` (`objectID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
alter table zt_iwfp
    add dealUser varchar(256) null comment '待处理人';
alter table zt_iwfp
    add logList text null comment '审批记录';
alter table zt_iwfp
    add processTaskId varchar(256) null comment '任务id';
alter table zt_iwfp
    add assign text null comment '委派用户';
alter table zt_iwfp
    add processXmlTaskId varchar(256) null comment '流程状态任务id';
-- ----------------------------
-- 迭代三十三 shixuyang 2024-1-4 配置信息
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'startWorkFlowUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'getButtonListUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'completeTaskWithClaimUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'getToDoTaskListUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'listApproveLogUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'turnBackUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'getFreeJumpNodeListUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'freeJumpUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'withDrawUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'addSignTaskUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'changeAssigneekUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'queryProcessTrackImageUrl', '');

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'jxPutproductionKey', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'jxPutproductionId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'tenantId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'AuthorizationKey', '');
-- ----------------------------
-- 迭代三十三 项目加报工开关 wangyongliang
-- ----------------------------
ALTER TABLE `zt_project`
ADD COLUMN `switch`  MEDIUMINT DEFAULT 1 NOT NULL COMMENT '报工开关： 1 开， 2 关',
ADD COLUMN `switchUser` varchar(250) default null COMMENT '报工开关维护人';
-- ----------------------------
-- 迭代三十三 更新已关闭和已挂起项目报工开关 wangyongliang
-- ----------------------------
update zt_project set switch = '2',switchUser ='admin' where `type` ='project' and status in('suspended','closed') and deleted ='0' ;

-- ----------------------------
-- 迭代三十三 金信投产待办邮件模板配置 wangshusen 2024-1-23
-- ----------------------------
INSERT INTO zt_config
(`owner`, `module`, `section`, `key`, `value`)
VALUES('system', 'common', 'global', 'setPutproductionMail', '{"mailTitle":"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406","variables":["\\u6295\\u4ea7\\u79fb\\u4ea4\\u5355"],"mailContent":"<span style=\\"font-weight:700;\\">\\u8bf7\\u8fdb\\u5165\\u3010\\u4ea4\\u4ed8\\u7ba1\\u7406\\u3011<\\/span><span><span style=\\"font-weight:700;\\">-<\\/span><\\/span><span style=\\"font-weight:700;\\">\\u3010\\u91d1\\u4fe1\\u4ea4\\u4ed8\\u3011- <span style=\\"font-weight:700;\\">\\u3010\\u6295\\u4ea7\\u79fb\\u4ea4\\u3011<\\/span>\\u67e5\\u770b<\\/span><span><span style=\\"font-weight:700;\\"><\\/span><\\/span><span style=\\"font-weight:700;\\">\\uff0c\\u6458\\u8981\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/span>"}');

-- ----------------------------
-- 迭代三十三 制版新增字段 wangyongliang 2024-1-25
-- ----------------------------
ALTER TABLE `zt_build`
ADD COLUMN `testRelevantUser`  varchar(250) default null COMMENT '测试相关配合人员',
ADD COLUMN `verifyRelevantUser` varchar(250) default null COMMENT '验证相关配合人员',
ADD COLUMN `actualVerifyUser` varchar(250) default null COMMENT '实际验证人员',
ADD COLUMN `actualVerifyDate` datetime  default null COMMENT '验证完成时间',
ADD COLUMN `verifyRejectBack` int(10) default 0 COMMENT '审批不通过退回系统部次数',
ADD COLUMN `updateFileDate` datetime DEFAULT NULL COMMENT '更新附件时间';
-- ----------------------------
-- 迭代三十三 年度计划相关sql START jinzhuliang 2024-1-29
-- ----------------------------
-- 外部年度计划新增字段
ALTER TABLE `zt_outsideplan` ADD COLUMN `apptype` varchar(50) DEFAULT '' COMMENT '系统类型',
                ADD COLUMN `projectinitplan` text DEFAULT null COMMENT '项目立项计划',
                ADD COLUMN `uatplanfinishtime` text DEFAULT null COMMENT 'UAT测试计划完成时间',
                ADD COLUMN `materialplanonlinetime` text DEFAULT null COMMENT '上线材料计划提交时间',
                ADD COLUMN `planonlinetime` text DEFAULT null COMMENT '项目上线时间',
                ADD COLUMN `projectisdelay` varchar(3) DEFAULT '' COMMENT '项目是否延迟  1否，2是',
                ADD COLUMN `projectisdelaydesc` text DEFAULT null COMMENT '项目是否延迟描述',
                ADD COLUMN `projectischange` varchar(3) DEFAULT '' COMMENT '项目是否变更  1否，2是',
                ADD COLUMN `projectischangedesc` text DEFAULT null COMMENT '项目是否变更描述';
-- 外部计划任务任务开始时间改为允许为空
ALTER TABLE zt_outsideplantasks MODIFY COLUMN subTaskBegin date NULL COMMENT '建设任务计划开始时间';

-- 外部项目计划-》项目是否变更 语言项配置
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','outsideplan','projectischangeList','','','1',0),
                                                                             ('zh-cn','outsideplan','projectischangeList','1','否','0',0),
                                                                             ('zh-cn','outsideplan','projectischangeList','2','是','0',0);
-- 外部项目计划-》系统类型 语言项配置
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','outsideplan','apptypeList','','','1',0),
                                                                             ('zh-cn','outsideplan','apptypeList','xtxxm','系统性项目','0',0),
                                                                             ('zh-cn','outsideplan','apptypeList','jgxm','机构项目','0',0);

-- 外部项目计划-》项目是否延迟 语言项配置
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','outsideplan','projectisdelayList','','','1',0),
                                                                             ('zh-cn','outsideplan','projectisdelayList','1','否','0',0),
                                                                             ('zh-cn','outsideplan','projectisdelayList','2','是','0',0);

-- 年度计划版本履历
CREATE TABLE zt_projectplan_action (
                                       `id` BIGINT(20) auto_increment NOT NULL,
                                       `planName` varchar(200) DEFAULT '' NOT NULL COMMENT '年度计划名称',
                                       `actionUser` varchar(100) DEFAULT '' NOT NULL COMMENT '操作人',
                                       `actionDay` varchar(20) DEFAULT '' NOT NULL COMMENT '创建日期精确到天',
                                       `planID` INT(10) NOT NULL COMMENT '年度计划ID',
                                       `status` varchar(100) DEFAULT '' NOT NULL COMMENT '流程状态',
                                       `snapshotVersion` varchar(100) DEFAULT '' NOT NULL COMMENT '快照标签',
                                       `fileUrl` varchar(200) DEFAULT '' NOT NULL COMMENT '快照下载地址',
                                       `deleted` int(10) DEFAULT 0 NOT NULL  COMMENT '是否删除 0 否 1删除',
                                       `createTime` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                       `updateTime` DATETIME DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP COMMENT '更新时间',
                                       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='年度计划版本履历';

-- ----------------------------
-- 迭代三十三 年度计划相关sql END jinzhuliang 2024-1-29
-- ----------------------------
-- ----------------------------
-- 迭代三十三 评审内部专家字段类型修改 wangjiurong 2024-1-30
-- ----------------------------
ALTER TABLE `zt_review`
MODIFY COLUMN `expert`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内部专家' AFTER `owner`;

-- ----------------------------
-- 迭代三十三 shixuyang 2024-1-31 金信生产变更单加所属外部项目
-- ----------------------------
ALTER TABLE `zt_modify` ADD COLUMN `outsidePlanId`  varchar(128) NULL COMMENT '所属外部项目';

-- ----------------------------
-- 迭代三十三 shixuyang 2024-1-31 金信投产表
-- ------------------------------
create table zt_putproduction
(
    id                     int(11) unsigned auto_increment
        primary key,
    code                   varchar(30)         default ''                    not null comment '投产单号',
    `desc`                 varchar(255)        default ''                    not null comment '摘要',
    inProjectIds           varchar(255)        default ''                    not null comment '内部项目ids',
    outsidePlanId          int                 default 0                     not null comment '关联外部项目id',
    app                    varchar(255)        default ''                    not null comment '投产系统名称',
    productId              varchar(255)        default ''                    not null comment '产品id 多个逗号分隔',
    demandId               varchar(50)         default ''                    not null comment '关联需求条目 多个逗号分隔',
    level                  tinyint(1) unsigned default 1                     not null comment '投产级别 1-重大投产 2-一般投产',
    property               varchar(30)         default ''                    not null comment '投产属性：多个逗号分隔',
    stage                  varchar(10)         default ''                    not null comment '投产阶段：多个逗号分隔',
    firstStagePid          int(11) unsigned    default 0                     not null comment '第一阶段移交单',
    dataCenter             varchar(30)         default ''                    not null comment '投产数据中心',
    isPutCentralCloud      tinyint(1) unsigned default 1                     not null comment '是否投产到央行云环境 1-否 2-是',
    fileUrlRevision        text                                              null comment '投产材料移交地址及Revision',
    isReview               tinyint(1) unsigned default 1                     not null comment '投产材料是否经过评审 1-否 2-是',
    reviewComment          text                                              null comment '投产材料评审意见',
    isBusinessCoopera      tinyint(1) unsigned default 1                     not null comment '是否需要业务配合 1-否 2-是',
    businessCooperaContent text                                              null comment '需要业务配合内容',
    isBusinessAffect       tinyint(1) unsigned default 1                     not null comment '投产期间是否有业务影响 1-否 2-是',
    businessAffect         text                                              null comment '给业务功能带来的影响',
    remark                 text                                              null comment '备注信息',
    version                smallint            default 1                     not null,
    status                 varchar(30)         default ''                    not null comment '当前状态',
    dealUser               varchar(255)        default ''                    not null comment '待处理人',
    cancelBy               varchar(30)         default ''                    not null comment '由谁取消',
    cancelDate             datetime            default '0000-00-00 00:00:00' not null comment '取消时间',
    cancelReason           varchar(255)        default ''                    not null comment '取消原因',
    lastVersion            smallint            default 0                     not null comment '操作取消前审批版本',
    lastStatus             varchar(30)         default ''                    not null comment '操作取消前状态',
    lastDealUser           varchar(255)        default ''                    not null comment '操作取消前待处理人',
    pushStatus             tinyint(1)          default 0                     not null comment '0 = 未推送 1 = 推送成功 2 = 推送失败 ',
    pushDate               datetime            default '0000-00-00 00:00:00' not null comment '推送时间',
    pushFailReason         varchar(255)        default ''                    not null comment '推送失败原因',
    pushFailTimes          tinyint(2)          default 0                     not null comment '推送失败的次数',
    externalId             varchar(30)         default ''                    not null comment '金信id',
    externalCode           varchar(30)         default ''                    not null comment '金信code',
    externalStatus         varchar(30)         default ''                    not null comment '外部单状态',
    isOnLine               varchar(50)         default ''                    not null comment '投产材料是否具备上线条件,金科回传',
    returnBy               varchar(30)         default ''                    not null comment '打回人,金科回传 ',
    returnTel              varchar(30)         default ''                    not null comment '打回人联系方式,金科回传 ',
    returnDate             datetime            default '0000-00-00 00:00:00' not null comment '退回时间',
    returnReason           text                                              null comment '打回原因',
    returnCount            tinyint(2)          default 0                     not null comment '退回次数',
    realStartTime          datetime            default '0000-00-00 00:00:00' not null comment '实际投产开始时间',
    realEndTime            datetime            default '0000-00-00 00:00:00' not null comment '实际投产结束时间',
    opResult               varchar(255)        default ''                    not null comment '投产实施结果 ',
    opFailReason           varchar(255)        default ''                    not null comment '投产失败原因 ',
    createdBy              varchar(30)         default ''                    not null comment '创建人',
    createdDept            int                 default 0                     not null comment '创建部门',
    createdDate            datetime            default '0000-00-00 00:00:00' not null comment '创建时间',
    editedBy               varchar(30)         default ''                    not null comment '编辑人',
    editedDate             datetime            default '0000-00-00 00:00:00' not null comment '编辑时间',
    lastDealDate           datetime            default '0000-00-00 00:00:00' not null comment '最新处理时间',
    deleted                tinyint             default 0                     not null comment '是否有效 -有效 1-无效',
    reviewerInfo           text                                              null comment '审批人员信息表',
    sftpPath               varchar(1023)       default ''                    not null comment 'sftpPath',
    remoteFileList         text                                              null comment 'sftp文件列表清单',
    releaseId              varchar(255)        default ''                    not null comment '关联版本id',
    workflowId             varchar(255)        default ''                    not null comment '工作流id',
    issubmit               varchar(10)         default ''                    not null comment '提交还是保存',
    openFile               varchar(8)                                        null comment '介质地址是否需要回显',
    jxfileId               text                                              null comment '金信文件id',
    putFileFailReason      varchar(256)                                      null comment '推送文件失败原因',
    implementedBy          varchar(255)                                      null comment '投产实施人',
    planStartTime          datetime                                          null comment '预计开始时间',
    planEndTime            datetime                                          null comment '预计结束时间',
    releaseSyncStatus      tinyint(1)          default 1                     not null comment '是否同步到发布状态 1未同步 2已同步'
)
    comment '金信投产' charset = utf8;

-- ----------------------------
-- 迭代三十三 产品经理自定义配置 2024-02-19
-- ----------------------------
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
('zh-cn','demand','productManagerList','shenlu','沈璐','0',0),
('zh-cn','demand','productManagerList','jiangyueli','姜月莉','0',0),
('zh-cn','demand','productManagerList','leiyuting','雷玉婷','0',0),
('zh-cn','demand','productManagerList','hujun','胡军','0',0),
('zh-cn','demand','productManagerList','wangyanlong','王彦龙','0',0),
('zh-cn','demand','productManagerList','pengjunmin','彭军民','0',0),
('zh-cn','demand','productManagerList','liyun','李慧','0',0),
('zh-cn','demand','productManagerList','qixiuyan','亓秀燕','0',0),
('zh-cn','demand','productManagerList','zhushumin','朱淑敏','0',0),
('zh-cn','demand','productManagerList','xinhaoyang','辛昊洋','0',0),
('zh-cn','demand','productManagerList','jinke','金科','0',0),
('zh-cn','demand','productManagerList','zhaorui','赵蕊','0',0),
('zh-cn','demand','productManagerList','yangmingyan','杨明妍','0',0),
('zh-cn','demand','productManagerList','caoyang','曹阳','0',0),
('zh-cn','demand','productManagerList','yinyaming','印雅萌','0',0);

-- ----------------------------
-- 迭代三十三 shixuyang 配置信息
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','api','nfsList','url','10.128.67.25','1');
grant update,select on zt_putproduction to 'media'@'%';
-- ----------------------------
-- 迭代三十三 liushengjie 配置信息
-- ----------------------------
grant update,select on zt_secondorder to 'media'@'%';
flush privileges;


-- ----------------------------
-- 迭代三十三 二线月报相关sql START jinzhuliang 2024-02-08
-- ----------------------------
-- 二线月报 表单快照字段
ALTER TABLE zt_whole_report ADD fileUrl2 varchar(255) DEFAULT '' NOT NULL COMMENT '表单快照地址';


-- 二线月报通用统计部门 统计环节使用
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','','','1',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','2','平台架构部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','5','研发一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','6','研发二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','7','研发三部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','8','研发四部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','9','研发五部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','10','研发六部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','12','系统部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','17','成都分公司','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','18','成都研发事业一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','19','成都研发事业二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','20','成都综合部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','26','天津分公司','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','27','天津综合保障部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','28','天津研发一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedDept','29','天津研发二部','0',0);


-- 二线月报通用合并统计部门 统计环节使用 (根据此配置，补齐缺少数据的部门)
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','','','1',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','2','平台架构部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','5','研发一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','6','研发二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','7','研发三部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','8','研发四部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','9','研发五部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','10','研发六部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','12','系统部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','17','成都分公司','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportNeedShowDept','26','天津分公司','0',0);




-- 二线月报排序展示部门  前台展示环节使用，不做补全(将统计表中的数据按照此配置进行顺序展示，可全量配置)
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','','','1',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','16','综合部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','1','产品创新部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','3','质量部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','2','平台架构部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','4','新技术实验室','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','5','研发一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','6','研发二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','7','研发三部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','8','研发四部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','9','研发五部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','10','研发六部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','12','系统部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','11','测试部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','17','成都分公司','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','18','成都研发事业一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','19','成都研发事业二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','20','成都综合部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','30','上海分公司','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','31','上海综合部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','32','上海技术管理部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','33','上海项目管理部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','34','上海信创实施部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','35','上海网络安全部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','36','上海平台研发部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','37','上海研发一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','38','上海研发二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','39','上海测试部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','40','上海创新部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','41','上海财务部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','26','天津分公司','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','27','天津综合保障部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','28','天津研发一部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','29','天津研发二部','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportOrderDept','-1','空','0',0);

-- 二线月报人月转换
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES ('zh-cn','secondmonthreport','monthReportWorkHours','workHours','21.75','1',0);

-- 二线月报 工作量统计的二线项目
INSERT INTO zt_lang (lang,module,`section`,`key`,value,`system`,`order`) VALUES
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','','','1',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1050','RD1_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1051','RD2_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1052','RD3_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1053','RD4_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1054','RD5_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1055','RD6_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1056','CDRD1_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1057','TED_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1058','SYD_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','1059','PAD_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','12292','TJRD1_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','12293','TJRD2_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','12294','CDRD2_二线管理','0',0),
                                                                             ('zh-cn','secondmonthreport','monthReportSecondLineProject','12539','产创_二线管理','0',0);

-- 将项目下的二线月报自定义快照人员改为二线月报下的配置
update zt_lang set `module`='secondmonthreport' where `lang`='zh-cn' and `module`='project' and `section`='monthReportCustomUser';


-- ----------------------------
-- 迭代三十三 二线月报相关sql END jinzhuliang 2024-02-08
-- ----------------------------

-- 清总生产变更单新增不能自动化部署字段
ALTER TABLE `zt_modifycncc`
ADD COLUMN `aadsReason`  text DEFAULT NULL COMMENT '不能自动化部署' AFTER `returnLog`;