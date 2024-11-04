set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 需求收集4597新增字段
ALTER table zt_modifycncc ADD changeImpactAnalysis TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '变更关联影响分析';
-- 需求收集4487
ALTER TABLE `zt_modify` ADD COLUMN `involveDatabase` varchar(10) DEFAULT '' COMMENT '是否涉及数据库表结构变化 1:是 2：否';

-- 需求单位或部门类型
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '1', '总中心内设部门', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '2', '总中心直属企业', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '3', '清算中心', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '4', '外单位', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '5', '总行司局', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitTypeList', '6', '参与机构', '0', 0,'');


-- 需求单位或部门列表
-- 总中心内设部门
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_1', '开发中心', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_2', '技术管理部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_3', '生产中心', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_4', '上海园区管理部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_5', '采购办', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_6', '测试中心', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_7', '业务中心', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_8', '事业发展部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_9', '上海清企', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_10', '松园山庄', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_11', '审计合规部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_12', '业务管理部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_13', '研究中心', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_14', '上海中心综合部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_15', '人力资源部', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_16', '成方金科', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList1', '1_17', '成方金信', '0', 0,'');

-- 总中心直属企业
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList2', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList2', '2_1', 'CIPS公司', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList2', '2_2', '中志公司', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList2', '2_3', '深圳金融电子结算中心', '0', 0,'');

-- 清算中心
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '1', '北京CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '2', '上海CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '3', '天津CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '4', '重庆CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '5', '成都CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '6', '广州CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '7', '贵阳CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '8', '海口CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '9', '杭州CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '10', '合肥CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '11', '济南CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '12', '昆明CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '13', '拉萨CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '14', '兰州CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '15', '南昌CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '16', '南京CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '17', '南宁CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '18', '深圳CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '19', '沈阳CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '20', '太原CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '21', '武汉CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '22', '西安CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '23', '西宁CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '24', '银川CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '25', '长春CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '26', '长沙CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '27', '郑州CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '28', '福州CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '29', '石家庄CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '30', '哈尔滨CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '31', '乌鲁木齐CCPC', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'demandUnitList3', '32', '呼和浩特CCPC', '0', 0,'');

-- 接口人配置
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'portList', 'portUser', '刘闰捷,谢昳', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'portList', 'portUserPhone', '13521219501,18600229417', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'infoqz', 'portList', 'portUserEmail', 'rjliu@cncc.cn,yixie01@cncc.cn', '0', 0,'');

ALTER TABLE `zt_infoqz`
    ADD `dataSystem` varchar(500) DEFAULT NULL COMMENT '数据所属业务系统',
    ADD `dataCollectApplyCompany` varchar(45) DEFAULT NULL COMMENT '需求单位或部门类型',
    ADD `demandUnitOrDep` varchar(255) DEFAULT NULL COMMENT '需求单位或部门',
    ADD `demandUser` varchar(255) DEFAULT NULL COMMENT '需求人',
    ADD `demandUserPhone` varchar(255) DEFAULT NULL COMMENT '需求人电话',
    ADD `demandUserEmail` varchar(255) DEFAULT NULL COMMENT '需求人邮箱',
    ADD `portUser` varchar(255) DEFAULT NULL COMMENT '接口人',
    ADD `portUserPhone` varchar(255) DEFAULT NULL COMMENT '接口人电话',
    ADD `portUserEmail` varchar(255) DEFAULT NULL COMMENT '接口人邮箱',
    ADD `supportUser` varchar(255) DEFAULT NULL COMMENT '支持人',
    ADD `supportUserPhone` varchar(255) DEFAULT NULL COMMENT '支持人电话',
    ADD `supportUserEmail` varchar(255) DEFAULT NULL COMMENT '支持人邮箱';

