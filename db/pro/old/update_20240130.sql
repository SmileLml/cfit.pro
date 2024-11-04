set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------
-- 评审 迭代三十二 挂起前状态记录 2023-10-20 wangjiurong
-- ----------------------------
ALTER TABLE `zt_review`
ADD COLUMN `lastStatus`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '上一个状态' AFTER `status`;


-- ----------------------------
-- 评审 迭代三十二 挂起前状态记录-历史数据补全 2023-10-20 wangjiurong
-- ----------------------------
UPDATE zt_review set lastStatus =
(SELECT zc.`before` from zt_consumed zc
where 1 and zc.objectType = 'review' and zc.objectID = zt_review.id and zc.version = zt_review.version and zc.`after` = 'suspend'
) where 1 and `status` = 'suspend' and `deleted` = '0';

-- ----------------------------
--  2023-10-23 wangyongliang
-- ----------------------------
ALTER TABLE `zt_archive`
           ADD COLUMN `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
           ADD COLUMN  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间';

-- ----------------------------
-- 风险新增字段 2023-10-23 wangyongliang
-- ----------------------------
ALTER TABLE `zt_risk` ADD COLUMN `preventionAndremedy` text DEFAULT NULL COMMENT '风险应对措施',
           ADD COLUMN `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
           ADD COLUMN  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间';

-- ----------------------------
-- 更新风险应对措施 2023-10-23 wangyongliang
-- ----------------------------
update zt_risk set preventionAndremedy = concat_ws((case when prevention != '' and  remedy != ''  then ';' else '' end) ,prevention,remedy) where deleted ='0';


-- ----------------------------
-- 清总缺陷 迭代三十二 通知人长度改长 2023-10-26 shixuyang
-- ----------------------------
ALTER TABLE `zt_defect`
    MODIFY COLUMN `cc`  varchar(255)  null comment '通知人员';

-- ----------------------------
-- 清总缺陷 迭代三十二 部门增加测试负责人 2023-10-27 shixuyang
-- ----------------------------
ALTER TABLE `zt_dept`
    ADD COLUMN `testLeader`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '测试负责人';
-- ----------------------------
-- 系统管理 迭代三十二 业务连续性级别设置成枚举类型 2023-11-1 wangjiurong
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'application', 'continueLevelList', 'level_5', '五级', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'application', 'continueLevelList', 'level_4', '四级', '0', 0);
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'application', 'continueLevelList', 'level_3', '三级', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'application', 'continueLevelList', 'level_2', '二级', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'application', 'continueLevelList', 'level_1', '一级', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'application', 'continueLevelList', '', '', '1', 0);
-- ----------------------------
-- 系统管理 迭代三十二 业务连续性级别设置成枚举类型,历史数据处理 2023-11-1 wangjiurong
-- ----------------------------
UPDATE zt_application set continueLevel = 'level_1' where 1 and continueLevel = '一级';
UPDATE zt_application set continueLevel = 'level_2' where 1 and continueLevel = '二级';
UPDATE zt_application set continueLevel = 'level_3' where 1 and continueLevel = '三级';
UPDATE zt_application set continueLevel = 'level_4' where 1 and continueLevel = '四级';
UPDATE zt_application set continueLevel = 'level_5' where 1 and continueLevel = '五级';
-- ----------------------------
-- 系统管理 迭代三十二 项目变更新增字段 2023-11-6 wangjiurong
-- ----------------------------
ALTER TABLE `zt_change`
ADD COLUMN `isInteriorPro`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否内部项目 0-默认 1-是 2-否' AFTER `type`,
ADD COLUMN `isMasterPro`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为主项目 0-默认 1-是 2-否' AFTER `isInteriorPro`,
ADD COLUMN `isSlavePro`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为从项目 0-默认 1-是 2-否' AFTER `isMasterPro`,
ADD COLUMN `subCategory`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更子类' AFTER `category`,
ADD COLUMN `mailUsers`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更通过后邮件通知人' AFTER `subCategory`;
-- ----------------------------
-- 系统管理 迭代三十二 项目变更配置 2023-11-6 wangjiurong
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'levelList', '', '', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'levelList', '1', '一级', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'levelList', '2', '二级', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'levelList', '3', '三级', '1', 0);

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'categoryList', '', '', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'categoryList', 'plan', '计划基线', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'categoryList', 'demand', '需求基线', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'categoryList', 'design', '设计基线', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'categoryList', 'demand_design', '需求基线+设计基线', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'categoryList', 'other', '其他', '0', 0);

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isInteriorProList', '', '', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isInteriorProList', '1', '是', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isInteriorProList', '2', '否', '0', 0);

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isMasterProList', '', '', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isMasterProList', '1', '是', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isMasterProList', '2', '否', '0', 0);


INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isSlaveProList', '', '', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isSlaveProList', '1', '是', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'isSlaveProList', '2', '否', '0', 0);

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'subCategoryList', '', '', '1', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'subCategoryList', 'demandRange', '需求范围', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'subCategoryList', 'milestone', '里程碑变化', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'subCategoryList', 'suspend', '项目暂停', '0', 0);
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ('zh-cn', 'change', 'subCategoryList', 'cancel', '项目撤销', '0', 0);

-- ----------------------------
-- 系统管理 迭代三十二 项目发布 打基线时间，打基线人的历史数据修改 2023-11-8 wangjiurong
-- ----------------------------
UPDATE zt_release INNER JOIN (
	SELECT zr.id, zr.`status`, zr.baseLineUser,zr.baseLineTime,
	za.actor,za.date
	from zt_release zr
	LEFT JOIN zt_action za on za.objectType = 'release' and za.objectID = zr.id
	LEFT JOIN zt_history zh on zh.action =za.id
	where zr.deleted = '0'
	and (zr.version = 0 OR (zr.version = 1 and zr.`status` = 'waitBaseline'))
	and za.objectType = 'release'
	and za.action = 'deal'
	and zh.field = 'status'
	and zh.old = 'waitBaseline'
	and zh.new = 'waitCmConfirm'
	and (zr.baseLineUser != za.actor OR zr.baseLineTime != za.date)
)temp on zt_release.id = temp.id set zt_release.baseLineUser = temp.actor, zt_release.baseLineTime = temp.date;

UPDATE zt_release_baseline_log zrl
INNER JOIN zt_release zr on  zrl.releaseId = zr.id and zrl.version = 0 set zrl.baseLineUser = zr.baseLineUser, zrl.baseLineTime = zr.baseLineTime;

-----------------------------------------
-- 组件管理 迭代32 组件引入申请审批终态时间 2023-11-07 jinzhuliang
-----------------------------------------
ALTER TABLE `zt_component` ADD COLUMN `finalstatetime` datetime(0) NULL COMMENT '审批节点的终态时间' AFTER `gitlab`;


-- ----------------------------
-- 系统管理 迭代三十二 项目计划历史数据修复（已关闭的项目任务置成100%）2023-11-9 wangjiurong
-- ----------------------------
UPDATE zt_project set progress = 100
where id in ( SELECT id FROM
(SELECT  zp2.id
from zt_project zp1
LEFT JOIN zt_project zp2 on zp2.project = zp1.id and zp2.type = 'stage'
where 1
and zp1.deleted = '0'
and zp1.type = 'project'
and zp1.`status` = 'closed'
and zp2.progress != 100
and zp2.deleted = '0'
and zp2.dataVersion = 1) temp);

UPDATE zt_task set progress = 100
where id in (SELECT id from (
SELECT zp2.id
from zt_project zp1
LEFT JOIN zt_task zp2 on zp2.project = zp1.id
where 1
and zp1.deleted = '0'
and zp1.type = 'project'
and zp1.`status` = 'closed'
and zp2.progress != 100
and zp2.deleted = '0'
and zp2.dataVersion = 1
) temp );

