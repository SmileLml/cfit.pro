set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 紧急需求4254  wangjiurong
ALTER TABLE zt_opinion ADD `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NULL COMMENT '需求类型';
ALTER TABLE zt_requirement ADD `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '需求类型';
ALTER TABLE zt_requirement ADD requireStartTime date DEFAULT '0000-00-00' NOT NULL COMMENT '需求启动时间';
