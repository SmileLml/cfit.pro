-- ----------------------------
-- 项目表加索引 2023-10-11
-- ----------------------------
alter table zt_project add index `project` (`project`);
-- ----------------------------
--  地盘慢sql优化添加索引 2023-10-11 wangjiurong
-- ----------------------------
CREATE INDEX idx_dealuser ON `zt_build` (`dealuser`);
CREATE INDEX idx_assignedTo ON `zt_flow_support` (`assignedTo`);
CREATE INDEX idx_line ON `zt_product` (`line`);
CREATE INDEX idx_owner ON `zt_testtask` (`owner`);

-- ----------------------------
-- 需求池 迭代三十一 wangshusen 2023-10-10 增加需求条目是否纳入反馈超期标记
-- ----------------------------
ALTER TABLE `zt_requirement` ADD COLUMN `feedbackOver` tinyint(4) NOT NULL DEFAULT '0' COMMENT '反馈是否超期  1：否 ；2：是';

-- ----------------------------
-- 问题池 迭代三十一 liushengjie 2023-10-10 增加问题单是否纳入反馈超期标记
-- ----------------------------
ALTER TABLE `zt_problem` ADD COLUMN `isBackExtended` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否纳入反馈超期  1：否 ；2：是';

-- ----------------------------
-- 需求池 迭代三十一 wangshusen 2023-10-26 需求任务增加实际实现方式
-- ----------------------------
ALTER TABLE `zt_requirement` ADD COLUMN `actualMethod` char(30) NOT NULL DEFAULT '' COMMENT '实际实现方式 需求条目实现方式去重并集';

-- ----------------------------
-- 二线月报 迭代三十一 刘胜杰 2023-10-26 创建快照表、统计详情表
-- ----------------------------
CREATE TABLE `zt_whole_report` (
   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
   `year` mediumint(8) unsigned NOT NULL COMMENT '年份',
   `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '标识某张统计表数据',
   `month` mediumint(8) NOT NULL COMMENT '月份',
   `fileUrl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '文件下载路径',
   `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
   `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='需求池、问题池整体情况统计表';


CREATE TABLE `zt_detail_report` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `deptID` int(10) NOT NULL COMMENT '所属部门',
    `tableType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '数据类型标识需求池、问题池数据',
    `wholeID` bigint(20) NOT NULL COMMENT 'whole_report的ID',
    `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '数据详情',
    `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `deptID` (`deptID`) USING BTREE,
    KEY `wholeID` (`wholeID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='需求池、问题池整体情况详情统计表';

-- ----------------------------
-- 需求池 迭代三十一 wangshusen 2023-11-3 需求任务增加最新发布时间
-- ----------------------------
ALTER TABLE `zt_requirement` ADD COLUMN `newPublishedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最新发布时间';