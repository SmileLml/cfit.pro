set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
--  问题池 紧急上线 刘胜杰 2023-09-20 延期审批表`type`字段名改为`objectType`
-- ----------------------------
alter table `zt_delay` change `type` `objectType` varchar(255);

