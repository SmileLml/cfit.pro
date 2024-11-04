set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ------------------------------ ----------------------------
-- 需求条目新增字段【二线研发计划】【二线研发状态】【是否已核定】【二线月报跟踪标记位】
-- ------------------------------ ----------------------------
ALTER TABLE `zt_demand`
    ADD COLUMN `secondLineDevelopmentPlan` text NOT NULL COMMENT '二线研发计划',
    ADD COLUMN `secondLineDevelopmentStatus` varchar(30) DEFAULT '' COMMENT '二线研发状态',
    ADD COLUMN `secondLineDevelopmentApproved` varchar(30) DEFAULT '' COMMENT '是否已核定',
    ADD COLUMN `secondLineDevelopmentRecord` enum('1','2') NOT NULL DEFAULT '1' COMMENT '二线月报跟踪标记位 1:纳入 2：不纳入';

--外部需求池
--是否已核定 1是 2否 3不涉及
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'ifApprovedList', '', '', '1', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'ifApprovedList', 'yes', '是', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'ifApprovedList', 'no', '否', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'ifApprovedList', 'noInvolved', '不涉及', '0', 0);

--二线研发状态
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', '', '', '1', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'noStart', '未启动', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'normal', '进度正常', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'deliverOnSchedule', '按期交付', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'delayedDeliver', '延期交付', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'deliverOnline', '按期上线', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'delayedOnline', '延期上线', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'closed', '已关闭', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'revoke', '已撤销', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'pause', '已暂停', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demand', 'secondLineDepStatusList', 'progressDelay', '进度延迟', '0', 0);

--内部需求池
--是否已核定 1是 2否 3不涉及
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'ifApprovedList', '', '', '1', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'ifApprovedList', 'yes', '是', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'ifApprovedList', 'no', '否', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'ifApprovedList', 'noInvolved', '不涉及', '0', 0);

--二线研发状态
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', '', '', '1', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'noStart', '未启动', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'normal', '进度正常', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'deliverOnSchedule', '按期交付', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'delayedDeliver', '延期交付', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'deliverOnline', '按期上线', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'delayedOnline', '延期上线', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'closed', '已关闭', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'revoke', '已撤销', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'pause', '已暂停', '0', 0);
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`) VALUES('zh-cn', 'demandinside', 'secondLineDepStatusList', 'progressDelay', '进度延迟', '0', 0);


-- ------------------------------ ----------------------------
-- 问题单新增字段【二线研发计划】【二线研发状态】【是否已核定】【二线月报跟踪标记位】wangyongliang
-- ------------------------------ ----------------------------
ALTER TABLE `zt_problem`
    ADD COLUMN `secondLineDevelopmentPlan` text NOT NULL COMMENT '二线研发计划',
    ADD COLUMN `secondLineDevelopmentStatus` varchar(30) DEFAULT '' COMMENT '二线研发状态',
    ADD COLUMN `secondLineDevelopmentApproved` varchar(30) DEFAULT '' COMMENT '是否已核定',
    ADD COLUMN `secondLineDevelopmentRecord` enum('1','2') NOT NULL DEFAULT '2' COMMENT '二线月报跟踪标记位 1:纳入 2：不纳入';

-- ------------------------------ ----------------------------
-- 任务工单 新增字段【二线研发计划】【二线研发状态】【是否已核定】【二线月报跟踪标记位】wangyongliang
-- ------------------------------ ----------------------------
ALTER TABLE `zt_secondorder`
    ADD COLUMN `secondLineDevelopmentPlan` text NOT NULL COMMENT '二线研发计划',
    ADD COLUMN `secondLineDevelopmentStatus` varchar(30) DEFAULT '' COMMENT '二线研发状态',
    ADD COLUMN `secondLineDevelopmentApproved` varchar(30) DEFAULT '' COMMENT '是否已核定',
    ADD COLUMN `secondLineDevelopmentRecord` enum('1','2') NOT NULL DEFAULT '2' COMMENT '二线月报跟踪标记位 1:纳入 2：不纳入';

-- ------------------------------ ----------------------------
-- 部门工单 新增字段【二线研发计划】【二线研发状态】【是否已核定】【二线月报跟踪标记位】wangyongliang
-- ------------------------------ ----------------------------
ALTER TABLE `zt_deptorder`
    ADD COLUMN `secondLineDevelopmentPlan` text NOT NULL COMMENT '二线研发计划',
    ADD COLUMN `secondLineDevelopmentStatus` varchar(30) DEFAULT '' COMMENT '二线研发状态',
    ADD COLUMN `secondLineDevelopmentApproved` varchar(30) DEFAULT '' COMMENT '是否已核定',
    ADD COLUMN `secondLineDevelopmentRecord` enum('1','2') NOT NULL DEFAULT '2' COMMENT '二线月报跟踪标记位 1:纳入 2：不纳入';









