set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------------------------------------------
-- 迭代27 2023-06-19  wangshusen 外部需求意向新增【需求最新变更时间】 外部需求任务新增【任务最新变更时间】
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_opinion     ADD COLUMN `lastChangeTime` datetime DEFAULT NULL COMMENT '需求最新变更时间' AFTER `receiveDate`;
ALTER TABLE zt_requirement ADD COLUMN `lastChangeTime` datetime DEFAULT NULL COMMENT '任务求最新变更时间' AFTER `acceptTime`;

-- ---------------------------------------------------------------------
-- 迭代27 2023-06-20  wangshusen 内部自建需求意向/任务主表增加变更次数和进行中状态
-- ---------------------------------------------------------------------
ALTER TABLE zt_opinion     ADD COLUMN `opinionChangeTimes`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '外部需求意向自建数据变更次数';
ALTER TABLE zt_opinion     ADD COLUMN `opinionChangeStatus` tinyint(3) NOT NULL DEFAULT 0 COMMENT '外部需求意向自建数据变更单状态 1：完成 2：变更进行中 3:已退回';
ALTER TABLE zt_requirement ADD COLUMN `requirementChangeTimes`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '外部需求任务自建数据变更次数';
ALTER TABLE zt_requirement ADD COLUMN `requirementChangeStatus` tinyint(3) NOT NULL DEFAULT 0 COMMENT '外部需求任务自建数据变更单状态 1：完成 2：变更进行中 3:已退回';

