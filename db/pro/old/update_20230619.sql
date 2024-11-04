set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

ALTER TABLE `zt_sectransfer` ADD `openFile` varchar(32) DEFAULT NULL COMMENT '是否需要查询文件列表：true-是；false-否';

grant update,select on cfitpms.zt_sectransfer to 'media'@'%';
grant insert,select,update on cfitpms.zt_action to 'media'@'%';
grant update,select on cfitpms.zt_reviewnode to 'media'@'%';
grant update,select on cfitpms.zt_reviewer to 'media'@'%';
grant insert,update,select on cfitpms.zt_consumed to 'media'@'%';
flush privileges;