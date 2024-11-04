set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------------------------------------------
-- 迭代29 2023-07-24  shixuyang 二线工单增加移交方式
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_secondorder ADD `handoverMethod` varchar(32) NULL COMMENT '移交方式';

-- ------------------------------------
-- 迭代29 wangyongliang 项目工程实施计划表
-- ------------------------------------
CREATE TABLE `zt_implementionplan` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `projectID` int(11)  DEFAULT NULL COMMENT '项目id',
                                       `uploadTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上传时间',
                                       `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '项目工程实施计划名称',
                                       `uploadPerson` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '上传人',
                                       `level` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变更级别',
                                       `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否刪除',
                                       `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
                                       `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间',
                                       PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目工程实施计划';


-- ---------------------------------
-- 迭代29 wangyongliang 项目表增加字段
-- ---------------------------------
ALTER TABLE `zt_project` ADD COLUMN `dataVersion` MEDIUMINT DEFAULT 1 NOT NULL COMMENT '数据版本（1：历史数据 2：新数据）' after `deleted`,
           ADD COLUMN `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
           ADD COLUMN  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间';

-- ---------------------------------
-- 迭代29 wangyongliang 任务表增加字段
-- ---------------------------------
ALTER TABLE `zt_task` ADD COLUMN `dataVersion` MEDIUMINT DEFAULT 1 NOT NULL COMMENT '数据版本（1：历史数据 2：新数据）' after `deleted`,
            ADD COLUMN `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
           ADD COLUMN  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间';

-- ---------------------------------
-- 迭代29 wangyongliang 工时表增加字段
-- ---------------------------------
ALTER TABLE `zt_effort` ADD COLUMN `dataVersion` MEDIUMINT DEFAULT 1 NOT NULL COMMENT '数据版本（1：历史数据 2：新数据）' after `deleted`,
           ADD COLUMN `workID` int(11)  DEFAULT 0 COMMENT '报工id',
           ADD COLUMN `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
           ADD COLUMN  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间';