-- -------------------------------------------------------
-- 迭代27 2023-06-20  wangshusen 内部自建需求意向/任务变更数据表
-- -------------------------------------------------------
CREATE TABLE `zt_opinionchange` (
                                    `id`                 bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                    `opinionID`          int(10) unsigned NOT NULL COMMENT '需求意向id',
                                    `changeCode`         varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更单号',
                                    `alteration`         varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更事项',
                                    `opinionTitle`       varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '需求意向主题',
                                    `changeTitle`        varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更后-需求意向主题',
                                    `opinionBackground`  text         COLLATE utf8mb4_bin COMMENT '需求意向背景',
                                    `changeBackground`   text         COLLATE utf8mb4_bin COMMENT '变更后-需求意向背景',
                                    `opinionOverview`    text         COLLATE utf8mb4_bin COMMENT '需求意向概述',
                                    `changeOverview`     text         COLLATE utf8mb4_bin COMMENT '变更后-需求意向概述',
                                    `opinionDeadline`    datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '期望完成时间',
                                    `changeDeadline`     datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '变更后-期望完成时间',
                                    `opinionFile`        varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '附件',
                                    `changeFile`         varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更后-附件',
                                    `changeReason`       text         COLLATE utf8mb4_bin COMMENT '变更原因',
                                    `reportLeader`       tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1 COMMENT '上报分管领导 1：不上报 2：上报',
                                    `nextDealUser`       varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '下一节点处理人',
                                    `nextDealNode`       varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更单下一处理节点',
                                    `po`                 varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '产品经理',
                                    `deptLeader`         varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '部门管理层',
                                    `status`             varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '状态',
                                    `version`            tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1  COMMENT '版本',
                                    `revokeRemark`       text         COLLATE utf8mb4_bin COMMENT '撤销原因',
                                    `revokeDate`         datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '撤销时间',
                                    `createdBy`          varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '发起人',
                                    `createdDate`        datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '变更单创建时间',
                                    `delete`             tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1 COMMENT '删除 1：未删除 2：已删除',
                                    PRIMARY KEY (`id`),
                                    KEY `opinionID` (`opinionID`) USING BTREE,
                                    KEY `changeCode` (`changeCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------
-- 迭代27 2023-06-20  wangshusen 内部自建需求任务变更数据表
-- --------------------------------------------------
CREATE TABLE `zt_requirementchangeoutside` (
                                               `id`                    bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                               `requirementID`         int(10) unsigned NOT NULL COMMENT '需求意向id',
                                               `changeCode`            varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更单号',
                                               `alteration`            varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更事项',
                                               `requirementTitle`      varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '需求任务主题',
                                               `changeTitle`           varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更后-需求任务主题',
                                               `requirementOverview`   text         COLLATE utf8mb4_bin COMMENT '需求任务概述',
                                               `changeOverview`        text         COLLATE utf8mb4_bin COMMENT '变更后-需求任务概述',
                                               `requirementDeadline`   datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '期望完成时间',
                                               `changeDeadline`        datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '变更后-期望完成时间',
                                               `requirementFile`       varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '附件',
                                               `changeFile`            varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更后-附件',
                                               `changeReason`          text         COLLATE utf8mb4_bin COMMENT '变更原因',
                                               `reportLeader`          tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1 COMMENT '上报分管领导 1：不上报 2：上报',
                                               `nextDealUser`          varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '下一节点处理人',
                                               `nextDealNode`          varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '变更单下一处理节点',
                                               `po`                    varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '产品经理',
                                               `deptLeader`            varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '部门管理层',
                                               `status`                varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '状态',
                                               `version`               tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1  COMMENT '版本',
                                               `revokeRemark`          text         COLLATE utf8mb4_bin COMMENT '撤销原因',
                                               `revokeDate`            datetime     COLLATE utf8mb4_bin DEFAULT NULL COMMENT '撤销时间',
                                               `createdBy`             varchar(255) COLLATE utf8mb4_bin NOT NULL  COMMENT '发起人',
                                               `createdDate`           datetime     COLLATE utf8mb4_bin DEFAULT NULL  COMMENT '变更单创建时间',
                                               `delete`                tinyint(3)   COLLATE utf8mb4_bin NOT NULL DEFAULT 1 COMMENT '删除 1：未删除 2：已删除',
                                               PRIMARY KEY (`id`),
                                               KEY `requirementID` (`requirementID`) USING BTREE,
                                               KEY `changeCode` (`changeCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ---------------------------------------------------------------------
-- 迭代27 2023-06-20  wangshusen 需求意向/任务新增变更单待处理人字段 用于外部需求意向自建数据变更流程
-- ---------------------------------------------------------------------
ALTER TABLE zt_opinion ADD COLUMN `changeDealUser` varchar(30) DEFAULT NULL COMMENT '变更单待处理人';
ALTER TABLE zt_requirement ADD COLUMN `changeDealUser` varchar(30) DEFAULT NULL COMMENT '变更单待处理人';
-- ----------------------------
-- 迭代27 2023-06-26  shixuyang 需求池延期字段
-- ----------------------------
ALTER TABLE `zt_demand` ADD COLUMN `delayResolutionDate` datetime DEFAULT NULL COMMENT '延期解决日期';
ALTER TABLE `zt_demand` ADD COLUMN `unitAgree` varchar(2) DEFAULT NULL COMMENT '提出单位是否同意:1-是；2-否';
ALTER TABLE `zt_demand` ADD COLUMN `delayReason` text DEFAULT NULL COMMENT '延期原因';
ALTER TABLE `zt_demand` ADD COLUMN `delayStatus` varchar(64) DEFAULT NULL COMMENT '延期状态';
ALTER TABLE `zt_demand` ADD COLUMN `delayVersion` int DEFAULT NULL COMMENT '延期审批版本';
ALTER TABLE `zt_demand` ADD COLUMN `delayStage` int DEFAULT NULL COMMENT '延期审批阶段';
ALTER TABLE `zt_demand` ADD COLUMN `delayDealUser` varchar(255) DEFAULT NULL COMMENT '延期审批待处理人';
ALTER TABLE `zt_demand` ADD COLUMN `delayUser` varchar(255) DEFAULT NULL COMMENT '由谁延期';
ALTER TABLE `zt_demand` ADD COLUMN `delayDate` datetime DEFAULT NULL COMMENT '延期时间';
ALTER TABLE `zt_demand` ADD COLUMN `originalResolutionDate` datetime DEFAULT NULL COMMENT '原计划解决日期';

-- ----------------------------
-- 年度计划修改
-- ----------------------------
ALTER TABLE `zt_projectplanchange` ADD COLUMN `isreview` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否走审批流 1:走;2:无需审核';
ALTER TABLE `zt_projectplan` MODIFY COLUMN `outsideProject` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';
ALTER TABLE `zt_projectplan` ADD COLUMN `changeReview` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否走审批流 1:走;2:无需审核';
ALTER TABLE `zt_reviewer` ADD COLUMN `reviewerType` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型   1评审 2指派';


-- ----------------------------
-- 新增OSSP变更申请表：
-- ----------------------------
CREATE TABLE `zt_osspchange` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `code` varchar(30) NOT NULL COMMENT '序号',
                                 `proposer` varchar(30) NOT NULL COMMENT '变更申请人',
                                 `title` varchar(1500) NOT NULL COMMENT '变更主题',
                                 `background` text COMMENT '变更背景',
                                 `content` text COMMENT '变更内容',
                                 `files` varchar(255) DEFAULT '' COMMENT '附件',
                                 `systemProcess` varchar(10) DEFAULT '' COMMENT '所属体系过程',
                                 `systemVersion` varchar(10) DEFAULT '' COMMENT '所属体系版本',
                                 `advise` text COMMENT '处理建议',
                                 `result` varchar(10) DEFAULT '' COMMENT '处理结果',
                                 `changeNotice` varchar(10) DEFAULT '' COMMENT '变更公告',
                                 `systemDept` mediumint(9) DEFAULT 0 COMMENT '体系过程归口部门',
                                 `systemManager` varchar(30) DEFAULT '' COMMENT '体系过程归口部门负责人',
                                 `QMDmanager` varchar(30) DEFAULT '' COMMENT '质量部部门负责人',
                                 `fileInfo` text COMMENT '文件信息',
                                 `closeResult` varchar(10) DEFAULT '' COMMENT '关闭处理结果',
                                 `closeComment` varchar(10) DEFAULT '' COMMENT '关闭备注说明',
                                 `notifyPerson` varchar(255) DEFAULT '' COMMENT '通知人员',
                                 `createdBy` char(30) NOT NULL COMMENT '由谁创建',
                                 `createdDate` datetime NOT NULL COMMENT '创建日期',
                                 `deleted` varchar(5) DEFAULT '0',
                                 `status` varchar(50) DEFAULT '' COMMENT '状态',
                                 `dealuser` varchar(128) DEFAULT '' COMMENT '待处理人',
                                 `reviewStage` varchar(8) DEFAULT '' COMMENT '当前审批阶段',
                                 `version` varchar(8) DEFAULT '' COMMENT '当前审批版本',
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8;
ALTER TABLE `zt_change` ADD COLUMN `category` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更分类' ;

ALTER TABLE `zt_osspchange` ADD COLUMN `submitBy` varchar(30) DEFAULT '' COMMENT '提交人';
ALTER TABLE `zt_osspchange` ADD COLUMN `submitDate` datetime COMMENT '提交时间';
ALTER TABLE `zt_osspchange` ADD COLUMN `editedBy` varchar(30) DEFAULT '' COMMENT '编辑人';
ALTER TABLE `zt_osspchange` ADD COLUMN `editedDate` datetime  COMMENT '编辑时间';
ALTER TABLE `zt_osspchange` ADD COLUMN `confirmBy` varchar(30) DEFAULT '' COMMENT '确认人';
ALTER TABLE `zt_osspchange` ADD COLUMN `confirmDate` datetime  COMMENT '确认时间';


ALTER TABLE `zt_modify` ADD COLUMN `abnormalCode` varchar(60) DEFAULT '' COMMENT '关联的异常变更单';
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `abnormalCode` varchar(60) DEFAULT '' COMMENT '关联的异常变更单';


-- ----------------------------
-- 迭代27 2023-07-05  shixuyang 用户表增加人员类型字段
-- ----------------------------
ALTER TABLE `zt_user` ADD COLUMN `staffType` varchar(64) DEFAULT NULL COMMENT '人员类型';
update `zt_user` set `staffType` = 'formal' where `account` not like 't\_%';
update `zt_user` set `staffType` = 'outsource' where `account` like 't\_%';
ALTER TABLE `zt_project` ADD COLUMN `planWorkload` varchar(64) DEFAULT NULL COMMENT '计划工作量';

ALTER TABLE `zt_osspchange` ADD COLUMN `lastReviewedBy` varchar(30) DEFAULT '' COMMENT '最近一次评审人';
ALTER TABLE `zt_osspchange` ADD COLUMN `lastReviewedDate` datetime  COMMENT '最近一次评审时间';
ALTER TABLE `zt_osspchange` ADD COLUMN `closedBy` varchar(30) DEFAULT '' COMMENT '关闭人';
ALTER TABLE `zt_osspchange` ADD COLUMN `closedDate` datetime  COMMENT '关闭时间';

-- ----------------------------
-- 迭代27 2023-07-12  liushengjie 延期审批表
-- ----------------------------
CREATE TABLE `zt_delay` (
                            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                            `type` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '关联类型',
                            `objectId` int(11) NOT NULL COMMENT '关联ID',
                            `originalResolutionDate` datetime NOT NULL COMMENT '原计划解决日期',
                            `delayResolutionDate` datetime NOT NULL COMMENT '延期解决日期',
                            `delayReason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '延期原因',
                            `delayStatus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '延期状态',
                            `delayVersion` int(11) NOT NULL COMMENT '延期审批版本',
                            `delayStage` int(11) DEFAULT NULL COMMENT '延期审批阶段',
                            `delayDealUser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '延期审批待处理人',
                            `delayUser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '延期申请单创建人',
                            `delayDate` datetime NOT NULL COMMENT '延期申请单创建日期',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `type_objectId` (`type`,`objectId`) USING BTREE COMMENT '类型+ID'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- -----------------------------------------------------------------------------------------
-- 迭代27 2023-7-28  wangshusen 需求任务新增【部门审核首次通过时间】、【产创审核首次通过时间】
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_requirement ADD COLUMN `deptPassTime` datetime DEFAULT NULL COMMENT '部门审核首次通过时间';
ALTER TABLE zt_requirement ADD COLUMN `innovationPassTime` datetime DEFAULT NULL COMMENT '产创审核首次通过时间';
-- -----------------------------------------------------------------------------------------
-- 迭代27 2023-8-3  wangshusen 需求任务、意向新增变更次数和最新变更时间历史数据修改
-- -----------------------------------------------------------------------------------------
UPDATE zt_requirement SET `requirementChangeTimes` = 1,`lastChangeTime` = '2023-07-05 11:17:21' WHERE id = 1274;
UPDATE zt_opinion SET `opinionChangeTimes` = 7,`lastChangeTime` = '2022-07-25 15:57:24' WHERE id = 184;
UPDATE zt_opinion SET `opinionChangeTimes` = 4,`lastChangeTime` = '2022-07-14 11:42:54' WHERE id = 159;
UPDATE zt_opinion SET `opinionChangeTimes` = 3,`lastChangeTime` = '2023-05-06 15:04:35' WHERE id = 550;
UPDATE zt_opinion SET `opinionChangeTimes` = 3,`lastChangeTime` = '2023-05-10 13:36:04' WHERE id = 553;
UPDATE zt_opinion SET `opinionChangeTimes` = 2,`lastChangeTime` = '2022-07-14 09:27:33' WHERE id = 139;
UPDATE zt_opinion SET `opinionChangeTimes` = 2,`lastChangeTime` = '2022-06-17 14:55:26' WHERE id = 149;
UPDATE zt_opinion SET `opinionChangeTimes` = 2,`lastChangeTime` = '2022-07-14 15:25:41' WHERE id = 181;
UPDATE zt_opinion SET `opinionChangeTimes` = 2,`lastChangeTime` = '2023-03-22 14:45:44' WHERE id = 500;
UPDATE zt_opinion SET `opinionChangeTimes` = 2,`lastChangeTime` = '2023-06-05 10:54:19' WHERE id = 570;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2021-12-28 21:52:48' WHERE id = 99;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2022-07-12 14:58:56' WHERE id = 141;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-02-08 17:15:27' WHERE id = 163;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2022-08-03 14:53:54' WHERE id = 176;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2022-11-09 13:36:24' WHERE id = 365;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-01-19 08:59:32' WHERE id = 428;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-02-08 17:10:24' WHERE id = 377;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-03-10 18:23:49' WHERE id = 410;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-04-07 09:11:00' WHERE id = 514;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-05-06 15:52:21' WHERE id = 549;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-05-10 13:34:23' WHERE id = 555;
UPDATE zt_opinion SET `opinionChangeTimes` = 1,`lastChangeTime` = '2023-07-06 10:41:52' WHERE id = 613;

-- -----------------------------------------------------------------------------------------
-- 迭代27 2023-8-3  wangshusen 需求任务内外部反馈超时起止时间取值变更，将历史数据初始化
-- -----------------------------------------------------------------------------------------
UPDATE zt_requirement SET `ifOverDate` = 100,`ifOverTimeOutSide` = 100 WHERE createdBy = 'guestcn';
