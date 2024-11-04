-- 问题单-变更
CREATE TABLE `zt_problem_change`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `objectType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `objectId` int(11) NOT NULL COMMENT 'ID',
  `changeOriginalResolutionDate` datetime NOT null COMMENT '变更前【计划解决（变更）时间】',
  `changeResolutionDate` datetime NOT null COMMENT '变更后【计划解决（变更）时间】',
  `changeReason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin null  COMMENT '申请变更原因',
  `changeStatus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT null comment '变更状态',
  `changeVersion` int(11) NOT null COMMENT '变更发起次数',
  `changeStage` int(11) NULL DEFAULT null ,
  `changeDealUser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `changeUser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT null COMMENT '由谁延期',
  `changeDate` datetime NOT null COMMENT '变更日期',
  `changeCommunicate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '外部沟通结果',
  `successVersion` int(11) NOT NULL DEFAULT 0 COMMENT '变更成功次数',
  `changeContent` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '变更内容（主要是计划解决时间前后差异）',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type_objectId`(`objectType` ASC, `objectId` ASC) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4  COMMENT='问题单变更表';