-- ---------------------------------
-- 迭代29 wangyongliang 报工表
-- ---------------------------------
CREATE TABLE `zt_workreport` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `project` int(11)  DEFAULT NULL COMMENT '项目id',
                                 `activity` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所属活动',
                                 `apps` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所属阶段/系统',
                                 `objects` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所属对象',
                                 `beginDate` datetime  DEFAULT NULL COMMENT '开始时间',
                                 `consumed` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '耗时',
                                 `workType` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '工作类型',
                                 `workContent` text CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT '工作内容',
                                 `account` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '填报人',
                                 `editTime` datetime DEFAULT NULL COMMENT '创建时间',
                                 `editedBy` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '由谁编辑',
                                 `versions` int(11) DEFAULT NULL COMMENT '提交版本',
                                 `weeklyNum` int(11)  DEFAULT NULL COMMENT '第几周',
                                 `append` int(11)  DEFAULT 0 NULL COMMENT '是否补报 1:是、 0：否',
                                 `deleted` int(11)  DEFAULT 0 COMMENT '是否删除',
                                 `createtime` datetime  DEFAULT CURRENT_TIMESTAMP   COMMENT '创建时间',
                                 `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间',
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我要报工';

-- ---------------------------------
-- 迭代29 wangyongliang 制版表更新字段
-- ---------------------------------
ALTER TABLE `zt_build`
    MODIFY COLUMN `execution`  varchar(255)  NOT NULL DEFAULT '0';

-- -----------------------------------------------------------------------------------------
-- 迭代29 2023-8-8  liushengjie 问题池新增【部门审核首次通过时间】、【产创审核首次通过时间】、【外单位是否更新】
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_problem ADD COLUMN `deptPassTime` datetime DEFAULT NULL COMMENT '部门审核首次通过时间';
ALTER TABLE zt_problem ADD COLUMN `innovationPassTime` datetime DEFAULT NULL COMMENT '产创审核首次通过时间';
ALTER TABLE zt_problem ADD COLUMN `feedbackStartTimeInside` datetime DEFAULT NULL COMMENT '内部反馈开始时间';
ALTER TABLE zt_problem ADD COLUMN `feedbackStartTimeOutside` datetime DEFAULT NULL COMMENT '外部反馈开始时间';
ALTER TABLE zt_problem ADD COLUMN `feedbackEndTimeInside` datetime DEFAULT NULL COMMENT '内部反馈截止时间';
ALTER TABLE zt_problem ADD COLUMN `feedbackEndTimeOutside` datetime DEFAULT NULL COMMENT '外部反馈截止时间';
ALTER TABLE zt_problem ADD COLUMN `isChange` tinyint DEFAULT 0 COMMENT '外单位是否更新';
ALTER TABLE zt_problem ADD COLUMN `isChangeFeedbackTime` tinyint DEFAULT 0 COMMENT '是否更新反馈期限';



-- -----------------------------------------------------------------------------------------
-- 迭代29 2023-8-8  wangshusen 增加需求任务外部反馈开始时间字段
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_requirement ADD COLUMN `feekBackStartTimeOutside` datetime DEFAULT NULL COMMENT '外部反馈开始时间';
ALTER TABLE zt_requirement ADD COLUMN `isUpdateOverStatus` tinyint(3)  COLLATE utf8mb4_bin NOT NULL DEFAULT 1  COMMENT '是否更新超时状态 1：更新 2：不更新';
ALTER TABLE zt_requirement ADD COLUMN `ifOutUpdate` tinyint(3)  COLLATE utf8mb4_bin NOT NULL DEFAULT 1  COMMENT '外部单位是否更新 1：更新过 2：未更新';

-- -----------------------------------------------------------------------------------------
-- 紧急需求2746 2023-08-24 shixuyang
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_modifycncc ADD COLUMN `applyReasonOutWindow` text DEFAULT NULL COMMENT '重要变更窗口外申请原因';
ALTER TABLE zt_modifycncc ADD COLUMN `keyGuaranteePeriodApplyReason` text DEFAULT NULL COMMENT '重保期变更必要性说明';

-- -----------------------------------------------------------------------------------------
-- 迭代29 2023-8-25  wangyongliang 中间表加更新时间
-- -----------------------------------------------------------------------------------------
ALTER TABLE zt_task_demand_problem
    ADD COLUMN  `updatetime` datetime   DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '更新时间';

-- -----------------------------------------------------------------------------------------
-- 迭代29 2023-8-31  wangshusen 增加自定义配置以及初始化需求池超时相关字段
-- -----------------------------------------------------------------------------------------
UPDATE `zt_lang` SET `value` = '60' WHERE `id` = '4378';
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'demandToOutTime', 'demandToOutTime', '55', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'requirementToOutTimeOutside', '', '', '1');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'requirementToOutTimeOutside', 'requireOut', '8', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'requirementOutTimeOutside', '', '', '1');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'requirementOutTimeOutside', 'requireToOut', '5', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'requirementToOutTime', 'requireOutTime', '5', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'demand', 'requirementOutTime', 'requireToOutTime', '2', '0');

UPDATE zt_requirement SET
                          ifOverDate = 100,ifOverTimeOutSide = 100,
                          feekBackStartTime = null,feekBackEndTimeInside = null,
                          feekBackStartTimeOutside = null,feekBackEndTimeOutSide = null,
                          deptPassTime = null,innovationPassTime = null
WHERE createdBy = 'guestcn';

-- -----------------------------------------------------------------------------------------
-- 迭代29 2023-8-30  liushengjie 问题单邮件提醒自定义后台配置
-- -----------------------------------------------------------------------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','inQzFBToTime','inQzFBToTime','2','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','inQzFBOutTime','inQzFBOutTime','4','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','inJxFBToTime','inJxFBToTime','13','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','inJxFBOutTime','inJxFBOutTime','15','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','outQzFBToTime','outQzFBToTime','2','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','outQzFBOutTime','outQzFBOutTime','4','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','outJxFBToTime','outJxFBToTime','13','1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','outJxFBOutTime','outJxFBOutTime','15','1');

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setInFBToTimeMail', '{\"mailTitle\":\"\\u3010\\u5f85\\u529e\\u3011\\u5185\\u90e8\\u53cd\\u9988\\u5373\\u5c06\\u8d85\\u671f\\u63d0\\u9192\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406\",\"mailContent\":\"&lt;span style=&quot;font-weight:700;&quot;&gt;\\u8bf7\\u8fdb\\u5165&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\u3010\\u95ee\\u9898\\u6c60\\u3011\\u67e5\\u770b&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a&lt;\\/span&gt;\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setInFBOutTimeMail', '{\"mailTitle\":\"\\u3010\\u901a\\u77e5\\u3011\\u5185\\u90e8\\u53cd\\u9988\\u5df2\\u8d85\\u671f\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406\",\"mailContent\":\"&lt;span style=&quot;font-weight:700;&quot;&gt;\\u8bf7\\u8fdb\\u5165&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\u3010\\u95ee\\u9898\\u6c60\\u3011\\u67e5\\u770b&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a&lt;\\/span&gt;\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setOutFBToTimeMail', '{\"mailTitle\":\"\\u3010\\u5f85\\u529e\\u3011\\u5916\\u90e8\\u53cd\\u9988\\u5373\\u5c06\\u8d85\\u671f\\u63d0\\u9192\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406\",\"mailContent\":\"&lt;span style=&quot;font-weight:700;&quot;&gt;\\u8bf7\\u8fdb\\u5165&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\u3010\\u95ee\\u9898\\u6c60\\u3011\\u67e5\\u770b&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a&lt;\\/span&gt;\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setOutFBOutTimeMail', '{\"mailTitle\":\"\\u3010\\u901a\\u77e5\\u3011\\u5916\\u90e8\\u53cd\\u9988\\u5df2\\u8d85\\u671f\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406\",\"mailContent\":\"&lt;span style=&quot;font-weight:700;&quot;&gt;\\u8bf7\\u8fdb\\u5165&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\u3010\\u95ee\\u9898\\u6c60\\u3011\\u67e5\\u770b&lt;\\/span&gt;&lt;span style=&quot;font-weight:700;&quot;&gt;\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a&lt;\\/span&gt;\"}');