CREATE TABLE `zt_system`
(
    `id`              int(11) NOT NULL AUTO_INCREMENT,
    `ciKey`           varchar(50) DEFAULT NULL COMMENT '外部系统唯一主键',
    `cName`           varchar(255) DEFAULT NULL COMMENT '系统中文名称',
    `eName`           varchar(100) DEFAULT NULL COMMENT '系统英文名称',
    `createdDate`     datetime    DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updatedDate`     datetime    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '编辑时间',
    `deleted`         enum('0','1') DEFAULT '0' COMMENT '1 已删除',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `zt_system_partition`
(
    `id`              int(11) NOT NULL AUTO_INCREMENT,
    `ciKey`           varchar(50) DEFAULT NULL COMMENT '外部分区唯一主键',
    `name`            varchar(100) DEFAULT NULL COMMENT '分区名称',
    `applicationCnName` varchar(255) DEFAULT NULL COMMENT '系统中文名称',
    `application`     varchar(100) DEFAULT NULL COMMENT '系统英文名称',
    `applicationName` varchar(100) DEFAULT NULL COMMENT '系统名称',
    `ip`              varchar(50) DEFAULT NULL COMMENT '分区ip',
    `dataOrigin`      tinyint  NOT NULL DEFAULT '0' COMMENT '1 NPC,2 CCPC ,3 央行云',
    `location`        varchar(50) DEFAULT NULL COMMENT '',
    `createdDate`     datetime    DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updatedDate`     datetime    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '编辑时间',
    `deleted`         enum('0','1') DEFAULT '0' COMMENT '1 已删除',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- 用户表新增 员工编号  wangyongliang
-- ----------------------------
ALTER TABLE zt_user ADD COLUMN `employeeNumber` varchar(100)  DEFAULT  NULL COMMENT '员工编号';

INSERT INTO zt_config(owner, module, `section`, `key`, value)VALUES( 'system', 'ldap', '', 'employeeNumber', 'uid');

-- 创建二线月报月度季度年度相关字段 刘胜杰 2024-9-25
ALTER TABLE zt_whole_report
    ADD `exportFields` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '表单快照导出字段',
  ADD `exportBasicFields` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '基础快照字段',
  ADD `useIDArr` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '统计表各表格中使用到ID数据',
  ADD `isyear` int(11) NOT NULL DEFAULT '1' COMMENT '是否年度数据 1否,2是; 3季度';
-- 创建历史结转数据表 刘胜杰 2024-9-25
CREATE TABLE `zt_secondmonthhistorydata`
(
    `id`         bigint(20) NOT NULL AUTO_INCREMENT,
    `sourceyear` mediumint(9) NOT NULL COMMENT '参与统计周期年份',
    `sourcetype` varchar(50) NOT NULL COMMENT '所属模块',
    `objectid`   bigint(20) NOT NULL COMMENT '对应业务数据ID编号',
    `createTime` datetime    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updateTime` datetime    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `deleted`    tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除:0正常;1删除',
    PRIMARY KEY (`id`),
    KEY          `year_objectid` (`sourceyear`,`sourcetype`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- 配置考核周期 刘胜杰 2024-9-25
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`, `extendInfo`)
VALUES ('zh-cn', 'secondmonthreport', 'examinecycleList', 'examineyear', '2024', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'problemExceed', '2$10-16~1$10-15', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'problemExceedBackIn', '2$12-16~1$12-15', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'problemExceedBackOut', '2$12-16~1$12-15', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'demand_realized', '2$10-16~1$10-15', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'requirement_inside', '2$12-16~1$12-15', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'requirement_outside', '2$12-16~1$12-15', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'examinecycleList', 'modifyabnormal', '2$12-16~1$12-15', '0', 0, '');
-- 配置季度统计相关部门 刘胜杰 2024-9-25
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`, `extendInfo`)
VALUES ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '2', '平台架构部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '5', '研发一部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '6', '研发二部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '7', '研发三部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '8', '研发四部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '9', '研发五部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '10', '研发六部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '12', '系统部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '17', '成都分公司', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '18', '成都研发事业一部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '19', '成都研发事业二部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '20', '成都综合部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '26', '天津分公司', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '27', '天津综合保障部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '28', '天津研发一部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '29', '天津研发二部', '1', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '1', '产品创新部', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '3', '质量部', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '11', '测试部', '0', 0, ''),
       ('zh-cn', 'secondmonthreport', 'quarterReportNeedDept', '-1', '空', '1', 0, '');