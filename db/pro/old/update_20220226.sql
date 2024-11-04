CREATE TABLE `zt_flowworkload` (
    `id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `projectID` mediumint(8) DEFAULT 0 COMMENT '项目id',
    `projectName` varchar(255) NOT NULL COMMENT '项目名称',
    `projectMark` char(30) NOT NULL COMMENT '项目代号',
    `projectCode` varchar(255) NOT NULL COMMENT '项目编号',
    `createdBy` varchar(30) DEFAULT '' COMMENT '发起人',
    `createdDept` mediumint(8) NOT NULL DEFAULT '0' COMMENT '所属部门',
    `reviewID` mediumint(8) DEFAULT 0 COMMENT '评审ID',
    `reviewName` varchar(255) NOT NULL COMMENT '评审名称',
    `status` char(30) NOT NULL COMMENT '流程状态',
    `type` char(30) NOT NULL COMMENT '评审类型',
    `trialDept` varchar(100) DEFAULT '' COMMENT '初审部门',
    `trialDeptLiasisonOfficer` varchar(255) DEFAULT '' COMMENT '初审接口人',
    `trialAdjudicatingOfficer` varchar(255) DEFAULT '' COMMENT '初审主审人员',
    `trialJoinOfficer` varchar(255) DEFAULT '' COMMENT '初审参与人员',
    `owner` varchar(255) NOT NULL DEFAULT '' COMMENT '评审主席',
    `qa` varchar(50) NOT NULL DEFAULT '' COMMENT '质量部QA',
    `qualityCm` varchar(100) DEFAULT '' COMMENT '质量部CM',
    `onLineExpert` varchar(100) DEFAULT '' COMMENT '实际在线评审专家',
    `realExpert` varchar(255) DEFAULT '' COMMENT '实际会议评审专家',
    `verifier` varchar(100) DEFAULT '' COMMENT '实际验证人员',
    `createdDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
    `firstPreReviewDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '申请时间',
    `closeTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '关闭时间',
    `baselineDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '基线完成时间',
    `suspendTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '挂起时间',
    `renewTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '恢复时间',
    `insertTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '数据插入时间',
    `preReviewBefore` varchar(10) DEFAULT '' COMMENT '预审前',
    `close` varchar(10) DEFAULT '' COMMENT '关闭',
    `preEdit` varchar(10) DEFAULT '' COMMENT '预审-修改',
    `firstEdit` varchar(10) DEFAULT '' COMMENT '初审-修改',
    `formalEdit` varchar(10) DEFAULT '' COMMENT '正式评审-线上评审修改',
    `meetingEdit` varchar(10) DEFAULT '' COMMENT '正式评审-会议评审修改',
    `outEdit` varchar(10) DEFAULT '' COMMENT '外部评审-修改',
    `verifyEdit` varchar(10) DEFAULT '' COMMENT '验证-修改',
    `suspend` varchar(10) DEFAULT '' COMMENT '挂起',
    `renew` varchar(10) DEFAULT '' COMMENT '恢复',
    `preReview` varchar(10) DEFAULT '' COMMENT '预审',
    `firstAssignDept` varchar(10) DEFAULT '' COMMENT '初审-指派初审部门',
    `firstAssignReviewer` varchar(10) DEFAULT '' COMMENT '初审-指派初审人员',
    `firstReview` varchar(10) DEFAULT '' COMMENT '初审人员审核',
    `firstMainReview` varchar(10) DEFAULT '' COMMENT '确定初审结果',
    `formalAssignReviewer` varchar(10) DEFAULT '' COMMENT '指派评审专家',
    `formalReview` varchar(10) DEFAULT '' COMMENT '专家在线评审',
    `formalOwnerReview` varchar(10) DEFAULT '' COMMENT '确定线上评审结论',
    `meetingReview` varchar(10) DEFAULT '' COMMENT '专家会议评审',
    `meetingOwnerReview` varchar(10) DEFAULT '' COMMENT '确定会议评审结论',
    `verify` varchar(10) DEFAULT '' COMMENT '验证评审材料',
    `outReview` varchar(10) DEFAULT '' COMMENT '外部审核',
    `archive` varchar(10) DEFAULT '' COMMENT '归档',
    `baseline` varchar(10) DEFAULT '' COMMENT '打基线',
    `recall` varchar(10) DEFAULT '' COMMENT '撤回',
    `rejectPreEdit` varchar(10) DEFAULT '' COMMENT '预审退回修改',
    `rejectFirstEdit` varchar(10) DEFAULT '' COMMENT '初审退回修改',
    `rejectFormalEdit` varchar(10) DEFAULT '' COMMENT '正式评审-线上评审退回修改',
    `rejectMeetingEdit` varchar(10) DEFAULT '' COMMENT '正式评审-线上评审退回修改',
    `rejectOutEdit` varchar(10) DEFAULT '' COMMENT '外部评审退回修改',
    `rejectVerifyEdit` varchar(10) DEFAULT '' COMMENT '验证退回修改',
    `updateFiles` varchar(10) DEFAULT '' COMMENT '修改上传附件',
    `deleted` tinyint(1) DEFAULT '0' COMMENT '是否删除',
    PRIMARY KEY (`id`),
    KEY `projectID` (`projectID`) USING BTREE,
    KEY `reviewID` (`reviewID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `zt_flowcostworkload` (
    `id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `projectID` mediumint(8) DEFAULT 0 COMMENT '项目id',
    `projectName` varchar(255) NOT NULL COMMENT '项目名称',
    `projectMark` char(30) NOT NULL COMMENT '项目代号',
    `projectCode` varchar(255) NOT NULL COMMENT '项目编号',
    `createdBy` varchar(30) DEFAULT '' COMMENT '发起人',
    `createdDept` mediumint(8) NOT NULL DEFAULT '0' COMMENT '所属部门',
    `reviewID` mediumint(8) DEFAULT 0 COMMENT '评审ID',
    `reviewName` varchar(255) NOT NULL COMMENT '评审名称',
    `status` char(30) NOT NULL COMMENT '流程状态',
    `type` char(30) NOT NULL COMMENT '评审类型',
    `trialDept` varchar(100) DEFAULT '' COMMENT '初审部门',
    `trialDeptLiasisonOfficer` varchar(255) DEFAULT '' COMMENT '初审接口人',
    `trialAdjudicatingOfficer` varchar(255) DEFAULT '' COMMENT '初审主审人员',
    `trialJoinOfficer` varchar(255) DEFAULT '' COMMENT '初审参与人员',
    `owner` varchar(255) NOT NULL DEFAULT '' COMMENT '评审主席',
    `qa` varchar(50) NOT NULL DEFAULT '' COMMENT '质量部QA',
    `qualityCm` varchar(100) DEFAULT '' COMMENT '质量部CM',
    `onLineExpert` varchar(100) DEFAULT '' COMMENT '实际在线评审专家',
    `realExpert` varchar(255) DEFAULT '' COMMENT '实际会议评审专家',
    `verifier` varchar(100) DEFAULT '' COMMENT '实际验证人员',
    `createdDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
    `firstPreReviewDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '申请时间',
    `closeTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '关闭时间',
    `baselineDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '基线完成时间',
    `suspendTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '挂起时间',
    `renewTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '恢复时间',
    `insertTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '数据插入时间',
    `reviewDays` varchar(50) DEFAULT '' COMMENT '评审天数',
    `preReviewDays` varchar(50) DEFAULT '' COMMENT '预审前天数',
    `preReviewBefore` varchar(50) DEFAULT '' COMMENT '预审前',
    `close` varchar(50) DEFAULT '' COMMENT '关闭',
    `preEdit` varchar(50) DEFAULT '' COMMENT '预审-修改',
    `firstEdit` varchar(50) DEFAULT '' COMMENT '初审-修改',
    `formalEdit` varchar(50) DEFAULT '' COMMENT '正式评审-线上评审修改',
    `meetingEdit` varchar(50) DEFAULT '' COMMENT '正式评审-会议评审修改',
    `outEdit` varchar(50) DEFAULT '' COMMENT '外部评审-修改',
    `verifyEdit` varchar(50) DEFAULT '' COMMENT '验证-修改',
    `suspend` varchar(50) DEFAULT '' COMMENT '挂起',
    `renew` varchar(50) DEFAULT '' COMMENT '恢复',
    `preReview` varchar(50) DEFAULT '' COMMENT '预审',
    `firstAssignDept` varchar(50) DEFAULT '' COMMENT '初审-指派初审部门',
    `firstAssignReviewer` varchar(50) DEFAULT '' COMMENT '初审-指派初审人员',
    `firstReview` varchar(50) DEFAULT '' COMMENT '初审人员审核',
    `firstMainReview` varchar(50) DEFAULT '' COMMENT '确定初审结果',
    `formalAssignReviewer` varchar(50) DEFAULT '' COMMENT '指派评审专家',
    `formalReview` varchar(50) DEFAULT '' COMMENT '专家在线评审',
    `formalOwnerReview` varchar(50) DEFAULT '' COMMENT '确定线上评审结论',
    `meetingReview` varchar(50) DEFAULT '' COMMENT '专家会议评审',
    `meetingOwnerReview` varchar(50) DEFAULT '' COMMENT '确定会议评审结论',
    `verify` varchar(50) DEFAULT '' COMMENT '验证评审材料',
    `outReview` varchar(50) DEFAULT '' COMMENT '外部审核',
    `archive` varchar(50) DEFAULT '' COMMENT '归档',
    `baseline` varchar(50) DEFAULT '' COMMENT '打基线',
    `recall` varchar(50) DEFAULT '' COMMENT '撤回',
    `rejectPreEdit` varchar(50) DEFAULT '' COMMENT '预审退回修改',
    `rejectFirstEdit` varchar(50) DEFAULT '' COMMENT '初审退回修改',
    `rejectFormalEdit` varchar(50) DEFAULT '' COMMENT '正式评审-线上评审退回修改',
    `rejectMeetingEdit` varchar(50) DEFAULT '' COMMENT '正式评审-线上评审退回修改',
    `rejectOutEdit` varchar(50) DEFAULT '' COMMENT '外部评审退回修改',
    `rejectVerifyEdit` varchar(50) DEFAULT '' COMMENT '验证退回修改',
    `updateFiles` varchar(50) DEFAULT '' COMMENT '修改上传附件',
    `deleted` tinyint(1) DEFAULT '0' COMMENT '是否删除',
    PRIMARY KEY (`id`),
    KEY `projectID` (`projectID`) USING BTREE,
    KEY `reviewID` (`reviewID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `zt_participantsworkload` (
    `id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `projectID` mediumint(8) DEFAULT 0 COMMENT '项目id',
    `projectName` varchar(255) NOT NULL COMMENT '项目名称',
    `projectMark` char(30) NOT NULL COMMENT '项目代号',
    `projectCode` varchar(255) NOT NULL COMMENT '项目编号',
    `createdBy` varchar(30) DEFAULT '' COMMENT '发起人',
    `createdDept` mediumint(8) NOT NULL DEFAULT '0' COMMENT '所属部门',
    `reviewID` mediumint(8) DEFAULT 0 COMMENT '评审ID',
    `reviewName` varchar(255) NOT NULL COMMENT '评审名称',
    `status` char(30) NOT NULL COMMENT '流程状态',
    `type` char(30) NOT NULL COMMENT '评审类型',
    `trialDept` varchar(100) DEFAULT '' COMMENT '初审部门',
    `trialDeptLiasisonOfficer` varchar(255) DEFAULT '' COMMENT '初审接口人',
    `trialAdjudicatingOfficer` varchar(255) DEFAULT '' COMMENT '初审主审人员',
    `trialJoinOfficer` varchar(255) DEFAULT '' COMMENT '初审参与人员',
    `blockDept` mediumint(8) DEFAULT '0' COMMENT '参与部门',
    `blockMember` char(30) COMMENT '参与部门',
    `blockTotal` varchar(30) DEFAULT '' COMMENT '累计工作量(小时)',
    `blockPerMonth` varchar(30) DEFAULT '' COMMENT '累计工作量(人月)',
    `owner` varchar(255) NOT NULL DEFAULT '' COMMENT '评审主席',
    `qa` varchar(50) NOT NULL DEFAULT '' COMMENT '质量部QA',
    `qualityCm` varchar(100) DEFAULT '' COMMENT '质量部CM',
    `onLineExpert` varchar(100) DEFAULT '' COMMENT '实际在线评审专家',
    `realExpert` varchar(255) DEFAULT '' COMMENT '实际会议评审专家',
    `verifier` varchar(100) DEFAULT '' COMMENT '实际验证人员',
    `createdDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
    `firstPreReviewDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '申请时间',
    `closeTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '关闭时间',
    `baselineDate` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '基线完成时间',
    `suspendTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '挂起时间',
    `renewTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '恢复时间',
    `insertTime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '数据插入时间',
    `deleted` tinyint(1) DEFAULT '0' COMMENT '是否删除',
    PRIMARY KEY (`id`),
    KEY `projectID` (`projectID`) USING BTREE,
    KEY `reviewID` (`reviewID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table zt_kanban
(
    id             mediumint unsigned auto_increment
        primary key,
    space          mediumint unsigned                         not null,
    name           varchar(255)                               not null,
    owner          varchar(30)                                not null,
    team           text                                       not null,
    `desc`         mediumtext                                 not null,
    acl            char(30)                  default 'open'   not null,
    whitelist      text                                       not null,
    archived       enum ('0', '1')           default '1'      not null,
    performable    enum ('0', '1')           default '0'      not null,
    status         enum ('active', 'closed') default 'active' not null,
    `order`        mediumint                 default 0        not null,
    displayCards   smallint                  default 0        not null,
    showWIP        enum ('0', '1')           default '1'      not null,
    fluidBoard     enum ('0', '1')           default '0'      not null,
    colWidth       smallint                  default 264      not null,
    minColWidth    smallint                  default 200      not null,
    maxColWidth    smallint                  default 384      not null,
    object         varchar(255)                               not null,
    alignment      varchar(10)               default 'center' not null,
    createdBy      char(30)                                   not null,
    createdDate    datetime                                   not null,
    lastEditedBy   char(30)                                   not null,
    lastEditedDate datetime                                   not null,
    closedBy       char(30)                                   not null,
    closedDate     datetime                                   not null,
    activatedBy    char(30)                                   not null,
    activatedDate  datetime                                   not null,
    deleted        enum ('0', '1')           default '0'      not null
)
    charset = utf8;

create table zt_kanbancard
(
    id             mediumint unsigned auto_increment
        primary key,
    kanban         mediumint unsigned              not null,
    region         mediumint unsigned              not null,
    `group`        mediumint unsigned              not null,
    fromID         mediumint unsigned              not null,
    fromType       varchar(30)                     not null,
    name           varchar(255)                    not null,
    status         varchar(30)     default 'doing' not null,
    pri            mediumint unsigned              not null,
    assignedTo     text                            not null,
    `desc`         mediumtext                      not null,
    begin          date                            not null,
    end            date                            not null,
    estimate       float unsigned                  not null,
    consumed       float unsigned                  null,
    `left`         float unsigned                  null,
    progress       float unsigned  default '0'     not null,
    color          char(7)                         not null,
    acl            char(30)        default 'open'  not null,
    whitelist      text                            not null,
    `order`        mediumint       default 0       not null,
    archived       enum ('0', '1') default '0'     not null,
    createdBy      char(30)                        not null,
    createdDate    datetime                        not null,
    lastEditedBy   char(30)                        not null,
    lastEditedDate datetime                        not null,
    archivedBy     char(30)                        not null,
    deleted        enum ('0', '1') default '0'     not null,
    assignedBy     char(30)                        not null,
    assignedDate   datetime                        not null,
    archivedDate   datetime                        not null
)
    charset = utf8;

create table zt_kanbancell
(
    id       int auto_increment
        primary key,
    kanban   mediumint not null,
    lane     mediumint not null,
    `column` mediumint not null,
    type     char(30)  not null,
    cards    text      not null,
    constraint card_group
        unique (kanban, type, lane, `column`)
)
    charset = utf8;

create table zt_kanbancolumn
(
    id       int auto_increment
        primary key,
    parent   mediumint       default 0   not null,
    type     char(30)                    not null,
    region   mediumint unsigned          not null,
    `group`  mediumint       default 0   not null,
    name     varchar(255)    default ''  not null,
    color    char(30)                    not null,
    `limit`  smallint        default -1  not null,
    `order`  mediumint       default 0   not null,
    archived enum ('0', '1') default '0' not null,
    deleted  enum ('0', '1') default '0' not null
)
    charset = utf8;

create table zt_kanbangroup
(
    id      mediumint unsigned auto_increment
        primary key,
    kanban  mediumint unsigned not null,
    region  mediumint unsigned not null,
    `order` smallint default 0 not null
)
    charset = utf8;

create table zt_kanbanlane
(
    id             int auto_increment
        primary key,
    execution      mediumint       default 0   not null,
    type           char(30)                    not null,
    region         mediumint unsigned          not null,
    `group`        mediumint unsigned          not null,
    groupby        char(30)                    not null,
    extra          char(30)                    not null,
    name           varchar(255)    default ''  not null,
    color          char(30)                    not null,
    `order`        smallint        default 0   not null,
    lastEditedTime datetime                    not null,
    deleted        enum ('0', '1') default '0' not null
)
    charset = utf8;

create table zt_kanbanregion
(
    id             mediumint unsigned auto_increment
        primary key,
    space          mediumint unsigned          not null,
    kanban         mediumint unsigned          not null,
    name           varchar(255)                not null,
    `order`        mediumint       default 0   not null,
    createdBy      char(30)                    not null,
    createdDate    datetime                    not null,
    lastEditedBy   char(30)                    not null,
    lastEditedDate datetime                    not null,
    deleted        enum ('0', '1') default '0' not null
)
    charset = utf8;

create table zt_kanbanspace
(
    id             mediumint unsigned auto_increment
        primary key,
    name           varchar(255)                               not null,
    type           varchar(50)                                not null,
    owner          varchar(30)                                not null,
    team           text                                       not null,
    `desc`         mediumtext                                 not null,
    acl            char(30)                  default 'open'   not null,
    whitelist      text                                       not null,
    status         enum ('active', 'closed') default 'active' not null,
    `order`        mediumint                 default 0        not null,
    createdBy      char(30)                                   not null,
    createdDate    datetime                                   not null,
    lastEditedBy   char(30)                                   not null,
    lastEditedDate datetime                                   not null,
    closedBy       char(30)                                   not null,
    closedDate     datetime                                   not null,
    activatedBy    char(30)                                   not null,
    activatedDate  datetime                                   not null,
    workHours      float                     default 22       not null,
    deleted        enum ('0', '1')           default '0'      not null
)
    charset = utf8;

ALTER TABLE zt_consumed ADD reviewStage varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '项目评审的评审阶段' AFTER account;

ALTER TABLE zt_release ADD version MEDIUMINT(9) DEFAULT 0 NOT NULL COMMENT '版本' after `status`;
ALTER TABLE zt_release ADD dealUser varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '待处理人' after `version`;
ALTER TABLE zt_release ADD syncObjectId INT(10) DEFAULT 0 NOT NULL COMMENT '同步ID';
ALTER table zt_release ADD syncObjectType varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '同步信息类型';
ALTER TABLE zt_release ADD syncObjectCreateTime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '同步信息创建时间';
ALTER table zt_release ADD baseLineCondition varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '是否打基线';
ALTER TABLE zt_release ADD baseLinePath TEXT  NOT NULL COMMENT '基线路径';
ALTER TABLE zt_release ADD baseLineTime datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打基线时间';
ALTER TABLE zt_release ADD baseLineUser varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '打基线人';
ALTER table zt_release ADD cmConfirm  ENUM('','pass','reject') NOT NULL DEFAULT '' COMMENT 'cm确认结果';
ALTER TABLE zt_release ADD cmConfirmTime datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '确认时间';
ALTER TABLE zt_release ADD cmConfirmUser varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'cm确认人';

CREATE TABLE `zt_release_baseline_log` (
                                           `id` int(11) NOT NULL AUTO_INCREMENT,
                                           `releaseId` int(11) NOT NULL COMMENT '项目发布ID',
                                           `version` mediumint(9) NOT NULL DEFAULT '0' COMMENT '版本',
                                           `baseLineCondition` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '是否打基线',
                                           `baseLinePath` text NOT NULL COMMENT '基线路径',
                                           `baseLineTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打基线时间',
                                           `baseLineUser` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '打基线人',
                                           `cmConfirm` enum('','pass','reject') NOT NULL DEFAULT '' COMMENT 'cm确认结果',
                                           `cmConfirmTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '确认时间',
                                           `cmConfirmUser` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'cm确认人',
                                           PRIMARY KEY (`id`),
                                           KEY `releaseId` (`releaseId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目发布打基线日志记录';

ALTER TABLE zt_modify ADD releaseSyncStatus TINYINT(1) DEFAULT 1 NULL COMMENT '是否同步到发布状态 1未同步 2已同步'  after `release`;

ALTER TABLE zt_modifycncc ADD releaseSyncStatus TINYINT(1) DEFAULT 1 NULL COMMENT '是否同步到发布状态 1未同步 2已同步'  after `release`;

ALTER TABLE zt_change ADD addBaseLineTime datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打基线时间'  after `baseLineTime`;

update zt_change Set addBaseLineTime = baseLineTime where 1 and baseLineTime is not null ;

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'cm', 'typeList', 'code', 'AC-代码基线', '1');

update zt_review as t1 left join zt_action as t2 on t1.id = t2.objectID set t1.createdDate = t2.date where t2.objectType = 'review' and t2.action = 'created';



alter table zt_productenroll modify productLine varchar(255) default null;
alter table zt_outwarddelivery modify productLine varchar(255) null comment '产品线';


alter table zt_modifycncc add isReview tinyint(4) null comment '是否评审方案：2-否，1-是';
alter table zt_modifycncc add reviewReport varchar(255) null comment '方案评审报告';


alter table zt_modify add isReview tinyint(4) null comment '是否评审方案：2-否，1-是';
alter table zt_modify add isReviewPass tinyint(4) null comment '方案评审结果：2-未通过，1-通过';
alter table zt_modify add reviewReport varchar(255) null comment '方案评审报告';


alter table zt_outwarddelivery add approvedNode varchar(255) null comment '已审批节点';
alter table zt_outwarddelivery add modifyLevel varchar(2) default '1' comment '是否修改级别：1-否；2-是';

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'resetPasswordIp', 'http://10.128.28.212');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'jenkinsServerIP', 'http://10.128.28.210:8080');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sonarcubeServerIP', 'http://10.128.28.191:9090');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'networkDiskServerIP', 'http://10.128.27.21');