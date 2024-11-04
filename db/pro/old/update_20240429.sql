set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE TABLE `zt_cmdbsync` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `type` varchar(32) NOT NULL COMMENT '同步类型：cmdb:cmdb同步/putproduction：金信投产',
                               `isDiffer` varchar(8) DEFAULT NULL COMMENT '是否差异：true/false',
                               `putproductionId` int(32) DEFAULT NULL COMMENT '投产单号',
                               `app` varchar(1024) DEFAULT NULL COMMENT '涉及系统id,逗号隔开',
                               `info` text DEFAULT NULL COMMENT '详细信息',
                               `status` varchar(32) DEFAULT NULL COMMENT '状态',
                               `dealUser` varchar(1024) DEFAULT NULL COMMENT '待处理人',
                               `createdBy` varchar(30) DEFAULT NULL COMMENT '创建人',
                               `createdDate` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                               `updatedDate` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                               `result` varchar(30) DEFAULT NULL COMMENT '处理结果',
                               `comment` text DEFAULT NULL COMMENT '处理意见',
                               `isAuto` varchar(30) DEFAULT NULL COMMENT '是否自动更新',
                               `deleted` enum('0','1') DEFAULT '0' COMMENT '1 已删除',
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
ALTER TABLE `zt_cmdbsync` ADD COLUMN `failNum` int(4) DEFAULT NULL COMMENT '推送失败次数';
ALTER TABLE `zt_cmdbsync` ADD COLUMN `sendStatus` varchar(32) DEFAULT NULL COMMENT '推送状态';

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'cmdbsync', 'apiDealUserList', 'wanglijiao', '王丽娇', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'cmdbsync', 'reSendUserList', 'shixuyang', '师旭阳', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '1', '总行', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '2', '央行云', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '3', '银联云', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '4', '支付', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '5', '通用', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '6', '总行-业务网', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '7', '总行-互联网', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'resourceLocatList', '8', '总行-办公网', '0', 0);


INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '1', '总行', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '2', '成方金信', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '3', '总中心', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '4', '通用', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '5', 'CIPS', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '6', '金信中心', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '7', '反洗钱', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'belongOrganizationList', '8', '运营商自备', '0', 0);


INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'facilitiesStatusList', '1', '设施在用', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'facilitiesStatusList', '2', '设施已停用', '0', 0);


INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'architectureList', '1', '云平台', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'architectureList', '2', 'PC服务器', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'architectureList', '3', '小型机', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'architectureList', '4', '开放平台', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'architectureList', '5', '大型机', '0', 0);


INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'userScopeList', '1', '总行用户', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'userScopeList', '2', '人行内部', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'userScopeList', '3', '社会公众', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'userScopeList', '4', '金融机构', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'userScopeList', '5', '外部单位', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '8', '技术服务类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '9', '决策分析类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '10', '业务信息类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '11', '交易处理类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '12', '其他', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '13', '资源类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '14', '传输与接入类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '15', '前置类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '16', '交易类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '17', '传输与接入类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '18', '公共基础类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '19', '同城系统', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '20', '运维类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '21', '大数据', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '22', '信息类', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'attributeList', '23', '综合管理类', '0', 0);


INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '30', '成方金信', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '31', '集采中心', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '32', '离退休干部局', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '33', '金标院', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '34', '消保局', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '35', '办公厅', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '36', '货金局', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '39', '国际司', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '40', '党委宣传部', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '42', '市场司', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '43', '货政司', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '44', '稳定局', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '45', '宏观审慎局', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '46', '其他', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '47', '清算总中心', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'fromUnitList', '48', '存保公司', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'networkList', '6', '央行云', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'networkList', '7', '支付网', '0', 0);
INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'networkList', '8', '管理网', '0', 0);

