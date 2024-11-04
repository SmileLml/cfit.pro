set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------------------------------------------
-- 紧急上线 2023-09-12  jinzhuliang 年度计划新增是否上一年结转
-- -----------------------------------------------------------------------------------------
ALTER TABLE `zt_projectplan`
    ADD COLUMN `isDelayPreYear` tinyint(3)  NOT NULL DEFAULT 2 COMMENT '是否上一年度延续 1是 2否';
