set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 制版是否需要系统部 默认不需要 2024-3-28 wangyongliang
-- ----------------------------
alter table zt_build alter column systemverify set default '0';
