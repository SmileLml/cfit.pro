set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 问题单、任务工单、部门工单 紧急上线 2024-03-25 刘胜杰
-- ----------------------------
ALTER TABLE zt_deptorder ADD `progressQA` mediumtext NOT NULL COMMENT '进展跟踪' ;
ALTER TABLE zt_secondorder ADD `progressQA` mediumtext NOT NULL COMMENT '进展跟踪';
ALTER TABLE zt_problem ADD `progressQA` mediumtext NOT NULL COMMENT '进展跟踪';


