set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------------------------------------------
-- 迭代三十 lang表新增排序字段
-- -----------------------------------------------------------------------------------------
ALTER TABLE `zt_lang` ADD COLUMN `order` int(5) DEFAULT '0' COMMENT '排序';

-- ----------------------------
-- 新增公共技术组件台账表：
-- ----------------------------
CREATE TABLE `zt_component_public_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `componentId` varchar(30) NOT NULL COMMENT '组件名称对应id',
  `componentVersion` varchar(30) NOT NULL COMMENT '组件版本',
  `projectDept` tinyint(5) NOT NULL COMMENT '项目所属部门',
  `projectName` int(10) NOT NULL COMMENT '项目id',
  `startYear` varchar(10) DEFAULT '' COMMENT '开始使用年份',
  `startQuarter` tinyint(5) NOT NULL COMMENT '季度',
  `comment` varchar(400) NOT NULL  COMMENT '备注',
  `createdBy` char(30) NOT NULL COMMENT '由谁创建',
  `createdDate` datetime NOT NULL COMMENT '创建日期',
  `editedBy` char(30) NOT NULL COMMENT '编辑人',
  `editedDate` datetime NOT NULL COMMENT '编辑时间',
  `deleted` varchar(5) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8;


-- -----------------------------------------------------------------------------------------
-- 年度计划
-- -----------------------------------------------------------------------------------------
CREATE TABLE `zt_projectplanmsrelation` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `mainPlanID` int(11) unsigned NOT NULL COMMENT '主项目ID',
    `slavePlanID` varchar(1000) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '从项目ID',
    `createdBy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '创建人',
    `editedBy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '编辑人',
    `createdDate` datetime NOT NULL COMMENT '创建时间',
    `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
    `deleted` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除0正常 1删除',
    `createTime` datetime DEFAULT CURRENT_TIMESTAMP,
    `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `mainplanID` (`mainPlanID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='年度计划主从关系表';

CREATE TABLE `zt_projectplan_isdelay_log` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `planID` int(11) NOT NULL COMMENT '年度计划id',
      `year` mediumint(8) NOT NULL COMMENT '统计年份',
      `status` tinyint(3) unsigned NOT NULL COMMENT '是否上一年度延续 1是 2否',
      `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
      `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
      `createdBy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
      `editedBy` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
      `createTime` datetime DEFAULT CURRENT_TIMESTAMP,
      `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `planid` (`planID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='年度计划是否上一年结转记录';

ALTER TABLE `zt_projectplan` ADD COLUMN `platformowner` varchar(255) NOT NULL DEFAULT '' COMMENT '所属平台' AFTER `changeReview`;

INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '', '', '1');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '1', '交易核算平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '2', '业务管理平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '3', '内部管理平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '4', '数据分析与服务平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '5', '数据传输平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '6', '机构服务平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '7', '公共基础平台', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'platformownerList', '8', '智能运维平台', '0');


ALTER TABLE `zt_projectplan`
ADD COLUMN `oldInsideStatus` varchar(30) NOT NULL DEFAULT '' COMMENT '历史内部项目状态' AFTER `isDelayPreYear`;


INSERT INTO `zt_lang`( `lang`, `module`, `section`, `key`, `value`, `system`) VALUES ( 'zh-cn', 'projectplan', 'isDelayPreYearList', '1', '是', '1');
INSERT INTO `zt_lang`( `lang`, `module`, `section`, `key`, `value`, `system`) VALUES ( 'zh-cn', 'projectplan', 'isDelayPreYearList', '2', '否', '1');
ALTER TABLE `zt_component_release`
    ADD COLUMN `createTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP AFTER `baseline`,
ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0) AFTER `createTime`,
ADD COLUMN `publishTime` datetime(0) NULL COMMENT '组件发布时间' AFTER `updateTime`;
ALTER TABLE `zt_grouppriv`
    MODIFY COLUMN `module` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `group`,
    MODIFY COLUMN `method` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `module`;
-- ----------------------------
-- 授权管理 shixuyang 2023-08-11
-- ----------------------------
create table zt_authorization
(
    id                   mediumint unsigned auto_increment
        primary key,
    objectType          varchar(256)                            null comment '模块类型',
    authorizer          varchar(256)                            null comment '授权人',
    authorizedPerson    text                                    null comment '被授权人',
    startTime           datetime                                null comment '授权开始时间',
    endTime             datetime                                null comment '授权结束时间',
    permanently         varchar(2)                              null comment '是否永久授权：1-否；2-是',
    createtime          datetime                                null comment '创建时间',
    updatetime          datetime                                null comment '修改时间'
) ENGINE=InnoDB DEFAULT charset = utf8;

