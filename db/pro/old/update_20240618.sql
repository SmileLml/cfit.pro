set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 紧急需求3310  songdi
ALTER TABLE `zt_modify` ADD COLUMN `isMakeAmends` varchar(10) DEFAULT 'no' COMMENT '是否后补 yes:是 no：否',
           ADD COLUMN `actualDeliveryTime` datetime DEFAULT NULL COMMENT '实际交付时间';

ALTER TABLE `zt_modifycncc` ADD COLUMN `isMakeAmends` varchar(10) DEFAULT 'no' COMMENT '是否后补 yes:是 no：否',
           ADD COLUMN `actualDeliveryTime` datetime DEFAULT NULL COMMENT '实际交付时间';

ALTER TABLE `zt_credit` ADD COLUMN `isMakeAmends` varchar(10) DEFAULT 'no' COMMENT '是否后补 yes:是 no：否',
           ADD COLUMN `actualDeliveryTime` datetime DEFAULT NULL COMMENT '实际交付时间';

-- 自定义添加配置扩展信息 wangjiurong
ALTER TABLE zt_lang ADD extendInfo TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '扩展字段信息';