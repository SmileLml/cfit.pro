set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 度量接口调整：新增字段与历史数据同步  于磊
-- ----------------------------
ALTER TABLE `zt_problem` ADD COLUMN `createTime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间';
update zt_problem set createTime = date_format(createdDate, '%Y-%m-%d %H:%i:%s') where 1 = 1;
ALTER TABLE `zt_application` ADD COLUMN `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP   COMMENT '更新时间';
update zt_application set updateTime = date_format(createdDate, '%Y-%m-%d %H:%i:%s')  where 1 = 1;
ALTER TABLE `zt_problem` ADD COLUMN `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP   COMMENT '更新时间';
update zt_problem zp set zp.updateTime = (select date from zt_action where id = (select max(id) from zt_action za where za.objectID = zp.id and za.objectType = 'problem' and za.`action` != 'deleted' group by objectID)) where 1 = 1;
update zt_opinion zp set zp.updateTime = (select date from zt_action where id = (select max(id) from zt_action za where za.objectID = zp.id and za.objectType = 'opinion' group by objectID)) where 1 = 1;
update zt_requirement zp set zp.updateTime = (select date from zt_action where id = (select max(id) from zt_action za where za.objectID = zp.id and za.objectType = 'requirement' group by objectID)) where 1 = 1;
update zt_demand zp set zp.updateTime = (select date from zt_action where id = (select max(id) from zt_action za where za.objectID = zp.id and za.objectType = 'demand' group by objectID)) where 1 = 1;
