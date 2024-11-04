ALTER TABLE `zt_requirement` ADD COLUMN `ifOverDate`  varchar(10) DEFAULT 100 COMMENT '内部反馈是否超时 100表示需修改，通过脚本修改 1：否 2：是';
ALTER TABLE `zt_requirement` ADD COLUMN `ifOverTimeOutSide`  varchar(10) DEFAULT 100 COMMENT '外部反馈是否超时 100表示需修改，通过脚本修改 1：否 2：是';
ALTER TABLE `zt_demand` MODIFY COLUMN `actualOnlineDate` datetime DEFAULT NULL COMMENT '实际上线时间';

-- 内部反馈超时5个工作日  外部8个工作日
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','demand','expireDaysList','insideDays','5','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','demand','expireDaysList','outsideDays','8','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','closePersonList','qzDealAccount','litianzi','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','closePersonList','jxDealAccount','zhangyun','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','OverDateList','openType','1','1');
update zt_lang set value ='15' where id ='2500';
ALTER TABLE `zt_problem`
    ADD COLUMN `dealAssigned`  datetime  DEFAULT '0000-00-00 00:00:00' COMMENT '待分配到待分析处理时间' AFTER `ifOverDate`,
    ADD COLUMN `dealFeedbackPass`  datetime  DEFAULT '0000-00-00 00:00:00' COMMENT '反馈单通过时间' AFTER `dealAssigned`,
    ADD COLUMN `problemGrade`  varchar(255)  DEFAULT '' COMMENT '问题分级' AFTER `dealFeedbackPass`,
    ADD COLUMN `standardVerify`  varchar(255)  DEFAULT '' COMMENT '是否基准验证' AFTER `problemGrade`;

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushProblemCommentUrl', 'http://plcm.cncc.cn:30080/api/project/apps/api/v1/osc/apps/yinqing_jinke_sync/environments/production/webtriggers/sync-problem-saveStatusFromJinKe');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushProblemCommentAppId', 'jinke');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushProblemCommentAppSecret', '482733936f2e45eaba0cc5768e5541eb');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushProblemCommentEnable', 'enable');

ALTER TABLE `zt_problem`
    MODIFY COLUMN `closedDate`  datetime NULL ;

ALTER TABLE `zt_case` ADD COLUMN `categories` varchar(50) NULL DEFAULT '' COMMENT '自动化分类 逗号分隔' AFTER `title`;