ALTER TABLE `zt_authorization` ADD COLUMN `enabled`  varchar(2) null comment '是否启用：1-否；2-是';
ALTER TABLE `zt_authorization` MODIFY COLUMN `objectType`  text null comment '模块类型';
ALTER TABLE `zt_authorization` ADD COLUMN `deleted`  varchar(2) default '1' comment '是否删除：1-否；2-是';
ALTER TABLE `zt_authorization` ADD COLUMN `num`  int default null comment '序号';

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setAuthorizationMail', '{"mailTitle":"\\u3010\\u901a\\u77e5\\u3011\\u6388\\u6743\\u63d0\\u9192","mailContent":"<strong>\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\/strong>"}');


-- 新增公共技术组件台账表(新增开始使用时间字段)
ALTER TABLE `zt_component_public_account` ADD COLUMN `startTime` int(10) DEFAULT '0' COMMENT '开始使用时间';


-- ----------------------------
-- 需求池 迭代三十 wangshusen 2023-08-25 变更锁
-- ----------------------------
ALTER TABLE `zt_opinion` ADD COLUMN `changeLock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '变更锁： 1正常 2变更锁';
ALTER TABLE `zt_requirement` ADD COLUMN `changeLock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '变更锁： 1正常 2变更锁';
ALTER TABLE `zt_demand` ADD COLUMN `changeLock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '变更锁： 1正常 2变更锁';
ALTER TABLE `zt_opinionchange` ADD COLUMN `affectRequirement` varchar(255)  DEFAULT '' COMMENT '受影响需求任务';

ALTER TABLE `zt_modify` ADD COLUMN `changeLock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '变更锁： 1正常 2变更锁';
ALTER TABLE `zt_info` ADD COLUMN `changeLock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '变更锁： 1正常 2变更锁';
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `changeLock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '变更锁： 1正常 2变更锁';

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh_cn','demand','unLockList','shenlu','沈璐','0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh_cn','demand','unLockList','yinyameng','印雅萌','0');

-- ----------------------------
-- 需求池 迭代三十 wangshusen 2023-09-05 增加updatetime
-- ----------------------------
ALTER TABLE `zt_demand` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
ALTER TABLE `zt_requirement` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
ALTER TABLE `zt_opinion` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
UPDATE zt_demand SET updateTime = NULL;
UPDATE zt_requirement SET updateTime = NULL;
UPDATE zt_opinion SET updateTime = NULL;

-- ----------------------------
--  迭代三十 wangyongliang 20230905 评审项目主从关系
-- ----------------------------
ALTER TABLE `zt_review` ADD COLUMN `mainRelationInfo` varchar(255) DEFAULT '' COMMENT '本项目为主项目,从项目为',
                ADD COLUMN `slaveRelationInfo` varchar(255) DEFAULT '' COMMENT '本项目为从项目,主项目为';

-- ----------------------------
--  问题池 迭代三十 刘胜杰 2023-09-13 问题单增加是否超期字段
-- ----------------------------
alter table `zt_problem` add `isExtended` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否超期  1：否 ；2：是';
-- -----------------------------------------------------------------------------------------
-- 厂商代码 2023-08-25 t_jinzhuliang
-- -----------------------------------------------------------------------------------------
ALTER TABLE `zt_case`
    ADD `bugs` MEDIUMINT NOT NULL DEFAULT '0' AFTER `deleted`,
ADD `results` MEDIUMINT NOT NULL DEFAULT '0' AFTER `bugs`,
ADD `caseFails` MEDIUMINT NOT NULL DEFAULT '0' AFTER `results`,
ADD `stepNumber` MEDIUMINT NOT NULL DEFAULT '0' AFTER `caseFails`;

ALTER TABLE `zt_testrun`
    ADD `taskBugs` MEDIUMINT NOT NULL DEFAULT '0' AFTER `status`,
ADD `taskResults` MEDIUMINT NOT NULL DEFAULT '0' AFTER `taskBugs`,
ADD `taskCaseFails` MEDIUMINT NOT NULL DEFAULT '0' AFTER `taskResults`,
ADD `taskStepNumber` MEDIUMINT NOT NULL DEFAULT '0' AFTER `taskCaseFails`;