UPDATE zt_project set `status` = 'closed'
where id in ( SELECT id FROM
(SELECT  zp2.id
from zt_project zp1
LEFT JOIN zt_project zp2 on zp2.project = zp1.id and zp2.type = 'stage'
where 1
and zp1.deleted = '0'
and zp1.type = 'project'
and zp1.`status` = 'closed'
and zp2.deleted = '0'
and zp2.`status` != 'closed'
and zp2.dataVersion = 1) temp);

UPDATE zt_task set `status` = 'closed'
where id in (SELECT id from (
SELECT zp2.id
from zt_project zp1
LEFT JOIN zt_task zp2 on zp2.project = zp1.id
where 1
and zp1.deleted = '0'
and zp1.type = 'project'
and zp1.`status` = 'closed'
and zp2.deleted = '0'
and zp2.`status` != 'closed'
and zp2.dataVersion = 1
) temp );


-- ----------------------------
-- 系统管理 迭代三十二 wangshusen 2023-11-10 需求任务、意向变更 增加受影响需求任务和需求条目
-- ----------------------------
ALTER TABLE `zt_requirementchangeoutside` ADD COLUMN `affectDemand` varchar(255)  DEFAULT '' COMMENT '受影响需求条目' AFTER `changeFile`;
ALTER TABLE zt_requirement MODIFY COLUMN `ifOutUpdate` tinyint(3)  COLLATE utf8mb4_bin NOT NULL DEFAULT 1  COMMENT '外部单位是否更新 1：未更新 2：更新过';
ALTER TABLE `zt_opinionchange` ADD COLUMN `affectDemand` varchar(255)  DEFAULT '' COMMENT '受影响需求条目' AFTER `affectRequirement`;

-- ----------------------------
-- 产品管理 迭代三十二 wangjiurong 2023-11-14 增加系统归属部门字段
-- ----------------------------
ALTER TABLE `zt_application`
ADD COLUMN `belongDeptIds`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '归属部门';

ALTER TABLE `zt_product`
ADD COLUMN `belongDeptIds`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '归属部门';




-- ----------------------------
-- 厂商代码 迭代三十二 jinzhuliang 2023-11-17
-- ----------------------------
ALTER TABLE `zt_project` ADD INDEX `td`(`type`, `deleted`) USING BTREE;

ALTER TABLE `zt_bug` ADD INDEX `dropp`(`deleted`, `resolution`, `openedBy`, `product`, `project`) USING BTREE;
ALTER TABLE `zt_bug` ADD INDEX `droppas`(`deleted`, `resolution`, `openedBy`, `product`, `project`, `assignedTo`, `resolvedBy`) USING BTREE;
ALTER TABLE `zt_bug` ADD INDEX `do`(`deleted`, `openedDate`) USING BTREE;
ALTER TABLE `zt_bug` ADD INDEX `pd`(`project`, `deleted`) USING BTREE;

ALTER TABLE `zt_defect` ADD INDEX `ppd`(`product`, `project`, `deleted`) USING BTREE;
ALTER TABLE `zt_defect` ADD INDEX `pd`(`project`, `deleted`) USING BTREE;

ALTER TABLE `zt_case` ADD INDEX `doo`(`deleted`, `openedBy`, `openedDate`) USING BTREE;
ALTER TABLE `zt_testtask` ADD INDEX `ppd`(`project`, `product`, `deleted`) USING BTREE;

-- ----------------------------
-- 对外移交编辑是否增加审批版本表示  迭代三十二 shixuyang 2023-11-20
-- ----------------------------
ALTER TABLE `zt_sectransfer` ADD COLUMN `isAddVersion` varchar(2)  DEFAULT '' COMMENT '是否需要增加审批版本：0-否；1-是';
-- ----------------------------
-- 清总生产变更单新增退回历史记录  迭代三十二 shixuyang 2023-11-21
-- ----------------------------
ALTER TABLE `zt_modifycncc` ADD COLUMN `returnLog` text  DEFAULT null COMMENT '退回历史记录';

