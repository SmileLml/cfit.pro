CREATE TABLE `zt_requirementchange` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `changeNumber` varchar(100) NOT NULL COMMENT '变更单唯一标识',
  `demandNumber` varchar(100) NOT NULL COMMENT '业务需求唯一标识',
  `changeBackground` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '变更背景',
  `changeContent` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '变更内容',
  `generalManager` varchar(100) DEFAULT '' COMMENT '部门总经理',
  `productManager` varchar(100) DEFAULT '' COMMENT '业务需求产品经理',
  `changeEntry` varchar(255) DEFAULT '' COMMENT '需求变更涉及条目',
  `circumstance` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '需求变更确认情况',
  `missedDemolition` enum('0','1') DEFAULT '0' COMMENT '是否为漏拆产品需求 0否 1是',
  `createdBy` char(30) DEFAULT '',
  `createdDate` datetime DEFAULT '0000-00-00 00:00:00',
  `editDate` datetime DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-有效 1已删除',
  PRIMARY KEY (`id`),
  KEY `changeNumber` (`changeNumber`) USING BTREE,
  KEY `demandNumber` (`demandNumber`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

ALTER TABLE `zt_requirement`
     ADD COLUMN `canceled` enum('0','1') NOT NULL DEFAULT '0'  COMMENT '是否已取消,0:否,1:是',
     ADD COLUMN `changeOrderNumber` varchar(100)  COMMENT '变更单号' after `canceled`,
     ADD COLUMN `nextDealuser` varchar(100)  COMMENT '下一节点处理人,待发布回显' after `changeOrderNumber`,
     ADD INDEX `changeOrderNumber` (`changeOrderNumber`) USING BTREE ;

ALTER TABLE `zt_problem`
    MODIFY COLUMN `product`  varchar(255) NOT NULL DEFAULT '0' COMMENT '产品 逗号分割' AFTER `editedBy`,
    MODIFY COLUMN `productPlan`  varchar(255) NOT NULL DEFAULT '0' COMMENT '产品版本 逗号分割' AFTER `product`;

ALTER TABLE `zt_project` add allowEnd date null comment '允许结束日期' after `realEnd`,
                       add allowBegin date null comment '允许开始日期' after `realEnd`,
                       add maintenanceStaff varchar(255)  null comment '允许报工维护人员' after `realEnd`;

ALTER TABLE `zt_problem` add repeatProblem Text null comment '重复问题单' after `type`;


ALTER TABLE `zt_demand`
    ADD COLUMN `application` varchar(255) COMMENT '用于创建任务的判断依据';


ALTER TABLE `zt_problem`
    ADD COLUMN `coordinators`  varchar(255) NULL DEFAULT '' COMMENT '配合人员 逗号分隔' AFTER `dealUser`;

ALTER TABLE `zt_demand`
    ADD COLUMN `coordinators`  varchar(255) NULL DEFAULT '' COMMENT '配合人员 逗号分隔' AFTER `actualOnlineDate`;

ALTER TABLE `zt_build`
    ADD FULLTEXT INDEX `problemid` (`problemid`) ;


update zt_problem set `status` = 'build' where `status` in ("testsuccess","solved","waitverify","verifysuccess","testfailed","versionfailed","verifyfailed");
update zt_demand set `status` = 'build' where `status` in ("solved","waitverify","testsuccess","verifysuccess","testfailed","versionfailed","verifyfailed");
update zt_problem set `status` = 'closed', closedBy = 'admin', closedDate = now() where `status` = "suspend";
update zt_demand set `status` = 'closed', closedBy = 'admin', closedDate = now() where `status` = "suspend";
update zt_demand set `status` = 'wait' where `status` in ("assigned");
update zt_demand set `status` = 'delivery' where `status` in ("onlinefailed");
update zt_problem set `status` = 'delivery' where `status` in ("onlinefailed");

-- ----------------------------
-- 2023-03-09 wangshusen 更新线上需求意向状态为已上线，无实际上线时间
-- ----------------------------
update zt_opinion set `onlineTimeByDemand` = '2022-10-20 02:00:05' where  `id` = 323;
update zt_opinion set `onlineTimeByDemand` = '2022-11-08 02:00:07' where  `id` = 300;
update zt_opinion set `onlineTimeByDemand` = '2022-11-17 02:00:07' where  `id` = 297;
update zt_opinion set `onlineTimeByDemand` = '2022-11-16 02:00:08' where  `id` = 268;
update zt_opinion set `onlineTimeByDemand` = '2022-10-10 11:04:05' where  `id` = 253;
update zt_opinion set `onlineTimeByDemand` = '2022-11-16 02:00:08' where  `id` = 246;
update zt_opinion set `onlineTimeByDemand` = '2022-10-10 11:04:05' where  `id` = 240;

update zt_requirement set `onlineTimeByDemand` = '2022-10-24 02:00:04' where  `id` = 755;
update zt_requirement set `onlineTimeByDemand` = '2022-11-15 02:00:08' where  `id` = 777;
update zt_requirement set `onlineTimeByDemand` = '2022-10-10 11:04:04' where  `id` = 796;
update zt_requirement set `onlineTimeByDemand` = '2022-10-10 11:04:04' where  `id` = 857;
update zt_requirement set `onlineTimeByDemand` = '2022-11-02 02:00:06' where  `id` = 861;
update zt_requirement set `onlineTimeByDemand` = '2022-10-10 11:04:04' where  `id` = 867;
update zt_requirement set `onlineTimeByDemand` = '2022-10-31 02:00:05' where  `id` = 873;
update zt_requirement set `onlineTimeByDemand` = '2022-11-16 02:00:06' where  `id` = 879;
update zt_requirement set `onlineTimeByDemand` = '2022-10-10 11:04:04' where  `id` = 884;
update zt_requirement set `onlineTimeByDemand` = '2022-10-10 11:04:04' where  `id` = 897;
update zt_requirement set `onlineTimeByDemand` = '2023-03-07 12:20:05' where  `id` = 911;
update zt_requirement set `onlineTimeByDemand` = '2022-11-16 02:00:06' where  `id` = 920;
update zt_requirement set `onlineTimeByDemand` = '2022-11-17 02:00:06' where  `id` = 1105;
update zt_requirement set `onlineTimeByDemand` = '2022-11-08 02:00:06' where  `id` = 1013;