ALTER TABLE zt_case ADD `intro` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE zt_bug ADD linkTesttask varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所属测试主键，逗号分割多个';
ALTER TABLE zt_testrun ADD precondition longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL NULL COMMENT '用于存储上一个版本的前置条件，默认应当为null，当同步时也更新为null。当为null时，case的前置条件发生变化，则更新这里的字段。当testrun更新同步时，则修改为null。';

-- 帮助手册自定义排序
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'helpdoc', 'navOrderList', 'opinion', '需求意向', '1','0');

-- 交付管理增加 updateTime
ALTER TABLE `zt_modify` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
ALTER TABLE `zt_modifycncc` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
ALTER TABLE `zt_testingrequest` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
ALTER TABLE `zt_productenroll` ADD COLUMN `updateTime` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0);
-- ----------------------------
-- 需求池 迭代三十 wangshusen 2023-09-14 增加需求条目是否纳入交付超期标记
-- ----------------------------
ALTER TABLE `zt_demand` ADD COLUMN `isExtended` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否超期  1：否 ；2：是';

-- 交付管理新增是否取消状态联动标记
ALTER TABLE `zt_modify` ADD COLUMN `demandCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '需求条目是否取消状态联动：0:正常 1：取消状态联动';
ALTER TABLE `zt_modify` ADD COLUMN `problemCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '问题池是否取消状态联动：0正常 1：取消状态联动';
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `demandCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '需求条目是否取消状态联动：0:正常 1：取消状态联动';
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `problemCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '问题池是否取消状态联动：0:正常 1：取消状态联动';
-- 需求条目是否已经联动过
ALTER TABLE `zt_modify` ADD COLUMN `demandLinked` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已经联动需求条目：0未联动 1：已联动';
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `demandLinked` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已经联动需求条目：0未联动 1：已联动';

-- ----------------------------
-- 需求池 迭代三十 shixuyang 2023-09-19 用户表加兼任部门
-- ----------------------------
ALTER TABLE `zt_user` ADD COLUMN `partDept` varchar(255) DEFAULT null COMMENT '兼任部门';

-- ----------------------------
-- 对外交付 迭代三十 刘胜杰 2023-09-20 数据获取增加解除状态联动字段
-- ----------------------------
ALTER TABLE `zt_info` ADD COLUMN `problemCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '问题池是否取消状态联动：0:正常 1：取消状态联动';
ALTER TABLE `zt_infoqz` ADD COLUMN `problemCancelLinkage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '问题池是否取消状态联动：0:正常 1：取消状态联动';

-- ----------------------------
-- 需求收集 迭代三十 jinzhuliang 2023-09-26 所属模块和所属平台联动配置
-- ----------------------------
INSERT INTO `zt_lang`( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'all', 'demandcollection', 'childTypeList', 'all', '{\"1\":{\"1\":\"\\u5730\\u76d8\",\"2\":\"\\u9700\\u6c42\\u6c60\",\"3\":\"\\u95ee\\u9898\\u6c60\",\"4\":\"\\u5de5\\u5355\\u6c60\",\"5\":\"\\u5e74\\u5ea6\\u8ba1\\u5212\",\"6\":\"\\u7cfb\\u7edf\\u7ba1\\u7406\",\"7\":\"\\u4ea7\\u54c1\\u7ba1\\u7406\",\"8\":\"\\u9879\\u76ee\\u7ba1\\u7406\",\"9\":\"\\u8bc4\\u5ba1\\u7ba1\\u7406\\uff08\\u9879\\u76ee\\u8bc4\\u5ba1\\uff09\",\"10\":\"\\u98ce\\u9669\\u7ba1\\u7406\",\"11\":\"\\u6d4b\\u8bd5\\u7ba1\\u7406\",\"12\":\"\\u4ea4\\u4ed8\\u7ba1\\u7406\",\"13\":\"\\u770b\\u677f\\u7ba1\\u7406\",\"14\":\"\\u7ec4\\u4ef6\\u7ba1\\u7406\",\"15\":\"\\u5176\\u4ed6\",\"16\":\"\\u9700\\u6c42\\u6536\\u96c6\",\"17\":\"\\u73b0\\u573a\\u670d\\u52a1\",\"18\":\"\\u77e5\\u8bc6\\u4ea7\\u6743\",\"19\":\"\\u8d28\\u91cf\\u7ba1\\u7406\",\"20\":\"\\u7edf\\u8ba1\"}}', '0', 0);

-- ----------------------------
-- 任务 已完成 指派给更新 2023-09-27 wangyongliang
-- ----------------------------
update zt_task set assignedTo ='closed' where status ='done' and assignedTo !='closed' and assignedTo !='' and deleted ='0' and grade ='2';