-- ----------------------------
-- 系统管理 迭代三十二 项目风险关闭原因配置 2023-11-28 wangjiurong
-- ----------------------------
INSERT INTO `zt_lang`  (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'risk', 'cancelReasonList', '', '', '1', 0);
INSERT INTO `zt_lang`  (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'risk', 'cancelReasonList', 'disappeared', '风险自行消失', '1', 0);
INSERT INTO `zt_lang`  (`lang`, `module`, `section`, `key`, `value`, `system`, `order`) VALUES ( 'zh-cn', 'risk', 'cancelReasonList', 'mistake', '识别错误', '1', 0);
-- ----------------------------
-- 系统管理 迭代三十二 数据修正：2724 项目发布数据修正 2023-11-28 wangjiurong
-- ----------------------------
UPDATE zt_release set `status` = 'passBaseline' where 1 and id in (3538, 3539, 3540) LIMIT 3;
UPDATE zt_release_baseline_log set baseLineCondition = 'yes', baseLinePath = 'http://gitlab.cfit.cn/rd6/ESFE/-/tags/TAG_ESFE_V2.0.1.9_20230810' where releaseId = 3538 LIMIT 1;
UPDATE zt_release_baseline_log set baseLineCondition = 'yes', baseLinePath = 'http://gitlab.cfit.cn/rd6/ESFE/-/tags/TAG_ESFE_V2.0.1.9_20230810' where releaseId = 3539 LIMIT 1;
UPDATE zt_release_baseline_log set baseLineCondition = 'yes', baseLinePath = 'http://gitlab.cfit.cn/rd6/ESFE/-/tags/TAG_ESFE_V2.0.1.9_20230810' where releaseId = 3540 LIMIT 1;
-- ----------------------------
-- 系统管理 迭代三十二 数据修正：2725 项目发布数据修正 2023-11-28 wangjiurong
-- ----------------------------
UPDATE zt_release set `status` = 'waitBaseline' where 1 AND id = 3448 LIMIT 1;

-- ----------------------------
-- 风险管理 迭代三十二 风险描述多行文本 2023-12-4 wangjiurong
-- ----------------------------
ALTER TABLE `zt_risk`
MODIFY COLUMN `name`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '风险描述' AFTER `project`;

-- ----------------------------
--迭代三十二 wangshusen 2023-12-05 需求任务新增任务开始时间
-- ----------------------------
ALTER TABLE zt_requirement ADD COLUMN `startTime` datetime DEFAULT NULL COMMENT '任务开始时间';

-- ----------------------------
--迭代三十二 wangshusen 2023-12-06 需求池、问题池删除后台旧的邮件超时提醒时间配置
-- ----------------------------
delete from zt_lang where `id` in (4378,4379,4380,4381,4382,4383,4384,4385,4386,4387,4388,4375,4376,4389,4390,4391,4392,4393,4394,4395,4396);

-- ----------------------------
--迭代三十二 wangshusen 2023-12-12 挂起状态下 测试中和已发布修复为开发中
-- ----------------------------
UPDATE zt_demand SET lastStatus = 'feedbacked' WHERE sourceDemand  = 1 and lastStatus in ('build','released');

-- ----------------------------
-- 系统管理 迭代三十二 脚本开关 2023-12-13 wangjiurong
-- ----------------------------
CREATE TABLE `zt_cronconfig` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `m` varchar(20) NOT NULL DEFAULT '',
  `h` varchar(20) NOT NULL DEFAULT '',
  `dom` varchar(20) NOT NULL DEFAULT '',
  `mon` varchar(20) NOT NULL DEFAULT '',
  `dow` varchar(20) NOT NULL DEFAULT '',
  `command` text NOT NULL,
  `remark` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'system',
  `buildin` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'normal',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除  0-有效 1-删除',
  `createBy` varchar(50) NOT NULL DEFAULT '' COMMENT '创建人',
  `createTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editBy` varchar(50) NOT NULL DEFAULT '' COMMENT '修改人',
  `editTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