INSERT INTO `zt_lang` (  `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'application', 'continueLevelList', 'level_6', '未定级', '0', 0);

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushCmdbDealUrl', 'https://172.22.7.4/zuul/api-service-provider/process/production/system/confirm');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushAppInfoUrl', 'https://172.22.7.4/zuul/api-service-provider/process/production/system/frompms');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushCmdbAppId', 'CFIT');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushCmdbAppSecret', '063dfb7fc52b7a3a3199476f5e238eed');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushCmdbAppEnable', 'enable');

-- ----------------------------
-- 迭代三十四 系统管理改造 2024-3-26 shixuyang
-- ----------------------------
ALTER TABLE `zt_application` ADD COLUMN `resourceLocat` varchar(64) DEFAULT NULL COMMENT '资源位置';
ALTER TABLE `zt_application` ADD COLUMN `belongOrganization` varchar(64) DEFAULT NULL COMMENT '归属机构';
ALTER TABLE `zt_application` ADD COLUMN `facilitiesStatus` varchar(32) DEFAULT NULL COMMENT '设施在用状态';
ALTER TABLE `zt_application` ADD COLUMN `architecture` varchar(512) DEFAULT NULL COMMENT '系统架构';
ALTER TABLE `zt_application` ADD COLUMN `userScope` varchar(512) DEFAULT NULL COMMENT '用户范围';
ALTER TABLE `zt_application` ADD COLUMN `serviceTime` varchar(256) DEFAULT NULL COMMENT '服务时间';
ALTER TABLE `zt_application` ADD COLUMN `recoveryStrategy` text DEFAULT NULL COMMENT '灾备策略';
ALTER TABLE `zt_application` ADD COLUMN `developmentUnit` varchar(32) DEFAULT NULL COMMENT '开发单位';
ALTER TABLE `zt_application` ADD COLUMN `ciKey` varchar(50) DEFAULT NULL COMMENT 'cmdb_key';
ALTER TABLE `zt_application` ADD COLUMN `baselineSystem` varchar(50) DEFAULT NULL COMMENT '基线对应系统';
ALTER TABLE `zt_application` ADD COLUMN `cfidKey` varchar(50) DEFAULT NULL COMMENT '金信系统id';
ALTER TABLE `zt_application` ADD COLUMN `createType` varchar(25) DEFAULT NULL COMMENT '系统创建类型：内部自建和外部系统';
ALTER TABLE `zt_application` ADD COLUMN `systemManager` varchar(512) DEFAULT NULL COMMENT '系统负责人';
ALTER TABLE `zt_application` ADD COLUMN `systemDept` varchar(128) DEFAULT NULL COMMENT '系统负责部门';
ALTER TABLE `zt_application` ADD COLUMN `productionFirstDate` datetime DEFAULT NULL COMMENT '首次投产日期';
ALTER TABLE `zt_application` ADD COLUMN `externalUpdateDate` datetime DEFAULT NULL COMMENT '外部更新时间';

CREATE TABLE `zt_baseapplication` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `name` varchar(512) NOT NULL COMMENT '系统名称',
                               `number` varchar(128) DEFAULT NULL COMMENT '系统基线名称',
                               `code` varchar(128) DEFAULT NULL COMMENT '英文缩写',
                               `systemRelation` varchar(128) DEFAULT NULL COMMENT '系统间关系',
                               `type` varchar(128) DEFAULT NULL COMMENT '系统类别',
                               `network` varchar(128) DEFAULT NULL COMMENT '所属网络',
                               `deleted` enum('0','1') DEFAULT '0' COMMENT '1 已删除',
                               `createdDate` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                               `updatedDate` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


alter table `zt_putproduction` ADD COLUMN `planUsedWindow` varchar(36) DEFAULT null comment '计划是否使用投产窗口';
alter table `zt_putproduction` ADD COLUMN `planUsedWindowReason` varchar(1024) DEFAULT null comment '计划不使用投产窗口原因';
alter table `zt_putproduction` ADD COLUMN `realUsedWindow` varchar(36) DEFAULT null comment '实际是否使用投产窗口';
alter table `zt_putproduction` ADD COLUMN `realUsedWindowReason` varchar(1024) DEFAULT null comment '实际不使用投产窗口原因';
alter table `zt_putproduction` ADD COLUMN `appCauseFail` varchar(36) DEFAULT null comment '是否由应用引起投产失败';