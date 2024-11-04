set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 清总评审新增
-- ----------------------------
ALTER TABLE `zt_reviewqz` ADD `timeInterval` varchar(20) DEFAULT '' COMMENT '评审时间区间：morning-上午；afternoon-下午';

-- ----------------------------
-- 清总同步需求任务新增所属研发子项
-- ----------------------------
ALTER TABLE `zt_requirement` ADD `ChildName` varchar(255) DEFAULT '' COMMENT '所属研发子项';

-- ----------------------------
-- 外部年度计划新增一列历史项目编号
-- ----------------------------
ALTER TABLE `zt_outsideplan` ADD `historyCode` varchar(255) DEFAULT '' COMMENT '历史项目编号';
update `zt_outsideplan` set `historyCode` = `code`;