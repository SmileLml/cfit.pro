CREATE TABLE `zt_opinion` (
                              `id` mediumint(8) NOT NULL AUTO_INCREMENT,
                              `code` varchar(255) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `category` char(30) NOT NULL,
                              `status` char(30) NOT NULL,
                              `sourceType` char(30) NOT NULL,
                              `sourceMode` char(30) NOT NULL,
                              `sourceName` varchar(255) NOT NULL,
                              `union` char(30) NOT NULL,
                              `date` date NOT NULL,
                              `contact` char(30) NOT NULL,
                              `contactInfo` char(30) NOT NULL,
                              `group` char(30) NOT NULL,
                              `owner` char(30) NOT NULL,
                              `background` text NOT NULL,
                              `overview` text NOT NULL,
                              `desc` text NOT NULL,
                              `createdBy` char(30) NOT NULL,
                              `createdDate` date NOT NULL,
                              `deadline` date NOT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_requirement` (
                                  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
                                  `opinion` mediumint(8) NOT NULL,
                                  `project` mediumint(8) NOT NULL,
                                  `product` varchar(255) NOT NULL,
                                  `line` varchar(255) NOT NULL,
                                  `app` mediumint(8) NOT NULL,
                                  `code` varchar(255) NOT NULL,
                                  `dept` mediumint(8) NOT NULL,
                                  `name` varchar(255) NOT NULL,
                                  `desc` text NOT NULL,
                                  `method` char(30) NOT NULL,
                                  `status` char(30) NOT NULL,
                                  `owner` char(30) NOT NULL,
                                  `end` date NOT NULL,
                                  `version` mediumint NOT NULL DEFAULT '1',
                                  `changeVersion` mediumint(9) NOT NULL DEFAULT '1',
                                  `changedTimes` tinyint NOT NULL DEFAULT '0',
                                  `createdBy` char(30) NOT NULL,
                                  `createdDate` date NOT NULL,
                                  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_application` (
                                  `id` int NOT NULL AUTO_INCREMENT,
                                  `program` int NOT NULL,
                                  `name` varchar(255) NOT NULL,
                                  `code` varchar(255) NOT NULL,
                                  `desc` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                  `createdDate` date NOT NULL,
                                  `deleted` tinyint NOT NULL DEFAULT '0',
                                  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_projectplan` (
                                  `id` mediumint NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                  `year` char(30) NOT NULL,
                                  `type` char(30) NOT NULL,
                                  `code` varchar(255) NOT NULL,
                                  `mark` char(30) NOT NULL,
                                  `name` varchar(255) NOT NULL,
                                  `line` varchar(255) NOT NULL,
                                  `app` mediumint(8) NOT NULL,
                                  `basis` char(30) NOT NULL,
                                  `status` char(30) NOT NULL,
                                  `content` text NOT NULL,
                                  `storyStatus` char(30) NOT NULL,
                                  `category` char(30) NOT NULL,
                                  `structure` char(30) NOT NULL,
                                  `localize` char(30) NOT NULL,
                                  `begin` date NOT NULL,
                                  `end` date NOT NULL,
                                  `workload` char(30) NOT NULL,
                                  `duration` char(30) NOT NULL,
                                  `bearDept` char(30) NOT NULL,
                                  `owner` char(30) NOT NULL,
                                  `phone` varchar(255) NOT NULL,
                                  `reviewDate` date NOT NULL,
                                  `reviewStage` tinyint NOT NULL,
                                  `depts` varchar(255) NOT NULL,
                                  `submitedBy` char(30) NOT NULL,
                                  `createdBy` char(30) NOT NULL,
                                  `createdDate` date NOT NULL,
                                  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_projectcreation` (
                                      `id` mediumint(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `plan` mediumint(8) NOT NULL,
                                      `code` varchar(255) NOT NULL,
                                      `mark` char(30) NOT NULL,
                                      `type` char(30) NOT NULL,
                                      `name` varchar(255) NOT NULL,
                                      `PM` char(30) NOT NULL,
                                      `dept` mediumint(8) NOT NULL,
                                      `status` char(30) NOT NULL,
                                      `source` char(30) NOT NULL,
                                      `linkPlan` char(30) NOT NULL,
                                      `begin` date NOT NULL,
                                      `end` date NOT NULL,
                                      `workload` char(30) NOT NULL,
                                      `background` text NOT NULL,
                                      `range` text NOT NULL,
                                      `goal` text NOT NULL,
                                      `stakeholder` text NOT NULL,
                                      `verify` text NOT NULL,
                                      `createdBy` char(30) NOT NULL,
                                      `createdDate` date NOT NULL,
                                      `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_review`
    ADD `type` char(30) NOT NULL AFTER `project`,
CHANGE `object` `object` char(30) NOT NULL AFTER `title`,
ADD `content` text COLLATE 'utf8_general_ci' NOT NULL AFTER `status`,
ADD `owner` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `status`,
ADD `expert` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `owner`;

ALTER TABLE `zt_reviewresult` ADD `method` char(30) NOT NULL AFTER `review`;
ALTER TABLE `zt_reviewresult` ADD UNIQUE `review_method` (`review`, `method`), DROP INDEX `reviewer`;
ALTER TABLE `zt_reviewissue` ADD `desc` text COLLATE 'utf8_general_ci' NOT NULL AFTER `title`;

CREATE TABLE `zt_requirementspec` (
                                      `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                                      `requirement` mediumint(9) NOT NULL,
                                      `line` varchar(255) NOT NULL,
                                      `app` mediumint(9) NOT NULL,
                                      `project` mediumint(9) NOT NULL,
                                      `product` varchar(255) NOT NULL,
                                      `code` varchar(255) NOT NULL,
                                      `dept` mediumint(9) NOT NULL,
                                      `name` varchar(255) NOT NULL,
                                      `desc` text NOT NULL,
                                      `method` char(30) NOT NULL,
                                      `owner` char(30) NOT NULL,
                                      `end` date NOT NULL,
                                      `createdBy` char(30) NOT NULL,
                                      `createdDate` date NOT NULL,
                                      `version` mediumint(9) NOT NULL DEFAULT 1,
                                      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_reviewnode` (
                                 `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                                 `status` enum('wait','pass','reject') NOT NULL DEFAULT 'wait',
                                 `objectType` varchar(100) NOT NULL,
                                 `objectID` mediumint(9) NOT NULL,
                                 `createdBy` char(30) NOT NULL,
                                 `version` mediumint(9) NOT NULL,
                                 `createdDate` date NOT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_reviewer` (
                               `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                               `node` mediumint(9) NOT NULL,
                               `reviewer` char(30) NOT NULL,
                               `status` enum('wait','pass','reject') NOT NULL DEFAULT 'wait',
                               `grade` tinyint(3) NOT NULL DEFAULT '1',
                               `comment` mediumtext DEFAULT NULL,
                               `reviewTime` datetime DEFAULT NULL,
                               `createdBy` char(30) NOT NULL,
                               `createdDate` date NOT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_dept` ADD `leader` char(30) COLLATE 'utf8_general_ci' NOT NULL;

ALTER TABLE `zt_review`
    ADD `grade` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `content`;
ALTER TABLE `zt_review`
    ADD `reviewer` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `grade`;
CREATE TABLE `zt_reviewobject` (
                                   `id` mediumint NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                   `review` mediumint NOT NULL,
                                   `object` varchar(255) NOT NULL,
                                   `url` varchar(255) NOT NULL,
                                   `createdBy` char(30) NOT NULL,
                                   `createdDate` date NOT NULL
);

ALTER TABLE `zt_reviewer`
    ADD `extra` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `comment`;

CREATE TABLE `zt_document` (
                               `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                               `name` varchar(255) NOT NULL,
                               `path` varchar(255) NOT NULL,
                               `prefix` varchar(100) NOT NULL,
                               `encoding` varchar(20) NOT NULL,
                               `SCM` varchar(10) NOT NULL,
                               `client` varchar(100) NOT NULL,
                               `commits` mediumint(8) unsigned NOT NULL,
                               `account` varchar(30) NOT NULL,
                               `password` varchar(30) NOT NULL,
                               `encrypt` varchar(30) NOT NULL DEFAULT 'plain',
                               `acl` text NOT NULL,
                               `synced` tinyint(1) NOT NULL DEFAULT '0',
                               `lastSync` datetime NOT NULL,
                               `desc` text NOT NULL,
                               `deleted` tinyint(1) NOT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `zt_docbranch` (
    `lib` mediumint(8) unsigned NOT NULL,
    `revision` mediumint(8) unsigned NOT NULL,
    `branch` varchar(255) NOT NULL,
    UNIQUE KEY `lib_revision_branch` (`lib`,`revision`,`branch`),
    KEY `branch` (`branch`),
    KEY `revision` (`revision`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `zt_docfiles` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `lib` mediumint(8) unsigned NOT NULL,
    `revision` mediumint(8) unsigned NOT NULL,
    `path` varchar(255) NOT NULL,
    `parent` varchar(255) NOT NULL,
    `type` varchar(20) NOT NULL,
    `action` char(1) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `path` (`path`),
    KEY `parent` (`parent`),
    KEY `repo` (`lib`),
    KEY `revision` (`revision`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `zt_dochistory` (
    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
    `lib` mediumint(9) NOT NULL,
    `revision` varchar(40) NOT NULL,
    `commit` mediumint(8) unsigned NOT NULL,
    `comment` text NOT NULL,
    `committer` varchar(100) NOT NULL,
    `time` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `repo` (`lib`),
    KEY `revision` (`revision`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `zt_processimprove` (
    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
    `process` char(30) NOT NULL,
    `involved` char(30) NOT NULL,
    `desc` text NOT NULL,
    `createdBy` char(30) NOT NULL,
    `createdDate` date NOT NULL,
    `source` char(30) NOT NULL,
    `judge` text NOT NULL,
    `judgedBy` char(30) NOT NULL,
    `judgedDate` date NOT NULL,
    `isAccept` tinyint(9) NOT NULL,
    `pri` char(30) NOT NULL,
    `isDeploy` tinyint(9) NOT NULL,
    `reviewedBy` char(30) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `zt_epgprocess` (
    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `host` varchar(255) NOT NULL,
    `desc` text NOT NULL,
    `createdBy` char(30) NOT NULL,
    `createdDate` date NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_problem` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `code` varchar(255) NOT NULL,
                              `type` varchar(255) NOT NULL,
                              `severity` varchar(255) NOT NULL,
                              `pri` varchar(255) NOT NULL,
                              `occurDate` date NOT NULL,
                              `app` varchar(255) NOT NULL,
                              `desc` mediumtext NOT NULL,
                              `acceptDept` mediumint(9) DEFAULT NULL,
                              `reason` mediumtext NOT NULL,
                              `solution` mediumtext NOT NULL,
                              `progress` mediumtext NOT NULL,
                              `status` enum('wait','reject','pass','feedbacked','closing','closed') NOT NULL DEFAULT 'wait',
                              `createdBy` char(30) NOT NULL,
                              `createdDate` date NOT NULL,
                              `closedBy` date DEFAULT NULL,
                              `closedDate` date DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_demand` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `code` varchar(255) NOT NULL,
                             `type` varchar(255) NOT NULL,
                             `source` varchar(255) NOT NULL,
                             `title` varchar(255) NOT NULL,
                             `endDate` date NOT NULL,
                             `app` varchar(255) NOT NULL,
                             `desc` mediumtext NOT NULL,
                             `acceptDept` mediumint(9) DEFAULT NULL,
                             `reason` mediumtext NOT NULL,
                             `solution` mediumtext NOT NULL,
                             `conclusion` mediumtext NOT NULL,
                             `progress` mediumtext NOT NULL,
                             `status` enum('wait','reject','pass','feedbacked','closing','closed') NOT NULL DEFAULT 'wait',
                             `createdBy` char(30) NOT NULL,
                             `createdDate` date NOT NULL,
                             `closedBy` date DEFAULT NULL,
                             `closedDate` date DEFAULT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `zt_duty` (
                           `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                           `type` char(30) NOT NULL,
                           `user` varchar(255) NOT NULL,
                           `occurDate` date NOT NULL,
                           `desc` text NOT NULL,
                           `createdBy` char(30) NOT NULL,
                           `createdDate` date NOT NULL,
                           PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `zt_application`
    ADD `isPayment` tinyint NOT NULL AFTER `desc`,
ADD `team` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `isPayment`;


ALTER TABLE `zt_reviewnode`
    CHANGE `status` `status` enum('wait','pass','reject','ignore') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `id`;
ALTER TABLE `zt_dept`
    CHANGE `leader` `leader` char(30) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `manager`,
    ADD `executive` char(30) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '';

ALTER TABLE `zt_reviewnode`
    ADD `stage` mediumint(9) NOT NULL AFTER `objectID`;


CREATE TABLE `zt_info` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `code` varchar(30) NOT NULL,
                           `action` enum('gain','fix') NOT NULL,
                           `type` varchar(30) NOT NULL,
                           `classify` varchar(30) NOT NULL,
                           `gainType` varchar(30) NOT NULL,
                           `reviewStage` smallint(6) NOT NULL DEFAULT 0,
                           `node` varchar(255) NOT NULL,
                           `planBegin` date NOT NULL,
                           `planEnd` date NOT NULL,
                           `actualBegin` date DEFAULT NULL,
                           `actualEnd` date DEFAULT NULL,
                           `app` mediumint(9) NOT NULL,
                           `from` varchar(255) NOT NULL,
                           `desc` mediumtext NOT NULL,
                           `reason` mediumtext NOT NULL,
                           `purpose` mediumtext NOT NULL,
                           `operation` mediumtext NOT NULL,
                           `test` mediumtext NOT NULL,
                           `step` mediumtext NOT NULL,
                           `status` enum('wait','pass','reject','reviewing') NOT NULL,
                           `supply` varchar(255) NOT NULL,
                           `result` mediumtext NOT NULL,
                           `createdBy` char(30) NOT NULL,
                           `createdDate` datetime NOT NULL,
                           PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_modify` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `code` varchar(30) NOT NULL,
                             `type` varchar(30) NOT NULL,
                             `level` varchar(30) NOT NULL,
                             `property` varchar(30) NOT NULL,
                             `classify` varchar(30) NOT NULL,
                             `isInterrupt` tinyint(4) NOT NULL DEFAULT 0,
                             `isAppend` tinyint(4) NOT NULL DEFAULT 0,
                             `reviewStage` smallint(6) NOT NULL DEFAULT 0,
                             `node` varchar(255) NOT NULL,
                             `planBegin` date NOT NULL,
                             `planEnd` date NOT NULL,
                             `actualBegin` date DEFAULT NULL,
                             `actualEnd` date DEFAULT NULL,
                             `app` mediumint(9) NOT NULL,
                             `from` varchar(255) NOT NULL,
                             `desc` mediumtext NOT NULL,
                             `reason` mediumtext NOT NULL,
                             `target` mediumtext NOT NULL,
                             `effect` mediumtext NOT NULL,
                             `plan` mediumtext NOT NULL,
                             `risk` mediumtext NOT NULL,
                             `operation` mediumtext NOT NULL,
                             `test` mediumtext NOT NULL,
                             `step` mediumtext NOT NULL,
                             `status` enum('wait','pass','reject','reviewing') NOT NULL,
                             `supply` varchar(255) NOT NULL,
                             `result` mediumtext NOT NULL,
                             `createdBy` char(30) NOT NULL,
                             `createdDate` datetime NOT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_problem`
    ADD `acceptUser` char(30) NULL AFTER `acceptDept`;
ALTER TABLE `zt_demand`
    ADD `acceptUser` char(30) NULL AFTER `acceptDept`;
ALTER TABLE `zt_demand`
    ADD `fixType` varchar(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `progress`;
ALTER TABLE `zt_problem`
    ADD `fixType` varchar(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `progress`;
ALTER TABLE `zt_processimprove`
    ADD `status` enum('wait','pass','reject','feedbacked','closed') NOT NULL AFTER `isAccept`;

/* 2021-05-26 */
ALTER TABLE `zt_requirement`
    ADD `mailto` varchar(255) NULL AFTER `version`;
ALTER TABLE `zt_opinion`
    ADD `mailto` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `owner`;
ALTER TABLE `zt_projectplan`
    ADD `isInit` tinyint NOT NULL DEFAULT '0' AFTER `status`;
ALTER TABLE `zt_projectplan`
    ADD `project` mediumint NULL AFTER `isInit`;

ALTER TABLE `zt_duty`
    ADD `application` int NOT NULL AFTER `type`;
ALTER TABLE `zt_duty`
    ADD `importantTime` enum('0','1') NOT NULL DEFAULT '0' AFTER `application`;
ALTER TABLE `zt_duty`
    ADD `realityUser` varchar(255) NOT NULL AFTER `user`;

/* 2021-05-27 */
ALTER TABLE `zt_processimprove`
    ADD `mailto` text AFTER `desc`;

/* 2021-05-28 */
ALTER TABLE `zt_duty`
    ADD `mailto` text AFTER `desc`;

/* 2021-05-31 */
ALTER TABLE `zt_task`
    ADD `path` varchar(255) NOT NULL AFTER `version`,
ADD `grade` tinyint NOT NULL AFTER `path`;

CREATE TABLE `zt_productrequirement` (
                                         `id` mediumint NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                         `product` mediumint NOT NULL,
                                         `requirement` mediumint NOT NULL
);

/* 2021-06-01 */
ALTER TABLE `zt_projectcreation`
    ADD `union` char(30) NOT NULL AFTER `mark`;

ALTER TABLE `zt_application`
    CHANGE `isPayment` `isPayment` char(30) NOT NULL AFTER `desc`;

ALTER TABLE `zt_reviewer`
    CHANGE `status` `status` enum('wait','pass','reject','ignore', 'pending') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `reviewer`;
ALTER TABLE `zt_reviewnode`
    CHANGE `status` `status` enum('wait','pass','reject','ignore','pending') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `id`;

CREATE TABLE `zt_productline` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `name` varchar(255) NOT NULL,
                                  `code` varchar(255) NOT NULL,
                                  `desc` mediumtext NOT NULL,
                                  `createdBy` varchar(30) NOT NULL,
                                  `createdDate` date NOT NULL,
                                  `deleted` tinyint(4) NOT NULL DEFAULT '0',
                                  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_processimprove`
    ADD `deployDate` date NOT NULL AFTER `isDeploy`;

ALTER TABLE `zt_processimprove`
    CHANGE `reviewedBy` `reviewedBy` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `deployDate`;

/* 2021-06-05 */
ALTER TABLE `zt_projectplan`
    CHANGE `begin` `begin` date NULL AFTER `localize`,
    CHANGE `end` `end` date NULL AFTER `begin`;

ALTER TABLE `zt_productline`
    ADD `depts` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `desc`;

/* 2021-06-08 */
ALTER TABLE `zt_projectplan`
    ADD `version` varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '0' AFTER `phone`;

/* 2021-06-09 */
ALTER TABLE `zt_modify`
    ADD `project` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `from`,
    ADD `problem` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `project`,
    ADD `demand` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `problem`;
ALTER TABLE `zt_modify`
    ADD `version` smallint NOT NULL DEFAULT '1' AFTER `demand`;
ALTER TABLE `zt_modify`
    ADD `release` mediumint NOT NULL AFTER `project`;


ALTER TABLE `zt_info`
    ADD `version` smallint(6) NOT NULL DEFAULT '1' AFTER `reviewStage`,
ADD `project` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `from`,
ADD `release` mediumint NOT NULL AFTER `project`,
ADD `demand` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `release`,
ADD `problem` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `demand`;

/* 2021-06-14 */
CREATE TABLE `zt_outsideplan` (
                                  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                                  `year` char(30) NOT NULL,
                                  `type` char(30) NOT NULL,
                                  `code` varchar(255) NOT NULL,
                                  `mark` char(30) NOT NULL,
                                  `name` varchar(255) NOT NULL,
                                  `linkedPlan` varchar(255) NOT NULL,
                                  `line` varchar(255) NOT NULL,
                                  `app` mediumint(8) NOT NULL,
                                  `basis` char(30) NOT NULL,
                                  `status` char(30) NOT NULL,
                                  `isInit` tinyint(4) NOT NULL DEFAULT 0,
                                  `project` mediumint(9) DEFAULT NULL,
                                  `content` text NOT NULL,
                                  `storyStatus` char(30) NOT NULL,
                                  `category` char(30) NOT NULL,
                                  `structure` char(30) NOT NULL,
                                  `localize` char(30) NOT NULL,
                                  `begin` date DEFAULT NULL,
                                  `end` date DEFAULT NULL,
                                  `workload` char(30) NOT NULL,
                                  `duration` char(30) NOT NULL,
                                  `bearDept` char(30) NOT NULL,
                                  `owner` char(30) NOT NULL,
                                  `phone` varchar(255) NOT NULL,
                                  `version` varchar(255) NOT NULL DEFAULT '0',
                                  `reviewDate` date NOT NULL,
                                  `reviewStage` tinyint(4) NOT NULL,
                                  `depts` varchar(255) NOT NULL,
                                  `submitedBy` char(30) NOT NULL,
                                  `createdBy` char(30) NOT NULL,
                                  `createdDate` date NOT NULL,
                                  `deleted` enum('0','1') NOT NULL DEFAULT '0',
                                  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_problem`
    ADD `state` varchar(255) NULL AFTER `occurDate`;
ALTER TABLE `zt_demand`
    ADD `state` varchar(255) NULL AFTER `endDate`;

ALTER TABLE `zt_info`
    ADD `fixType` varchar(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `gainType`;
ALTER TABLE `zt_modify`
    ADD `fixType` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `classify`;

ALTER TABLE `zt_duty`
    CHANGE `user` `user` char(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `importantTime`,
    ADD `actualUser` char(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `user`,
    CHANGE `occurDate` `planDate` date NOT NULL AFTER `realityUser`,
    ADD `actualDate` date NOT NULL AFTER `planDate`;

ALTER TABLE `zt_release`
    ADD `path` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `desc`,
    ADD `mailto` mediumtext COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `path`;
ALTER TABLE `zt_release`
    ADD `isSent` tinyint NOT NULL DEFAULT '0' AFTER `mailto`;


ALTER TABLE `zt_application`
    ADD `attribute` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `team`,
    ADD `network` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `attribute`,
    ADD `fromUnit` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `network`,
    ADD `feature` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `fromUnit`,
    ADD `version` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `feature`,
    ADD `range` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `version`,
    ADD `useDept` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `range`,
    ADD `projectMonth` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `useDept`,
    ADD `productDate` date NOT NULL AFTER `projectMonth`,
    ADD `opsDate` date NOT NULL AFTER `productDate`,
    ADD `promote` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `opsDate`,
    ADD `protectLevel` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `promote`,
    ADD `continueLevel` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `protectLevel`,
    ADD `securityLevel` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `continueLevel`,
    ADD `cityBak` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `securityLevel`,
    ADD `offsiteBak` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `cityBak`,
    ADD `vendor` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `offsiteBak`,
    ADD `vendorContact` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `vendor`,
    ADD `retrofit` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `vendorContact`,
    ADD `info` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `retrofit`,
    ADD `runStatus` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `info`,
    ADD `runUnit` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `runStatus`,
    ADD `runLeader` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `runUnit`,
    ADD `runDept` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `runLeader`,
    ADD `owner` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `runDept`,
    ADD `opsLeader` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `owner`,
    ADD `opsDept` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `opsLeader`,
    ADD `opsManager` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `opsDept`,
    ADD `devManager` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `opsManager`;

/* 2021-06-17 */
ALTER TABLE `zt_problem`
    CHANGE `status` `status` enum('wait','reject','pass','feedbacked','closing','closed','deleted') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `fixType`;
ALTER TABLE `zt_demand`
    CHANGE `status` `status` enum('wait','reject','pass','feedbacked','closing','closed','deleted') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `fixType`;
ALTER TABLE `zt_info`
    CHANGE `status` `status` enum('wait','pass','reject','reviewing','deleted') COLLATE 'utf8_general_ci' NOT NULL AFTER `step`;
ALTER TABLE `zt_modify`
    CHANGE `status` `status` enum('wait','pass','reject','reviewing','deleted') COLLATE 'utf8_general_ci' NOT NULL AFTER `step`;
ALTER TABLE `zt_projectplan`
    CHANGE `app` `app` varchar(255) NOT NULL AFTER `line`;
ALTER TABLE `zt_outsideplan`
    CHANGE `app` `app` varchar(255) NOT NULL AFTER `line`;
ALTER TABLE `zt_problem`
    ADD `consumed` float NOT NULL DEFAULT '0' AFTER `progress`;
ALTER TABLE `zt_demand`
    ADD `consumed` float NOT NULL DEFAULT '0' AFTER `fixType`;

/* 2021-06-19 */
ALTER TABLE `zt_problem`
    ADD `dealUser` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `acceptUser`;
ALTER TABLE `zt_problem`
    CHANGE `status` `status` varchar(50) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `fixType`;
ALTER TABLE `zt_demand`
    ADD `dealUser` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `acceptUser`,
    CHANGE `status` `status` varchar(20) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'wait' AFTER `consumed`;
CREATE TABLE `zt_consumed` (
                               `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                               `objectType` varchar(100) NOT NULL,
                               `objectID` mediumint(9) NOT NULL,
                               `consumed` float NOT NULL,
                               `account` char(30) NOT NULL,
                               `before` varchar(255) NOT NULL,
                               `after` varchar(255) NOT NULL,
                               `createdBy` char(30) NOT NULL,
                               `createdDate` datetime NOT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `zt_modify`
    CHANGE `status` `status` varchar(50) COLLATE 'utf8_general_ci' NOT NULL AFTER `step`;
ALTER TABLE `zt_info`
    CHANGE `status` `status` varchar(50) COLLATE 'utf8_general_ci' NOT NULL AFTER `step`;

/* 2021-06-20 */
ALTER TABLE `zt_consumed`
    ADD `mailto` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `after`;

ALTER TABLE `zt_dept`
    ADD `cm` char(30) COLLATE 'utf8_general_ci' NOT NULL;
ALTER TABLE `zt_modify`
    CHANGE `release` `release` varchar(255) NOT NULL AFTER `project`;
ALTER TABLE `zt_info`
    CHANGE `release` `release` varchar(255) NOT NULL AFTER `project`;

/* 2021-06-22 */
ALTER TABLE `zt_risk`
    ADD `timeFrame` varchar(10) NOT NULL AFTER `actualClosedDate`;


/* 2021-06-23 */
ALTER TABLE `zt_task`
    ADD `resource` varchar(255) NULL AFTER `deadline`;
ALTER TABLE `zt_project`
    ADD `resource` varchar(255) NOT NULL AFTER `storyConcept`;

/* 2021-07-08 */
ALTER TABLE `zt_projectcreation`
    CHANGE `union` `union` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `mark`;
ALTER TABLE `zt_info`
    ADD `checkList` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `step`;
ALTER TABLE `zt_modify`
    ADD `checkList` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `step`;

/* 2021-07-27 */
ALTER TABLE `zt_modify`
    CHANGE `app` `app` varchar(255) NOT NULL AFTER `actualEnd`;
ALTER TABLE `zt_info`
    CHANGE `app` `app` varchar(255) NOT NULL AFTER `actualEnd`;
ALTER TABLE `zt_problem`
    ADD `createdDept` int NOT NULL AFTER `createdBy`;
ALTER TABLE `zt_demand`
    ADD `createdDept` int NOT NULL AFTER `createdBy`;
ALTER TABLE `zt_modify`
    ADD `createdDept` int NOT NULL AFTER `createdBy`;
ALTER TABLE `zt_info`
    ADD `createdDept` int NOT NULL AFTER `createdBy`;
ALTER TABLE `zt_demand`
    ADD `requirement` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `app`;
ALTER TABLE `zt_modify`
    ADD `mode` varchar(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `type`;
ALTER TABLE `zt_reviewissue`
    ADD `changelog` text COLLATE 'utf8_general_ci' NOT NULL AFTER `desc`;
ALTER TABLE `zt_review`
    ADD `outside` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `grade`;

/* 2021-08-02 */
ALTER TABLE `zt_dept`
    CHANGE `executive` `executive` varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `leader`,
    CHANGE `cm` `cm` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `executive`,
    ADD `po` varchar(255) COLLATE 'utf8_general_ci' NOT NULL;

/* 2021-08-03*/
ALTER TABLE `zt_task`
    ADD `progress` float NOT NULL AFTER `realStarted`;
ALTER TABLE `zt_effort`
    ADD `progress` int NOT NULL AFTER `consumed`;
ALTER TABLE `zt_project`
    ADD `progress` int NOT NULL AFTER `realEnd`;
ALTER TABLE `zt_project`
    ADD `planHour` int(11) NOT NULL AFTER `realDuration`,
ADD `realHour` int(11) NOT NULL AFTER `planHour`;

/* 2021-08-10 */
ALTER TABLE `zt_reviewresult`
    MODIFY COLUMN `opinion` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `result`;

ALTER TABLE `zt_problem`
    ADD COLUMN `abstract` varchar(255) NULL AFTER `id`;

ALTER TABLE `zt_dept`
    CHANGE `manager` `manager` varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `function`,
    ADD `manager1` char(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `manager`,
    CHANGE `leader` `leader` varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `manager1`,
    ADD `leader1` char(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `leader`;

/* 2021-08-11 */
ALTER TABLE `zt_build`
    ADD COLUMN `purpose` varchar(255) NULL AFTER `name`,
ADD COLUMN `rounds` varchar(255) NULL AFTER `purpose`;

/* 2021-08-13 */
ALTER TABLE `zt_task`
    ADD COLUMN `resourceTo` varchar(255) NULL AFTER `assignedTo`;

/* 2021-08-14 */
CREATE TABLE `zt_change` (
                             `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                             `code` varchar(255) NOT NULL,
                             `project` mediumint(9) NOT NULL,
                             `level` mediumint(9) NOT NULL,
                             `reason` mediumtext NOT NULL,
                             `content` mediumtext NOT NULL,
                             `effect` mediumtext NOT NULL,
                             `type` varchar(255) NOT NULL,
                             `status` varchar(255) NOT NULL,
                             `version` mediumint(9) NOT NULL DEFAULT 1,
                             `reviewStage` mediumint(9) NOT NULL,
                             `result` mediumtext NOT NULL,
                             `createdBy` char(30) NOT NULL,
                             `createdDept` mediumint(9) NOT NULL,
                             `createdDate` datetime NOT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_problem`
    ADD `result` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `fixType`;

/* 2021-08-15 */
CREATE TABLE `zt_projectdocument` (
                                      `id` mediumint NOT NULL AUTO_INCREMENT,
                                      `name` varchar(255) NOT NULL,
                                      `path` varchar(255) NOT NULL,
                                      `prefix` varchar(100) NOT NULL,
                                      `encoding` varchar(20) NOT NULL,
                                      `SCM` varchar(10) NOT NULL,
                                      `client` varchar(100) NOT NULL,
                                      `commits` mediumint unsigned NOT NULL,
                                      `account` varchar(30) NOT NULL,
                                      `password` varchar(30) NOT NULL,
                                      `encrypt` varchar(30) NOT NULL DEFAULT 'plain',
                                      `acl` text NOT NULL,
                                      `synced` tinyint(1) NOT NULL DEFAULT '0',
                                      `lastSync` datetime NOT NULL,
                                      `desc` text NOT NULL,
                                      `deleted` tinyint(1) NOT NULL,
                                      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* 2021-08-25*/
ALTER TABLE `zt_problem`
    ADD COLUMN `assignUser` varchar(100) NULL AFTER `closedDate`,
ADD COLUMN `assignDept` mediumint NULL AFTER `assignUser`;

/* 2021-08-29 */
ALTER TABLE `zt_modify` ADD COLUMN `productCode` text NULL AFTER `demand`;

/* 2021-09-01 */
CREATE TABLE `zt_secondline` (
                                 `id` int NOT NULL AUTO_INCREMENT,
                                 `objectType` varchar(100) NOT NULL,
                                 `objectID` int NOT NULL,
                                 `relationType` varchar(100) NOT NULL,
                                 `relationID` int NOT NULL,
                                 `createdBy` varchar(200) DEFAULT NULL,
                                 `createdDate` datetime DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 KEY `objectType` (`objectType`,`objectID`),
                                 KEY `objectType2` (`relationType`,`relationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* 2021-09-02 */
ALTER TABLE `zt_problem` ADD COLUMN `lastStatus` varchar(50) NULL AFTER `status`;
ALTER TABLE `zt_demand` ADD COLUMN `lastStatus` varchar(50) NULL AFTER `status`;

/* 2021-09-04 */
ALTER TABLE `zt_problem` ADD COLUMN `source` varchar(255) NULL AFTER `type`;

/* 2021-09-06 */
ALTER TABLE `zt_problem` DROP COLUMN `assignUser`;
ALTER TABLE `zt_problem` DROP COLUMN `assignDept`;

/* 2021-09-07 */
ALTER TABLE `zt_product` ADD COLUMN `app` varchar(100) NOT NULL AFTER `code`;

/* 2021-09-10 */
ALTER TABLE `zt_consumed` ADD COLUMN `details` text NULL AFTER `after`;

/* 2021-09-13 */
ALTER TABLE `zt_projectdocument` ADD COLUMN `projectID` int NULL DEFAULT 0 AFTER `id`;

/* 2021-09-14 */
CREATE TABLE `zt_baseline` (
                               `id` int(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                               `project` int NOT NULL,
                               `title` varchar(255) NOT NULL,
                               `type` char(30) NOT NULL,
                               `changed` enum('0','1') NOT NULL DEFAULT '0',
                               `createdBy` char(30) NOT NULL,
                               `createdDate` date NOT NULL,
                               `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_cmitem` (
                             `id` int(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                             `baseline` int(8) NOT NULL,
                             `title` varchar(255) NOT NULL,
                             `code` varchar(255) NOT NULL,
                             `version` char(30) NOT NULL,
                             `changed` enum('0','1') NOT NULL DEFAULT '0',
                             `changedID` int(8) NOT NULL,
                             `changedDate` date NOT NULL,
                             `path` text NOT NULL,
                             `comment` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_productcode` (
                                  `id` int NOT NULL AUTO_INCREMENT,
                                  `product` int NOT NULL,
                                  `modify` int NOT NULL,
                                  `code` varchar(255) NOT NULL,
                                  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* 2021-09-15 */
ALTER TABLE `zt_project`
    ADD `QA` varchar(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `RD`;

ALTER TABLE `zt_baseline`
    ADD `cm` char(30) NOT NULL AFTER `createdDate`,
ADD `cmDate` date NOT NULL AFTER `cm`,
ADD `reviewer` char(30) NOT NULL AFTER `cmDate`,
ADD `reviewedDate` date NOT NULL AFTER `reviewer`;

/* 2021-11-04 */
CREATE TABLE `zt_requestlog` (
                                 `id` mediumint NOT NULL AUTO_INCREMENT,
                                 `url` varchar(255) DEFAULT NULL,
                                 `objectType` varchar(30) DEFAULT NULL,
                                 `purpose` varchar(50) DEFAULT NULL,
                                 `requestType` char(30) DEFAULT NULL,
                                 `status` char(30) DEFAULT NULL,
                                 `params` longtext,
                                 `response` longtext,
                                 `requestDate` datetime DEFAULT NULL,
                                 `extra` varchar(100) DEFAULT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=MyISAM;

ALTER TABLE `zt_opinion` ADD COLUMN `demandCode` varchar(150) NULL AFTER `code`;

/* 2021-11-16 */
ALTER TABLE `zt_requirement` ADD COLUMN `entriesCode` varchar(50) NULL AFTER `opinion`, ADD COLUMN `parentCode` varchar(50) NULL AFTER `entriesCode`;
ALTER TABLE `zt_requirement` ADD COLUMN `contact` varchar(30) NULL AFTER `end`;
ALTER TABLE `zt_requirement` ADD COLUMN `implement` text NULL AFTER `contact`,ADD COLUMN `handling` text NULL AFTER `implement`,ADD COLUMN `analysis` text NULL AFTER `handling`;
ALTER TABLE `zt_requirement` ADD COLUMN `feedbackCode` varchar(50) NULL AFTER `parentCode`;
ALTER TABLE `zt_requirement` ADD COLUMN `feedbackBy` char(30) NULL AFTER `createdDate`,ADD COLUMN `feedbackDate` date NULL AFTER `feedbackBy`;
ALTER TABLE `zt_requirement` ADD COLUMN `reviewComments` text NULL AFTER `feedbackDate`;

ALTER TABLE `zt_requirementspec` ADD COLUMN `entriesCode` varchar(50) NULL AFTER `app`, ADD COLUMN `parentCode` varchar(50) NULL AFTER `entriesCode`;
ALTER TABLE `zt_requirementspec` ADD COLUMN `contact` varchar(30) NULL AFTER `end`;
ALTER TABLE `zt_requirementspec` ADD COLUMN `implement` text NULL AFTER `contact`,ADD COLUMN `handling` text NULL AFTER `implement`,ADD COLUMN `analysis` text NULL AFTER `handling`;
ALTER TABLE `zt_requirementspec` ADD COLUMN `feedbackCode` varchar(30) NULL AFTER `parentCode`;

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushUrl', 'http://180.76.121.222/app/osc/jinke_sync/webhooks/feedback');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushAppId', 'jinke');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushAppSecret', '482733936f2e45eaba0cc5768e5541eb');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushUsername', 'test');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushEnable', 'enable');

/* 2021-11-24 guchaonan*/
ALTER TABLE zt_opinion ADD COLUMN synUnion VARCHAR(255);
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('all','opinion','synUnionList','1','不同步','0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('all','opinion','synUnionList','2','同步总行','0');

/* 2021-12-06 */
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setProblemMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u4e8c\\u7ebf\\u95ee\\u9898\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u4e8c\\u7ebf\\u95ee\\u9898<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setDemandMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u4e8c\\u7ebf\\u9700\\u6c42\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u4e8c\\u7ebf\\u9700\\u6c42<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setModifyMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u751f\\u4ea7\\u53d8\\u66f4\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u751f\\u4ea7\\u53d8\\u66f4<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setFixMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u6570\\u636e\\u4fee\\u6b63\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u6570\\u636e\\u4fee\\u6b63<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setGainMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u6570\\u636e\\u83b7\\u53d6\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u6570\\u636e\\u83b7\\u53d6<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setPlanMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u9879\\u76ee\\u7acb\\u9879\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u9879\\u76ee\\u7acb\\u9879\\u3011\\u6216\\u3010\\u5e74\\u5ea6\\u8ba1\\u5212\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5e74\\u5ea6\\u4fe1\\u606f\\u5316\\u9879\\u76ee\\u8ba1\\u5212\\u7ef4\\u62a4\\u3011\\u5904\\u7406<\\/strong><strong><span style=\\\"color:#E53333\\\">\\u9879\\u76ee\\u7acb\\u9879<\\/span><\\/strong><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><span><\\/span><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setReviewMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u9879\\u76ee\\u8bc4\\u5ba1\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u9879\\u76ee\\u8bc4\\u5ba1\\u3011\\u6216\\u3010\\u9879\\u76ee\\u7ba1\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u8bc4\\u5ba1\\u3011\\u5904\\u7406<span style=\\\"color:#E53333\\\">\\u9879\\u76ee\\u8bc4\\u5ba1<\\/span><\\/strong><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><span><\\/span><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setChangeMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u9879\\u76ee\\u53d8\\u66f4\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u9879\\u76ee\\u53d8\\u66f4\\u3011\\u6216\\u3010\\u9879\\u76ee\\u7ba1\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u53d8\\u66f4\\u3011\\u5904\\u7406<span style=\\\"color:#E53333\\\">\\u9879\\u76ee\\u53d8\\u66f4<\\/span><span style=\\\"color:#E53333\\\"><\\/span><\\/strong><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><span><\\/span><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setEntriesMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u9700\\u6c42\\u6761\\u76ee\\u53d8\\u66f4\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u9700\\u6c42\\u6761\\u76ee\\u3011\\u6216\\u3010\\u9700\\u6c42\\u6c60\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u9700\\u6c42\\u6761\\u76ee\\u3011\\u5904\\u7406<span style=\\\"color:#E53333\\\">\\u9700\\u6c42\\u6761\\u76ee\\u53d8\\u66f4<\\/span><\\/strong><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><span><\\/span><\\/p>\"}');

/* 2021-12-08 */
ALTER TABLE `zt_change` ADD COLUMN `actualBegin` date NULL AFTER `createdDate`, ADD COLUMN `actualEnd` date NULL AFTER `actualBegin`, ADD COLUMN `supply` varchar(255) NULL AFTER `actualEnd`;
ALTER TABLE `zt_requirement` ADD COLUMN `changedDate` date NULL AFTER `changedTimes`;
ALTER TABLE `zt_requirementspec` ADD COLUMN `changedDate` date NULL AFTER `createdDate`;

/* 2021-12-09 */
update zt_cron set `status` = 'stop' where command = 'moduleName=backup&methodName=backup';
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'systemMailName', '研发过程管理平台');
ALTER TABLE `zt_problem` ADD COLUMN `lastDealDate` date NULL AFTER `closedDate`;
ALTER TABLE `zt_demand` ADD COLUMN `lastDealDate` date NULL AFTER `closedDate`;
ALTER TABLE `zt_modify` ADD COLUMN `lastDealDate` date NULL AFTER `createdDate`;
ALTER TABLE `zt_info` ADD COLUMN `lastDealDate` date NULL AFTER `createdDate`;
ALTER TABLE `zt_projectplan` ADD COLUMN `lastDealDate` date NULL AFTER `deleted`;

/* 2021-12-16 */
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'flowPending', '[{\"flowCode\":\"sublist\",\"flowName\":\"\\u793a\\u4f8b\\u6d41\\u7a0b\",\"flowView\":\"id\",\"flowAssign\":\"assignedBy\",\"flowApp\":\"data-app=\\\"sublist\\\"\",\"flowOrder\":\"99\",\"flowEnable\":\"disable\"}]');

/* 2021-12-21 */
delete from zt_config where `owner` = 'system' and module = 'common' and section = 'global' and `key` = 'setProblemMail';
delete from zt_config where `owner` = 'system' and module = 'common' and section = 'global' and `key` = 'setDemandMail';

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setProblemMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u95ee\\u9898\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u95ee\\u9898\\u6c60\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u95ee\\u9898<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setDemandMail', '{\"mailTitle\":\"\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u9700\\u6c42\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u9700\\u6c42\\u6c60\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u9700\\u6c42<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');

ALTER TABLE `zt_problem`
    ADD COLUMN `editedBy` varchar(50) NOT NULL AFTER `lastDealDate`,
ADD COLUMN `editedDate` date NULL AFTER `editedBy`;

ALTER TABLE `zt_demand`
    ADD COLUMN `editedBy` varchar(50) NOT NULL AFTER `lastDealDate`,
ADD COLUMN `editedDate` date NULL AFTER `editedBy`;

ALTER TABLE `zt_problem` ADD COLUMN `projectPlan` mediumint(8) NOT NULL DEFAULT 0 AFTER `lastStatus`;
ALTER TABLE `zt_demand` ADD COLUMN `projectPlan` mediumint(8) NOT NULL DEFAULT 0 AFTER `lastStatus`;

/*2021-12-23 qijingwang*/
CREATE TABLE `zt_publishrecord` (
                                    `id` mediumint(8) NOT NULL AUTO_INCREMENT,
                                    `publishId` mediumint(8) NOT NULL,
                                    `userId` mediumint(8) NOT NULL,
                                    `createTime` datetime NOT NULL,
                                    PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_flow_publish`  (
                                    `id` mediumint unsigned NOT NULL,
                                    `parent` mediumint unsigned NOT NULL,
                                    `assignedTo` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `createdDate` datetime(0) NOT NULL,
                                    `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `editedDate` datetime(0) NOT NULL,
                                    `assignedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `assignedDate` datetime(0) NOT NULL,
                                    `mailto` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
                                    `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    `sdate` datetime(0) NOT NULL,
                                    `endDate` datetime(0) NOT NULL,
                                    `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                    PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_flow_publish`
    MODIFY COLUMN `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT FIRST;

INSERT INTO `zt_workflow`(`parent`, `child`, `type`, `navigator`, `app`, `position`, `module`, `table`, `name`, `flowchart`, `js`, `css`, `order`, `buildin`, `administrator`, `desc`, `version`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('', '', 'flow', 'primary', 'publish', 'aftermy', 'publish', 'zt_flow_publish', '公告管理', '', '', '', 0, 0, '', '', '1.0', 'normal', 'admin', '2021-12-16 15:36:23', 'admin', '2021-12-16 15:57:33');

INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'browse', '浏览列表', 'single', 'same', 'override', 'normal', 'menu', '', 'direct', 0, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'enable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'create', '新建', 'single', 'same', 'override', 'normal', 'menu', '', 'direct', 1, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'enable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'batchcreate', '批量新建', 'batch', 'different', 'override', 'normal', 'menu', '', 'direct', 2, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'edit', '编辑', 'single', 'same', 'override', 'normal', 'browseandview', '', 'direct', 3, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'enable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'view', '查看详情', 'single', 'same', 'override', 'normal', 'browse', '', 'direct', 4, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'enable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'delete', '删除', 'single', 'same', 'override', 'none', 'browseandview', '', 'dropdownlist', 5, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'enable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'link', '关联数据', 'single', 'same', 'override', 'none', 'view', '', 'direct', 6, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'unlink', '移除数据', 'single', 'same', 'override', 'none', 'view', '', 'direct', 7, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'export', '导出数据', 'single', 'same', 'override', 'none', 'menu', '', 'direct', 8, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'exporttemplate', '下载模板', 'single', 'same', 'override', 'none', 'menu', '', 'direct', 9, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'import', '导入数据', 'single', 'same', 'override', 'none', 'menu', '', 'direct', 10, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'showimport', '导入确认', 'single', 'same', 'override', 'none', 'menu', '', 'direct', 11, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'report', '报表', 'single', 'same', 'override', 'none', 'menu', '', 'direct', 12, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'assign', '指派', 'single', 'same', 'override', 'modal', 'browseandview', '', 'direct', 13, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'batchedit', '批量编辑', 'batch', 'different', 'override', 'normal', 'browse', '', 'direct', 14, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowaction`(`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'batchassign', '批量指派', 'batch', 'same', 'override', 'normal', 'browse', '', 'direct', 15, 0, 0, '[]', '', '[]', '', '', '', '', '', '', 'disable', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');

INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'id', 'mediumint', '8', '编号', 'label', '', '[]', '', 'unique', '', 717, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'parent', 'mediumint', '8', '父流程ID', 'label', '', '[]', '0', '', '', 718, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'assignedTo', 'varchar', '30', '指派给', 'select', '', 'user', '', '', '', 719, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'status', 'varchar', '30', '发布状态', 'select', '', '{\"1\":\"\\u5f85\\u53d1\\u5e03\",\"2\":\"\\u5df2\\u53d1\\u5e03\",\"3\":\"\\u5df2\\u5931\\u6548\",\"4\":\"\\u5df2\\u4f5c\\u5e9f\"}', '', '', '', 720, 0, 0, '0', '0', '0', '0', 0, '', 'admin', '2021-12-16 15:36:23', 'admin', '2021-12-16 15:52:23');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'createdBy', 'varchar', '30', '由谁创建', 'select', '', 'user', '', '', '', 721, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'createdDate', 'datetime', '', '创建日期', 'datetime', '', '[]', '', '', '', 722, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'editedBy', 'varchar', '30', '由谁编辑', 'select', '', 'user', '', '', '', 723, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'editedDate', 'datetime', '', '编辑日期', 'datetime', '', '[]', '', '', '', 724, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'assignedBy', 'varchar', '30', '由谁指派', 'select', '', 'user', '', '', '', 725, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'assignedDate', 'datetime', '', '指派日期', 'datetime', '', '[]', '', '', '', 726, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'mailto', 'text', '', '抄送给', 'multi-select', '', 'user', '', '', '', 727, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'deleted', 'varchar', '10', '是否删除', 'radio', '', '[\"\\u672a\\u5220\\u9664\",\"\\u5df2\\u5220\\u9664\"]', '0', '', '', 728, 0, 0, '0', '0', '0', '1', 0, '', 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'type', 'varchar', '255', '公告类型', 'radio', '', '{\"1\":\"\\u4e0a\\u7ebf\\u516c\\u544a\",\"2\":\"\\u505c\\u673a\\u516c\\u544a\",\"3\":\"\\u666e\\u901a\\u516c\\u544a\"}', '', '', '', 0, 0, 0, '0', '0', '0', '0', 0, '', 'admin', '2021-12-16 15:48:28', 'admin', '2021-12-16 15:57:20');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'content', 'text', '', '公告内容', 'richtext', '', '[]', '', '', '', 0, 0, 0, '0', '0', '0', '0', 0, '', 'admin', '2021-12-16 15:49:10', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'sdate', 'datetime', '', '生效时间', 'datetime', '', '[]', '', '', '', 0, 0, 0, '0', '0', '0', '0', 0, '', 'admin', '2021-12-16 15:49:44', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'endDate', 'datetime', '', '失效时间', 'datetime', '', '[]', '', '', '', 0, 0, 0, '0', '0', '0', '0', 0, '', 'admin', '2021-12-16 15:50:05', '', '0000-00-00 00:00:00');
INSERT INTO `zt_workflowfield`(`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'name', 'varchar', '255', '公告名称', 'input', '', '[]', '', '', '', 0, 0, 0, '0', '0', '0', '0', 0, '', 'admin', '2021-12-16 15:50:30', '', '0000-00-00 00:00:00');

INSERT INTO `zt_workflowlabel`(`module`, `code`, `label`, `params`, `orderBy`, `order`, `buildin`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES ('publish', 'browse77', '所有', '[{\"field\":\"deleted\",\"operator\":\"equal\",\"value\":\"0\"}]', '', 0, 0, 'admin', '2021-12-16 15:36:23', '', '0000-00-00 00:00:00');

INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'type', 1, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'content', 2, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'sdate', 3, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'endDate', 4, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'name', 5, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'id', 6, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'assignedTo', 7, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'status', 8, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'createdBy', 9, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'createdDate', 10, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'editedBy', 11, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'editedDate', 12, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'assignedBy', 13, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'assignedDate', 14, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'mailto', 15, 0, 'left', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'browse', 'actions', 16, 160, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'type', 1, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'content', 2, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'sdate', 3, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'endDate', 4, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'name', 5, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'assignedTo', 6, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'status', 7, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'create', 'mailto', 8, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'type', 1, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'content', 2, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'sdate', 3, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'endDate', 4, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'name', 5, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'assignedTo', 6, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'status', 7, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'edit', 'mailto', 8, 0, '', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'type', 1, 0, 'info', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'content', 2, 0, 'info', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'sdate', 3, 0, 'info', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'endDate', 4, 0, 'info', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'name', 5, 0, 'info', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'id', 6, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'assignedTo', 7, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'status', 8, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'createdBy', 9, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'createdDate', 10, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'editedBy', 11, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'editedDate', 12, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'assignedBy', 13, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'assignedDate', 14, 0, 'basic', '0', '0', '', '', '');
INSERT INTO `zt_workflowlayout`(`module`, `action`, `field`, `order`, `width`, `position`, `readonly`, `mobileShow`, `summary`, `defaultValue`, `layoutRules`) VALUES ('publish', 'view', 'mailto', 15, 0, 'basic', '0', '0', '', '', '');

ALTER TABLE `zt_projectplan` ADD COLUMN `secondLine` tinyint NOT NULL DEFAULT 0 AFTER `lastDealDate`;

update zt_projectplan set secondLine = 1 where `name` = '2021二线管理';

/* 2022-01-04 */
ALTER TABLE `zt_demand` ADD COLUMN `opinionID` int NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE `zt_demand` ADD COLUMN `end` date NULL AFTER `editedDate`, ADD COLUMN `product` mediumint NOT NULL DEFAULT 8 AFTER `end`;
ALTER TABLE `zt_demand` ADD COLUMN `productPlan` mediumint NOT NULL DEFAULT 0 AFTER `product`;
ALTER TABLE `zt_demand` ADD COLUMN `isPayment` varchar(100) NOT NULL AFTER `productPlan`;

update zt_demand set type = '1' where type = 'top';
update zt_demand set type = '4' where type = 'outsice';
update zt_demand set type = '5' where type = 'feedback';
update zt_demand set type = '11' where type = 'inside';
update zt_demand set type = '7' where type = 'other';

update zt_demand set `desc` = CONCAT(`desc`,'<p>',requirement,'<p>') where LENGTH(requirement) > 0;
update zt_demand set `requirement` = '' where LENGTH(requirement) > 0;

ALTER TABLE `zt_opinion` ADD `assignedTo` varchar(255) NOT NULL AFTER `createdDate`, ADD `workload` float NOT NULL AFTER `assignedTo`;

ALTER TABLE `zt_opinion` ADD COLUMN `lastStatus` char(30) NOT NULL AFTER `synUnion`;

REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '8', '清算总中心', '0');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '7', '其他', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '6', '需求变更', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '5', '二线需求', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '4', '来函', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '3', '科技司', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '9', '来函/签报', '0');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '10', '技术驱动', '0');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '', '', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '1', '行长来签报', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '2', '需求意向书', '1');
REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'opinion', 'sourceModeList', '11', '内部', '0');

/*2022-1-10 问题模块 */
ALTER TABLE `zt_problem` ADD COLUMN `isPayment` VARCHAR(255) NOT NULL AFTER `app`;
/*2022-1-10  生产变更模块 */
ALTER TABLE `zt_modify` ADD COLUMN `isPayment` VARCHAR(255) NOT NULL AFTER `app`;
/*2022-1-10  数据修正模块 */
ALTER TABLE `zt_info` ADD COLUMN `isPayment` VARCHAR(255) NOT NULL AFTER `app`;

/*2022-1-10 初始化系统分类数据 */
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '4', 'CIPS类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '3', '征信类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '2', '总行类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '1', '支付类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '', '', '1');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '6', '其他', '0');

/*2022-1-10 更新应用系统表数据  */
UPDATE `zt_application` SET isPayment = '1' WHERE `isPayment` = 'yes';
UPDATE `zt_application` SET isPayment = '' WHERE `isPayment` <> '1';

/*2022-1-11 系统管理增加修改人，修改时间 */
ALTER TABLE `zt_application` ADD COLUMN `editedBy` varchar(30) NOT NULL AFTER `createdDate`,ADD COLUMN `editedDate` date NULL AFTER `editedBy`;

CREATE TABLE `zt_mailrecord`  (
                                  `id` int NOT NULL AUTO_INCREMENT,
                                  `account` varchar(80) NOT NULL,
                                  `sendDate` date NOT NULL,
                                  `sendResult` char(10) NOT NULL,
                                  `createdDate` datetime NOT NULL,
                                  PRIMARY KEY (`id`)
);

/*2022-1-13 更新系统分类数据顺序 */
delete from zt_lang where `module` = 'application' and section = 'isPaymentList';
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '', '', '1');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '1', '支付类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '2', '总行类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '3', '征信类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '4', 'CIPS类', '0');
INSERT INTO `zt_lang`(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'application', 'isPaymentList', '6', '其他', '0');

/*2022-1-13 生产变更表增加修改人、修改时间 */
ALTER TABLE `zt_modify` ADD COLUMN `editedBy` varchar(30) NOT NULL AFTER `createdDate`,ADD COLUMN `editedDate` datetime NULL AFTER `editedBy`;

/*2022-1-13 数据修正和数据获取表增加修改人、修改时间 */
ALTER TABLE `zt_info` ADD COLUMN `editedBy` varchar(30) NOT NULL AFTER `createdDate`,ADD COLUMN `editedDate` datetime NULL AFTER `editedBy`;

/* 2022-1-17 */
ALTER TABLE `zt_demand` MODIFY COLUMN `closedBy` varchar(50) NULL DEFAULT NULL AFTER `createdDate`;
ALTER TABLE `zt_problem` MODIFY COLUMN `closedBy` varchar(50) NULL DEFAULT NULL AFTER `createdDate`;
ALTER TABLE `zt_problem` ADD COLUMN `buildTimes` mediumint NOT NULL DEFAULT 0 AFTER `editedDate`;

/* 2022-1-24 */
ALTER TABLE `zt_demand` MODIFY COLUMN `requirement` mediumtext NULL DEFAULT NULL;
ALTER TABLE `zt_product` ADD COLUMN `codebasePath` varchar(100) AFTER `status`;
ALTER TABLE `zt_projectplan` ADD COLUMN `outsideProject` varchar(255) NULL DEFAULT NULL AFTER `name`;

/* 2022-1-26 */
ALTER TABLE `zt_projectplan` MODIFY COLUMN `phone` varchar(30) NOT NULL AFTER `owner`, MODIFY COLUMN `version` varchar(20)  NOT NULL DEFAULT '0' AFTER `phone`, ADD COLUMN `yearVersion` mediumint NOT NULL DEFAULT 0 AFTER `version`;

ALTER TABLE `zt_projectplan` MODIFY COLUMN `version` mediumint NOT NULL DEFAULT '0' AFTER `phone`;

/* 2022-2-14 */
ALTER TABLE `zt_dept`
    ADD COLUMN `groupleader` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '组长审核' AFTER `function`;

/* 2022-2-15 */
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setWorkFlowMail', '{\"mailTitle\":\"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a%s\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\",\"variables\":[\"xx\"],\"mailContent\":\"<strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011-\\u3010\\u5f85\\u5904\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>%s<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong>\"}');




/* 2022-3-08 新增部门质量部QA */
ALTER TABLE `zt_dept` ADD COLUMN `qa` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '质量部QA' AFTER `cm`;

/* 2022-3-9 */ALTER TABLE `zt_processimprove` ADD COLUMN `createdDept` mediumint(9) NOT NULL DEFAULT -1 AFTER `createdBy`;

/* 2022-3-9 tongyanqi  产品增加字段*/
ALTER TABLE `zt_product`
    ADD COLUMN `os` varchar(30) NULL COMMENT '支持平台' AFTER `bind`,
ADD COLUMN `arch` varchar(30) NULL COMMENT '硬件平台' AFTER `os`;

/* 2022-3-9 tongyanqi 项目关联产品计划*/
DROP TABLE IF EXISTS `zt_projectplanrelation`;
CREATE TABLE `zt_projectplanrelation` (
                                          `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '表id',
                                          `projectId` bigint(20) NOT NULL COMMENT '项目id',
                                          `planRelation` varchar(255) NOT NULL COMMENT '关联的计划版本 json 格式',
                                          `createTime` datetime DEFAULT NULL COMMENT '创建时间',
                                          `updateTime` datetime DEFAULT NULL COMMENT '更新时间',
                                          PRIMARY KEY (`id`),
                                          KEY `projectid` (`projectId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/* 2022-3-9 tongyanqi 需求表添加索引*/
ALTER TABLE `zt_demand`
ADD INDEX (`projectPlan`) ;

/*2022-3-9 tongyanqi 产品系统平台选择下拉内容 */
delete from zt_lang where `module` = 'productplan' and `section` = 'osTypeList';
delete from zt_lang where `module` = 'productplan' and `section` = 'archTypeList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '1', 'AIX71', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'archTypeList', '1', 'arm64', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'archTypeList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '2', 'AIX7200', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '3', 'Andriod', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '4', 'BAAS1000-X86', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '5', 'CentOS6.7', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '6', 'CentOS7', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '7', 'CentOS7.6', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '8', 'CSE', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '9', 'CSE803', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '10', 'HCS803', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '11', 'IOS', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '12', 'KUBESPHERE30-X86', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '13', 'Kylin10', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '14', 'Kylin7060', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '15', 'Linux77', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '16', 'Multiplatform', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '17', 'REDHAT7.5', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '18', 'SUSE11', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '19', 'SUSE11-TLQ', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '20', 'SUSE11SP4', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '21', 'SUSE12', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '22', 'SUSE1200', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '23', 'SUSE12000', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'productplan', 'osTypeList', '24', 'SUSE12SP5', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`)  VALUES ('zh-cn', 'productplan', 'osTypeList', '26', '麒麟V10', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`)  VALUES ('zh-cn', 'productplan', 'osTypeList', '27', '如缺失联系质量部', '0');

/*2022-3-9 tongyanqi 周报内外状态选择下拉内容 */
delete from zt_lang where `module` = 'productplan' and `section` = 'insideReportStatusList';
delete from zt_lang where `module` = 'productplan' and `section` = 'outsideReportStatusList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '0', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '1', '已取消', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '2', '待立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '3', '延迟立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '4', '暂停立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '5', '立项中', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '6', '已立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '7', '进度正常', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '8', '进度延迟', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '9', '项目已暂停', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '10', '项目已撤销', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'insideReportStatusList', '11', '已结项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '0', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '1', '已取消', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '2', '待立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '3', '延迟立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '4', '暂停立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '5', '立项中', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '6', '已立项', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '7', '进度正常', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '8', '进度延迟', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`)  VALUES ('zh-cn', 'project', 'outsideReportStatusList', '9', '项目已暂停', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '10', '项目已撤销', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'outsideReportStatusList', '11', '已结项', '0');

/*2022-3-9 tongyanqi 周报表 */
CREATE TABLE `zt_projectweeklyreport` (
                                          `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                          `projectId` bigint(20) DEFAULT NULL COMMENT '项目id',
                                          `projectName` varchar(125) DEFAULT NULL COMMENT '项目名称',
                                          `projectCode` varchar(50) DEFAULT NULL COMMENT '项目编码',
                                          `projectAlias` varchar(50) DEFAULT NULL COMMENT '项目代号',
                                          `projectProgress` varchar(3) DEFAULT '' COMMENT '整体进度',
                                          `projectStartDate` date DEFAULT NULL COMMENT '项目开始时间',
                                          `projectEndDate` date DEFAULT NULL COMMENT '项目结束时间',
                                          `reportStartDate` date DEFAULT NULL COMMENT '报告开始时间',
                                          `reportEndDate` date DEFAULT NULL COMMENT '报告结束时间',
                                          `productPlan` text DEFAULT NULL COMMENT '介子及相关时间 json',
                                          `projectType` varchar(30) DEFAULT '' COMMENT '内部项目类型',
                                          `productDemand` text DEFAULT NULL COMMENT '产品需求',
                                          `progressStatus` varchar(20) DEFAULT NULL COMMENT '当前阶段',
                                          `devDept` varchar(30) DEFAULT '' COMMENT '开发部门',
                                          `insideStatus` varchar(20) DEFAULT NULL COMMENT '内部项目状态',
                                          `outsideStatus` varchar(20) DEFAULT NULL COMMENT '外部项目状态',
                                          `reportDesc` text DEFAULT NULL COMMENT '报告内容',
                                          `productPublishDesc` text DEFAULT NULL,
                                          `transDesc` text DEFAULT NULL COMMENT '移交说明',
                                          `remark` varchar(255) DEFAULT NULL COMMENT '备注',
                                          `createTime` datetime DEFAULT NULL,
                                          `updateTime` datetime DEFAULT NULL,
                                          `createdBy` varchar(20) DEFAULT NULL,
                                          `outProjectCode` varchar(50) DEFAULT NULL COMMENT '（外部） 项目编号',
                                          `outProjectName` varchar(50) DEFAULT NULL COMMENT '（外部）项目名称',
                                          `outSubProjectName` varchar(50) DEFAULT NULL COMMENT '（外部）\n子项目名称',
                                          `govDept` varchar(50) DEFAULT NULL COMMENT '司局',
                                          `outDemander` varchar(50) DEFAULT NULL COMMENT '外部需求方',
                                          `outBearCompany` varchar(50) DEFAULT NULL COMMENT '（外部）\n承建单位',
                                          `outPlanStartDate` date DEFAULT NULL,
                                          `outPlanEndDate` date DEFAULT NULL,
                                          `outPlanWorkload` varchar(10) DEFAULT NULL COMMENT '（外部）计划工作量(人月)',
                                          `outPlanChange` varchar(255) DEFAULT NULL COMMENT '（外部）变更情况',
                                          `productBuilds` text DEFAULT NULL COMMENT '产品发布情况',
                                          `pm` varchar(20) DEFAULT NULL COMMENT '项目经理',
                                          `milestone` varchar(255) DEFAULT NULL COMMENT '项目里程碑',
                                          `risks` text DEFAULT NULL COMMENT '风险描述',
                                          `issues` text DEFAULT NULL COMMENT '问题描述',
                                          `editedBy` varchar(20) DEFAULT NULL COMMENT '最后编辑人',
                                          `deleted` tinyint(4) unsigned zerofill DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          KEY `projectId` (`projectId`) USING BTREE,
                                          KEY `reportStartDate` (`reportStartDate`)  USING BTREE,
                                          KEY `reportEndDate` (`reportEndDate`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;
/* 2022-03-10 */
ALTER TABLE `zt_problem` ADD COLUMN `product` mediumint(9)  NOT NULL DEFAULT 8 COMMENT '产品' AFTER `editedBy`;
ALTER TABLE `zt_problem` ADD COLUMN `productPlan` mediumint(9)  NOT NULL DEFAULT 0 COMMENT '产品版本' AFTER `product`;
/*2022-3-9 tongyanqi 周报表修改 */
ALTER TABLE `zt_projectweeklyreport` MODIFY COLUMN `progressStatus`  varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '当前阶段' AFTER `productDemand`;
ALTER TABLE `zt_release` ADD COLUMN`createdBy` varchar(20) DEFAULT NULL AFTER `deleted`;

/* 2022-03-18 新增阶段历史记录表 */
CREATE TABLE `zt_executionspec` (
                                    `execution` mediumint NOT NULL,
                                    `version` smallint NOT NULL,
                                    `code` varchar(80) DEFAULT NULL,
                                    `name` varchar(255) NOT NULL,
                                    `milestone` enum('0','1') NOT NULL DEFAULT '0',
                                    `begin` date NOT NULL,
                                    `end` date NOT NULL,
                                    `realBegan` date DEFAULT NULL,
                                    `realEnd` date DEFAULT NULL,
                                    UNIQUE KEY `execution` (`execution`,`version`)
);

/* 2022-03-22 */
ALTER TABLE `zt_project` ADD COLUMN `startBy` varchar(30)  NOT NULL DEFAULT '' AFTER `deleted`,
ADD COLUMN `startDate` datetime(0) NOT NULL AFTER `startBy`,
ADD COLUMN `changeBy` varchar(30)  NOT NULL DEFAULT '' AFTER `startDate`,
ADD COLUMN `changeDate` datetime(0) NOT NULL AFTER `changeBy`;

ALTER TABLE `zt_project` ADD COLUMN `finishBy` varchar(30) NOT NULL DEFAULT '' AFTER `changeDate`,
ADD COLUMN `finishDate` datetime(0) NOT NULL AFTER `finishBy`,
ADD COLUMN `splitBy` varchar(30) NOT NULL DEFAULT '' AFTER `finishDate`,
ADD COLUMN `splitDate` datetime(0) NOT NULL AFTER `splitBy`;

/* 2022-03-22*/
ALTER TABLE `zt_executionspec` ADD COLUMN `planDuration` int(0) NULL DEFAULT 0 AFTER `realEnd`;

/* 2022-03-23*/
update zt_project  set `order` = id * 5;
ALTER TABLE `zt_executionspec` ADD COLUMN `desc` text NULL AFTER `planDuration`;

/* 2022-03-23 */
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'task', '', 'workThreshold', '18');

/*20220311 新增 问题需求表字段*/
ALTER TABLE `zt_demand` ADD COLUMN `systemverify` enum('0','1') NOT NULL DEFAULT '1' COMMENT '是否需要系统部验证,0:不需要,1:需要' AFTER `isPayment`;
ALTER TABLE `zt_demand` ADD COLUMN `verifyperson` varchar(255)   DEFAULT  NULL COMMENT '验证人员' AFTER `systemverify`;
ALTER TABLE `zt_demand` ADD COLUMN `laboratorytest` varchar(255)   DEFAULT  NULL COMMENT '实验室测试' AFTER `verifyperson`;
ALTER TABLE `zt_demand` ADD COLUMN `testperson` varchar(255)   DEFAULT  NULL COMMENT '测试人员' AFTER `laboratorytest`;

ALTER TABLE `zt_problem` ADD COLUMN `systemverify` enum('0','1') NOT NULL DEFAULT '1' COMMENT '是否需要系统部验证,0:不需要,1:需要' AFTER `productPlan`;
ALTER TABLE `zt_problem` ADD COLUMN `verifyperson` varchar(255)   DEFAULT  NULL COMMENT '验证人员' AFTER `systemverify`;
ALTER TABLE `zt_problem` ADD COLUMN `laboratorytest` varchar(255)   DEFAULT  NULL COMMENT '实验室测试' AFTER `verifyperson`;
ALTER TABLE `zt_problem` ADD COLUMN `testperson` varchar(255)   DEFAULT  NULL COMMENT '测试人员' AFTER `laboratorytest`;

/*20220316 新增 问题表字段*/
ALTER TABLE `zt_problem` ADD COLUMN `belongapp` varchar(255)  DEFAULT NULL   COMMENT '所属应用系统' AFTER `testperson`;

/*2022-3-17 tangfei 项目团队角色下拉框选择内容*/
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '0', '技术经理', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '1', '项目主管', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '2', '项目经理', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '3', '项目副经理', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '4', '测试经理', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '5', '架构工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '6', '开发工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '7', '功能测试工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '8', '性能测试工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '9', '安全测试工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '10', '集成工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '11', '质量保证工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '12', '配置管理工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '13', '度量与分析工程师', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '14', '产品经理', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'project', 'roleList', '15', '其他', '0');

/* 2022-03-24 厂商的新表 @ 2022-3-9 */
CREATE TABLE `zt_relationplan`
(
    `id` int NOT NULL AUTO_INCREMENT,
    `project` int NOT NULL DEFAULT 0,
    `product` int NOT NULL DEFAULT 0,
    `plan` int NOT NULL DEFAULT 0,
    `createdDate` date NOT NULL,
    `createdBy` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/* 2022-03-25 wangbing 增加业务需求单位和接收时间 */
ALTER TABLE `zt_demand` ADD COLUMN `rcvDate` DATE default NULL;
ALTER TABLE `zt_demand` ADD COLUMN `union` varchar(255) default NULL;
/* 2022-03-28 tongyanqi 产品版本关系json var 太短了 换text */
ALTER TABLE `zt_projectplanrelation` MODIFY COLUMN `planRelation`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '产品版本关系json' AFTER `projectId`;
/* 2022-3-29 */
update zt_task set parent = '0' where parent = '-1';

/* 设置SESSION、GLOBAL 最大值 迭代六厂商的历史数据处理方法用到*/
/* 2022-4-2 */
SET GLOBAL group_concat_max_len = 10240000;
SET SESSION group_concat_max_len = 10240000;

/* 2022-04-04 tangfei 增加parentId,标识出当前记录是id为xx记录里面配合人员中的一条记录 */
ALTER TABLE `zt_consumed` ADD COLUMN `parentId` mediumint default 0;
ALTER TABLE `zt_consumed` ADD COLUMN `deleted` enum('0','1') NOT NULL DEFAULT '0';

/* 2022-4-7 */
ALTER TABLE `zt_project` MODIFY COLUMN `progress` float NOT NULL AFTER `realEnd`;

/* 2022-4-15 */
CREATE TABLE `zt_abnormal`  (
      `id` int NOT NULL AUTO_INCREMENT,
      `task` int NOT NULL,
      `project` int NOT NULL,
      `effort` int NOT NULL,
      PRIMARY KEY (`id`)
);

/* 2022-4-21 */
ALTER TABLE `zt_effort` ADD COLUMN `realDate` datetime NULL AFTER `date`;

/* 2022-4-28 */
ALTER TABLE `zt_im_chatuser` ADD `lastReadMessage` int(11) unsigned NOT NULL DEFAULT 0 AFTER `category`;
ALTER TABLE `zt_im_conferenceaction` CHANGE `type` `type` ENUM('create','invite','join','leave','close') NOT NULL DEFAULT 'create';
ALTER TABLE `zt_im_chat` ADD `ownedBy` varchar(30) NOT NULL DEFAULT '' AFTER `createdDate`;
ALTER TABLE `zt_im_conference` CHANGE `rid` `rid` CHAR(40) NOT NULL DEFAULT '';
ALTER TABLE `zt_im_conferenceaction` CHANGE `rid` `rid` CHAR(40) NOT NULL DEFAULT '';
ALTER TABLE `zt_im_conferenceaction` CHANGE `type` `type` enum('create','invite','join','leave','close','publish') NOT NULL DEFAULT 'create';
ALTER TABLE `zt_im_conferenceaction` ADD `data` text NOT NULL DEFAULT '' AFTER `type`;

/* 2022-4-29 */
delete from zt_cron where command = 'moduleName=measurement&methodName=initCrontabQueue';
delete from zt_cron where command = 'moduleName=measurement&methodName=execCrontabQueue';

/* 2022-04-07 tangfei 问题与需求增加‘制版申请’和‘制版信息’两个字段 todo */
ALTER TABLE `zt_problem`  ADD `plateMakAp` mediumtext  NOT NULL AFTER `solution`;
ALTER TABLE `zt_problem`  ADD `plateMakInfo` mediumtext  NOT NULL AFTER `plateMakAp`;
ALTER TABLE `zt_demand`   ADD `plateMakAp` mediumtext  NOT NULL AFTER `conclusion`;
ALTER TABLE `zt_demand`   ADD `plateMakInfo` mediumtext  NOT NULL AFTER `plateMakAp`;


/*2022-4-8 tongyanqi 年度计划负责人改为多人 */
ALTER TABLE `zt_projectplan` MODIFY COLUMN `owner`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `bearDept`;
/*2022-4-8 tongyanqi 年度计划表增加 【架构转型】【系统整合】【上云】【密码改造】【备注说明】【是否重点项目】 */
ALTER TABLE `zt_projectplan`
    ADD COLUMN `architrcturalTransform`  varchar(30) NULL COMMENT '架构转型'  AFTER `basis`,
ADD COLUMN `systemAssemble`  varchar(30) NULL COMMENT '系统集合' AFTER `architrcturalTransform`,
ADD COLUMN `cloudComputing`  varchar(30) NULL COMMENT '上云' AFTER `systemAssemble`,
ADD COLUMN `passwordChange`  varchar(30) NULL COMMENT '密码更变' AFTER `cloudComputing`,
ADD COLUMN `planRemark`  text NULL COMMENT '备注说明' AFTER `secondLine`,
ADD COLUMN `isImportant`  varchar(30) NULL COMMENT '是否重点项目' AFTER `planRemark`,
ADD COLUMN `productsRelated`  text NULL COMMENT '涉及产品 json' AFTER `isImportant`;
/*2022-4-12 tongyanqi 项目白名单维护表 */
ALTER TABLE `zt_acl` ADD COLUMN `reason`  int(4) NULL COMMENT '添加原因 1001=立项领导'  AFTER `source`;
/*2022-4-12 tongyanqi 项目周边添加字段*/
ALTER TABLE `zt_projectweeklyreport`
    ADD COLUMN `insideMilestone`  text NULL COMMENT '内部里程碑' AFTER `reportDesc`,
ADD COLUMN `outsideMilestone`  text NULL COMMENT '外部里程碑' AFTER `insideMilestone`,
ADD COLUMN `outsidePlan`  text NULL COMMENT '外部计划升级显示多个 存json数组' AFTER `deleted`;
/*2022-4-12 tongyanqi 年度计划自定义字段默认值 添加删除方法以防重复执行*/
delete from zt_lang where `module` = 'productplan' and `section` = 'architrcturalTransformList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'architrcturalTransformList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'architrcturalTransformList', '1', '是', '0');
delete from zt_lang where `module` = 'productplan' and `section` = 'systemAssembleList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'systemAssembleList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'systemAssembleList', '1', '是', '0');
delete from zt_lang where `module` = 'productplan' and `section` = 'cloudComputingList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'cloudComputingList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'cloudComputingList', '1', '是', '0');
delete from zt_lang where `module` = 'productplan' and `section` = 'passwordChangeList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'passwordChangeList', '', '', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'passwordChangeList', '1', '是', '0');
delete from zt_lang where `module` = 'productplan' and `section` = 'isImportantList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'isImportantList', '1', '是', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'projectplan', 'isImportantList', '', '', '0');

/* 2022-4-18 迭代七，增加平台需求收集表*/
CREATE TABLE `zt_demandcollection` (
                                       `id` mediumint NOT NULL AUTO_INCREMENT COMMENT '编号',
                                       `title` varchar(255) NOT NULL COMMENT '主题',
                                       `dept` mediumint DEFAULT NULL COMMENT '部门id',
                                       `submitter` varchar(30) DEFAULT NULL,
                                       `type` varchar(20) DEFAULT NULL COMMENT '需求类型',
                                       `desc` mediumtext COMMENT '需求描述',
                                       `analysis` mediumtext COMMENT '需求分析',
                                       `priority` varchar(10) DEFAULT NULL COMMENT '优先级',
                                       `createDate` datetime DEFAULT NULL,
                                       `createBy` varchar(30) DEFAULT NULL COMMENT '创建人',
                                       `processingDate` datetime DEFAULT NULL,
                                       `processingBy` varchar(30) DEFAULT NULL COMMENT '处理人',
                                       `handoverDate` datetime DEFAULT NULL,
                                       `handoverBy` varchar(30) DEFAULT NULL COMMENT '移交人',
                                       `receiptDate` datetime DEFAULT NULL,
                                       `feedbackDate` datetime DEFAULT NULL,
                                       `feedbackResult` varchar(50) DEFAULT NULL COMMENT '评审结果',
                                       `scheduledDate` datetime DEFAULT NULL,
                                       `launchDate` date DEFAULT NULL,
                                       `Expected` varchar(50) DEFAULT NULL COMMENT '期望迭代',
                                       `Actual` varchar(50) DEFAULT NULL COMMENT '实际迭代',
                                       `Implementation` mediumint DEFAULT NULL COMMENT '实现部门',
                                       `Developer` varchar(30) DEFAULT NULL COMMENT '开发人员',
                                       `state` varchar(30) DEFAULT NULL,
                                       `updateDate` datetime DEFAULT NULL,
                                       `updateBy` varchar(30) DEFAULT NULL COMMENT '修改时间',
                                       `product` varchar(30) DEFAULT NULL COMMENT '产品经理',
                                       `productmanager` varchar(30) DEFAULT NULL COMMENT '产品经理',
                                       `assignFor` varchar(30) DEFAULT NULL,
                                       `copyFor` varchar(500) DEFAULT NULL,
                                       `dealuser` varchar(30) DEFAULT NULL,
                                       `deleted` int DEFAULT '0',
                                       `developstate` varchar(20) DEFAULT NULL,
                                       `confirmBy` varchar(30) DEFAULT NULL,
                                       `confirmDate` datetime DEFAULT NULL,
                                       `closedBy` varchar(30) DEFAULT NULL,
                                       `closedDate` datetime DEFAULT NULL,
                                       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户需求收集表';

/* 2022-4-18 迭代七，初始化平台需求收集的状态和类型*/
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','','','1');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','1','已新建','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','2','已采纳','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','3','待分析','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','4','已移交','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','5','已上线','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','6','待确认','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','7','已确认','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','8','未采纳','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','9','已取消','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','10','已撤销','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','statusList','11','重复需求','0');

INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','','','1');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','1','新需求','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','2','体验问题','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','3','产品BUG','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','4','性能问题','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','5','与实际不符','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','6','数据修正','0');
INSERT INTO zt_lang (`lang`,`module`,`section`,`key`,`value`,`system`) values ('zh-cn','demandcollection','typeList','7','其它','0');

/*2022-4-20 过程改进意见-反馈增加选项*/
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'processimprove', 'isAcceptList', '1', '是', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'processimprove', 'isAcceptList', '2', '否', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'processimprove', 'isAcceptList', '3', '待定', '0');
/*2022-4-20 tongyanqi 添加解决时间*/
ALTER TABLE `zt_problem` ADD COLUMN `solvedTime`  datetime NULL COMMENT '1、如果当前单子状态为“已关闭”，则取关闭时间；'  ;
ALTER TABLE `zt_demand` ADD COLUMN `solvedTime`  datetime NULL COMMENT '1、如果当前单子状态为“已关闭”，则取关闭时间；' ;
/*2022-4-26 tongyanqi  项目来源字段扩容*/
ALTER TABLE `zt_projectplan` MODIFY COLUMN `basis`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `app`;
/*2022-4-28 以上 */



/* 2022-4-29 迭代7 项目评审 */
ALTER TABLE zt_dept
    ADD `firstReviewer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '评审接口人',
    ADD `reviewer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '评审专员';
/* 2022-4-29 迭代7 项目评审 */
ALTER TABLE `zt_review`
    MODIFY COLUMN `project` mediumint(8) unsigned NOT NULL COMMENT '项目id',
    MODIFY COLUMN `type` char(30) NOT NULL COMMENT '评审类型',
    MODIFY COLUMN `title` varchar(255) NOT NULL COMMENT '评审标题',
    MODIFY COLUMN  `object` varchar(255) NOT NULL,
    MODIFY COLUMN `status` char(30) NOT NULL COMMENT '评审状态',
    MODIFY COLUMN `owner` varchar(255) NOT NULL COMMENT '评审主席',
    MODIFY COLUMN `expert` varchar(255) NOT NULL COMMENT '评审专家',
    MODIFY COLUMN `content` text NOT NULL COMMENT '评审信息',
    MODIFY COLUMN `grade` varchar(255) NOT NULL COMMENT '评审方法',
    MODIFY COLUMN `outside` varchar(255) NOT NULL COMMENT '外部评审人员',
    MODIFY COLUMN `reviewer` varchar(255) NOT NULL COMMENT '评审人员',
    MODIFY COLUMN `reviewedBy` varchar(255) NOT NULL COMMENT '评审参与人员',
    MODIFY COLUMN `auditedBy` varchar(255) NOT NULL COMMENT '',
    MODIFY COLUMN `createdBy` char(30) NOT NULL COMMENT '创建人',
    MODIFY COLUMN `createdDate` date NOT NULL COMMENT '创建时间',
    MODIFY COLUMN `lastReviewedBy` varchar(255) DEFAULT NULL COMMENT '最后审核人',
    MODIFY COLUMN  `lastReviewedDate` date NOT NULL COMMENT '最后审核时间',
    MODIFY COLUMN `deadline` date NOT NULL COMMENT '计划完成时间',
    MODIFY COLUMN `lastAuditedBy` varchar(255) NOT NULL COMMENT '',
    MODIFY COLUMN `lastAuditedDate` date NOT NULL COMMENT '',
    MODIFY COLUMN `lastEditedBy` varchar(255) NOT NULL COMMENT '修改人',
    MODIFY COLUMN `lastEditedDate` date NOT NULL COMMENT '修改时间',
    MODIFY COLUMN `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
    ADD `isFirstReview` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否需要初审 1-需要 2-不需要 ' AFTER status,
    ADD `qa` varchar(50) NOT NULL DEFAULT '' COMMENT 'QA审核人员' AFTER isFirstReview,
    ADD `dealUser` varchar(255) NOT NULL DEFAULT '' COMMENT '待处理人' AFTER reviewedBy,
    ADD `version` mediumint(9) NOT NULL DEFAULT 0 COMMENT '正式审核版本或者外部审核版本' AFTER dealUser,
    ADD `verifyVersion` mediumint(9) NOT NULL DEFAULT 0 COMMENT '验证版本' AFTER version,
    ADD `rejectStage` tinyint(1) NOT NULL DEFAULT 0 COMMENT '驳回阶段  默认0 1-预审驳回 2-初审驳回 3-正式审核驳回 4-外部审核驳回 5-验证驳回' AFTER verifyVersion,
    ADD `reviewStage` mediumint(8) NOT NULL DEFAULT 0 COMMENT '审核步骤id' AFTER rejectStage,
    ADD `createdDept` mediumint(8) NOT NULL DEFAULT 0 COMMENT '创建人部门id' AFTER createdBy,
    ADD `preReviewDeadline` date NOT NULL COMMENT '预审截止时间' AFTER createdDate,
    ADD `firstReviewDeadline` date NOT NULL COMMENT '初审截至时间' AFTER preReviewDeadline,
    ADD isEditInfo TINYINT(1) DEFAULT 0 NOT NULL COMMENT '是否需要修改资料 0默认  1-需要修改资料 2不需要修改资料'  AFTER isFirstReview;
/* 2022-4-29 迭代7 项目评审节点 */
ALTER TABLE zt_reviewnode
    ADD `subObjectType` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '子分类' AFTER objectType,
ADD `type` TINYINT(1) DEFAULT 1 NOT NULL COMMENT '类型   1评审 2指派' AFTER objectID;

/* 2022-4-29 迭代7 项目评审节点权限 */
UPDATE zt_grouppriv set  method = 'review'
where 1
  AND  module = 'review'
  and `method` = 'assess';

/* 2022-4-29 新增review表 字段 wangyongliang*/
ALTER TABLE `zt_review`
    ADD COLUMN `editBy` varchar(100)   DEFAULT  NULL COMMENT '由谁编辑' AFTER `deleted`,
ADD COLUMN `editDate` datetime(0) NOT NULL COMMENT '编辑时间' AFTER `editBy`,
ADD COLUMN `closePerson` varchar(100)   DEFAULT  NULL COMMENT '关闭人员' AFTER `editDate`,
ADD COLUMN `closeTime` datetime(0) NOT NULL COMMENT '关闭时间' AFTER `closePerson`,
ADD COLUMN `trialDept` varchar(100)   DEFAULT  NULL COMMENT '初审部门' AFTER `closeTime`,
ADD COLUMN `trialDeptLiasisonOfficer` varchar(255)   DEFAULT  NULL COMMENT '初审部门接口人' AFTER `trialDept`,
ADD COLUMN `trialAdjudicatingOfficer` varchar(255)   DEFAULT  NULL COMMENT '初审主审人员' AFTER `trialDeptLiasisonOfficer`,
ADD COLUMN `trialJoinOfficer` varchar(255)   DEFAULT  NULL COMMENT '初审参与人员' AFTER `trialAdjudicatingOfficer`,
ADD COLUMN `closeDate` date NOT NULL COMMENT '关闭日期' AFTER `trialJoinOfficer`,
ADD COLUMN `qualityCm` varchar(100)   DEFAULT  NULL COMMENT '质量部CM' AFTER `closeDate`,
ADD COLUMN `comment` mediumtext  DEFAULT  NULL COMMENT '备注' AFTER `qualityCm`;


/* 2022-04-29 新增自定义类型  wangyongliang*/
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'review', 'reviewerList', '', '', '0');

/* 2020-4-29 迭代七 审批->问题列表新增有谁创建字段和创建日期字段*/
ALTER TABLE `zt_reviewissue`
    ADD COLUMN `validation` char(30) NOT NULL COMMENT '验证人员',
    ADD COLUMN `verifyDate` date NOT NULL COMMENT '验证日期',
    ADD COLUMN `raiseBy` char(30) NOT NULL COMMENT '提出人',
    ADD COLUMN `raiseDate` date NOT NULL COMMENT '提出日期',
    ADD COLUMN `dealDesc` text NOT NULL COMMENT '处理情况描述',
    ADD COLUMN `delDesc` text NOT NULL COMMENT '删除备注',
    ADD COLUMN `editBy` varchar(255) NOT NULL COMMENT '由谁编辑',
    ADD COLUMN `editDate` date NOT NULL COMMENT '编辑日期';

/*2022-4-29 wangjiurong 工时表添加扩展信息字段*/
ALTER TABLE zt_consumed ADD extra varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '扩展信息' AFTER mailto;
/*2022-4-29 wangjiurong 修改黑名单字段*/
ALTER TABLE zt_acl MODIFY COLUMN reason int(4) DEFAULT 0 NULL COMMENT '添加原因 1001=立项领导',
    ADD subObjectID INT(10) DEFAULT 0 NOT NULL COMMENT '子对象id' AFTER objectType;


/* 2022-4-29 将reviewissue数据表原type字段更新 wangshusen*/
UPDATE zt_reviewissue SET `type`= "meeting" WHERE `type` = "meetting";
UPDATE zt_reviewissue SET `type`= "trial" WHERE `type` = "first";
UPDATE zt_reviewissue SET `type`= "out" WHERE `type` = "external";

/* 2022-4-29 将zt_review历史数据更新(修复历史数据，上线以后再执行) wangjiurong  */
update zt_review set status = 'waitApply', dealUser = createdBy where 1 and status = 'wait';
update zt_review set status = 'waitApply', dealUser = createdBy where 1 and status = 'draft';


/*  2022-5-19 需求任务新增期望完成时间 wangshusen*/
ALTER TABLE `zt_requirement` ADD COLUMN `deadLine` date NOT NULL COMMENT '期望完成时间';
ALTER TABLE `zt_requirement` ADD COLUMN `dealUser` char(30) NOT NULL COMMENT '下一节点处理人';

/*2022-5-9 tongyanqi 修改问题表*/
ALTER TABLE `zt_problem`
ADD COLUMN `IssueId`  varchar(25) NULL COMMENT '总中心接口：外部单号(API)' AFTER `solvedTime`,
ADD COLUMN `DepIdofIssueCreator`  varchar(25) NULL COMMENT '外部创建人部门(API)' AFTER `IssueId`,
ADD COLUMN `ChangeIdRelated`  varchar(255) NULL COMMENT '引发该问题的变更(API)' AFTER `DepIdofIssueCreator`,
ADD COLUMN `IncidentIdRelated`  varchar(255) NULL COMMENT '引发该问题的事件(API)' AFTER `ChangeIdRelated`,
ADD COLUMN `EffectOfService`  text NULL COMMENT '业务影响（API）' AFTER `IncidentIdRelated`,
ADD COLUMN `RecoveryTime`  datetime NULL COMMENT '恢复时间(API)' AFTER `EffectOfService`,
ADD COLUMN `ProblemSummary`  text NULL COMMENT '问题摘要(API)' AFTER `RecoveryTime`,
ADD COLUMN `IssueCreator`  varchar(25) NULL COMMENT '问题创建人(API)' AFTER `ProblemSummary`,
ADD COLUMN `TeleNoOfCreator`  varchar(25) NULL COMMENT '创建人联系方式(API)' AFTER `IssueCreator`,
ADD COLUMN `NodeIdOfIssue`  varchar(25) NULL COMMENT '问题节点(API)' AFTER `TeleNoOfCreator`,
ADD COLUMN `DrillCausedBy`  varchar(255) NULL DEFAULT '' COMMENT '引发该问题的演练(API)' AFTER `NodeIdOfIssue`,
ADD COLUMN `Optimization`  varchar(255) NULL COMMENT '优化及改进建议(API)' AFTER `DrillCausedBy`,
ADD COLUMN `TimeOfReport`  datetime NULL COMMENT '清算报告时间（api）' AFTER `Optimization`,
ADD COLUMN `IssueStatus`  varchar(255) NULL COMMENT '外部问题单状态（API）' AFTER `TimeOfReport`,
ADD COLUMN `TimeOfClosing`  datetime NULL COMMENT '外部关闭时间（API）' AFTER `IssueStatus`;
ALTER TABLE `zt_problem` ADD INDEX `IssueId` (`IssueId`) USING BTREE ;
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','apiDealUserList','userAccount','litianzi','1');

/*2022-5-9 tangfei  增加问题反馈字段*/
ALTER TABLE `zt_problem`
ADD COLUMN `Tier1Feedback`  mediumtext NULL COMMENT '初步反馈' AFTER `TimeOfClosing`,
ADD COLUMN `SolutionFeedback`  varchar(30) NULL COMMENT '反馈单解决方式' AFTER `Tier1Feedback`,
ADD COLUMN `TeleOfIssueHandler`  varchar(30) NULL COMMENT '处理人联系方式' AFTER `SolutionFeedback`,
ADD COLUMN `IfultimateSolution`  varchar(30) NULL COMMENT '是否为最终方案' AFTER `TeleOfIssueHandler`,
ADD COLUMN `ChangeSolvingTheIssue`  mediumtext NULL COMMENT '解决该问题的变更' AFTER `IfultimateSolution`,
ADD COLUMN `PlannedTimeOfChange`  datetime NULL COMMENT '计划解决（变更）时间' AFTER `ChangeSolvingTheIssue`,
ADD COLUMN `PlannedDateOfChangeReport`  date NULL  COMMENT '计划提交变更日期' AFTER `PlannedTimeOfChange`,
ADD COLUMN `PlannedDateOfChange`   date NULL COMMENT '计划变更日期' AFTER `PlannedDateOfChangeReport`,
ADD COLUMN `ReviewResult`  enum('0','1') NOT NULL DEFAULT '0' COMMENT '审批结果' AFTER `PlannedDateOfChange`,
ADD COLUMN `ReasonOfIssueRejecting`  mediumtext  NOT NULL COMMENT '问题退回原因' AFTER `ReviewResult`,
ADD COLUMN `ReviewStatus`  varchar(30) NULL COMMENT '反馈单审批状态' AFTER `ReasonOfIssueRejecting`,
ADD COLUMN `ReviewOpinion`  mediumtext  NOT NULL COMMENT '反馈单待审批意见' AFTER `ReviewStatus`,
ADD COLUMN `EditorImpactscope`  mediumtext  NOT NULL COMMENT '影响范围' AFTER `ReviewOpinion`;

/*2022-5-9 tongyanqi 新增反馈期限*/
ALTER TABLE `zt_problem` ADD COLUMN `feedbackExpireTime`  datetime NULL COMMENT '反馈期限' AFTER `editedDate`;

/*2022-5-11 shixuyang 新增新建反馈单字段*/
ALTER TABLE `zt_problem` ADD COLUMN `ifReturn`  varchar(8) NULL COMMENT '是否退回' AFTER `feedbackExpireTime`;
ALTER TABLE `zt_problem` ADD COLUMN `CorresProduct`  varchar(255) NULL COMMENT '对应产品' AFTER `ifReturn`;
ALTER TABLE `zt_problem` ADD COLUMN `revisionRecord`  mediumtext NULL COMMENT '修订记录' AFTER `CorresProduct`;

/*2022-5-12 shixuyang 新增新建反馈单字段*/
ALTER TABLE `zt_problem` ADD COLUMN `feedbackToHandle` varchar(255) NULL COMMENT '反馈单待处理人' AFTER `revisionRecord`;

/*2022-5-16 tongyanqi 接口定义表内容*/
INSERT INTO `zt_entry` (`name`, `account`, `code`, `key`, `freePasswd`, `ip`, `desc`, `createdBy`, `createdDate`, `calledTime`, `editedBy`, `editedDate`, `deleted`) VALUES ('问题接受', 'admin', 'jinke1problem', 'gk9aya7gat6f9lqdm72wkradfzfcbuhb', '0', '*', '', 'admin', '2022-4-29 17:17:30', 0, 'admin', '2022-5-5 15:05:02', '0');

/*2022-5-17 tangfei 增加反馈单审批字段*/
ALTER TABLE `zt_problem`
ADD COLUMN `reviewStage` mediumint(8) NOT NULL DEFAULT 0 COMMENT '审核步骤id' AFTER revisionRecord,
ADD `version` mediumint(9) NOT NULL DEFAULT 0 COMMENT '正式审核版本或者外部审核版本' AFTER revisionRecord;

/*2022-5-18 shixuyang 增加反馈单次数*/
ALTER TABLE `zt_problem` ADD COLUMN `feedbackNum` int(4) NULL DEFAULT '0' COMMENT '反馈单次数' AFTER `feedbackToHandle`;

/*2022-5-19 tongyanqi  返库期限自定义*/
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','expireDaysList','days','3','1');

/*2022-5-19 tangfei 问题池推送反馈单请求配置*/
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushProblemFeedbackUrl','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushProblemFeedbackAppId','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushProblemFeedbackAppSecret','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushProblemFeedbackUsername','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushProblemFeedbackEnable','enable');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushProblemFileIP','');

/*2022-5-23 tongyanqi 问题来源类型*/
ALTER TABLE `zt_problem` ADD COLUMN `ProblemSource` varchar(255) NULL COMMENT '问题来源类型' AFTER `IssueId`;


/*2022-5-25 wangjiurong 数据获取单*/
CREATE TABLE `zt_infoqz` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `code` varchar(30) NOT NULL COMMENT '数据单号',
                             `action` enum('gain','fix') NOT NULL COMMENT '数据来源   gain 数据获取  fix数据修正',
                             `type` varchar(30) NOT NULL COMMENT '数据类型 tech：技术数据 business：业务数据',
                             `classify` varchar(30) NOT NULL COMMENT '数据类别',
                             `gainType` varchar(30) NOT NULL COMMENT '数据获取的数据获取方式 1-桌面获取 2-非桌面获取',
                             `fixType` varchar(30) NOT NULL COMMENT '实现方式  project-项目实现  second-二线实现',
                             `systemType` varchar(30) NOT NULL COMMENT '系统类别 deal交易系统,  info:信息系统',
                             `reviewStage` smallint(6) NOT NULL DEFAULT 0 COMMENT '审核流程步骤id',
                             `isNPC` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否NPC  1:是 2-否',
                             `version` smallint(6) NOT NULL DEFAULT 1 COMMENT '版本 默认是1 驳回时+1',
                             `node` varchar(255) NOT NULL COMMENT '获取节点',
                             `planBegin` datetime NOT NULL COMMENT '计划开始日期',
                             `planEnd` datetime NOT NULL COMMENT '计划结束日期',
                             `actualBegin` datetime DEFAULT NULL COMMENT '实际开始日期',
                             `actualEnd` datetime DEFAULT NULL COMMENT '实际结束日期',
                             `app` varchar(255) NOT NULL COMMENT '受影响业务系统id，支持多选',
                             `isPayment` varchar(255) NOT NULL COMMENT '系统分类',
                             `from` varchar(255) NOT NULL COMMENT '关联问题单/数据单',
                             `project` varchar(255) NOT NULL COMMENT '关联项目 支持多选',
                             `release` varchar(255) NOT NULL COMMENT '关联版本发布',
                             `demand` varchar(255) NOT NULL COMMENT '关联需求单 支持多选',
                             `problem` varchar(255) NOT NULL COMMENT '关联问题单子 支持多选',
                             `desc` mediumtext NOT NULL COMMENT '摘要 数据修正摘要或者数据获取摘要',
                             `content` mediumtext CHARACTER SET utf8mb4 NOT NULL COMMENT '数据内容',
                             `reason` mediumtext NOT NULL COMMENT '申请原因',
                             `purpose` mediumtext NOT NULL COMMENT '数据获取用途',
                             `operation` mediumtext NOT NULL COMMENT '数据修正下的操作内容',
                             `test` mediumtext NOT NULL COMMENT '测试情况',
                             `step` mediumtext NOT NULL COMMENT '操作步骤',
                             `checkList` mediumtext NOT NULL COMMENT '上线材料清单',
                             `status` varchar(50) NOT NULL COMMENT '数据状态',
                             `deliveryType` varchar(30) NOT NULL COMMENT '交付类型',
                             `supply` varchar(255) NOT NULL COMMENT '支持人员',
                             `result` mediumtext NOT NULL COMMENT 'result',
                             `externalId` varchar(50) NOT NULL COMMENT '外部清算中心id',
                             `externalStatus` tinyint(1) NOT NULL DEFAULT 0 COMMENT '外部清算中心审核状态',
                             `externalRejectReason` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '外部驳回原因',
                             `pushExternalStatus` tinyint(1) NOT NULL DEFAULT 0 COMMENT '推送到清算中心的状态 0-默认 1-推送成功 2推送失败',
                             `isTest` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否通过测试 1是 2否',
                             `desensitization` mediumtext CHARACTER SET utf8mb4 NOT NULL COMMENT '数据脱敏要求信息',
                             `createdBy` char(30) NOT NULL COMMENT '创建人',
                             `createdDept` int(11) NOT NULL COMMENT '创建人部门id',
                             `createUserPhone` varchar(20) NOT NULL COMMENT '申请人联系方式',
                             `createdDate` datetime NOT NULL COMMENT '创建时间',
                             `editedBy` varchar(30) NOT NULL COMMENT '最近修改人',
                             `editedDate` datetime DEFAULT NULL COMMENT '最近修改时间',
                             `lastDealDate` date DEFAULT NULL COMMENT '最后处理时间',
                             PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*2022-5-16 王九荣  迭代八 二线管理-数据获取接口配置*/
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushInfoGainUrl', '');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushInfoGainAppId', '');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushInfoGainAppSecret', '');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushInfoGainUsername', '');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushInfoGainEnable', '');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global','pushInfoGainFileIP','');

/*2022-5-16 王九荣  迭代八 二线管理-数据获取新增字符类型列表值*/
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'infoQz', 'deliveryTypeList', '', '', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'infoQz', 'deliveryTypeList', 'clearCenter', '清算总中心', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'infoQz', 'deliveryTypeList', 'cfjx', '成方金信', '0');
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'infoQz', 'deliveryTypeList', 'other', '其他', '0');

/*2022-5-24 tangfei 增加审批状态*/
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked');

/*2022-5-25 tongyanqi  返库期限自定义*/
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','','','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','tofeedback','已反馈','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','Reopen','重开启','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','Re-open','重开启','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','Closed','已关闭','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','firstpassed','初步解决反馈通过','1');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','IssueStatusList','finalpassed','最终解决反馈通过','1');
ALTER TABLE `zt_problem` MODIFY COLUMN `product`  mediumint(9) NOT NULL DEFAULT 0 COMMENT '产品' AFTER `editedBy`;
ALTER TABLE `zt_demand`  MODIFY COLUMN `product`  mediumint(9) NOT NULL DEFAULT 0 COMMENT '产品' AFTER `end`;

/*2022-6-1 shixuyang 问题反馈单最近状态*/
ALTER TABLE `zt_problem` ADD COLUMN `lastReviewStatus` varchar(30) NULL COMMENT '问题反馈单最近状态' AFTER `feedbackToHandle`;

/* 2022-4-29 新增两个字段 wangshusen*/
ALTER TABLE `zt_reviewissue`
    ADD COLUMN `dealOwner` varchar(30) NOT NULL COMMENT '当前处理人',
    ADD COLUMN `dealDate` date NOT NULL COMMENT '当前处理时间';

/*2022-6-1 lizhongzheng 需求任务期望完成日期 历史数据处理 */
UPDATE zt_requirement rq, zt_opinion  op SET rq.deadLine = op.deadline WHERE rq.opinion = op.id;
/* 2022-5-23 生产变更新增表 */
CREATE TABLE `zt_modifycncc` (
                                 `id` int NOT NULL AUTO_INCREMENT,
                                 `mode` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `fixType` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `isInterrupt` tinyint NOT NULL DEFAULT '0',
                                 `isAppend` tinyint NOT NULL DEFAULT '0',
                                 `reviewStage` smallint DEFAULT '0',
                                 `isPayment` varchar(255) NOT NULL,
                                 `project` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `release` varchar(255) NOT NULL,
                                 `demand` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `productCode` text,
                                 `version` smallint NOT NULL DEFAULT '1',
                                 `checkList` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `createdDept` int NOT NULL,
                                 `createdDate` datetime NOT NULL,
                                 `editedBy` varchar(30) NOT NULL,
                                 `editedDate` datetime DEFAULT NULL,
                                 `feedbackDate` datetime NOT NULL,
                                 `closeBy` varchar(30) DEFAULT NULL,
                                 `closeDate` datetime DEFAULT NULL,
                                 `code` varchar(30) NOT NULL,
                                 `type` varchar(30) NOT NULL,
                                 `level` varchar(30) NOT NULL,
                                 `property` varchar(30) NOT NULL,
                                 `classify` varchar(30) NOT NULL,
                                 `node` varchar(255) NOT NULL,
                                 `planBegin` datetime NOT NULL,
                                 `planEnd` datetime NOT NULL,
                                 `app` varchar(255) NOT NULL,
                                 `problem` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                 `desc` varchar(200) NOT NULL,
                                 `reason` mediumtext NOT NULL,
                                 `target` mediumtext NOT NULL,
                                 `effect` mediumtext NOT NULL,
                                 `plan` mediumtext NOT NULL,
                                 `risk` mediumtext NOT NULL,
                                 `test` mediumtext NOT NULL,
                                 `step` mediumtext NOT NULL,
                                 `createdBy` char(30) NOT NULL,
                                 `applyUsercontact` varchar(20) DEFAULT NULL COMMENT '变更申请人联系方式',`lastDealDate` date DEFAULT NULL,
                                 `backspaceExpectedStartTime` datetime DEFAULT NULL COMMENT '预计回退开始时间',
                                 `backspaceExpectedEndTime` datetime DEFAULT NULL COMMENT '预计回退结束时间',
                                 `backupDataCenterChangeSyncDesc` text COMMENT '主备数据中心变更同步情况说明',
                                 `businessFunctionAffect` text COMMENT '各业务功能带来的影响',
                                 `changeContentAndMethod` text COMMENT '变更的内容和方法',
                                 `feasibilityAnalysis` varchar(255) DEFAULT NULL COMMENT '变更可行性分析',
                                 `changeRelation` text COMMENT '关联变更单列表',
                                 `changeSource` varchar(50) DEFAULT NULL COMMENT '变更来源',
                                 `changeStage` varchar(50) DEFAULT NULL COMMENT '变更阶段',
                                 `controlTableFile` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '关联项目控制表名称',
                                 `controlTableSteps` text COMMENT '关联项目控制表步骤',
                                 `cooperateDepNameList` varchar(30) DEFAULT NULL COMMENT '配合业务部门',
                                 `emergencyManageAffect` text COMMENT '对应急处置策略的影响（对故障处置策略自动化切换等的影响）',
                                 `implementModality` varchar(30) DEFAULT NULL COMMENT '实施方式',
                                 `isBusinessAffect` varchar(2) DEFAULT NULL COMMENT '实施期间是否有业务影响',
                                 `businessAffect` text COMMENT '实施期间业务影响',
                                 `isBusinessCooperate` varchar(2) DEFAULT NULL COMMENT '是否需要业务配合',
                                 `isBusinessJudge` varchar(2) DEFAULT NULL COMMENT '是否需要业务验证',
                                 `isPerAuthorization` varchar(2) DEFAULT NULL COMMENT '是否预授权',
                                 `judgeDep` varchar(50) DEFAULT NULL COMMENT '验证部门',
                                 `judgePlan` text COMMENT '验证方案',
                                 `productRegistrationCode` varchar(255) DEFAULT NULL COMMENT '产品登记号',
                                 `riskAnalysisEmergencyHandle` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '风险分析与应急处置',
                                 `techniqueCheck` text COMMENT '技术验证',
                                 `benchmarkVerificationType` varchar(50) DEFAULT NULL COMMENT '基准验证类型',
                                 `verificationResults` varchar(255) DEFAULT NULL COMMENT '验证结果',
                                 `CNCCprojectIdUnique` text COMMENT '关联项目ID',
                                 `businessCooperateContent` text COMMENT '需要业务配合内容',
                                 `relatedDemandNum` text COMMENT '关联需求任务',
                                 `businessSystemIdList` text COMMENT '变更对象内容',
                                 `feedBackId` varchar(255) DEFAULT NULL COMMENT '反馈单id',
                                 `operationName` varchar(255) DEFAULT NULL COMMENT '操作名称',
                                 `depOddName` varchar(255) DEFAULT NULL COMMENT '开发部单号',
                                 `changeNum` varchar(255) DEFAULT NULL COMMENT '变更单号(运行部)',
                                 `operationStaff` varchar(255) DEFAULT NULL COMMENT '操作人员（运行部）',
                                 `problemDescription` text COMMENT '问题描述',
                                 `resolveMethod` text COMMENT '原因分析/解决方法',
                                 `supply` varchar(255) NOT NULL COMMENT '支持人员',
                                 `result` mediumtext NOT NULL COMMENT '执行结果',
                                 `operationType` varchar(255) DEFAULT NULL COMMENT '操作类型',
                                 `changeStatus` varchar(255) DEFAULT NULL COMMENT '外部状态',
                                 `changeRemark` text COMMENT '执行纪录',
                                 `reasonCNCC` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '退回原因',
                                 `actualBegin` datetime DEFAULT NULL,
                                 `actualEnd` datetime DEFAULT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

/* 2022-5-24新增生产变更接口调用配置*/
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushModifycnccUrl','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushModifycnccAppId','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushModifycnccAppSecret','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushModifycnccUsername','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushModifycnccEnable','enable');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','pushModifycnccFileIP','');

/*2022-5-30 系统对应分区表 */
CREATE TABLE `zt_partition`
(
    `id`          int         NOT NULL AUTO_INCREMENT,
    `name`        varchar(32) NOT NULL COMMENT '分区名',
    `application` varchar(64) NOT NULL COMMENT '系统编号',
    `ip`          varchar(32) NOT NULL COMMENT 'ip',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*2022-5-30 chendongcheng 新增字段 */
ALTER TABLE `zt_productcode`
    ADD COLUMN`modifycncc` int(8) NOT NULL AFTER `modify`;

/*2022-5-30 chendongcheng 新增字段 */
ALTER TABLE `zt_secondline`
    ADD `relationship` varchar(100) NOT NULL;

ALTER TABLE `zt_modifycncc`
    ADD `isSyncModifycncc` enum('0','1') NOT NULL COMMENT '是否同步过变更单',
    ADD `isSyncFeedback` enum('0','1') NOT NULL COMMENT '是否同步过反馈单',
    ADD `isSyncState` enum('0','1') NOT NULL COMMENT '是否同步过状态';

/*2022-5-30 guchaonan 创建CBP项目表 */
CREATE TABLE `zt_cbpproject` (
                                 `id` int NOT NULL AUTO_INCREMENT,
                                 `code` varchar(30)  NOT NULL,
                                 `name` varchar(255)  NOT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `zt_cbpproject` (code,name) VALUES
    ('CBP202125','2021年综合业务服务平台三期CBP项目'),
    ('CBP202201','2022年数据交换管理平台优化改造CBP项目'),
    ('CBP202202','2022年账户与支付业务风险监测系统功能完善研发CBP项目'),
    ('CBP202203','2022年计费2.0及跨行账户信息认证服务平台    (CBAC)完善研发CBP项目'),
    ('CBP202204','2021年企业信息联网核查系统优化改造及MIVS2.0研发CBP项目'),
    ('CBP202205','2022年支付报文传输平台优化CBP项目'),
    ('CBP202206','2022年支付业务    (含大数据）运维及监控类应用功能完善CBP项目'),
    ('CBP202207','2022年共享前置功能完善及信创改造CBP项目'),
    ('CBP202208','2022年数据中台功能优化改造研发CBP项目'),
    ('CBP202209','2022年央行资金清算平台建设CBP项目'),
    ('CBP202210','2022年支付信息统计分析系统优化改造CBP项目'),
    ('CBP202211','2022年自研分布式和CJDP5框架（二期）研发CBP项目'),
    ('CBP202212','2022年境内外币支付系统2.0分布式系统研发CBP项目'),
    ('CBP202213','2022年交易系统功能忧化改造研发CBP项目集'),
    ('CBP202214','2022年公共控制管理系统&公共参数管理系统优化改造CBP项目集'),
    ('CBP202215','2022年金融机构对账服务平台研发CBP项目集'),
    ('CBP202101','2021年利率报备监测分析系统建设CBP项目'),
    ('CBP202104_1','2021年支付系统公共管理服务改造CBP项目'),
    ('CBP202104_2','2021年支付系统查询数据库（PQDB）国产化建设CBP项目'),
    ('CBP202104_3','2021年SCPS对账子系统改造CBP项目'),
    ('CBP202105_2','2021年全栈国产化IBPS实例建设CBP项目'),
    ('CBP202105_3','2021年中央银行会计核算数据集中系统（ACS）应用软件优化研发CBP项目'),
    ('CBP202114','2021年CNCCQ消息中间件产品优化和完善研发CBP项目'),
    ('CBP202115','2021年大额平行系统账户子系统升级改造CBP项目'),
    ('CBP202116','2021年数据管理平台迁移CBP项目'),
    ('CBP202117','2021年供应链金融数字化平台建设CBP项目'),
    ('CBP202118','2021年账户管理系统升级改造CBP项目'),
    ('CBP202119','2021年差错处理平台功能完善研发CBP项目');

/*2022-6-6 guchaonan 支持关联关系逻辑删除 */
ALTER TABLE `zt_secondline`
    ADD `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否被删除';

/*2022-6-7 tongyanqi  质量部sftp*/
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'sftpList', 'host', '10.2.8.213', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'sftpList', 'port', '2222', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'sftpList', 'username', 'guest', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'sftpList', 'password', '1qaz!QAZ', '1');

/*2022-6-8 唐飞 增加反馈单操作类型 */
ALTER TABLE `zt_modifycncc`
    ADD `feedBackOperationType` varchar(255) DEFAULT NULL COMMENT '反馈单操作类型',
    ADD `executionResults` varchar(255) DEFAULT NULL COMMENT '反馈单执行结果';

/*2022-6-8 顾超男 区分内外部支持人员 */
ALTER TABLE `zt_modifycncc` ADD `internalSupply` varchar(100) DEFAULT NULL COMMENT '内部支持人员';


-- ----------------------------
-- 2022-06-09 tongyanqi kv表
-- ----------------------------
CREATE TABLE `zt_kv` (
`key` varchar(50) NOT NULL,
`value` varchar(255) DEFAULT NULL,
`expireTime` int(11) DEFAULT NULL,
PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `zt_kv` VALUES ('download', '0', '0');


-- ----------------------------
-- 2022-06-09 tongyanqi 下载列表
-- ----------------------------
CREATE TABLE `zt_downloads` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
`filename` varchar(255) NOT NULL COMMENT '文件名',
`downloadTime` datetime DEFAULT NULL COMMENT '下载时间',
`createTime` datetime NOT NULL COMMENT '创建时间',
`expireTime` int(11) NOT NULL DEFAULT 0 COMMENT '到期时间',
`status` varchar(10) NOT NULL COMMENT '状态',
`code` varchar(25) NOT NULL COMMENT '请求方code',
`fileDeleted` tinyint(4) DEFAULT 0 COMMENT '文件是否删除',
`downloadDeleted` tinyint(4) DEFAULT 0,
`md5` varchar(255) DEFAULT NULL,
`exception` varchar(255)  DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `expireTime` (`expireTime`) USING BTREE,
KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*2022-6-9 wangjiurong 清总-数据获取增加变更记录字段*/
ALTER TABLE zt_infoqz ADD externalChangeRemark varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' NOT NULL COMMENT '外部变更记录' AFTER externalRejectReason;

/* 2022-6-15 lizhongzheng需求意向-内部需求，需求来源方式历史数据修改 。修改zt_requirement所属应用系统字段类型*/
UPDATE zt_opinion set category = 'business' WHERE id IN (1, 12, 114);
UPDATE zt_opinion set category = 'structure' WHERE id IN (96, 97, 100);
UPDATE zt_opinion set sourceMode = 7 WHERE sourceMode = 10;
ALTER TABLE `zt_requirement` MODIFY COLUMN `app` varchar(255) NOT NULL;
ALTER TABLE `zt_requirementspec` MODIFY COLUMN `app` varchar(255) NOT NULL;
-- ----------------------------
-- 2022-6-15  tongyanqi 下载地址配置
-- ----------------------------
/* 2022-6-15 lizhongzheng需求意向-内部需求，需求来源方式历史数据修改 。修改zt_requirement所属应用系统字段类型*/
UPDATE zt_opinion set category = 'business' WHERE id IN (1, 12, 114);
UPDATE zt_opinion set category = 'structure' WHERE id IN (96, 97, 100);
UPDATE zt_opinion set sourceMode = 7 WHERE sourceMode = 10;
ALTER TABLE `zt_requirement` MODIFY COLUMN `app` varchar(255) NOT NULL;INSERT INTO `zt_config` VALUES ('system', 'common', 'global', 'downloadIP', 'http://172.22.99.203/api.php?m=api&f=download&time=1&token=80f45dfe6b7603a00d77236861fe4965&sign=%s&filename=%s');
INSERT INTO `zt_config` VALUES ('system', 'common', 'global', 'downloadIP', 'http://172.22.99.203/api.php?m=api&f=download&time=1&token=80f45dfe6b7603a00d77236861fe4965&sign=%s&filename=%s');

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'downloadIP', 'http://172.22.99.203/api.php?m=api&f=download&time=1&token=80f45dfe6b7603a00d77236861fe4965&sign=%s&filename=%s');

-- ----------------------------
-- hotfix
-- 2022-6-17  tongyanq
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'sftpList', 'info', '（SFTP://10.128.28.213:2222，用户密码：guest /1qaz!QAZ）', '1');
-- ----------------------------
-- 迭代11
-- 2022-6-17  tongyanqi 对外交付配置
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'productEnrollPushUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'testingRequestPushUrl', '');

-- ----------------------------
-- 2022-6-17  shixuyang 对外交付启用配置
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushOutwarddeliveryEnable', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushOutwarddeliveryAppId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushOutwarddeliveryAppSecret', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushOutwarddeliveryUsername', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushOutwarddeliveryFileIP', '');

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushOutwarddeliveryFileIP', '');
-- ----------------------------
-- 迭代11
-- 2022-6-22  tongyanqi 对外交付配置
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'checkDepartmentList', '', '', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'installationNodeList', '', '', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'productLineList', '', '', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'checkDepartmentList', '0', '测试中心', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'checkDepartmentList', '1', '其他', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'checkDepartmentList', '2', '开发中心', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'checkDepartmentList', '3', '生产中心', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'installationNodeList', '0', 'NPC', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'installationNodeList', '1', 'CCPC', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'installationNodeList', '2', '参与者', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'installationNodeList', '3', 'COC', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'installationNodeList', '4', '其他', '1');
-- ----------------------------
-- 迭代11
-- 2022-6-27  tongyanqi 对外交付表
-- ----------------------------
CREATE TABLE `zt_outwarddelivery` (
                                      `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
                                      `outwardDeliveryDesc` varchar(255) DEFAULT '' COMMENT '描述',
                                      `code` varchar(30) DEFAULT NULL COMMENT '单号',
                                      `productInfoCode` varchar(50) DEFAULT '' COMMENT '产品信息登记号 ',
                                      `isNewTestingRequest` tinyint(1) DEFAULT 0 COMMENT '是否新建测试申请 0 = 不是 1= 新建',
                                      `isNewProductEnroll` tinyint(1) DEFAULT 0 COMMENT '是否新建产品等级 0 = 不是 1= 新建',
                                      `isNewModifycncc` tinyint(1) DEFAULT 0 COMMENT '是否新建变更 0 = 不是 1= 新建',
                                      `testingRequestId` bigint(20) DEFAULT 0 COMMENT '测试申请id',
                                      `productEnrollId` bigint(20) DEFAULT 0 COMMENT '产品登记id',
                                      `modifycnccId` bigint(20) DEFAULT 0 COMMENT '生产变更id',
                                      `productLine` varchar(30) DEFAULT NULL COMMENT '产品线',
                                      `app` varchar(30) DEFAULT NULL COMMENT '所属系统 多个逗号分隔',
                                      `contactName` varchar(50) DEFAULT NULL COMMENT '联系人',
                                      `contactTel` varchar(50) DEFAULT NULL COMMENT '联系电话',
                                      `contactEmail` varchar(50) DEFAULT '' COMMENT '联系信箱',
                                      `productId` varchar(255) DEFAULT '' COMMENT '产品id 多个逗号分隔',
                                      `implementationForm` varchar(25) DEFAULT '0' COMMENT '实施形式 product 项目 second 二线',
                                      `projectPlanId` varchar(255) DEFAULT '0' COMMENT '关联项目id',
                                      `problemId` varchar(30) DEFAULT '' COMMENT '问题id 逗号分隔',
                                      `demandId` varchar(30) DEFAULT '0' COMMENT '需求id 逗号分隔',
                                      `requirementId` varchar(30) DEFAULT '' COMMENT '需求任务id 逗号分隔',
                                      `CBPprojectId` varchar(255) DEFAULT '' COMMENT 'cbp项目id',
                                      `rejectTimes` int(4) DEFAULT NULL COMMENT '外部打回次数',
                                      `createdBy` varchar(30) DEFAULT '' COMMENT '创建人',
                                      `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
                                      `createdDept` varchar(20) DEFAULT '' COMMENT '创建人部门，填写key值，创建时默认填写',
                                      `deleted` tinyint(4) DEFAULT 0,
                                      `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
                                      `editedBy` varchar(255) DEFAULT NULL COMMENT '编辑人',
                                      `closed` tinyint(4) DEFAULT 0 COMMENT '0 = 正常 1 = 已关闭',
                                      `closedDate` datetime DEFAULT NULL COMMENT '关闭时间',
                                      `closedBy` varchar(30) DEFAULT '' COMMENT '由谁关闭',
                                      `closedReason` varchar(255) DEFAULT NULL COMMENT '关闭原因',
                                      `release` varchar(255) DEFAULT NULL COMMENT '关联版本发布',
                                      `ROR` varchar(255) DEFAULT NULL COMMENT '修订记录，json格式，内含RORDate修订时间，RORContent修订内容',
                                      `revertBy` varchar(100) DEFAULT NULL COMMENT '内部退回按钮操作人',
                                      `revertDate` datetime DEFAULT NULL COMMENT '内部退回操作时间',
                                      `revertComment` varchar(100) DEFAULT NULL COMMENT '内部退回操作备注',
                                      `revertReason` varchar(100) DEFAULT NULL COMMENT '内部退回原因，枚举值可配置',
                                      `ifMediumChanges` tinyint(1) DEFAULT 1 COMMENT '介质是否变化 0：是，1：否',
                                      `reviewStage` smallint(6) DEFAULT NULL,
                                      `status` varchar(50) DEFAULT NULL COMMENT '状态',
                                      `version` mediumint(9) DEFAULT NULL COMMENT '版本',
                                      `level` varchar(30) DEFAULT NULL,
                                      `reviewSubject` varchar(20) DEFAULT NULL COMMENT '审核内容outwarddelivery testingrequest productenroll modifycncc',
                                      `lastDealDate` date DEFAULT NULL COMMENT '最后提交日期',
                                      PRIMARY KEY (`id`),
                                      KEY `testingRequestId` (`testingRequestId`) USING BTREE,
                                      KEY `productEnrollId` (`productEnrollId`),
                                      KEY `modifycnccId` (`modifycnccId`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 迭代11
-- 2022-6-27  tongyanqi 测试申请表
-- ----------------------------
CREATE TABLE `zt_testingrequest` (
                                     `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
                                     `code` varchar(30) DEFAULT NULL COMMENT '单号',
                                     `content` text DEFAULT NULL COMMENT '测试内容',
                                     `testTarget` varchar(255) DEFAULT NULL COMMENT '测试目标',
                                     `currentStage` varchar(30) DEFAULT NULL COMMENT '目前阶段',
                                     `env` text DEFAULT NULL COMMENT '环境综述',
                                     `os` varchar(30) DEFAULT '' COMMENT '操作系统',
                                     `testProduct` varchar(50) DEFAULT '' COMMENT '被测试产品',
                                     `testProductName` varchar(255) DEFAULT NULL COMMENT '被测试产品 名',
                                     `db` varchar(255) DEFAULT '' COMMENT '数据库类型',
                                     `testCase` text DEFAULT NULL COMMENT '测试用例',
                                     `testReport` text DEFAULT NULL COMMENT '测试报告',
                                     `problemId` varchar(255) DEFAULT '' COMMENT '问题清单',
                                     `demandId` varchar(255) DEFAULT NULL,
                                     `requirementId` varchar(255) DEFAULT NULL COMMENT '需求任务id 逗号分隔',
                                     `CBPprojectId` varchar(255) DEFAULT NULL COMMENT 'CBPprojectId 逗号分隔',
                                     `contactName` varchar(255) DEFAULT NULL,
                                     `contactTel` varchar(255) DEFAULT NULL,
                                     `projectCode` varchar(255) DEFAULT NULL COMMENT '项目名称',
                                     `projectName` varchar(255) DEFAULT NULL,
                                     `app` varchar(255) DEFAULT NULL,
                                     `implementationForm` varchar(255) DEFAULT NULL COMMENT '实现方式 project second',
                                     `productId` varchar(255) DEFAULT NULL COMMENT '产品信息 逗号分隔',
                                     `projectPlanId` varchar(255) DEFAULT '' COMMENT '项目信息 逗号分隔',
                                     `sampleNum` int(11) DEFAULT NULL COMMENT '样本编号 每次更新加1',
                                     `mediaTestNum` int(11) DEFAULT NULL COMMENT '介子测试次数',
                                     `returnReason` text DEFAULT NULL COMMENT '打回原因',
                                     `TestReportFromTestCenter` text DEFAULT NULL COMMENT '测试中心测试报告',
                                     `cardStatus` varchar(10) DEFAULT NULL COMMENT '外部审批状态 默认空 emis通过：1，打回：0，gitee通过：2',
                                     `returnDate` datetime DEFAULT NULL COMMENT '打回时间',
                                     `returnTimes` int(4) DEFAULT 0 COMMENT '退回次数',
                                     `returnPerson` varchar(30) DEFAULT NULL COMMENT '打回人',
                                     `returnCase` varchar(255) DEFAULT NULL COMMENT '打回原因',
                                     `createdDept` int(11) DEFAULT NULL COMMENT '创建部门',
                                     `createdBy` varchar(255) DEFAULT NULL COMMENT '创建人',
                                     `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
                                     `deleted` tinyint(4) DEFAULT 0 COMMENT '是否删除 0=正常 1= 删除',
                                     `editedBy` varchar(255) DEFAULT NULL COMMENT '编辑人',
                                     `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
                                     `remoteFilePath` varchar(70) DEFAULT NULL COMMENT 'sftp介质路径',
                                     `ifMediumChanges` tinyint(1) DEFAULT 1 COMMENT '介质是否变化 0：是，1：否',
                                     `giteeId` varchar(50) DEFAULT NULL COMMENT '金信id 由gitee返回',
                                     `status` varchar(20) DEFAULT NULL,
                                     `lastDealDate` date DEFAULT NULL COMMENT '最后提交时间',
                                     `version`  int NULL DEFAULT 0 COMMENT '审核版本',
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 迭代11
-- 2022-6-27  tongyanqi 产品登记表
-- ----------------------------
CREATE TABLE `zt_productenroll` (
                                    `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
                                    `code` varchar(30) DEFAULT NULL COMMENT '单号',
                                    `title` varchar(75) DEFAULT NULL COMMENT '标题',
                                    `isPlan` tinyint(1) DEFAULT 0 COMMENT '是否计划内软件 0 是 1否',
                                    `planProductName` varchar(75) DEFAULT NULL COMMENT '计划软件产品名称',
                                    `issueId` bigint(20) DEFAULT NULL COMMENT '问题条目',
                                    `demandId` varchar(60) DEFAULT NULL COMMENT '需求条目',
                                    `testingRequestCode` varchar(60) DEFAULT '' COMMENT '测试申请单号',
                                    `contactName` varchar(255) DEFAULT '' COMMENT '联系人',
                                    `contactTel` varchar(255) DEFAULT '' COMMENT '联系方式',
                                    `contactEmail` varchar(255) DEFAULT '' COMMENT '联系邮箱',
                                    `implementationForm` varchar(20) DEFAULT '0' COMMENT '实施形式 project 项目 second 二线',
                                    `productId` varchar(255) DEFAULT NULL COMMENT '产品信息 逗号分隔',
                                    `projectPlanId` varchar(255) DEFAULT '' COMMENT '项目信息 逗号分隔',
                                    `problemId` varchar(255) DEFAULT NULL COMMENT '问题id 逗号分隔',
                                    `requirementId` varchar(255) DEFAULT NULL COMMENT '需求任务id 逗号分隔',
                                    `CBPprojectId` varchar(255) DEFAULT NULL COMMENT 'CBPprojectId 逗号分隔',
                                    `implementationDepartment` varchar(255) DEFAULT '' COMMENT '负责部门',
                                    `productCode` varchar(255) DEFAULT NULL COMMENT '产品代码',
                                    `productName` varchar(255) DEFAULT NULL COMMENT '产品名称',
                                    `versionNum` varchar(30) DEFAULT NULL COMMENT '版本号',
                                    `lastVersionNum` varchar(30) DEFAULT '' COMMENT '上个版本号',
                                    `projectCode` varchar(255) DEFAULT NULL COMMENT '项目代码',
                                    `projectName` varchar(255) DEFAULT NULL COMMENT '项目名称',
                                    `lastEOStime` datetime DEFAULT NULL COMMENT '上一版本EOS时间',
                                    `cdCode` varchar(50) DEFAULT NULL COMMENT '光盘编号',
                                    `registeredCdNum` tinyint(4) DEFAULT NULL COMMENT '登记光盘数',
                                    `cdType` varchar(50) DEFAULT '' COMMENT '光盘类型（0：补丁光盘，1：软件光盘）',
                                    `attchment` varchar(50) DEFAULT NULL COMMENT '附件',
                                    `remoteFilePath` varchar(255) DEFAULT NULL COMMENT '介质路径',
                                    `mediaInfo` text DEFAULT NULL COMMENT '介质名称+字节数 json',
                                    `app` varchar(255) DEFAULT '' COMMENT '业务系统',
                                    `planSoftwareName` varchar(255) DEFAULT NULL COMMENT '计划软件名',
                                    `platform` tinyint(1) DEFAULT 0 COMMENT '所属平台 后台配置',
                                    `checkDepartment` tinyint(1) DEFAULT 0 COMMENT '检测单位 后台配置',
                                    `result` tinyint(1) DEFAULT 0 COMMENT '结论 0 通过 1 未通过',
                                    `installationNode` tinyint(1) DEFAULT 0 COMMENT '安装节点 后台配置',
                                    `productLine` tinyint(4) DEFAULT 0 COMMENT '产品线 后台配置',
                                    `optionSystem` varchar(50) DEFAULT '0' COMMENT '业务系统 后台配置',
                                    `planDistributionTime` bigint(20) DEFAULT NULL COMMENT '计划发布时间',
                                    `planUpTime` bigint(20) DEFAULT NULL COMMENT '计划上线时间',
                                    `softwareProductPatch` tinyint(1) DEFAULT 0 COMMENT '软件产品补丁 0 是 1否',
                                    `reasonFromJinke` varchar(255) DEFAULT NULL COMMENT '理由',
                                    `introductionToFunctionsAndUses` text DEFAULT NULL COMMENT '主要功能及用途',
                                    `remark` text DEFAULT NULL COMMENT '备注',
                                    `ifMediumChanges` tinyint(1) DEFAULT 1 COMMENT '介质是否变化 0：是，1：否',
                                    `giteeId` varchar(50) DEFAULT NULL COMMENT '金信id 由gitee返回',
                                    `status` varchar(20) DEFAULT NULL,
                                    `lastDealDate` date DEFAULT NULL COMMENT '最后提交时间',
                                    `softwareCopyrightRegistration` tinyint(1) DEFAULT NULL COMMENT '申请计算机软件著作权登记 0：是，1：否',
                                    `createdDept` int(10) DEFAULT NULL COMMENT '创建部门id',
                                    `createdBy` varchar(255) DEFAULT NULL,
                                    `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
                                    `cardStatus` varchar(10) DEFAULT NULL COMMENT '外部审批状态 默认空 emis通过：1，打回：0，gitee通过：2',
                                    `returnTimes` int(4) DEFAULT 0 COMMENT '退回次数',
                                    `returnDate` datetime DEFAULT NULL COMMENT '打回时间',
                                    `returnPerson` varchar(30) DEFAULT NULL COMMENT '打回人',
                                    `returnCase` varchar(255) DEFAULT NULL COMMENT '打回原因',
                                    `deleted` tinyint(4) DEFAULT 0 COMMENT '是否删除 0=正常 1= 删除',
                                    `editedBy` varchar(255) DEFAULT '' COMMENT '编辑人',
                                    `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
                                    `emisRegisterNumber` varchar(255) DEFAULT NULL COMMENT 'emis',
                                    `productenrollDesc` varchar(255) DEFAULT NULL COMMENT '登记摘要',
                                    `applyTime` datetime DEFAULT NULL COMMENT '申请时间',
                                    `dynacommEn` varchar(255) DEFAULT NULL COMMENT '产品英文名',
                                    `dynacommCn` varchar(255) DEFAULT NULL COMMENT '产品中文名',
                                    `version`  int NULL DEFAULT 0 COMMENT '审核版本',
                                    PRIMARY KEY (`id`),
                                    UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 迭代11
-- 2022-6-28  tangfei
-- ----------------------------
ALTER TABLE `zt_modifycncc` ADD `ifMediumChanges` tinyint(1) DEFAULT 1 COMMENT '介质是否变化 0：是，1：否';

-- ----------------------------
-- 迭代11
-- 2022-6-30  tongyanqi  推送标识
-- ----------------------------
ALTER TABLE `zt_testingrequest`
    ADD COLUMN `pushStatus`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未推送 1 = 推送成功 2 = 推送失败 ' AFTER `version`;
ALTER TABLE `zt_modifycncc`
    ADD COLUMN `pushStatus`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未推送 1 = 推送成功 2 = 推送失败 ' AFTER `version`;
ALTER TABLE `zt_productenroll`
    ADD COLUMN `pushStatus`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未推送 1 = 推送成功 2 = 推送失败 ' AFTER `version`;

-- ----------------------------
-- 迭代11
-- 2022-6-30  tongyanqi  附件地址加长
-- ----------------------------
ALTER TABLE `zt_file`
    MODIFY COLUMN `pathname`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '附件地址' AFTER `id`;

-- ----------------------------
-- 迭代11
-- 2022-6-30  chendongcheng  产品线新增emisId
-- ----------------------------
ALTER TABLE `zt_productline` ADD emisId int(11) NOT NULL;
UPDATE `zt_productline` z SET z.emisId = 0 WHERE z.code = 'PAY';
UPDATE `zt_productline` z SET z.emisId = 1 WHERE z.code = 'MTS';
UPDATE `zt_productline` z SET z.emisId = 2 WHERE z.code = 'MIS';
UPDATE `zt_productline` z SET z.emisId = 3 WHERE z.code = 'MON';
UPDATE `zt_productline` z SET z.emisId = 7 WHERE z.code = 'BDP';
UPDATE `zt_productline` z SET z.emisId = 10 WHERE z.code = 'COM';
UPDATE `zt_productline` z SET z.emisId = 12 WHERE z.code = 'TEC';
UPDATE `zt_productline` z SET z.emisId = 13 WHERE z.code = 'MSP';
UPDATE `zt_productline` z SET z.emisId = 14 WHERE z.code = 'PSP';
UPDATE `zt_productline` z SET z.emisId = 17 WHERE z.code = 'TA';
UPDATE `zt_productline` z SET z.emisId = 18 WHERE z.code = 'RE';
UPDATE `zt_productline` z SET z.emisId = 19 WHERE z.code = 'EI';
UPDATE `zt_productline` z SET z.emisId = 20 WHERE z.code = 'DA';
UPDATE `zt_productline` z SET z.emisId = 21 WHERE z.code = 'MT';
UPDATE `zt_productline` z SET z.emisId = 22 WHERE z.code = 'IS';
UPDATE `zt_productline` z SET z.emisId = 23 WHERE z.code = 'BT';
UPDATE `zt_productline` z SET z.emisId = 24 WHERE z.code = 'PT';

-- ----------------------------
-- 迭代11
-- 2022-6-30  shixuyang  测试申请单附件加长
-- ----------------------------
ALTER TABLE `zt_testingrequest` MODIFY COLUMN `remoteFilePath` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'sftp介质路径';
-- ----------------------------
-- 迭代11
-- 2022-7-1  tangfei 对外交付配置
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'revertReasonList', '', '', '1');

-- ----------------------------
-- 迭代11
-- 2022-7-1  tangfei 对外交付增加是否外部退回字段，用于审批时判断是否用退回页面选择的审批节点
-- ----------------------------
ALTER TABLE `zt_outwarddelivery` ADD isOutsideReject tinyint(1) DEFAULT 0 COMMENT '是否外部退回 0：否，1：是',
                                 ADD requiredReviewNode varchar(255) DEFAULT '' COMMENT '退回页面选择的需要审核的节点';
ALTER TABLE `zt_testingrequest`  ADD isOutsideReject tinyint(1) DEFAULT 0 COMMENT '是否外部退回 0：否，1：是',
                                 ADD requiredReviewNode varchar(255) DEFAULT '' COMMENT '退回页面选择的需要审核的节点';
ALTER TABLE `zt_productenroll`   ADD isOutsideReject tinyint(1) DEFAULT 0 COMMENT '是否外部退回 0：否，1：是',
                                 ADD requiredReviewNode varchar(255) DEFAULT '' COMMENT '退回页面选择的需要审核的节点';
-- 迭代10项目评审
/*2022-07-01 liugaoyang 后台用户增加专家类型字段,评审表增加关联人员*/
ALTER TABLE `zt_user` ADD COLUMN `expertType` varchar(30) NULL DEFAULT NULL AFTER `type`;
ALTER TABLE `zt_review`
    ADD `relatedUsers` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `outside`;

/*2022-07-01 wangjiurong 审核节点增加标识*/
ALTER TABLE zt_reviewnode ADD nodeCode varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '节点标识：用户标识节点在流转中的名称标识' AFTER version;

/*2022-07-01 wangjiurong 审核表增加字段*/
ALTER TABLE zt_review
    ADD `isConfirmGrade` TINYINT(1) DEFAULT 0 NOT NULL COMMENT '是否确认评审方式 0-未确认 1-确认' AFTER content,
    ADD `meetingPlanTime` timestamp NOT NULL COMMENT '预计会议时间' AFTER deadline,
    ADD `meetingCode` varchar(30) NOT NULL DEFAULT '' COMMENT '会议号' AFTER meetingPlanTime,
    ADD `meetingRealTime` timestamp NOT NULL COMMENT '会议时间日期' AFTER meetingCode,
    ADD `isSkipMeetingResult` TINYINT(1) DEFAULT 2 NOT NULL COMMENT '是否跳过在线评审结论 1是 2否' AFTER isFirstReview,
    ADD closeMailAccount varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '关闭是收件人账户' AFTER closeTime;

/*2022-07-01 wangjiurong 增加索引*/
CREATE INDEX meetingCode USING BTREE ON zt_review (meetingCode);

/*2022-07-01 wangjiurong 增加会议评审信息*/
CREATE TABLE `zt_review_meeting` (
         `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
         `meetingCode` varchar(30) NOT NULL DEFAULT '' COMMENT '会议号',
         `createUser` varchar(30) NOT NULL COMMENT '创建人',
         `createTime` timestamp NOT NULL COMMENT '创建时间',
         PRIMARY KEY (`id`),
         KEY `meetingCode` (`meetingCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目评审会议信息表';

/*2022-07-01 wangjiurong 增加会议评审详情*/
CREATE TABLE `zt_review_meeting_detail` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
        `review_meeting_id` int(11) NOT NULL COMMENT '会议id',
        `review_id` int(11) NOT NULL COMMENT '项目评审id',
        `meetingCode` varchar(30) NOT NULL DEFAULT '' COMMENT '会议号',
        `meetingRealTime` timestamp NOT NULL COMMENT '会议时间日期',
        `consumed` float NOT NULL DEFAULT 0 COMMENT '会议时间小时',
        `realExport` varchar(255) NOT NULL DEFAULT '' COMMENT '实际评审专家',
        `meetingContent` text CHARACTER SET utf8mb4 NOT NULL  COMMENT '评审内容',
        `meetingSummary` text CHARACTER SET utf8mb4 NOT NULL  COMMENT '评审纪要',
        `createUser` varchar(30) NOT NULL COMMENT '创建人',
        `createTime` timestamp NOT NULL COMMENT '创建时间',
        PRIMARY KEY (`id`),
        KEY `review_meeting_id` (`review_meeting_id`) USING BTREE,
        KEY `review_id` (`review_id`) USING BTREE,
        KEY `meetingCode` (`meetingCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目评审会议详情表';

/*2022-07-01 wangjiurong 修改工时信息添加版本*/
ALTER TABLE zt_consumed ADD version MEDIUMINT(9) DEFAULT 0 NOT NULL COMMENT '对象版本' AFTER parentId;
/*2022-07-01 wangjiurong 修改评审历史信息*/
update zt_reviewnode set nodeCode = 'preReview' where 1 and objectType = 'review' and stage = 1 and nodeCode = '';
update zt_reviewnode set nodeCode = 'firstAssignDept' where 1 and objectType = 'review' and stage = 2 and nodeCode = '';
update zt_reviewnode set nodeCode = 'firstAssignReviewer' where 1 and objectType = 'review' and stage = 3 and nodeCode = '';
update zt_reviewnode set nodeCode = 'firstReview' where 1 and objectType = 'review' and stage = 4 and nodeCode = '';
update zt_reviewnode set nodeCode = 'firstMainReview' where 1 and objectType = 'review' and stage = 5 and nodeCode = '';
update zt_reviewnode set nodeCode = 'formalAssignReviewer' where 1 and objectType = 'review' and stage = 6 and nodeCode = '';
update zt_reviewnode set nodeCode = 'formalReview' where 1 and objectType = 'review' and stage = 7 and nodeCode = '';
update zt_reviewnode set nodeCode = 'formalOwnerReview' where 1 and objectType = 'review' and stage = 8 and nodeCode = '';
update zt_reviewnode set nodeCode = 'verify' where 1 and objectType = 'review' and stage = 9 and nodeCode = '';
update zt_reviewnode set nodeCode = 'outReview' where 1 and objectType = 'review' and stage = 10 and nodeCode = '';

/*2022-07-01 wangjiurong 修改评审历史信息是否确定评审*/
update zt_review set isConfirmGrade = 1 where id in (
    select objectID  from zt_reviewnode
    where 1
      and objectType = 'review'
      and nodeCode in ('formalAssignReviewer', 'formalAssignReviewerAppoint')
      and status = 'pass'
    group by objectID
);
/* 2022-07-01 wangshusen 增加打基线字段*/
ALTER TABLE `zt_review`
    ADD `baseLineType` varchar(100) DEFAULT NULL COMMENT '基线类型' AFTER `relatedUsers`,
    ADD `baseLinePath` varchar(200) DEFAULT NULL COMMENT '基线路径' AFTER `baseLineType`,
    ADD `baseLineTime` date DEFAULT NULL COMMENT '基线时间' AFTER `baseLinePath`,
    ADD `baseLineCondition` varchar(40) DEFAULT NULL COMMENT '基线情况' AFTER `baseLineTime`;

-- 项目变更
/* 2022-07-01  lizhongzheng 变更单号历史数据处理,退回变更单已编辑字段*/
UPDATE zt_change cg, zt_projectplan pl SET cg.code = CONCAT(pl.mark, '-', cg.code) WHERE cg.project = pl.project AND pl.mark != ' ';
ALTER TABLE `zt_change` ADD column `failedEdit` varchar(2) NULL DEFAULT NULL COMMENT '退回编辑标识' AFTER supply;
ALTER TABLE `zt_change` ADD INDEX `failedEdit` (`failedEdit`) USING BTREE;
ALTER TABLE `zt_change` ADD column `reviewer` varchar(255) NULL DEFAULT NULL COMMENT '节点评审人' AFTER failedEdit;
ALTER TABLE `zt_change` ADD INDEX `reviewer` (`reviewer`) USING BTREE;

-- 2022-07-01 wanyyongliang 项目变更表
ALTER TABLE `zt_change`
 ADD `baseLineType` varchar(100) DEFAULT NULL COMMENT '基线类型' AFTER `supply`,
 ADD `baseLinePath` varchar(200) DEFAULT NULL COMMENT '基线路径' AFTER `baseLineType`,
 ADD `baseLineTime` date DEFAULT NULL COMMENT '基线时间' AFTER `baseLinePath`,
 ADD `baseLineCondition` varchar(40) DEFAULT NULL COMMENT '基线情况' AFTER `baseLineTime`;
/* 2022-07-01 lizhongzheng 增加字段保存跳过节点*/
ALTER TABLE `zt_change` ADD COLUMN `skipReviewNode` VARCHAR(255) COMMENT '审核跳过节点' AFTER reviewer;

/* 2022-07-01 更新字段类型*/
ALTER TABLE  `zt_cmitem` MODIFY COLUMN changedID VARCHAR(200) NOT NULL;

-- ----------------------------
-- 迭代10
-- 2022-7-1  liugaoyang  后台用户历史数据处理
-- ----------------------------
UPDATE  `zt_user` SET expertType = "outside" WHERE realname LIKE "c\_%" OR realname LIKE "zz\_%";

-- ----------------------------
-- 迭代11
-- 2022-7-4  chendongcheng cbp项目数据更新
ALTER TABLE `zt_cbpproject`
    ADD `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否被删除';

INSERT INTO `zt_cbpproject` (code,name) VALUES
                                            ('CBP202216','2022年支付系统开放接入平台功能优化CBP项目'),
                                            ('CBP202217','2022年技术基础类功能优化改造CBP项目'),
                                            ('CBP202218','2022年金融统计监测管理信息系统及理财和资金信托统计监测系统升级改造CBP项目'),
                                            ('CBP202219','2022年金融教育网站和房地产金融监测分析系统和金融消费权益保护信息管理升级改造CBP项目');

UPDATE `zt_cbpproject` z SET z.deleted = '1' WHERE z.code = 'CBP202201';
UPDATE `zt_cbpproject` z SET z.deleted = '1' WHERE z.code = 'CBP202209';
UPDATE `zt_cbpproject` z SET z.deleted = '1' WHERE z.code = 'CBP202214';
UPDATE `zt_cbpproject` z SET z.name = '2022年计费2.0及跨行账户信息认证服务平台(CBAC)完善研发CBP项目' WHERE z.code = 'CBP202204';
UPDATE `zt_cbpproject` z SET z.name = '2022年支付业务(含大数据）运维及监控类应用功能完善CBP项目' WHERE z.code = 'CBP202206';
UPDATE `zt_cbpproject` z SET z.name = '2022年自研分布式框架研发CBP项目' WHERE z.code = 'CBP202211';
UPDATE `zt_cbpproject` z SET z.name = '2022年交易类功能优化改造CBP项目' WHERE z.code = 'CBP202213';

-- ----------------------------
-- 迭代11
-- 2022-7-4  tangfei
-- ----------------------------
ALTER TABLE `zt_modifycncc`     ADD `returnTimes` int(4) DEFAULT 0 COMMENT '外部打回次数';


-- ----------------------------
-- 迭代11
-- 2022-7-4 tongyanqi 日志表加字段索引
-- ----------------------------
ALTER TABLE `zt_requestlog`
    ADD COLUMN `objectId`  bigint NULL COMMENT '业务表id' AFTER `objectType`,
    ADD INDEX `objectType_id` (`objectType`, `objectId`) USING BTREE ;

-- ----------------------------
-- 迭代11
-- 2022-7-4 tongyanqi 修改对外交付表
-- ----------------------------
ALTER TABLE `zt_outwarddelivery`
    MODIFY COLUMN `rejectTimes`  int(4) NULL DEFAULT 0 COMMENT '外部打回次数 默认0' AFTER `CBPprojectId`,
    MODIFY COLUMN `ROR`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '修订记录：json格式，内含RORDate修订时间，RORContent修订内容' AFTER `release`;

-- ----------------------------
-- 迭代11
-- 2022-7-4 tongyanqi 定时测试时间记录
-- ----------------------------
INSERT INTO `zt_kv` VALUES ('testTime', '0', '0');
-- ----------------------------
-- 迭代11
-- 2022-7-4 tongyanqi 修改对生产变更表
-- ----------------------------
ALTER TABLE `zt_modifycncc`
    ADD COLUMN `deleted`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 正常 1= 删除' AFTER `ifMediumChanges`,
    ADD COLUMN `cardStatus`  varchar(10) DEFAULT NULL COMMENT '外部审批状态 默认空 emis通过：1，打回：0，gitee通过：2' AFTER `deleted`;

-- ----------------------------
-- 迭代11
-- 2022-7-6 tangfei
-- ----------------------------
ALTER TABLE `zt_testingrequest`  ADD `release` varchar(255) NOT NULL COMMENT '介质';
ALTER TABLE `zt_productenroll`   ADD `release` varchar(255) NOT NULL COMMENT '介质';
-- ----------------------------
-- 迭代11
-- 2022-7-6 tongyanqi 定时推送默认开启
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'outwardDeliveryCron', 'enable');

-- ----------------------------
-- 迭代11
-- 2022-7-6  tangfei 对外交付配置
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'closedReasonList', '', '', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'closedReasonList', '1', '中途终止', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'outwarddelivery', 'closedReasonList', '2', '正常结束', '1');


-- ----------------------------
-- 迭代11
-- 2022-7-7  tangfei 增加退回字段
-- ----------------------------
ALTER TABLE `zt_modifycncc`
    ADD COLUMN `closed`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未关闭 1= 关闭';
ALTER TABLE `zt_testingrequest`
    ADD COLUMN `closed`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未关闭 1= 关闭';
ALTER TABLE `zt_productenroll`
    ADD COLUMN `closed`  tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未关闭 1= 关闭';

ALTER TABLE `zt_modifycncc`
    ADD COLUMN `closedDate` datetime DEFAULT NULL COMMENT '关闭时间',
    ADD COLUMN `closedBy` varchar(30) DEFAULT '' COMMENT '由谁关闭',
    ADD COLUMN `closedReason` varchar(255) DEFAULT NULL COMMENT '关闭原因';

ALTER TABLE `zt_testingrequest`
    ADD COLUMN `closedDate` datetime DEFAULT NULL COMMENT '关闭时间',
    ADD COLUMN `closedBy` varchar(30) DEFAULT '' COMMENT '由谁关闭',
    ADD COLUMN `closedReason` varchar(255) DEFAULT NULL COMMENT '关闭原因';

ALTER TABLE `zt_productenroll`
    ADD COLUMN `closedDate` datetime DEFAULT NULL COMMENT '关闭时间',
    ADD COLUMN `closedBy` varchar(30) DEFAULT '' COMMENT '由谁关闭',
    ADD COLUMN `closedReason` varchar(255) DEFAULT NULL COMMENT '关闭原因';

-- ----------------------------
-- 迭代11
-- 2022-7-7  tongyanqi 推送失败的次数
-- ----------------------------
ALTER TABLE `zt_testingrequest`
    ADD COLUMN `pushFailTimes`  tinyint(2) NULL DEFAULT 0 COMMENT '推送失败的次数' AFTER `pushStatus`;
ALTER TABLE `zt_productenroll`
    ADD COLUMN `pushFailTimes`  tinyint(2) NULL DEFAULT 0 COMMENT '推送失败的次数' AFTER `pushStatus`;
ALTER TABLE `zt_modifycncc`
    ADD COLUMN `pushFailTimes`  tinyint(2) NULL DEFAULT 0 COMMENT '推送失败的次数' AFTER `pushStatus`;
-- ----------------------------
-- 迭代11
-- 2022-7-8  shixuyang 同步清总的时间
-- ----------------------------
ALTER TABLE `zt_testingrequest`
    ADD COLUMN `pushDate`   datetime DEFAULT NULL COMMENT '同步清总时间' AFTER `status`;
ALTER TABLE `zt_productenroll`
    ADD COLUMN `pushDate`   datetime DEFAULT NULL COMMENT '同步清总时间' AFTER `status`;
ALTER TABLE `zt_modifycncc`
    ADD COLUMN `pushDate`   datetime DEFAULT NULL COMMENT '同步清总时间' AFTER `status`;

-- ----------------------------
-- 迭代11
-- 2022-7-13  tongyanqi 问题接口下载附件
-- ----------------------------
ALTER TABLE `zt_problem`
    ADD COLUMN `relationFiles`  text NULL COMMENT '附件json' AFTER `EditorImpactscope`;
ALTER TABLE `zt_file`
    ADD COLUMN `apiFile`  tinyint(1) NULL DEFAULT 0 COMMENT '是否接口传来的文件 0 = 不是 1= 是' AFTER `deleted`;

-- ----------------------------
-- 迭代12
-- 2022-7-11  王九荣  预计参会专家
-- ----------------------------

ALTER TABLE zt_review
    ADD verifyDeadline DATE DEFAULT '0000-00-00' NOT NULL COMMENT '验证截至时间' AFTER firstReviewDeadline,
    ADD editDeadline DATE DEFAULT '0000-00-00' NOT NULL COMMENT '编辑截止时间' AFTER verifyDeadline,
    ADD meetingPlanExport MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '预计参会专家' AFTER meetingPlanTime,
    ADD projectType TINYINT(1) DEFAULT 0 NOT NULL COMMENT '关联项目类型',
    ADD isImportant TINYINT(1) DEFAULT 2 NOT NULL COMMENT '是否重点项目 1是 2否';

/* 2022-7-11 修改评审历史数据是否重点项目*/
update zt_review set isImportant = 1
where id in (
    select zr.id from zt_review zr
    left join zt_projectplan zp on zr.project = zp.project
    where 1
    and zp.isImportant = 1
);
/* 2022-7-11 修改评审历史数据项目类型*/
update zt_review t1,
    (
    select zp.project , zp.`type` from zt_projectplan zp where 1
    ) as temp1
set  t1.projectType = temp1.type
where t1.project = temp1.project;

/* 2022-7-11 预计会议时间*/
CREATE INDEX meetingPlanTime USING BTREE ON zt_review (meetingPlanTime);
/* 2022-7-11 会议评审表修改字段*/
ALTER TABLE zt_review_meeting
    ADD status CHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '会议评审状态： waitFormalReview:在线评审中， waitMeetingReview：会议评审中，waitMeetingOwnerReview 待确定会议评审结论
pass评审结束',
    ADD `type` CHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '会议类型',
    ADD meetingPlanTime TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL COMMENT '预计会议时间',
    ADD meetingRealTime TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '实际会议评审时间',
    ADD  realExport varchar(255) NOT NULL DEFAULT '' COMMENT '实际评审专家',
    ADD meetingPlanExport MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL COMMENT '预计参会专家',
    ADD meetingSummaryCode varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '会议纪要编号',
    ADD meetingSummarySortId int(10) DEFAULT 0 NOT NULL COMMENT '排序，会议纪要号的排序',
    ADD dealUser varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '待处理人',
    ADD owner varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '评审主席',
    ADD reviewer varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '评审专员',
    ADD `deleted` TINYINT DEFAULT 0 NOT NULL COMMENT '是否删除 0有效  1删除',
    ADD `allOwner` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL COMMENT '评审所有人员',
    ADD sortId int(10) DEFAULT 0 NOT NULL COMMENT '排序，会议号中不包含会议类型字段';

/*索引*/
CREATE INDEX sortId USING BTREE ON zt_review_meeting (sortId);
CREATE INDEX meetingSummarySortId USING BTREE ON zt_review_meeting (meetingSummarySortId);
CREATE INDEX createTime USING BTREE ON zt_review_meeting (createTime);

ALTER TABLE zt_review_meeting
    CHANGE createUser createUser varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '创建人' AFTER `deleted`,
    CHANGE createTime createTime timestamp NOT NULL COMMENT '创建时间' AFTER createUser;

/* 2022-7-11 会议评审详情表修改字段*/
ALTER TABLE zt_review_meeting_detail
    ADD status CHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '会议评审状态',
    ADD `deleted` TINYINT DEFAULT 0 NOT NULL COMMENT '是否删除 0有效  1删除';

/*索引*/
CREATE INDEX createTime USING BTREE ON zt_review_meeting_detail (createTime);

ALTER TABLE zt_review_meeting_detail
    CHANGE createUser createUser varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '创建人' AFTER `deleted`,
    CHANGE createTime createTime timestamp NOT NULL COMMENT '创建时间' AFTER createUser;

/*2022-07-12 wss 迭代12 增加评审问题待处理人字段*/
ALTER TABLE `zt_reviewissue` ADD column `dealUser` varchar(255) NULL DEFAULT NULL COMMENT '待处理人' AFTER dealDate;

/* 2022-7-11 会议评审详情表修改字段*/
ALTER TABLE zt_reviewer ADD parentId INT(10) DEFAULT 0 NOT NULL COMMENT '父节点id' AFTER id;

/*2022-07-20 wss 迭代12 增加评审问题待会议编号字段*/
ALTER TABLE `zt_reviewissue` ADD column `meetingCode` varchar(255) NULL DEFAULT NULL COMMENT '会议编号' AFTER dealUser;

-- 迭代11
-- 2022-7-21  shixuyang 修改产品编号字段长度
-- ----------------------------
alter table `zt_outwarddelivery`  modify column `productInfoCode` varchar(300) ;


-- ----------------------------
-- 迭代11
-- 2022-7-13  tangfei 评审时间
-- ----------------------------
ALTER TABLE `zt_reviewnode`
    MODIFY COLUMN `createdDate` datetime DEFAULT NULL COMMENT '评审时间';

-- ----------------------------
-- 迭代11
-- 2022-7-13  tangfei 数据获取增加同步失败原因
-- ----------------------------
ALTER TABLE `zt_infoqz`
    ADD COLUMN `synFailedReason` varchar(255) DEFAULT NULL COMMENT '同步清总失败原因' AFTER `status`;

-- ----------------------------
-- 迭代11
-- 2022-7-18  shixuyang 对外交付邮件
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setOutwardDeliveryMail', '{"mailTitle":"\u3010\u5f85\u529e\u3011\u60a8\u6709\u4e00\u4e2a\u3010%s\u3011\u5f85\u5904\u7406\uff0c\u8bf7\u53ca\u65f6\u767b\u5f55\u7814\u53d1\u8fc7\u7a0b\u7ba1\u7406\u5e73\u53f0\u8fdb\u884c\u5904\u7406","variables":["\u5bf9\u5916\u4ea4\u4ed8\u5355"],"mailContent":"<span style=\"font-weight:700;\">\u8bf7\u8fdb\u5165\u3010\u5730\u76d8\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5f85\u5904\u7406\u3011<\/span><span><span style=\"font-weight:700;\">-<\/span><\/span><span style=\"font-weight:700;\">\u3010\u5ba1\u6279\u3011\u6216\u3010\u4e8c\u7ebf\u7ba1\u7406\u3011\u5904\u7406<\/span><span style=\"color:#E53333;\"><span style=\"font-weight:700;\">\u5bf9\u5916\u4ea4\u4ed8<\/span><\/span><span style=\"font-weight:700;\">\uff0c\u5177\u4f53\u4fe1\u606f\u5982\u4e0b\uff1a<\/span>"}');
-- ----------------------------
-- 迭代11
-- 2022-7-18  chendongcheng 新增字段
-- ----------------------------
ALTER TABLE `zt_outwarddelivery` ADD COLUMN `currentReview` tinyint(1) DEFAULT 0 COMMENT '当前审批 0：''，1：对外交付，2：测试申请，3：产品登记，4：生产变更',
                                 ADD COLUMN `dealUser` char(30) NOT NULL COMMENT '下一节点处理人';
-- ----------------------------
-- 迭代11
-- 2022-7-28  guchaonan 新增字段
-- ----------------------------
ALTER TABLE `zt_modifycncc` ADD COLUMN `belongedApp` varchar(100) NULL COMMENT '所属系统，父表单同步';

ALTER TABLE `zt_bug` ADD COLUMN `childType` varchar(50) NULL AFTER `type`;
ALTER TABLE `zt_testtask` ADD COLUMN `oddNumber` varchar(30) NULL AFTER `id`;
ALTER TABLE `zt_testtask` ADD COLUMN `createdBy` varchar(50) NULL AFTER `deleted`, ADD COLUMN `createdDate` datetime NULL AFTER `createdBy`;
ALTER TABLE `zt_testtask` ADD COLUMN `problem` int NULL DEFAULT 0 AFTER `createdDate`, ADD COLUMN `requirement` int NULL DEFAULT 0 AFTER `problem`;

-- ----------------------------
-- 2022-7-27  tangfei 总行类接口人后台可配置：张蕴;变更单增加giteeId字段;
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','headOfficeApiDealUserList','userAccount','zhangyun','1');
ALTER TABLE `zt_modifycncc` ADD COLUMN`giteeId` varchar(50) DEFAULT NULL COMMENT '金信id 由gitee返回';

-- ----------------------------
-- 2022-7-28  shixuyang 迭代十一 添加索引;
-- ----------------------------
CREATE INDEX testingrequestCode USING BTREE ON zt_testingrequest (code);
-- 2022-7-27 tongyanqi 添加索引
ALTER TABLE `zt_outwarddelivery`
    ADD INDEX `code` (`code`) USING BTREE ,
    ADD INDEX `deleted` (`deleted`) USING BTREE ,
    ADD INDEX `status` (`status`) USING BTREE ,
    ADD INDEX `closed` (`closed`) USING BTREE ;
-- ----------------------------
-- 2022-7-28  chendongcheng 迭代十一 添加索引;
-- ----------------------------
CREATE INDEX productenrollCode USING BTREE ON zt_productenroll (code);

-- 2022-7-28 tangfei 添加索引
ALTER TABLE `zt_modifycncc`
    ADD INDEX `code` (`code`) USING BTREE ,
    ADD INDEX `deleted` (`deleted`) USING BTREE ,
    ADD INDEX `status` (`status`) USING BTREE ,
    ADD INDEX `level` (`level`) USING BTREE ,
    ADD INDEX `closed` (`closed`) USING BTREE ;
-- ----------------------------
-- 迭代12
/* 2022-08-01  leiyong 测试管理修改 */
delete from zt_lang where lang = 'zh-cn' and module = 'bug' and section = 'typeList';
delete from zt_lang where lang = 'all' and module = 'bug' and section = 'childTypeList';
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('all', 'bug', 'childTypeList', 'all', '{\"security\":{\"a1\":\"\\u6e90\\u7801\\u5b89\\u5168\\u95ee\\u9898\",\"a2\":\"\\u7ec4\\u4ef6\\u5b89\\u5168\\u95ee\\u9898\",\"a3\":\"\\u4e3b\\u673a\\u5b89\\u5168\\u95ee\\u9898\",\"a4\":\"\\u6e17\\u900f\\u5b89\\u5168\\u95ee\\u9898\",\"a5\":\"\\u7b49\\u4fdd\\u5b89\\u5168\\u95ee\\u9898\",\"a6\":\"\\u5176\\u4ed6\"},\"funcdetect\":{\"b1\":\"\\u5b9e\\u73b0\\u9519\\u8bef\",\"b2\":\"\\u5b9e\\u73b0\\u9057\\u6f0f\",\"b3\":\"\\u4ea7\\u54c1\\u8bbe\\u8ba1\",\"b4\":\"\\u4ea7\\u54c1\\u4f53\\u9a8c\",\"b5\":\"\\u7cfb\\u7edf\\u5f02\\u5e38\",\"b6\":\"\\u5176\\u4ed6\"}}', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'bug', 'typeList', 'funcdetect', '功能缺陷', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'bug', 'typeList', 'performance', '性能缺陷', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'bug', 'typeList', 'security', '安全缺陷', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'bug', 'typeList', 'requiredect', '文档缺陷', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'bug', 'typeList', '', '', '1');

ALTER TABLE `zt_bug` ADD COLUMN `childType` varchar(50) NULL AFTER `type`;
ALTER TABLE `zt_testtask` ADD COLUMN `oddNumber` varchar(30) NULL AFTER `id`;
ALTER TABLE `zt_testtask` ADD COLUMN `createdBy` varchar(50) NULL AFTER `deleted`, ADD COLUMN `createdDate` datetime NULL AFTER `createdBy`;
ALTER TABLE `zt_testtask` ADD COLUMN `problem` int NULL DEFAULT 0 AFTER `createdDate`, ADD COLUMN `requirement` int NULL DEFAULT 0 AFTER `problem`;
ALTER TABLE `zt_testtask` MODIFY COLUMN `problem` varchar(200) NULL DEFAULT '' AFTER `createdDate`, MODIFY COLUMN `requirement` varchar(200) NULL DEFAULT '' AFTER `problem`;
-- 2022-8-1  liugaoyang 迭代十二
-- ----------------------------
ALTER TABLE `zt_review_meeting`
    ADD COLUMN `editBy` varchar(30) DEFAULT NULL COMMENT '由谁编辑' AFTER `allOwner`,
    ADD COLUMN `editTime` timestamp DEFAULT NULL COMMENT '编辑时间' AFTER `editBy`;

/*2022-08-2 wss 迭代12 增加索引*/
ALTER TABLE `zt_reviewissue`
    ADD INDEX `review` (`review`) USING BTREE ,
    ADD INDEX `dealUser` (`dealUser`) USING BTREE ,
    ADD INDEX `createdBy` (`createdBy`) USING BTREE ,
    ADD INDEX `resolutionBy` (`resolutionBy`) USING BTREE ,
    ADD INDEX `validation` (`validation`) USING BTREE ;
/*2022-08-03 评审表新增字段*/
ALTER TABLE zt_review  ADD `allOwner` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL COMMENT '评审所有人员' AFTER `isImportant`;
/*迭代十二 2022-08-08 年度计划 项目立项所属部门支持多选*/
ALTER TABLE zt_projectcreation MODIFY COLUMN dept VARCHAR(50) NOT NULL;

/*迭代十二liugaoyang 2022-08-10 评审和会议评审表添加索引*/
ALTER TABLE `zt_review`
    ADD INDEX `dealUser` (`dealUser`) USING BTREE ,
    ADD INDEX `status` (`status`) USING BTREE ;
ALTER TABLE `zt_review_meeting`
    ADD INDEX `dealUser` (`dealUser`) USING BTREE ;


/*2022-8-18 shixuyang 平台需求收集846*/
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed');

ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed');

-- ----------------------------
-- 2022-8-1  shixuyang 迭代十三 新增反馈单的状态;
-- ----------------------------
ALTER TABLE `zt_requirement`
    ADD COLUMN `feedbackStatus` varchar(64) DEFAULT NULL COMMENT '反馈单状态' AFTER `status`;
ALTER TABLE `zt_requirement`
    ADD COLUMN `comment` mediumtext DEFAULT NULL COMMENT '备注' AFTER `reviewComments`;
ALTER TABLE `zt_requirement`
    ADD COLUMN `ignoreStatus` tinyint(1) DEFAULT 0 COMMENT '是否忽略，0-不忽略，1-忽略' AFTER `status`;
ALTER TABLE `zt_requirement`
    ADD COLUMN `sourceMode` varchar(30) DEFAULT NULL COMMENT '需求来源方式';
ALTER TABLE `zt_requirement`
    ADD COLUMN `sourceName` varchar(255) DEFAULT NULL COMMENT '需求来源名称';
ALTER TABLE `zt_requirement`
    ADD COLUMN `union` varchar(255) DEFAULT NULL COMMENT '业务需求单位';
ALTER TABLE `zt_requirement`
    ADD COLUMN `deadlineByOpinion` date DEFAULT NULL COMMENT '需求意向期望完成日期';
ALTER TABLE `zt_requirement`
    ADD COLUMN `dateByOpinion` date DEFAULT NULL COMMENT '需求意向接收时间';
ALTER TABLE `zt_requirement`
    ADD COLUMN `nameByOpinion` varchar(255) DEFAULT NULL COMMENT '需求意向主题';
ALTER TABLE `zt_requirement`
    ADD COLUMN `productManager` varchar(255) DEFAULT NULL COMMENT '产品经理';
ALTER TABLE `zt_requirement`
    ADD COLUMN `projectManager` varchar(255) DEFAULT NULL COMMENT '项目经理';
ALTER TABLE `zt_requirement`
    ADD COLUMN `editedBy` varchar(255) DEFAULT NULL COMMENT '由谁编辑';
ALTER TABLE `zt_requirement`
    MODIFY COLUMN `dealUser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '下一节点处理人';



-- ----------------------------
-- 2022-8-2  tongyanqi 迭代十三 需求关联项目;
-- ----------------------------

ALTER TABLE `zt_projectplan`
    ADD COLUMN `opinion`  varchar(255) NULL DEFAULT '' COMMENT '需求意向id 逗号分隔' AFTER `productsRelated`,
    ADD COLUMN `requirement`  varchar(255) NULL DEFAULT '' COMMENT '需求任务 逗号分隔' AFTER `opinion`,
    ADD COLUMN `demand`  varchar(255) NULL DEFAULT '' COMMENT '需求条目 逗号分隔' AFTER `requirement`;
-- ----------------------------
-- 2022-8-2  tongyanqi 迭代十三 区分新老需求逻辑;
-- ----------------------------
UPDATE `zt_projectplan` set demand = "-1" where demand = "";

ALTER TABLE `zt_opinion`
    ADD COLUMN `project`  varchar(255) NULL DEFAULT '' COMMENT '项目id 逗号分隔' AFTER `lastStatus`;
ALTER TABLE `zt_demand`
    ADD COLUMN `project`  varchar(255) NULL DEFAULT '' COMMENT '项目id 逗号分隔' AFTER `solvedTime`;
ALTER TABLE `zt_requirement`
    MODIFY COLUMN `project`  varchar(255) NOT NULL DEFAULT '' COMMENT '项目id 逗号分隔' AFTER `feedbackCode`;

-- ----------------------------
-- 2022-8-2  陈东程 迭代十三 需求意向新增字段;
-- ----------------------------
ALTER table `zt_opinion` ADD column `dealUser` varchar(255) default null COMMENT '待处理人';
ALTER table `zt_opinion` ADD column `receiveDate` date default null COMMENT '接受日期' after `deadline`;
ALTER table `zt_opinion` ADD column `remark` text default null COMMENT '备注' after `desc`;
ALTER table `zt_opinion` ADD column `lastStatus` varchar(30) default 'created' NOT null COMMENT '关闭前状态' after `status`;
ALTER table `zt_opinion` ADD column `editedBy` varchar(30) default '' NOT null COMMENT '由谁编辑' after `createdDate`;
ALTER table `zt_opinion` ADD column `editedDate` datetime default null COMMENT '编辑时间' after `editedBy`;
ALTER table `zt_opinion` ADD column `closedBy` varchar(30) default '' NOT null COMMENT '由谁关闭' after `editedDate`;
ALTER table `zt_opinion` ADD column `closedDate` datetime default null COMMENT '关闭时间' after `closedBy`;
ALTER table `zt_opinion` ADD column `activedBy` varchar(30) default '' NOT null COMMENT '由谁激活' after `closedDate`;
ALTER table `zt_opinion` ADD column `activedDate` datetime default null COMMENT '激活时间' after `activedBy`;
ALTER table `zt_opinion` ADD column `suspendBy` varchar(30) default '' NOT null COMMENT '由谁忽略' after `activedDate`;
ALTER table `zt_opinion` ADD column `suspendDate` datetime default null COMMENT '忽略时间' after `suspendBy`;
ALTER table `zt_opinion` ADD column `recoveredBy` varchar(30) default '' NOT null COMMENT '由谁恢复' after `suspendDate`;
ALTER table `zt_opinion` ADD column `recoveredDate` datetime default null COMMENT '恢复时间' after `recoveredBy`;
/* 李忠正 更新下节点处理人字段*/
ALTER TABLE `zt_requirement` MODIFY COLUMN `dealUser` varchar(255) NOT NULL COMMENT '下一个节点处理人';
ALTER TABLE `zt_requirement` ADD COLUMN `lastStatus` varchar(20) DEFAULT NULL COMMENT '关闭之前状态';
-- ----------------------------
-- 2022-8-8  陈东程 迭代十三 需求意向新增字段;
-- ----------------------------
ALTER table `zt_opinion` ADD column `level` tinyint(2) default null COMMENT '需求级别';

-- ----------------------------
-- 2022-8-1  shixuyang 迭代十三 新增反馈单的审批状态;
-- ----------------------------
ALTER TABLE `zt_requirement`
    ADD COLUMN `reviewStage` mediumint(8) DEFAULT NULL COMMENT '反馈单审批节点';

-- ----------------------------
-- 2022-8-  陈东程 迭代十三 需求意向新增字段;
-- ----------------------------
ALTER table `zt_opinion` ADD column `planDeadline` datetime default null COMMENT '计划完成时间' AFTER `deadline`;

-- ----------------------------
-- 2022-8-10  shixuyang 迭代十三 新增编辑时间;
-- ----------------------------
ALTER TABLE `zt_requirement`
    ADD COLUMN `editedDate` datetime DEFAULT NULL COMMENT '编辑时间';
ALTER TABLE `zt_requirement`
    ADD COLUMN `closedBy` varchar(255) DEFAULT NULL COMMENT '由谁关闭';
ALTER TABLE `zt_requirement`
    ADD COLUMN `closedDate` datetime DEFAULT NULL COMMENT '关闭时间';
ALTER TABLE `zt_requirement`
    ADD COLUMN `activatedBy` varchar(255) DEFAULT NULL COMMENT '由谁激活';
ALTER TABLE `zt_requirement`
    ADD COLUMN `activatedDate` datetime DEFAULT NULL COMMENT '激活时间';
ALTER TABLE `zt_requirement`
    ADD COLUMN `ignoredBy` varchar(255) DEFAULT NULL COMMENT '由谁忽略';
ALTER TABLE `zt_requirement`
    ADD COLUMN `ignoredDate` datetime DEFAULT NULL COMMENT '忽略时间';
ALTER TABLE `zt_requirement`
    ADD COLUMN `recoveryedBy` varchar(255) DEFAULT NULL COMMENT '由谁恢复';
ALTER TABLE `zt_requirement`
    ADD COLUMN `recoveryedDate` datetime DEFAULT NULL COMMENT '恢复时间';
ALTER TABLE `zt_requirement`
    ADD COLUMN `feedbackDealUser` varchar(512) DEFAULT NULL COMMENT '反馈单待处理人';
ALTER TABLE `zt_requirement` MODIFY COLUMN `feedbackDate` DATETIME NULL;
ALTER TABLE `zt_requirement` MODIFY COLUMN `createdDate` DATETIME NOT NULL;
ALTER TABLE `zt_requirement`
    ADD COLUMN `acceptTime` datetime DEFAULT NULL COMMENT '接受日期';
ALTER TABLE `zt_requirement`
    ADD COLUMN `onlineTimeByDemand` datetime DEFAULT NULL COMMENT '需求条目最晚的上线日期';



-- ----------------------------
-- 2022-8-10  tongyanqi 迭代十三 年度计划新增字段;
-- ----------------------------
ALTER TABLE `zt_project`
    ADD COLUMN `opinions`  varchar(255) NULL DEFAULT '' COMMENT '需求意向id 逗号分隔' AFTER `splitDate`,
ADD COLUMN `requirements`  varchar(255) NULL DEFAULT '' COMMENT '需求任务 逗号分隔' AFTER `opinions`,
ADD COLUMN `demands`  varchar(255) NULL DEFAULT '' COMMENT '需求条目 逗号分隔' AFTER `requirements`;

-- ----------------------------
-- 2022-8-11  wangshusen 迭代十三 需求条目增加字段实际上线时间;
-- ----------------------------
ALTER TABLE `zt_demand` ADD COLUMN `requirementID` int(8) DEFAULT 0 COMMENT '关联需求任务ID' AFTER `opinionID`;
ALTER TABLE `zt_demand` ADD COLUMN `comment` text NOT NULL COMMENT '备注' AFTER `project`;
ALTER TABLE `zt_demand` ADD column `onlineDate` date DEFAULT NULL COMMENT '实际上线时间' AFTER `solvedTime`;
ALTER TABLE `zt_demand` ADD COLUMN `activatedBy` varchar(255) DEFAULT NULL COMMENT '由谁激活' AFTER `editedDate`;
ALTER TABLE `zt_demand` ADD COLUMN `activatedDate` datetime DEFAULT NULL COMMENT '激活时间' AFTER `activatedBy`;
ALTER TABLE `zt_demand` ADD COLUMN `ignoreStatus` tinyint NOT NULL DEFAULT '0' COMMENT '忽略/恢复状态 0正常 1忽略' AFTER `activatedDate`;
ALTER TABLE `zt_demand` ADD COLUMN `ignoredBy` varchar(255) DEFAULT NULL COMMENT '由谁忽略' AFTER `activatedDate`;
ALTER TABLE `zt_demand` ADD COLUMN `ignoredDate` datetime DEFAULT NULL COMMENT '忽略时间' AFTER `ignoredBy`;
ALTER TABLE `zt_demand` ADD COLUMN `recoveryedBy` varchar(255) DEFAULT NULL COMMENT '由谁恢复' AFTER `ignoredDate`;
ALTER TABLE `zt_demand` ADD COLUMN `recoveryedDate` date DEFAULT NULL COMMENT '恢复时间' AFTER `recoveryedBy`;
ALTER TABLE `zt_demand` modify column `projectPlan` varchar(255);
update zt_demand set `status` = 'wait' where `status` = 'confirmed';

-- ----------------------------
-- 2022-8-10  tongyanqi 迭代十三 优化项目产品关联表;
-- ----------------------------
ALTER TABLE `zt_projectproduct`
    ADD COLUMN `createdTime`  datetime NULL DEFAULT NULL COMMENT '创建时间' AFTER `plan`,
    ADD COLUMN `id`  bigint NOT NULL AUTO_INCREMENT FIRST ,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`id`, `project`, `product`);


UPDATE `zt_projectplan` set demand = "-1";

/* 李忠正 增加“忽略”字段, '变更前已拆分状态'*/
ALTER TABLE `zt_opinion` ADD COLUMN `ignore` varchar(1000) DEFAULT NULL COMMENT '忽略记录', ADD INDEX index_name (`ignore`) USING BTREE;
ALTER TABLE `zt_opinion` ADD COLUMN `beforeChangedStatus` varchar(20) DEFAULT NULL COMMENT '变更前已拆分状态';

-- ----------------------------
-- 2022-8-10  tongyanqi 介质校验;
-- ----------------------------
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'mediaCheckList', 'release', '0', '1');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'api', 'mediaCheckList', 'link', '1', '1');


-- ----------------------------
-- 2022-8-19  shixuyang 增加状态;
-- ----------------------------
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','feedbacksuccess','feedbackfail','secondlineapproved');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','feedbacksuccess','feedbackfail','secondlineapproved');
-- ----------------------------
-- 2022-8-19  tongyanqi 反馈失败次数;
-- ----------------------------
ALTER TABLE `zt_problem`
    ADD COLUMN `syncFailTimes`  tinyint NULL DEFAULT 0 COMMENT '反馈失败次数' AFTER `relationFiles`;
-- 2022-8-15 guchaonan 对外交付-退回原因字段改成text类型
ALTER TABLE `zt_outwarddelivery` MODIFY COLUMN revertReason TEXT NULL COMMENT '内部退回原因，枚举值可配置';

-- ----------------------------
-- 迭代13
-- 2022-8-16  陈东程 金信增加回退需要审核节点。
-- ----------------------------
ALTER TABLE `zt_modify` ADD requiredReviewNode varchar(255) DEFAULT '' COMMENT '退回页面选择的需要审核的节点';
ALTER TABLE `zt_info` ADD requiredReviewNode varchar(255) DEFAULT '' COMMENT '退回页面选择的需要审核的节点';

-- ----------------------------
-- 2022-8-25  shixuyang 修改部门为字符串;
-- ----------------------------
ALTER TABLE `zt_requirement` MODIFY COLUMN `dept` VARCHAR(255) NOT NULL;

-- ----------------------------
-- 2022-8-29  chendongcheng 需求866新增字段
-- ----------------------------
ALTER table `zt_testingrequest` ADD column `testSummary` varchar(50) default '' COMMENT '测试摘要';
ALTER table `zt_testingrequest` ADD column `acceptanceTestType` tinyint(2) default null COMMENT '验收测试类型';
-- ----------------------------
-- 2022-8-29  tongyanqi 年度计划 添加项目索引;
-- ----------------------------
ALTER TABLE `zt_projectplan`
    ADD INDEX `project` (`project`) USING BTREE ;
-- ----------------------------
-- 2022-8-30  tongyanqi 问题&需求条目实际上线时间;
-- ----------------------------
ALTER TABLE `zt_problem`
    ADD COLUMN `actualOnlineDate`  date NULL COMMENT '实际上线时间' AFTER `syncFailTimes`;

ALTER TABLE `zt_demand`
    ADD COLUMN `actualOnlineDate`  date NULL COMMENT '实际上线时间' AFTER `comment`;


-- ----------------------------
-- 2022-9-1  chendongcheng 金信交付退回原因;
-- ----------------------------
ALTER TABLE `zt_modify` ADD `revertReason` TEXT DEFAULT NULL COMMENT '内部退回原因，枚举值可配置';
ALTER TABLE `zt_info` ADD `revertReason` TEXT DEFAULT NULL COMMENT '内部退回原因，枚举值可配置';
/*2022-8-18 shixuyang 平台需求收集846*/
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed');


-----------------------迭代15-------------------
/* 迭代15-增加金信问题单处理人*/
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','apiDealUserList','jxDealAccount','zhangyun','1');

/*2022-09-06 lzz 问题推送金信反馈单请求*/
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','jxProblemFeedbackUrl','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','jxProblemFeedbackAppId','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','jxProblemFeedbackAppSecret','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','jxProblemFeedbackUsername','');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','jxProblemFeedbackEnable','enable');
INSERT INTO zt_config (`owner`, `module`, `section`, `key`, `value`) VALUES ('system','common','global','jxProblemFileIP','');
/*2022-09-07 lzz 金信反馈期限 */
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','expireDaysList','jxExpireDays','30','1');
/*2022-09-19 lzz 增加金信问题反馈单状态 */
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved');
------------问题级别-------------
replace INTO zt_lang(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','severityList',1,'一级','1');
replace INTO zt_lang(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','severityList',2,'二级','1');
replace INTO zt_lang(`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','severityList',3,'三级','1');
replace INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','problem','severityList',4,'四级','1');

----------金信取消变更增加--------
ALTER TABLE `zt_modify` ADD COLUMN `lastStatus` varchar(50) DEFAULT null COMMENT '取消前状态';
ALTER TABLE `zt_modify` ADD COLUMN `cancelStatus` varchar(50) DEFAULT null COMMENT '取消状态';
ALTER TABLE `zt_modify` ADD COLUMN `canceledBy` varchar(50) DEFAULT null COMMENT '由谁取消';
ALTER TABLE `zt_modify` ADD COLUMN `canceledDate` datetime DEFAULT null COMMENT '取消时间';
ALTER TABLE `zt_modify` ADD COLUMN `cancelReason` varchar(300) DEFAULT null COMMENT '取消原因';
ALTER TABLE `zt_modify` ADD COLUMN `lastStage` varchar(20) DEFAULT null COMMENT '取消前stage';
ALTER TABLE `zt_modify` ADD COLUMN `lastDealUser` varchar(255) DEFAULT null COMMENT '取消前待处理人';
ALTER TABLE `zt_modify` ADD COLUMN `cancelReviewer` varchar(255) DEFAULT null COMMENT '取消审核人';
ALTER TABLE `zt_modify` ADD COLUMN `cancelComment` varchar(1000) DEFAULT null COMMENT '取消审核意见';
ALTER TABLE `zt_modify` ADD COLUMN `cancelReviewDate` datetime DEFAULT null COMMENT '取消审核时间';
ALTER TABLE `zt_problem` ADD COLUMN `extId`  varchar(50) NULL COMMENT '金信Id' ;
ALTER TABLE `zt_problem` ADD COLUMN `firstPush`  varchar(10) DEFAULT 1 COMMENT '首次推送金信' ;
ALTER TABLE `zt_problem` ADD COLUMN `isAfterSubmit`  varchar(10) DEFAULT 0 COMMENT '提交过最终方案' ;
ALTER TABLE `zt_problem` ADD COLUMN `firstPushDate`  datetime NULL COMMENT '首次推送时间' ;
ALTER TABLE `zt_problem` ADD COLUMN `firstPushDateFlag`  varchar(10) DEFAULT 1 COMMENT '是否記錄推送時間' ;
ALTER TABLE `zt_problem` ADD COLUMN `ifOverDate`  varchar(10) DEFAULT 0 COMMENT '是否超时' ;
ALTER TABLE `zt_problem` modify COLUMN `NodeIdOfIssue`  varchar(255) COMMENT '问题节点' ;

-- ----------------------------
-- 2022-9-6  shixuyang 金信交付新增字段;
-- ----------------------------
ALTER TABLE `zt_modify` ADD COLUMN `contactName` varchar(50) DEFAULT null COMMENT '联系人';
ALTER TABLE `zt_modify` ADD COLUMN `contactTel` varchar(50) DEFAULT null COMMENT '联系电话';
ALTER TABLE `zt_modify` ADD COLUMN `backupDataCenterChangeSyncDesc` text DEFAULT null COMMENT '主备数据中心变更同步情况说明';
ALTER TABLE `zt_modify` ADD COLUMN `businessFunctionAffect` text DEFAULT null COMMENT '给业务功能带来的影响变化';
ALTER TABLE `zt_modify` ADD COLUMN `feasibilityAnalysis` varchar(255) DEFAULT null COMMENT '变更可行性分析';
ALTER TABLE `zt_modify` ADD COLUMN `changeSource` varchar(50) DEFAULT null COMMENT '变更来源';
ALTER TABLE `zt_modify` ADD COLUMN `changeStage` varchar(50) DEFAULT null COMMENT '变更阶段';
ALTER TABLE `zt_modify` ADD COLUMN `changeContentAndMethod` text DEFAULT null COMMENT '变更的内容和方法';
ALTER TABLE `zt_modify` ADD COLUMN `emergencyManageAffect` text DEFAULT null COMMENT '对应急处置策略的影响（对故障处置策略自动化切换等的影响）';
ALTER TABLE `zt_modify` ADD COLUMN `implementModality` varchar(30) DEFAULT null COMMENT '实施方式';
ALTER TABLE `zt_modify` ADD COLUMN `isBusinessAffect` varchar(2) DEFAULT null COMMENT '实施期间是否有业务影响';
ALTER TABLE `zt_modify` ADD COLUMN `isBusinessCooperate` varchar(2) DEFAULT null COMMENT '是否需要业务配合';
ALTER TABLE `zt_modify` ADD COLUMN `isBusinessJudge` varchar(2) DEFAULT null COMMENT '是否需要业务验证';
ALTER TABLE `zt_modify` ADD COLUMN `productenrollId` bigint(20) DEFAULT null COMMENT '产品登记Id';
ALTER TABLE `zt_modify` ADD COLUMN `riskAnalysisEmergencyHandle` text DEFAULT null COMMENT '风险分析与应急处置';
ALTER TABLE `zt_modify` ADD COLUMN `externalId` varchar(225) DEFAULT null COMMENT '金信id';
ALTER TABLE `zt_modify` ADD COLUMN `externalCode` varchar(225) DEFAULT null COMMENT '金信code';
ALTER TABLE `zt_modify` ADD COLUMN `closeReason` varchar(225) DEFAULT null COMMENT '关闭原因';
ALTER TABLE `zt_modify` ADD COLUMN `changeType` varchar(32) DEFAULT null COMMENT '变更类别';
ALTER TABLE `zt_modify` ADD COLUMN `returnReason` text DEFAULT null COMMENT '金信退回原因';
ALTER TABLE `zt_modify` ADD COLUMN `operateName` varchar(512) DEFAULT null COMMENT '反馈-操作名称';
ALTER TABLE `zt_modify` ADD COLUMN `operateType` varchar(512) DEFAULT null COMMENT '反馈-操作类型';
ALTER TABLE `zt_modify` ADD COLUMN `implementResult` varchar(512) DEFAULT null COMMENT '反馈-执行结果';
ALTER TABLE `zt_modify` ADD COLUMN `startTime` datetime DEFAULT null COMMENT '反馈-开始时间';
ALTER TABLE `zt_modify` ADD COLUMN `endTime` datetime DEFAULT null COMMENT '反馈-结束时间';
ALTER TABLE `zt_modify` ADD COLUMN `supportUserName` varchar(128) DEFAULT null COMMENT '反馈-支持人员';
ALTER TABLE `zt_modify` ADD COLUMN `operateUserName` varchar(128) DEFAULT null COMMENT '反馈-操作人员';
ALTER TABLE `zt_modify` ADD COLUMN `issueDesc` varchar(1024) DEFAULT null COMMENT '反馈-问题描述';
ALTER TABLE `zt_modify` ADD COLUMN `resolveMethod` varchar(1024) DEFAULT null COMMENT '反馈-原因分析/解决方法';
ALTER TABLE `zt_modify` ADD COLUMN `changeStatus` varchar(32) DEFAULT null COMMENT '执行结果状态';
ALTER TABLE `zt_modify` ADD COLUMN `changeRemark` varchar(1024) DEFAULT null COMMENT '执行纪录';
ALTER TABLE `zt_modify` ADD COLUMN `realStartTime` datetime DEFAULT null COMMENT '开始时间';
ALTER TABLE `zt_modify` ADD COLUMN `realEndTime` datetime DEFAULT null COMMENT '结束时间';
ALTER TABLE `zt_modify` ADD COLUMN `pushStatus`  tinyint(1)  DEFAULT 0 COMMENT '0 = 未推送 1 = 推送成功 2 = 推送失败 ';
ALTER TABLE `zt_modify` ADD COLUMN `pushFailTimes`  tinyint(2)  DEFAULT 0 COMMENT '推送失败的次数';
ALTER TABLE `zt_modify` ADD COLUMN `pushDate`  datetime  DEFAULT null COMMENT '推送时间';
ALTER TABLE `zt_modify` ADD COLUMN `pushFailReason`  varchar(1024)  DEFAULT null COMMENT '推送失败原因';
ALTER TABLE `zt_modify` ADD COLUMN `cancelPushStatus`  tinyint(1)  DEFAULT 0 COMMENT '变更单取消0 = 未推送 1 = 推送成功 2 = 推送失败 ';
ALTER TABLE `zt_modify` ADD COLUMN `cancelPushFailTimes`  tinyint(2)  DEFAULT 0 COMMENT '变更单取消推送失败的次数';
ALTER TABLE `zt_modify` ADD COLUMN `cancelPushDate`  datetime  DEFAULT null COMMENT '变更单取消推送时间';
ALTER TABLE `zt_modify` ADD COLUMN `cancelPushFailReason`  varchar(1024)  DEFAULT null COMMENT '变更单取消推送失败原因';
ALTER TABLE `zt_modify` ADD COLUMN `returnTime`  int(8)  DEFAULT 0 COMMENT '退回次数';
ALTER TABLE `zt_modify` ADD COLUMN `feedbackId`  varchar(128)  DEFAULT null COMMENT '反馈单单号';
ALTER TABLE `zt_modify` ADD COLUMN `implementers`  varchar(512)  DEFAULT null COMMENT '金信实施人员';
ALTER TABLE `zt_modify` ADD COLUMN `implementDepartment`  varchar(512)  DEFAULT null COMMENT '金信实施部门';
ALTER TABLE `zt_modify` ADD COLUMN `implementStartTime`  datetime  DEFAULT null COMMENT '金信实施开始时间';
ALTER TABLE `zt_modify` ADD COLUMN `implementEndTime`  datetime  DEFAULT null COMMENT '金信实施结束时间';
ALTER TABLE `zt_modify` ADD COLUMN `feedbackDate`  datetime  DEFAULT null COMMENT '反馈时间';
ALTER TABLE `zt_modify` ADD COLUMN `changeDate`  datetime  DEFAULT null COMMENT '变更完成时间';
ALTER TABLE `zt_modify` ADD COLUMN `jsreturn`  tinyint(1)  DEFAULT 0 COMMENT '是否金信退回 0-否 1-是';
-- ----------------------------
-- 2022-9-8  shixuyang 金信交付配置
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'modifyInitiatePushUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'modifyCommitPushUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'modifyClosePushUrl', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushModifyEnable', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushModifyAppId', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushModifyAppSecret', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushModifyUsername', '');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushModifyFileIP', '');
INSERT INTO `zt_entry` (`name`, `account`, `code`, `key`, `freePasswd`, `ip`, `desc`, `createdBy`, `createdDate`, `calledTime`, `editedBy`, `editedDate`, `deleted`) VALUES ('金信对接', 'admin', 'jinxin', '19cc5be8dcfe3083e96b976d85cf5668', '0', '*', '', 'admin', '2022-9-13 17:17:30', 0, 'admin', '2022-9-13 15:05:02', '0');

-- ----------------------------
-- 2022-9-9  chendongcheng 金信交付新增字段;
-- ----------------------------
ALTER TABLE `zt_modify` ADD `testingRequestId` bigint(20) DEFAULT 0 COMMENT '测试申请id';
ALTER TABLE `zt_modify` ADD `productLine` varchar(30) DEFAULT NULL COMMENT '产品线';
ALTER TABLE `zt_modify` ADD `productId` varchar(255) DEFAULT '' COMMENT '产品id 多个逗号分隔';
ALTER TABLE `zt_modify` ADD `productInfoCode` varchar(1024) DEFAULT '' COMMENT '产品信息登记号';
ALTER TABLE `zt_modify` CHANGE `project` `projectPlanId` varchar(255) DEFAULT '0' COMMENT '关联项目id';
ALTER TABLE `zt_modify` CHANGE `problem` `problemId` varchar(30) DEFAULT '' COMMENT '问题id 逗号分隔';
ALTER TABLE `zt_modify` CHANGE `demand` `demandId` varchar(128) DEFAULT '0' COMMENT '需求id 逗号分隔';
ALTER TABLE `zt_modify` CHANGE `fixType` `implementationForm` varchar(25) DEFAULT '0' COMMENT '实施形式 product 项目 second 二线';
ALTER TABLE `zt_modify` ADD `CBPprojectId` varchar(255) DEFAULT '' COMMENT 'cbp项目id';
ALTER TABLE `zt_modify` ADD `backspaceExpectedStartTime` datetime DEFAULT NULL COMMENT '预计回退开始时间';
ALTER TABLE `zt_modify` ADD `techniqueCheck` text COMMENT '技术验证';
ALTER TABLE `zt_modify` ADD `cooperateDepNameList` varchar(30) DEFAULT NULL COMMENT '配合业务部门';
ALTER TABLE `zt_modify` ADD `businessCooperateContent` text COMMENT '需要业务配合内容';
ALTER TABLE `zt_modify` ADD `judgeDep` varchar(50) DEFAULT NULL COMMENT '验证部门';
ALTER TABLE `zt_modify` ADD `judgePlan` text COMMENT '验证方案';
ALTER TABLE `zt_modify` ADD `controlTableFile` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '关联项目控制表名称';
ALTER TABLE `zt_modify` ADD `controlTableSteps` text COMMENT '关联项目控制表步骤';
ALTER TABLE `zt_modify` ADD `ifMediumChanges` tinyint(1) DEFAULT 1 COMMENT '介质是否变化 0：是，1：否';
ALTER TABLE `zt_modify` ADD `outwardDeliveryDesc` varchar(255) DEFAULT '' COMMENT '交付摘要';
ALTER TABLE `zt_modify` ADD `requirementId` varchar(30) DEFAULT '' COMMENT '需求任务id 逗号分隔';
ALTER TABLE `zt_modify` ADD `ROR` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '修订记录：json格式，内含RORDate修订时间，RORContent修订内容';
ALTER TABLE `zt_modify` ADD `dealUser` varchar(255) default null COMMENT '待处理人';
ALTER TABLE `zt_modify` ADD `businessAffect` text COMMENT '实施期间业务影响';
ALTER TABLE `zt_modify` ADD `benchmarkVerificationType` varchar(50) DEFAULT NULL COMMENT '基准验证类型';
ALTER TABLE `zt_modify` ADD `verificationResults` varchar(255) DEFAULT NULL COMMENT '验证结果';
ALTER TABLE `zt_modify` ADD `applyUsercontact` varchar(20) DEFAULT NULL COMMENT '变更申请人联系方式';
ALTER TABLE `zt_modify` ADD `backspaceExpectedEndTime` datetime DEFAULT NULL COMMENT '预计回退结束时间';
ALTER TABLE `zt_modify` ADD `operationType` varchar(255) DEFAULT NULL COMMENT '操作类型';
ALTER TABLE `zt_modify` ADD `closedBy` varchar(255) DEFAULT NULL COMMENT '由谁关闭';
ALTER TABLE `zt_modify` ADD `closed` tinyint(1) NULL DEFAULT 0 COMMENT '0 = 未关闭 1= 关闭';
ALTER TABLE `zt_modify` ADD `closedDate` datetime DEFAULT NULL COMMENT '关闭时间';
ALTER TABLE `zt_modify` ADD `revertBy` varchar(255) DEFAULT NULL COMMENT '由谁退回（内部）';
ALTER TABLE `zt_modify` MODIFY COLUMN planBegin DATETIME NOT NULL;
ALTER TABLE `zt_modify` MODIFY COLUMN planEnd DATETIME NOT NULL;


-- ----------------------------
-- 2022-9-20  chendongcheng 分区表新增字段;
-- ----------------------------
ALTER TABLE `zt_partition` ADD `applicationName` varchar(128) DEFAULT NULL COMMENT '系统中文名';
update zt_partition set applicationName = application;


/*迭代十四*/
/*迭代十四  2022-8-31 项目评审挂起和恢复*/
ALTER TABLE zt_review ADD suspendBy varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '挂起人';
ALTER TABLE zt_review ADD suspendTime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '挂起时间';
ALTER TABLE zt_review ADD suspendReason varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' NOT NULL COMMENT '挂起原因';
ALTER TABLE zt_review ADD renewBy varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '恢复人';
ALTER TABLE zt_review ADD renewTime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '恢复时间';
ALTER TABLE zt_review ADD renewReason varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' NOT NULL COMMENT '恢复原因';

/*迭代十四 2022-9-1 liugaoyang 产品关联编号表*/
CREATE TABLE `zt_productcodeinfo` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product` int(11) NOT NULL COMMENT '关联的产品id',
    `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品编号',
    `enableTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '启用时间',
    `createTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
    `updateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
    `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
    `desc` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
    PRIMARY KEY (`id`),
    KEY `code` (`code`) USING BTREE,
    KEY `enableTime` (`enableTime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品关联编号表';
/*迭代十四 2022-9-1 liugaoyang 产品关联编号表添加索引*/
ALTER TABLE `zt_productcodeinfo`
    ADD INDEX `code` (`code`) USING BTREE ,
    ADD INDEX `enableTime` (`enableTime`) USING BTREE ;

/*2022.9.4添加工时计算参数 可配置 songdi*/
ALTER TABLE `zt_project` ADD `workHours` float NOT NULL DEFAULT '22' AFTER `requirements`;
/*新建邮件通知记录表*/
CREATE TABLE `zt_review_meeting_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_meeting_id` int(11) NOT NULL COMMENT '会议ID',
  `type` char(30) DEFAULT NULL COMMENT '邮件类型 notice邮件通知',
  `addressee` varchar(255) DEFAULT NULL COMMENT '收件人',
  `mailto` varchar(255) DEFAULT NULL COMMENT '抄送人',
  `title` varchar(255) DEFAULT NULL COMMENT '邮件标题',
  `content` text COMMENT '邮件内容',
  `sendTime` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*迭代十四 2022-9-1 liugaoyang 金信交付变更表加产品编号字段*/
ALTER table `zt_modify` ADD column `productCodeInfo`  varchar(50) default null COMMENT '关联产品编号' AFTER `requiredReviewNode`;
/*迭代十四 2022-9-1 liugaoyang release表加产品编号字段*/
ALTER table `zt_release` ADD column `productCodeInfo`  varchar(50) default null COMMENT '关联产品编号' AFTER `createdBy`;

/* 2022-09-01 leiyong 新一期迭代SQL */
ALTER TABLE `zt_user` ADD COLUMN `number` varchar(50) NULL AFTER `clientLang`;
ALTER TABLE `zt_bug` ADD COLUMN `analysis` text NULL AFTER `deleted`;
ALTER TABLE `zt_testtask` MODIFY COLUMN `build` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL AFTER `execution`;

/* 2022-09-20 leiyong 每日查ldap更新用户信息。*/
CREATE TABLE `zt_ldapupdate`  (
      `id` int NOT NULL AUTO_INCREMENT,
      `account` varchar(100) NOT NULL,
      `realname` varchar(100) NOT NULL,
      `dept` varchar(100) NOT NULL,
      `role` varchar(100) NOT NULL,
      `ldap` varchar(150) NULL,
      `processDate` date NULL,
      `process` tinyint(1) DEFAULT 0,
      PRIMARY KEY (`id`)
);

ALTER TABLE `zt_dept` ADD COLUMN `ldapName` varchar(100) NULL AFTER `name`;

CREATE TABLE `zt_ldapdept`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `deptID` mediumint NOT NULL,
  `deptName` varchar(100) NOT NULL,
  `ldapName` varchar(50) NOT NULL,
  `processDate` date NULL,
  `process` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

CREATE TABLE `zt_ldapuser`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `ldapAccount` varchar(150) NOT NULL,
  `ldapName` varchar(100) NULL,
  `deptID` mediumint NULL DEFAULT 0,
  `createDate` date NULL,
  PRIMARY KEY (`id`)
);

/*2022.9.28 shixuyang  数据获取增加两个字段*/
ALTER TABLE `zt_infoqz` ADD COLUMN `isDesensitize` tinyint(1)  DEFAULT null COMMENT '是否需要脱敏，1=是，0=否';
ALTER TABLE `zt_infoqz` ADD COLUMN `desensitizeProcess` text  DEFAULT null COMMENT '脱敏步骤';

/*2022-10-08 shixuyang 增加金信下载地址*/
INSERT INTO `zt_config`(`owner`,`module`,`section`,`key`,`value`) VALUES ('system', 'common', 'global', 'downloadIPJX', 'http://172.22.67.19/api.php?m=api&f=download&code=jinke1problem&time=1&token=80f45dfe6b7603a00d77236861fe4965&sign=%s&filename=%s');

/*2022-10-09 shixuyang 测试申请单增加是否为集中测试*/
ALTER TABLE `zt_testingrequest` ADD `isCentralizedTest` TINYINT(4) DEFAULT NULL COMMENT '是否为集中测试：0-否；1-是';

-- ----------------------------
-- 2022-10-10 tongyanqi 介质服务器地址配置 dev15_project_renew
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sftpServerIP', 'http://172.22.67.213');


/**
*迭代16 2022-09-28
*/
/*迭代16 2022-09-28 wangjiurong 驻场支持模板分类*/
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'typeList', '1', '支付类', '1');

INSERT into zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'typeList', '2', '总行类', '1');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'subTypeList', '1', '常规类', '1');

INSERT into zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'subTypeList', '2', '重保类', '1');

INSERT into zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'subTypeList', '3', '假期类', '1');

/*迭代16 2022-09-29 songdi 时长类型*/
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'durationTypeList', '1', 'T<8H', '1');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'durationTypeList', '2', '8H <= T < 12H', '1');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'durationTypeList', '3', 'T >= 12H', '1');

/*迭代16 2022-09-29 songdi 值班岗位*/
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'postType', '1', '全部', '1');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'postType', '2', '主机岗', '1');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'postType', '3', '开放岗', '1');

INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'postType', '4', 'MIS岗', '1');

/*迭代16 20220929  项目任务中制版区分*/
ALTER TABLE `zt_task` ADD COLUMN `source` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '数据来源（0：用户创建 1：系统创建）' after `deleted`,
    ADD COLUMN`tasktype` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '任务类型（0：默认 1：制版）' after `source`,
    ADD COLUMN `productVersion` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '产品版本' after `tasktype`,
    ADD COLUMN `dropType` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '废弃任务' after `productVersion`;

/*迭代16 20220929  制版表新增*/
 ALTER TABLE `zt_build`
     ADD COLUMN `status` varchar(255) COMMENT '状态' after `name`,
     ADD COLUMN `dealuser` varchar(255) COMMENT '待处理人' after `status`,
     ADD COLUMN `version` varchar(255) COMMENT '产品版本' after `dealuser`,
     ADD COLUMN `problemid` varchar(255) COMMENT '问题单号' after `version`,
     ADD COLUMN `demandid` varchar(255) COMMENT '需求单号' after `problemid`,
     ADD COLUMN `sendlineId` varchar(255)  COMMENT '二线工单单号' after `demandid`,
     ADD COLUMN `createdBy` varchar(255)  COMMENT '由谁创建' after `sendlineId`,
     ADD COLUMN `createdDate` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL  COMMENT '创建时间' after `createdBy`,
     ADD COLUMN `testUser` varchar(255)  COMMENT '测试人员' after `demandid`,
     ADD COLUMN `systemverify` enum('0','1') NOT NULL DEFAULT '1'  COMMENT '是否需要系统部验证,0:不需要,1:需要' after `testUser`,
     ADD COLUMN `verifyUser` varchar(255)  COMMENT '验证人员' after `systemverify`,
     ADD COLUMN `editedBy` varchar(255)  COMMENT '由谁编辑' after `verifyUser`,
     ADD COLUMN `editedDate` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL   COMMENT '编辑时间' after `editedBy`,
     ADD COLUMN `svnPath` varchar(255)  COMMENT 'SVN地址' after `editedDate`,
     ADD COLUMN `buildManual` varchar(255)  COMMENT '制版手册' after `svnPath`,
     ADD COLUMN `taskName` varchar(255)  COMMENT '所属任务' after `buildManual`,
     ADD COLUMN `app` varchar(255)  COMMENT '应用系统' after `taskName`,
     ADD COLUMN `taskid` varchar(255)  COMMENT '任务id' after `app`,
     ADD COLUMN `releaseName` varchar(255)  COMMENT '发布名称' after `taskid`,
     ADD COLUMN `releasePath` varchar(255)  COMMENT '发布地址' after `releaseName`,
     ADD COLUMN `plateName` varchar(255)  COMMENT '制品名称' after `releasePath`,
     ADD COLUMN `lastDealDate` date NOT NULL COMMENT '处理日期' after `plateName`;

/*迭代16 20220929  任务和其他关联关系新增*/
CREATE TABLE `zt_task_demand_problem`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `typeid` varchar(255) COMMENT '类型id' ,
  `product` varchar(250) COMMENT '所属产品',
  `project` varchar(150) COMMENT '所属项目',
  `application` varchar(255) COMMENT '所属应用系统' ,
  `version` varchar(100) COMMENT '所属版本',
  `execution` varchar(255) COMMENT '所属阶段',
  `code` varchar(255) COMMENT '单号',
  `assignTo` varchar(255) COMMENT '指派给',
  `type` varchar(100) COMMENT '数据类型（问题单 需求单 二线工单）',
  `taskid` varchar(100) COMMENT '任务id',
  `createdDate` datetime NOT NULL COMMENT '创建时间',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0：默认 1：删除',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- 2022-10-18 chendongcheng 需求意向新增上线日期字段
-- ----------------------------
ALTER TABLE `zt_opinion` ADD COLUMN `onlineTimeByDemand` datetime DEFAULT NULL COMMENT '需求条目最晚的上线日期';
ALTER TABLE `zt_opinion` MODIFY COLUMN receiveDate DATETIME NULL COMMENT '接受时间';
ALTER TABLE `zt_partition` ADD `dataOrigin` tinyint(4) DEFAULT 1 COMMENT '1:npc,2:cpcc';
ALTER TABLE `zt_partition` ADD `deleted` tinyint(4) DEFAULT 0 COMMENT '1：删除,2:未删除';
ALTER TABLE `zt_partition` ADD `deletedDate` date DEFAULT NULL COMMENT '删除日期';
update zt_partition set deleted = 1,deletedDate = curdate();
ALTER TABLE `zt_partition` MODIFY COLUMN name varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分区名';

/* 2022-10-18 zentao 工作流更新 */
REPLACE INTO `zt_workflow` (`parent`, `child`, `type`, `navigator`, `app`, `position`, `module`, `table`, `name`, `flowchart`, `js`, `css`, `order`, `buildin`, `administrator`, `desc`, `version`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES
('',  '', 'flow', 'primary',  'project',  '', 'program',  'zt_project', '项目集',  '', '', '', 0,  1,  '', '', '1.0',  'normal', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('',  '', 'flow', 'primary',  'project',  '', 'project',  'zt_project', '项目', '', '', '', 0,  1,  '', '', '1.0',  'normal', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('',  '', 'flow', 'primary',  'project',  '', 'execution',  'zt_project', '执行', '', '', '', 0,  1,  '', '', '1.0',  'normal', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00');

REPLACE INTO `zt_workflowaction` (`module`, `action`, `name`, `type`, `batchMode`, `extensionType`, `open`, `position`, `layout`, `show`, `order`, `buildin`, `virtual`, `conditions`, `verifications`, `hooks`, `linkages`, `js`, `css`, `toList`, `blocks`, `desc`, `status`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES
('program', 'browse', '项目集列表',  'single', 'different',  'none', 'normal', 'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'create', '添加项目集',  'single', 'different',  'none', 'normal', 'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'edit', '编辑项目集',  'single', 'different',  'none', 'normal', 'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'view', '项目集详情',  'single', 'different',  'none', 'normal', 'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'delete', '删除项目集',  'single', 'different',  'none', 'none', 'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'close',  '关闭项目集',  'single', 'different',  'none', 'modal',  'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'activate', '激活项目集',  'single', 'different',  'none', 'modal',  'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'start',  '启动项目集',  'single', 'different',  'none', 'modal',  'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'suspend',  '挂起项目集',  'single', 'different',  'none', 'modal',  'browseandview',  'normal', 'dropdownlist', 0,  1,  0,  '', '', '', '', '', '', '', '', '', 'enable', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00');

REPLACE INTO `zt_workflowfield` (`module`, `field`, `type`, `length`, `name`, `control`, `expression`, `options`, `default`, `rules`, `placeholder`, `order`, `searchOrder`, `exportOrder`, `canExport`, `canSearch`, `isValue`, `readonly`, `buildin`, `desc`, `createdBy`, `createdDate`, `editedBy`, `editedDate`) VALUES
('program', 'id', 'mediumint',  '8',  '编号', 'input',  '', '', '', '', '', 1,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'type', 'varchar',  '20', '类型', 'select', '', '16', 'sprint', '', '', 2,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'parent', 'mediumint',  '8',  '父项目集', 'input',  '', '', '0',  '', '', 3,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'name', 'varchar',  '90', '项目集名称',  'input',  '', '', '', '', '', 4,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'budget', 'varchar',  '30', '预算', 'input',  '', '', '0',  '', '', 6,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'budgetUnit', 'char', '30', '预算单位', 'input',  '', '', 'CNY',  '', '', 7,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  'admin',  '2021-07-28 16:55:23'),
('program', 'begin',  'date', '', '计划开始', 'input',  '', '', '', '', '', 8,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'end',  'date', '', '计划完成', 'input',  '', '', '', '', '', 9,  0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'days', 'smallint', '5',  '可用工作日',  'input',  '', '', '', '', '', 10, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'status', 'varchar',  '10', '状态', 'select', '', '17', '', '', '', 11, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'stage',  'enum', '', '阶段', 'input',  '', '', '1',  '', '', 12, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'pri',  'enum', '', '优先级',  'input',  '', '', '1',  '', '', 13, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'desc', 'text', '', '项目集描述',  'input',  '', '', '', '', '', 14, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'openedBy', 'varchar',  '30', '由谁创建', 'select', '', 'user', '', '', '', 15, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'openedDate', 'datetime', '', '创建日期', 'input',  '', '', '', '', '', 16, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'closedBy', 'varchar',  '30', '由谁关闭', 'select', '', 'user', '', '', '', 17, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'closedDate', 'datetime', '', '关闭日期', 'input',  '', '', '', '', '', 18, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'canceledBy', 'varchar',  '30', '由谁取消', 'select', '', 'user', '', '', '', 19, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'canceledDate', 'datetime', '', '取消日期', 'input',  '', '', '', '', '', 20, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'PO', 'varchar',  '30', 'PO', 'select', '', 'user', '', '', '', 21, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'PM', 'varchar',  '30', '负责人',  'select', '', 'user', '', '', '', 22, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'QD', 'varchar',  '30', 'QD', 'select', '', 'user', '', '', '', 23, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'RD', 'varchar',  '30', 'RD', 'select', '', 'user', '', '', '', 24, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'team', 'varchar',  '90', '团队', 'input',  '', '', '', '', '', 25, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'acl',  'enum', '', '访问控制', 'select', '', '18', 'open', '', '', 26, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'whitelist',  'text', '', '白名单',  'select', '', '7',  '', '', '', 27, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'order',  'mediumint',  '8',  '排序', 'input',  '', '', '', '', '', 28, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00'),
('program', 'deleted',  'enum', '', '已删除',  'select', '', '[\"\\u672a\\u5220\\u9664\",\"\\u5df2\\u5220\\u9664\"]',  '0',  '', '', 29, 0,  0,  '0',  '0',  '0',  '1',  1,  '', '', '2021-07-28 16:54:38',  '', '0000-00-00 00:00:00');

/* 2022年10月24日 LDAP功能改造。 */
CREATE TABLE zt_ldaphistory  (
      `id` int NOT NULL AUTO_INCREMENT,
      `ldapAccount` varchar(100) NOT NULL,
      `addTime` datetime NULL,
      `result` varchar(30) NULL,
      PRIMARY KEY (`id`)
);

/*2022-10-08 shixuyang 迭代十六组件管理表*/
CREATE TABLE `zt_component` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `type` varchar(32) DEFAULT NULL COMMENT '组件类型：thirdParty-第三方组件；public-公共组件',
                                `applicationMethod` varchar(32) DEFAULT NULL COMMENT '申请方式：new-新引入；upgrade-升级；exit-退出',
                                `level` varchar(32) DEFAULT NULL COMMENT '级别：company-公司级；dept-部门级',
                                `name` varchar(60) DEFAULT NULL COMMENT '组件名称',
                                `version` varchar(255) DEFAULT NULL COMMENT '组件版本',
                                `developLanguage` varchar(8) DEFAULT NULL COMMENT '开发语言',
                                `licenseType` varchar(40) DEFAULT NULL COMMENT '许可证类型',
                                `artifactId` varchar(255) DEFAULT NULL COMMENT 'Artifactid',
                                `groupId` varchar(255) DEFAULT NULL COMMENT 'groupId',
                                `projectId` varchar(64) DEFAULT NULL COMMENT '关联项目',
                                `applicationReason` text DEFAULT NULL COMMENT '申请原因',
                                `evidence` text DEFAULT NULL COMMENT '评估举证',
                                `createdBy` varchar(30) DEFAULT NULL COMMENT '创建人',
                                `createdDept` int(11) DEFAULT NULL COMMENT '创建部门/维护部门',
                                `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
                                `editedBy` varchar(30) DEFAULT NULL COMMENT '编辑人',
                                `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
                                `maintainer` varchar(30) DEFAULT NULL COMMENT '维护人',
                                `location` varchar(255) DEFAULT NULL COMMENT '获取位置',
                                `functionDesc` text DEFAULT NULL COMMENT '功能说明',
                                `status` varchar(32) DEFAULT NULL COMMENT '状态',
                                `dealUser` varchar(1024) DEFAULT NULL COMMENT '待处理人',
                                `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除；1-删除',
                                `reviewStage` varchar(8) DEFAULT NULL COMMENT '评审阶段',
                                `changeVersion` int(8) DEFAULT 0 COMMENT '变更版本',
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*2022-10-13 liuyuhan 迭代十七组件管理表 公共组件设计是否已经通过专业评审 */
ALTER TABLE zt_component ADD COLUMN hasProfessionalReview varchar(8) DEFAULT NULL COMMENT '公共组件设计是否已经通过专业评审 1-是,0-否';
/* 迭代17-增加组件管理申请开发语言*/
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'developLanguageList', '1', 'Java', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'developLanguageList', '2', 'C++', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'developLanguageList', '3', 'C', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'developLanguageList', '4', 'Python', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'developLanguageList', '5', 'Php', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'developLanguageList', '6', 'JavaScript', '0');
/*2022-10-18 shixuyang 增加指派审批小组审批 */
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint');
/*2022-10-24 shixuyang 迭代十七组件管理表 增加评审通过时间 */
ALTER TABLE zt_component ADD COLUMN reviewTime datetime DEFAULT NULL COMMENT '评审通过时间';




-- ----------------------------
-- 2022-9-9  wangshusen 内部年度计划新增字段; dev17_project_renew
-- ----------------------------
ALTER TABLE `zt_projectplan`
    ADD COLUMN `outsideTask`  varchar(255) NULL DEFAULT '' COMMENT '外部项目建设任务' AFTER `outsideProject`,
    ADD COLUMN `dataEnterLake`  varchar(255)  NULL DEFAULT '' COMMENT '数据入湖' AFTER `outsideTask`,
    ADD COLUMN `basicUpgrade`  varchar(255) NULL DEFAULT '' COMMENT '基础软硬件升级' AFTER `dataEnterLake`,
    ADD COLUMN `planCode`  varchar(255)  NULL DEFAULT '' COMMENT '项目计划编号' AFTER `dataEnterLake`,
    ADD COLUMN `insideStatus`  tinyint(2) default null COMMENT '内部项目状态'  AFTER `status`;

-- ----------------------------
-- 2022-9-9  tongyanqi 外部年度计划修改字段; dev17_project_renew
-- ----------------------------
ALTER TABLE `zt_outsideplan`
    MODIFY COLUMN `deleted`  tinyint NOT NULL DEFAULT 0 AFTER `createdDate`;
-- ----------------------------
-- 2022-9-16  tongyanqi 外部项目表增加字段; dev17_project_renew
-- ----------------------------
ALTER TABLE `zt_outsideplan`
    ADD COLUMN `changes`  text NULL COMMENT '变更情况' AFTER `content`,
    ADD COLUMN `remark`  text NULL COMMENT '操作备注' AFTER `changes`,
    ADD COLUMN `milestone`  text NULL COMMENT '里程碑' AFTER `remark`,
    ADD COLUMN `files`  varchar(255) NULL DEFAULT '' COMMENT '附件' AFTER `deleted`,
    ADD COLUMN `maintainers`  varchar(255) NULL COMMENT '维护人员 逗号分割' AFTER `files`;
-- ----------------------------
-- 2022-9-16  tongyanqi 外部项目子项;dev17_project_renew
-- ----------------------------
CREATE TABLE `zt_outsideplansubprojects` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `outsideProjectPlanID` int(11) NOT NULL COMMENT '外部年度计划id',
                                             `subProjectName` varchar(100) NOT NULL COMMENT '子任务名称',
                                             `deleted` tinyint(1) DEFAULT 0 COMMENT '是否删除 1= 已删除 0 =正常',
                                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- 2022-9-16  tongyanqi 外部项目任务;dev17_project_renew
-- ----------------------------
CREATE TABLE `zt_outsideplantasks` (
                                       `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                       `outsideProjectPlanID` int(11) NOT NULL,
                                       `subProjectID` int(11) NOT NULL COMMENT '建设任务名称',
                                       `subTaskName` varchar(100) NOT NULL,
                                       `subTaskDesc` text NOT NULL COMMENT '建设任务描述',
                                       `subTaskBegin` date NOT NULL COMMENT '建设任务计划开始时间',
                                       `subTaskEnd` date DEFAULT NULL COMMENT '建设任务计划结束时间',
                                       `subTaskUnit` varchar(100) DEFAULT NULL COMMENT '业务司局',
                                       `subTaskBearDept` varchar(100) DEFAULT NULL COMMENT '承建单位',
                                       `subTaskDemandParty` varchar(100) DEFAULT NULL COMMENT '需求方（外部）',
                                       `subTaskDemandContact` varchar(100) DEFAULT NULL COMMENT '需求方（外部）联系人及联系方式',
                                       `subTaskDemandDeadline` date DEFAULT NULL COMMENT '需求方建议完成时间',
                                       `deleted` tinyint(1) DEFAULT NULL COMMENT '是否删除',
                                       PRIMARY KEY (`id`),
                                       KEY `outsideProjectPlanID` (`outsideProjectPlanID`),
                                       KEY `subProjectID` (`subProjectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- ----------------------------
-- 2022-9-16  tongyanqi 年度计划添加字段 dev17_project_renew
-- ----------------------------

ALTER TABLE `zt_projectplan`
    ADD COLUMN `outsideSubProject`  varchar(255)  NULL DEFAULT '' COMMENT '外部项目子项' AFTER `outsideTask`,
    ADD COLUMN `mailto`  varchar(255)  NULL DEFAULT '' COMMENT '抄送人' AFTER `lastDealDate`,
    ADD COLUMN `commentCommit`  varchar(255)  NULL DEFAULT '' COMMENT '提交申请备注' AFTER `planRemark`,
    ADD COLUMN `planStages`  text NULL COMMENT '项目阶段' AFTER `outsideSubProject`,
    MODIFY COLUMN `bearDept`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '承建部门' AFTER `duration`;

-- ----------------------------
-- 2022-9-16  wss 年度计划添加字段修改 dev17_project_renew
-- ----------------------------
ALTER TABLE `zt_projectplan` MODIFY COLUMN `insideStatus` VARCHAR(30);

ALTER TABLE `zt_projectplan` ADD COLUMN `rejectStatus`  int(10)  NULL DEFAULT 0 COMMENT '回退节点记录';
ALTER TABLE `zt_projectplan` ADD COLUMN `beforeStage`  int(10)  NULL DEFAULT 0 COMMENT '上一个节点';
ALTER TABLE `zt_projectplan` ADD COLUMN `changeStatus`  VARCHAR(30)  NULL DEFAULT 'no' COMMENT '变更状态';
ALTER TABLE `zt_projectplan` ADD COLUMN `changeVersion`  tinyint(1) DEFAULT 0 COMMENT '变更版本';
ALTER TABLE `zt_projectplan` ADD COLUMN `changeStage`  tinyint(1) DEFAULT 0 COMMENT '变更节点';
ALTER TABLE `zt_projectplan` ADD COLUMN `changeMailto` VARCHAR(255) NULL DEFAULT '' COMMENT '变更抄送人';
ALTER TABLE `zt_projectplan` ADD COLUMN `leaderApproval` VARCHAR(255) NULL DEFAULT '' COMMENT '领导审批';

-- ----------------------------
-- 2022-9-29  wss 年度计划变更表 dev17_project_renew
-- ----------------------------
CREATE TABLE `zt_projectplanchange`(
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `planID` int(11) NULL DEFAULT 0 COMMENT '年度计划id',
    `status` varchar(255) NULL DEFAULT 'pending' COMMENT '变更状态',
    `content` text NULL COMMENT '年度计划内容',
    `new` text NULL COMMENT '变更后内容',
    `old` text NULL COMMENT '变更前内容',
    `createdDate` date NOT NULL COMMENT '变更提出时间',
    `end`         date NOT NULL COMMENT '变更结束时间',
    `createdBy`   varchar(255) NOT NULL COMMENT '变更提出人',
    `deleted` tinyint(1) DEFAULT 0 COMMENT '是否删除 1= 已删除 0 =正常',
    PRIMARY KEY (`id`),
    KEY `planID` (`planID`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE `zt_projectplanchange` ADD COLUMN `version`  tinyint(1) DEFAULT 0 COMMENT '变更版本';
ALTER TABLE `zt_projectplanchange` ADD COLUMN `planRemark` text NULL COMMENT '变更内容备注';

-- ----------------------------
-- 2022-10-10 tongyanqi 建设任务deleted默认0 dev17_project_renew
-- ----------------------------
ALTER TABLE `zt_outsideplantasks`
    MODIFY COLUMN `deleted`  tinyint(1) NULL DEFAULT 0 COMMENT '是否删除' AFTER `subTaskDemandDeadline`;

-- ----------------------------
-- 2022-10-17 wangshusen dept增加年度计划接口人字段 dev15_project_renew
-- ----------------------------
ALTER TABLE `zt_dept` ADD COLUMN `planPerson`  varchar(255)  NULL DEFAULT '' COMMENT '年度计划接口人';
-- ----------------------------
-- 2022-10-25 tongyanqi 旧编号
-- ----------------------------
ALTER TABLE `zt_projectplan`
    ADD COLUMN `oldPlanCode`  varchar(255) NULL DEFAULT '' COMMENT '历史编号' AFTER `planCode`;
/* 2022-10-26 liuyuhan 迭代十七组件管理 增加组件管理架构部处理人*/
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'productManagerReviewer', 'zhangyang', '张扬', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'productManagerReviewer', 'fengqing', '冯庆', '0');


-- ----------------------------
-- 2022-10-26 chendongcheng 二线专员
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','opinion','apiDealUserList','userAccount','litianzi','1');

/*2022-10-18 liuyuhan 增加待架构部领导确认的【确定中】 */
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming');

/* 2022年11月03日 测试管理功能改造。 */
ALTER TABLE `zt_bug` ADD COLUMN `applicationID` int NOT NULL DEFAULT 0 AFTER `product`;
ALTER TABLE `zt_case` ADD COLUMN `applicationID` mediumint NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE `zt_testsuite` ADD COLUMN `applicationID` mediumint NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE `zt_testreport` ADD COLUMN `applicationID` mediumint NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE `zt_testtask` ADD COLUMN `applicationID` mediumint NOT NULL DEFAULT 0 AFTER `oddNumber`;


-- ----------------------------
-- 2022-11-15 tongyanqi 介质传输升级 dev18_media
-- ----------------------------
ALTER TABLE `zt_release`
ADD COLUMN `uuid`  varchar(50) NULL DEFAULT '' COMMENT '介质uuid' AFTER `productCodeInfo`,
ADD COLUMN `pushStatusQz`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '推送状态 0=不推送 1=需要推送 2=正在推送 3=完成推送 4=网络异常 -2=文件不存在 -3=MD5不存在 -1=对方连不上(用户密码错误)' AFTER `uuid`,
ADD COLUMN `pushTimeQz`  datetime NULL COMMENT '推送时间' AFTER `pushStatusQz`,
ADD COLUMN `pushFailsQz`  tinyint(1) NOT NULL DEFAULT 0 AFTER `pushTimeQz`,
ADD COLUMN `pushStatusJx`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '推送状态 0=不推送 1=需要推送 2=正在推送 3=完成推送 4=网络异常 -2=文件不存在 -3=MD5不存在 -1=对方连不上(用户密码错误)' AFTER `pushFailsQz`,
ADD COLUMN `pushTimeJx`  datetime NULL COMMENT '推送时间' AFTER `pushStatusJx`,
ADD COLUMN `pushFailsJx`  tinyint(1) NOT NULL DEFAULT 0 AFTER `pushTimeJx`,
ADD COLUMN `remotePathQz`  varchar(255)  NOT NULL DEFAULT "" COMMENT '清总文件地址' AFTER `pushFailsQz`,
ADD COLUMN `remotePathJx`  varchar(255) NOT NULL DEFAULT "" COMMENT '金信远程地址' AFTER `pushFailsJx`,
ADD COLUMN `md5`  varchar(255) NOT NULL DEFAULT '' COMMENT '压缩包MD5值' AFTER `remotePathJx`;

-- 推送日志表
CREATE TABLE `zt_pushlog` (
                              `id`  int(11) NOT NULL AUTO_INCREMENT ,
                              `releaseId`  int(11) NOT NULL COMMENT '介质id' ,
                              `type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '推送类型 1= 清总 2=金信' ,
                              `pushTime`  datetime NOT NULL COMMENT '推送时间' ,
                              `pushStatus`  tinyint NOT NULL COMMENT '推送状态' ,
                              PRIMARY KEY (`id`),
                              INDEX (`releaseId`)
)
;

-- 处理历史外部计划子项目和任务
UPDATE `zt_projectplan` set outsideSubProject = CONCAT(',',outsideSubProject,',')  where outsideSubProject <> '';
UPDATE `zt_projectplan` set outsideTask = CONCAT(',',outsideTask,',') where outsideTask <> '';

-- 创建介质服务器用户
CREATE user media@'%' IDENTIFIED by 'media$cfit';
GRANT SELECT, UPDATE ON `cfitpms`.`zt_release` TO `media`@`%`;
GRANT SELECT, UPDATE, INSERT ON `cfitpms`.`zt_pushlog` TO `media`@`%`;
ALTER USER 'media'@'%' IDENTIFIED WITH mysql_native_password By 'media$cfit';
FLUSH PRIVILEGES;
-- ----------------------------
-- 2022-11-15 tongyanqi 介质传输升级 dev18_media end
-- ----------------------------

-- ----------------------------
-- 2022-11-15 陈东程 对外交付新增二线专员
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','outwarddelivery','apiDealUserList','userAccount','litianzi','1');

-- ----------------------------
-- 2022-11-18 刘高杨 项目评审表加截止日期字段
-- ----------------------------
ALTER TABLE `zt_review` ADD COLUMN `endDate` DATETIME DEFAULT '0000-00-00 00:00:00' COMMENT '评审截止日期' AFTER `dealUser` ;
ALTER TABLE `zt_review` ADD COLUMN `submitDate` DATETIME DEFAULT '0000-00-00 00:00:00' COMMENT '评审提交日期' AFTER `endDate`;
ALTER TABLE `zt_reviewnode` ADD COLUMN `endDate` DATETIME DEFAULT '0000-00-00 00:00:00' COMMENT '评审提交日期' AFTER `createdDate`;


-- ----------------------------
-- 2022-11-24 迭代十六相关表
-- ----------------------------

-- ----------------------------
-- 2022-11-24 wangjiurong 迭代十六 驻场支持相关表
-- ----------------------------
CREATE TABLE `zt_resident_support_day` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL DEFAULT '0' COMMENT '驻场排班模板id',
  `dutyDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '排班日期',
  `dutyGroupLeader` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '值班组长',
  `enable` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否开启  1开启 0关闭',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `createdBy` char(30) NOT NULL COMMENT '创建人',
  `createdDept` mediumint(8) NOT NULL DEFAULT '0' COMMENT '创建人部门id',
  `createdTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editBy` char(30) NOT NULL COMMENT '编辑人',
  `editByTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `dutyDate` (`dutyDate`) USING BTREE,
  KEY `templated` (`templateId`) USING BTREE,
  KEY `dutyGroupLeader` (`dutyGroupLeader`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='驻场支持天排期';


CREATE TABLE `zt_resident_support_day_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL DEFAULT '0' COMMENT '模板ID',
  `dayId` int(11) NOT NULL DEFAULT '0' COMMENT '驻场排版日id',
  `dutyUser` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值班人',
  `dutyUserDept` int(11) NOT NULL DEFAULT '0' COMMENT '值班人部门',
  `postType` tinyint(4) DEFAULT '0' COMMENT '工作岗位',
  `timeType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '时长类型',
  `startTime` time NOT NULL COMMENT '值班当天开始时间',
  `endTime` time NOT NULL COMMENT '值班当天结束时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 1正常状态  2-变更待审核',
  `requireInfo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '值班要求',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `createdBy` char(30) NOT NULL COMMENT '创建人',
  `createdTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editBy` char(30) NOT NULL COMMENT '编辑人',
  `editByTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `dayId` (`dayId`) USING BTREE,
  KEY `dutyUser` (`dutyUser`) USING BTREE,
  KEY `dutyUserDept` (`dutyUserDept`) USING BTREE,
  KEY `templateId` (`templateId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='驻场支持天排期明细';

CREATE TABLE `zt_resident_support_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0000-00' COMMENT '模板名称',
  `enable` enum('0','1') DEFAULT '1' COMMENT '是否开启  1-开启 0-未开启',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '排版模板一级分类',
  `subType` tinyint(4) NOT NULL DEFAULT '1' COMMENT '二级分类 （常规类、重保类等） ',
  `startDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '模板开始时间',
  `endDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '模板结束时间',
  `version` tinyint(4) NOT NULL DEFAULT '0' COMMENT '版本',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '创建人',
  `createdDept` mediumint(8) NOT NULL DEFAULT '0' COMMENT '创建人部门id',
  `createdTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `editBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编辑人',
  `editByTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE,
  KEY `type` (`type`,`subType`) USING BTREE,
  KEY `start_end_date` (`startDate`,`endDate`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='驻场支持模板信息';

CREATE TABLE `zt_resident_support_template_dept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL DEFAULT '0' COMMENT '驻场排班模板id',
  `deptId` int(11) NOT NULL DEFAULT '0' COMMENT '部门id',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'waitSchedule' COMMENT '状态:waitSchedule 待排期 waitApply-待提交 waitDeptReview-待部门审核 waitPdReview-待产创部门审核  pass-审核通过 reject-退回',
  `isModify` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否变更 1未变更  2-变更',
  `version` tinyint(4) NOT NULL DEFAULT '0' COMMENT '版本',
  `dealUsers` varchar(255) NOT NULL DEFAULT '' COMMENT '待处理人',
  `applySubmitBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '提交人',
  `applySubmitTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '提交人时间',
  `createdBy` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '创建人',
  `createdTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `createdDept` mediumint(8) NOT NULL DEFAULT '0' COMMENT '创建人部门id',
  `editBy` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编辑人',
  `editByTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `templateId` (`templateId`) USING BTREE,
  KEY `deptId` (`deptId`) USING BTREE,
  KEY `dealUsers` (`dealUsers`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='驻场支持模板涉及部门表';

CREATE TABLE `zt_resident_support_work` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL COMMENT '驻场支持模板',
  `dayId` int(11) NOT NULL DEFAULT '0' COMMENT '驻场排版日id',
  `dutyDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '值班日期 字段用冗余',
  `groupLeader` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '实际值班组长',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '状态',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '有效状态 0有效 1删除',
  `area` tinyint(4) NOT NULL COMMENT '值班地点',
  `dateType` tinyint(4) NOT NULL COMMENT '日期类型',
  `isEmergency` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '是否紧急事件  1是 2否',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '应急事件说明',
  `logs` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值班日志',
  `warnLogs` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '下一值班重点关注',
  `analysis` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付交易系统运行质量日报分析',
  `mailCtoUsers` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮件抄送人',
  `daytype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '填报日志时选的值班类型',
  `isDraft` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1' COMMENT '是否是草稿 1-否 2-是',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dayId` (`dayId`) USING BTREE,
  KEY `dutyDate` (`dutyDate`) USING BTREE,
  KEY `templateId` (`templateId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='驻场支持工作日志';

CREATE TABLE `zt_resident_support_work_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL COMMENT '驻场支持模板',
  `workId` int(11) NOT NULL DEFAULT '0' COMMENT '工作日志id',
  `realDutyuser` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '实际值班人员',
  `realDutyuserDept` int(11) DEFAULT '0' COMMENT '实际值班人员部门',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '有效状态 0有效 1删除',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `editedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `workId` (`workId`) USING BTREE,
  KEY `realDutyuser` (`realDutyuser`) USING BTREE,
  KEY `realDutyuserDept` (`realDutyuserDept`) USING BTREE,
  KEY `templateId` (`templateId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='驻场支持工作日志详情表';

CREATE TABLE `zt_resident_support_work_detail_draft` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL COMMENT '驻场支持模板',
  `workId` int(11) NOT NULL DEFAULT '0' COMMENT '工作日志id',
  `realDutyuser` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '实际值班人员',
  `realDutyuserDept` int(11) DEFAULT '0' COMMENT '实际值班人员部门',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '有效状态 0有效 1删除',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `editedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `workId` (`workId`) USING BTREE,
  KEY `realDutyuser` (`realDutyuser`) USING BTREE,
  KEY `realDutyuserDept` (`realDutyuserDept`) USING BTREE,
  KEY `templateId` (`templateId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='驻场支持工作日志草稿详情表';

CREATE TABLE `zt_resident_support_work_draft` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `templateId` int(11) NOT NULL COMMENT '驻场支持模板',
  `dayId` int(11) NOT NULL DEFAULT '0' COMMENT '驻场排版日id',
  `dutyDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '值班日期 字段用冗余',
  `groupLeader` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '实际值班组长',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '有效状态 0有效 1删除',
  `area` tinyint(4) NOT NULL COMMENT '值班地点',
  `dateType` tinyint(4) NOT NULL COMMENT '日期类型',
  `isEmergency` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '是否紧急事件  1是 2否',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '应急事件说明',
  `logs` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值班日志',
  `warnLogs` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '下一值班重点关注',
  `analysis` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付交易系统运行质量日报分析',
  `mailCtoUsers` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮件抄送人',
  `daytype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '填报日志时选的值班类型',
  `createdBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dayId` (`dayId`) USING BTREE,
  KEY `dutyDate` (`dutyDate`) USING BTREE,
  KEY `templateId` (`templateId`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='驻场支持工作日志草稿表';

-- ----------------------------
-- 2022-11-24 wangjiurong 迭代十六 驻场支持邮件设置
-- ----------------------------
INSERT INTO zt_config (owner, module, `section`, `key`, value)
VALUES('system', 'common', 'global', 'setResidentSupportBacklogMail', '{"mailTitle":"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u5904\\u7406","variables":["\\u9a7b\\u573a\\u652f\\u6301"],"mailContent":"<p class=\\"MsoNormal\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u5904\\u7406<\\/strong><span style=\\"color:#E53333;\\"><strong>\\u9a7b\\u573a\\u652f\\u6301<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>"}');
INSERT INTO zt_config (owner, module, `section`, `key`, value)
VALUES('system', 'common', 'global', 'setResidentSupportNoticeMail', '{"mailTitle":"\\u3010\\u901a\\u77e5\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u9a7b\\u573a\\u652f\\u6301\\u3010%s\\u3011\\u901a\\u77e5\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u8fdb\\u884c\\u67e5\\u770b","variables":["\\u9a7b\\u573a\\u652f\\u6301"],"mailContent":"<p class=\\"MsoNormal\\"><strong>\\u8bf7\\u8fdb\\u5165<\\/strong><strong>\\u3010\\u4e8c\\u7ebf\\u7ba1\\u7406\\u3011\\u67e5\\u770b<\\/strong><span style=\\"color:#E53333;\\"><strong>\\u9a7b\\u573a\\u652f\\u6301<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>"}');


-- ----------------------------
-- 2022-11-24 wangjiurong 迭代十六 驻场支持自定义配置相关数据
-- ----------------------------
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'postType', '1', '全部', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'postType', '2', '主机岗', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'postType', '3', '开放岗', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES( 'zh-cn', 'residentsupport', 'postType', '4', 'MIS岗', '1');

INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES( 'zh-cn', 'residentsupport', 'durationTypeList', '1', 'T<8H', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'durationTypeList', '2', '8H <= T < 12H', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES( 'zh-cn', 'residentsupport', 'durationTypeList', '3', 'T >= 12H', '1');

INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'dateTypeList', '1', '工作日', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'dateTypeList', '2', '周末', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'dateTypeList', '3', '节假日', '1');

INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'areaList', '1', '德胜', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES( 'zh-cn', 'residentsupport', 'areaList', '2', '稻香湖', '1');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'areaList', '3', '复兴门', '0');

INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'typeList', '', '', '0');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'typeList', '1', '支付类', '0');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES( 'zh-cn', 'residentsupport', 'typeList', '2', '总行类', '0');

INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'subTypeList', '', '', '0');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'subTypeList', '1', '常规类', '0');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'subTypeList', '2', '重保类', '0');
INSERT INTO zt_lang ( lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'residentsupport', 'subTypeList', '3', '假期类', '0');



-- ----------------------------
-- 2022-11-24 lizhongzheng 迭代十六 二线管理
-- ----------------------------
CREATE TABLE `zt_secondorder` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `code` varchar(50) NOT NULL COMMENT '单号',
                              `summary` varchar(255) NOT NULL COMMENT '摘要',
                              `type` varchar(20) NOT NULL COMMENT '类型',
                              `subtype` varchar(20) NOT NULL COMMENT '子类型',
                              `source` varchar(20) NOT NULL COMMENT '来源方式',
                              `team` varchar(50) NOT NULL COMMENT '承建单位',
                              `union` varchar(50) DEFAULT NULL COMMENT '业务需求单位',
                              `exceptDoneDate` date DEFAULT NULL COMMENT '期望完成日期',
                              `dealUser` varchar(50) NOT NULL COMMENT '下节点处理人',
                              `ccList` varchar(255) DEFAULT NULL COMMENT '编辑抄送人',
                              `acceptUser` varchar(50) DEFAULT NULL COMMENT '受理人',
                              `acceptDept` mediumint(9) DEFAULT NULL COMMENT '受理部门',
                              `createdDept` mediumint(9) NOT NULL COMMENT '发起部门',
                              `status` varchar(20) NOT NULL COMMENT '流程状态',
                              `app` varchar(255) DEFAULT NULL COMMENT '应用系统',
                              `closeReason` mediumtext DEFAULT NULL COMMENT '关闭原因',
                              `ifAccept` char(5) DEFAULT NULL COMMENT '是否受理',
                              `progress` mediumtext DEFAULT NULL COMMENT '当前进展',
                              `completeStatus` char(5) DEFAULT NULL COMMENT '完成情况',
                              `createdBy` char(30) NOT NULL COMMENT '由谁创建',
                              `createdDate` datetime NOT NULL COMMENT '创建日期',
                              `closedBy` char(30) DEFAULT NULL COMMENT '由谁关闭',
                              `closedDate` datetime DEFAULT NULL COMMENT '关闭时间',
                              `editedBy` char(30) DEFAULT NULL COMMENT '由谁编辑',
                              `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
                              `startDate` date DEFAULT NULL COMMENT '实际开始',
                              `overDate` date DEFAULT NULL COMMENT '实际结束',
                              `planstartDate` date DEFAULT NULL COMMENT '计划开始',
                              `planoverDate` date DEFAULT NULL COMMENT '计划结束',
                              `consultRes` mediumtext DEFAULT NULL COMMENT '咨询评估结果',
                              `testRes` mediumtext DEFAULT NULL COMMENT '测试验证结果',
                              `dealRes` mediumtext DEFAULT NULL COMMENT '处理结果',
                              `deleted` varchar(5) DEFAULT '0',
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setSecondorderMail', '{\"mailTitle\":\"\\u3010\\u5f85\\u529e\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010%s\\u3011\\u5f85\\u5904\\u7406\\uff0c\\u8bf7\\u8fdb\\u5165\\u7814\\u53d1\\u8fc7\\u7a0b\\u7ba1\\u7406\\u5e73\\u53f0\\u5904\\u7406\",\"variables\":[\"\\u4e8c\\u7ebf\\u5de5\\u5355\"],\"mailContent\":\"<p class=\\\"MsoNormal\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u5730\\u76d8\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5f85\\u5904\\u7406\\u3011<\\/strong><span><strong>-<\\/strong><\\/span><strong>\\u3010\\u5ba1\\u6279\\u3011\\u6216\\u3010\\u4e8c\\u7ebf\\u5de5\\u5355\\u3011\\u5904\\u7406<\\/strong><span style=\\\"color:#E53333\\\"><strong>\\u4e8c\\u7ebf\\u5de5\\u5355<\\/strong><\\/span><strong>\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><\\/p>\"}');

INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('all',   'secondorder', 'childTypeList', 'all', '{\"consult\":{\"a1\":\"\\u95ee\\u9898\\u54a8\\u8be2\",\"a2\":\"\\u65b9\\u6848\\u54a8\\u8be2\\u8bc4\\u4f30\",\"a3\":\"\\u7f16\\u5199\\u811a\\u672c\",\"a4\":\"\\u5176\\u4ed6\\u54a8\\u8be2\\u8bc4\\u4f30\"},\"test\":{\"b1\":\"\\u517c\\u5bb9\\u6027\\u6d4b\\u8bd5\",\"b2\":\"\\u8054\\u8c03\\u6d4b\\u8bd5\",\"b3\":\"\\u5176\\u4ed6\\u6d4b\\u8bd5\"},\"script\":{\"d1\":\"\\u6570\\u636e\\u83b7\\u53d6\\u811a\\u672c\", \"d2\":\"\\u5e94\\u7528\\u76d1\\u63a7\\u811a\\u672c\", \"d3\":\"\\u5176\\u4ed6\\u811a\\u672c\"},\"plan\":{\"e1\":\"\\u65b9\\u6848\\u6587\\u6863\"},\"other\":{\"c1\":\"\\u5176\\u4ed6\"}}', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'typeList', 'consult', '咨询评估', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'typeList', 'test', '测试验证', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'typeList', 'script', '脚本编制', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'typeList', 'plan', '方案文档', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'typeList', 'other', '其他', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'sourceList', 'mail', '邮件方式', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'sourceList', 'tel', '电话方式', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'sourceList', 'letter', '外部来函', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'secondorder', 'sourceList', 'other', '其他方式', '0');



/*迭代16 2022-11-24  项目任务中制版区分*/
ALTER TABLE `zt_task` ADD COLUMN `source` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '数据来源（0：用户创建 1：系统创建）' after `deleted`,
    ADD COLUMN`tasktype` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '任务类型（0：默认 1：制版）' after `source`,
    ADD COLUMN `productVersion` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '产品版本' after `tasktype`,
    ADD COLUMN `dropType` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '废弃任务' after `productVersion`;

/*迭代16 2022-11-24  制版表新增*/
 ALTER TABLE `zt_build`
     ADD COLUMN `status` varchar(255) COMMENT '状态' after `name`,
     ADD COLUMN `dealuser` varchar(255) COMMENT '待处理人' after `status`,
     ADD COLUMN `version` varchar(255) COMMENT '产品版本' after `dealuser`,
     ADD COLUMN `problemid` varchar(255) COMMENT '问题单号' after `version`,
     ADD COLUMN `demandid` varchar(255) COMMENT '需求单号' after `problemid`,
     ADD COLUMN `sendlineId` varchar(255)  COMMENT '二线工单单号' after `demandid`,
     ADD COLUMN `createdBy` varchar(255)  COMMENT '由谁创建' after `sendlineId`,
     ADD COLUMN `createdDate` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL  COMMENT '创建时间' after `createdBy`,
     ADD COLUMN `testUser` varchar(255)  COMMENT '测试人员' after `demandid`,
     ADD COLUMN `systemverify` enum('0','1') NOT NULL DEFAULT '1'  COMMENT '是否需要系统部验证,0:不需要,1:需要' after `testUser`,
     ADD COLUMN `verifyUser` varchar(255)  COMMENT '验证人员' after `systemverify`,
     ADD COLUMN `editedBy` varchar(255)  COMMENT '由谁编辑' after `verifyUser`,
     ADD COLUMN `editedDate` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL   COMMENT '编辑时间' after `editedBy`,
     ADD COLUMN `svnPath` varchar(255)  COMMENT 'SVN地址' after `editedDate`,
     ADD COLUMN `buildManual` varchar(255)  COMMENT '制版手册' after `svnPath`,
     ADD COLUMN `taskName` varchar(255)  COMMENT '所属任务' after `buildManual`,
     ADD COLUMN `app` varchar(255)  COMMENT '应用系统' after `taskName`,
     ADD COLUMN `taskid` varchar(255)  COMMENT '任务id' after `app`,
     ADD COLUMN `releaseName` varchar(255)  COMMENT '发布名称' after `taskid`,
     ADD COLUMN `releasePath` varchar(255)  COMMENT '发布地址' after `releaseName`,
     ADD COLUMN `plateName` varchar(255)  COMMENT '制品名称' after `releasePath`,
     ADD COLUMN `lastDealDate` date NOT NULL COMMENT '处理日期' after `plateName`;

/*迭代16 2022-11-24  任务和其他关联关系新增*/
CREATE TABLE `zt_task_demand_problem`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `typeid` varchar(255) COMMENT '类型id' ,
  `product` varchar(250) COMMENT '所属产品',
  `project` varchar(150) COMMENT '所属项目',
  `application` varchar(255) COMMENT '所属应用系统' ,
  `version` varchar(100) COMMENT '所属版本',
  `execution` varchar(255) COMMENT '所属阶段',
  `code` varchar(255) COMMENT '单号',
  `assignTo` varchar(255) COMMENT '指派给',
  `type` varchar(100) COMMENT '数据类型（问题单 需求单 二线工单）',
  `taskid` varchar(100) COMMENT '任务id',
  `createdDate` datetime NOT NULL COMMENT '创建时间',
  `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0：默认 1：删除',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*迭代16 2022-11-24  新增*/
ALTER TABLE `zt_problem`
 ADD COLUMN `execution` varchar(255) COMMENT '所属阶段' after `ifOverDate`;

 /*迭代16 2022-11-24  新增*/
ALTER TABLE `zt_demand`
 ADD COLUMN `execution` varchar(255) COMMENT '所属阶段' after `requirementID`;

/*迭代16 2022-11-24  新增*/
ALTER TABLE `zt_testtask` ADD COLUMN `secondorder` varchar(200) DEFAULT '' AFTER `requirement`;

/*迭代16 2022-11-24  新增*/
ALTER TABLE `zt_effort` ADD COLUMN `source` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '数据来源（0：用户创建 1：制版创建）' after `deleted`,
               ADD COLUMN `buildID` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '制版id 默认0' after `source`,
               ADD COLUMN `consumedID` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '工作量ID' after `buildID`;

/*迭代16 2022-11-24 更新字段类型*/
ALTER TABLE  `zt_demand` MODIFY COLUMN lastDealDate datetime ;
ALTER TABLE  `zt_problem`  MODIFY COLUMN lastDealDate datetime ;

/**迭代16 2022-11-24 测试单*/
ALTER TABLE `zt_testtask` MODIFY COLUMN `name`  char(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称';

/*迭代16 2022-11-24 制版*/
ALTER TABLE `zt_build` MODIFY COLUMN `filePath`  text   NOT NULL  COMMENT '测试地址',
MODIFY COLUMN `svnPath`  text     COMMENT 'svn地址',
MODIFY COLUMN `scmPath`  text   NOT NULL COMMENT 'git地址';

-- ----------------------------
--迭代16 2022-11-24 tongyanqi 问题单添加清总审批人
-- ----------------------------
ALTER TABLE `zt_problem`
    ADD COLUMN `approverName`  varchar(50) NOT NULL DEFAULT '' COMMENT '清总审批人' AFTER `ifOverDate`;

-- ----------------------------
-- 迭代16 2022-11-24 shixuyang 需求任务添加清总审批人
-- ----------------------------
ALTER TABLE `zt_requirement` ADD `approverName` varchar(50) NULL COMMENT '清总审批人';
ALTER TABLE `zt_modify` ADD `approverName` varchar(50) NULL COMMENT '金信审批人';
ALTER TABLE `zt_modifycncc` ADD `approverName` varchar(50) NULL COMMENT '清总审批人';
-- ----------------------------
--迭代16 2022-11-24 xiangyaong
-- ----------------------------
ALTER TABLE `zt_demandcollection` ADD COLUMN `scheme` MEDIUMTEXT COMMENT '解决方案';
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'setDemandcollectionMail', '{\"mailTitle\":\"\\u3010\\u901a\\u77e5\\u3011\\u60a8\\u6709\\u4e00\\u4e2a\\u3010\\u9700\\u6c42\\u6536\\u96c6\\u3011\\u4e0a\\u7ebf\\u6210\\u529f\\uff0c\\u8bf7\\u53ca\\u65f6\\u767b\\u5f55\\u7814\\u53d1\\u8fc7\\u751f\\u5e73\\u53f0\\u8fdb\\u884c\\u67e5\\u770b\",\"variables\":[\"\\u9700\\u6c42\\u6536\\u96c6\\u5355\"],\"mailContent\":\"<span style=\\\"font-size:14px;background-color:#FFFFFF;\\\"><strong>\\u8bf7\\u8fdb\\u5165\\u3010\\u9700\\u6c42\\u6536\\u96c6\\u3011\\uff0c\\u67e5\\u770b\\u4fe1\\u606f\\u8be6\\u60c5\\u4f7f\\u7528\\uff0c\\u5177\\u4f53\\u4fe1\\u606f\\u5982\\u4e0b\\uff1a<\\/strong><span><\\/span><\\/p>\"}');

-- ----------------------------
--数据获取新增打回人 2022-11-24 陈东程
-- ----------------------------
ALTER TABLE `zt_infoqz` add `approverName` varchar(32) default NULL comment '审批人姓名';

---2022-12-1 主场支持 值班日志表新增日志推送状态字段
ALTER TABLE `zt_resident_support_work`
    ADD `pushStatus` tinyint default 0 comment '0暂未推送，1推送成功，2推送失败' AFTER `editedDate`;





/* 迭代18-数据使用表-shixuyang*/
CREATE TABLE `zt_datause` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `code` varchar(32) DEFAULT NULL COMMENT '数据使用单编号',
                              `infoId` int(11) DEFAULT NULL COMMENT '数据获取ID',
                              `infoCode` varchar(32) DEFAULT NULL COMMENT '数据获取单号',
                              `type` varchar(30) DEFAULT NULL COMMENT '数据类型',
                              `isJk` varchar(30) DEFAULT NULL COMMENT '是否进入金科',
                              `desensitizeType` varchar(30) DEFAULT NULL COMMENT '脱敏类型',
                              `useDeadline` datetime DEFAULT NULL COMMENT '使用期限至',
                              `isDeadline` tinyint(1) DEFAULT NULL COMMENT '是否长期使用：1-是；2-否',
                              `isDesensitize` tinyint(1) DEFAULT NULL COMMENT '是否需要脱敏，1=是，0=否',
                              `createdBy` char(30) DEFAULT NULL COMMENT '创建人',
                              `createdDate` datetime DEFAULT NULL COMMENT '创建时间人',
                              `desc` mediumtext DEFAULT NULL COMMENT '数据获取摘要',
                              `reason` mediumtext DEFAULT NULL COMMENT '数据获取原因',
                              `source` varchar(32) DEFAULT NULL COMMENT '数据来源：info-金信交付-数据获取，infoqz-清总交付-数据获取',
                              `delayedBy` varchar(30) DEFAULT NULL COMMENT '由谁延期',
                              `delayedDate` datetime DEFAULT NULL COMMENT '延期日期',
                              `destroyedBy` varchar(30) DEFAULT NULL COMMENT '由谁销毁',
                              `destroyedDate` datetime DEFAULT NULL COMMENT '销毁日期',
                              `reviewedBy` varchar(30) DEFAULT NULL COMMENT '由谁复核',
                              `reviewedDate` datetime DEFAULT NULL COMMENT '复核日期',
                              `delayReason` text DEFAULT NULL COMMENT '延期原因',
                              `delayDeadline` datetime DEFAULT NULL COMMENT '延期截止日期',
                              `destroyedReason` text DEFAULT NULL COMMENT '销毁原因',
                              `actualEndTime` datetime DEFAULT NULL COMMENT '实际结束时间',
                              `status` varchar(32) DEFAULT NULL COMMENT '状态',
                              `dealUser` varchar(1024) DEFAULT NULL COMMENT '待处理人',
                              `reviewStage` varchar(8) DEFAULT NULL COMMENT '审批阶段',
                              `changeVersion` int(8) DEFAULT NULL COMMENT '审批版本',
                              `destroyedDeal` varchar(64) DEFAULT NULL COMMENT '待销毁执行人',
                              `reviewedDeal` varchar(64) DEFAULT NULL COMMENT '待复核人',
                              `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除；1-删除',
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



/* 迭代18-等待阅览表-shixuyang*/
CREATE TABLE `zt_toread` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `objectType` varchar(32) DEFAULT NULL COMMENT '所属模块',
                             `objectId` int(11) DEFAULT NULL COMMENT '所属模块id',
                             `status` varchar(32) DEFAULT NULL COMMENT '状态',
                             `dealUser` varchar(1024) DEFAULT NULL COMMENT '待谁处理',
                             `extra` text DEFAULT NULL COMMENT '额外描述',
                             `comment` text DEFAULT NULL COMMENT '备注',
                             `dealDate` datetime DEFAULT NULL COMMENT '操作时间',
                             `messageType` varchar(32) DEFAULT NULL COMMENT '消息类型',
                             `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除；1-删除',
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- 2022-11-03 chendongcheng 金信数据获取
-- ----------------------------
ALTER TABLE `zt_info` ADD `dataManagementCode` varchar(32) default NULL COMMENT '数据管理code';
ALTER TABLE `zt_info` ADD `fetchResult` tinyint(4) default NULL COMMENT '获取结果';
ALTER TABLE `zt_info` ADD `isJinke` tinyint(4) default 2 COMMENT '是否进入金科2否1是';
ALTER TABLE `zt_info` ADD `desensitizationType` tinyint(4) DEFAULT NULL COMMENT '脱敏类型';
ALTER TABLE `zt_info` ADD `isDeadline` tinyint(4) DEFAULT 2 COMMENT '是否有【使用期限至】，2自定义1长期';
ALTER TABLE `zt_info` ADD `deadline` datetime DEFAULT NULL COMMENT '使用期限至';
ALTER TABLE `zt_infoqz` ADD `dataManagementCode` varchar(32) default NULL COMMENT '数据管理code';
ALTER TABLE `zt_infoqz` ADD `fetchResult` tinyint(4) default NULL COMMENT '获取结果';
ALTER TABLE `zt_infoqz` ADD `isJinke` tinyint(4) default 2 COMMENT '是否进入金科，2否1是';
ALTER TABLE `zt_infoqz` ADD `desensitizationType` tinyint(4) DEFAULT NULL COMMENT '脱敏类型';
ALTER TABLE `zt_infoqz` ADD `isDeadline` tinyint(4) DEFAULT 2 COMMENT '是否有【使用期限至】，2自定义1长期';
ALTER TABLE `zt_infoqz` ADD `deadline` datetime DEFAULT NULL COMMENT '使用期限至';

ALTER TABLE `zt_info` MODIFY COLUMN `planBegin` datetime NULL;
ALTER TABLE `zt_info` MODIFY COLUMN `planEnd` datetime NULL;
ALTER TABLE `zt_info` MODIFY COLUMN `actualBegin` datetime NULL;
ALTER TABLE `zt_info` MODIFY COLUMN `actualEnd` datetime NULL;
ALTER TABLE `zt_infoqz` MODIFY COLUMN `planBegin` datetime NULL;
ALTER TABLE `zt_infoqz` MODIFY COLUMN `planEnd` datetime NULL;


ALTER TABLE `zt_infoqz` ADD requiredReviewNode varchar(255) DEFAULT '' COMMENT '退回页面选择的需要审核的节点';
ALTER TABLE `zt_infoqz` ADD `revertReason` TEXT DEFAULT NULL COMMENT '内部退回原因，枚举值可配置';

-- 平台需求收集1307
ALTER TABLE `zt_modify` ADD `isDiskDelivery` tinyint(4) default 0 comment '是否光盘交付,0:不是，1：是';
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn','modify','apiDealUserList','userAccount','litianzi','1');


ALTER TABLE `zt_info` MODIFY COLUMN classify varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `zt_infoqz` MODIFY COLUMN classify varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `zt_infoqz` ADD `isSyncDestroyed` tinyint(4) default 0 comment '0：未同步，1：已同步销毁，2：已同步暂未销毁，3：已同步不需要销毁';

/* 迭代18-测试部节点处理人-shixuyang*/
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'datamanagement', 'testDepartReviewer', 'yangjianxu', '杨建旭', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'datamanagement', 'testDepartReviewer', 'huangjun', '黄俊', '0');
INSERT INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'datamanagement', 'testDepartReviewer', 'houmanli', '侯漫丽', '0');

/*2022-11-14 liuyuhan 数据管理-延期申请中增加【延期终止】状态 */
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','delaystopped');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','delaystopped');

/* 2022-11-21 平台需求收集1335-shixuyang*/
ALTER TABLE `zt_modify` ADD `jxLevel` varchar(30) default NULL COMMENT '外部变更级别';
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'jxLevelList', '1', '一级', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'jxLevelList', '2', '二级', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'jxLevelList', '3', '三级', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'secondLineReviewList', 'zhangyun', '张蕴(总行)', '0');

/*20221201修改字段类型*/
ALTER TABLE `zt_build` MODIFY COLUMN `plateName`  text  ;


-- ----------------------------
-- 2022-12-08 tongyanqi 年度计划增加字段
-- ----------------------------
ALTER TABLE `zt_projectplan`
ADD COLUMN `workloadBase`  float(11,1)  NOT NULL DEFAULT '0' COMMENT '本年度计划工作量（不含成都分公司）' AFTER `workload`,
ADD COLUMN `workloadChengdu`  float(11,1)  NOT NULL DEFAULT '0' COMMENT '本年度成都分公司计划工作量' AFTER `workloadBase`,
ADD COLUMN `nextYearWorkloadBase`  float(11,1) NOT NULL DEFAULT '0' COMMENT '下年度计划工作量（不含成都分公司）' AFTER `workloadChengdu`,
ADD COLUMN `nextYearWorkloadChengdu`   float(11,1)  NOT NULL DEFAULT '0' COMMENT '下年度成都分公司计划工作量' AFTER `nextYearWorkloadBase`;

/*2022-12-15 主场支持新增字段*/
ALTER TABLE `zt_resident_support_work`
ADD COLUMN `type`  tinyint(4)  NOT NULL DEFAULT '0' COMMENT '值班类型' AFTER `templateId`,
ADD COLUMN `subType`  tinyint(4)  NOT NULL DEFAULT '0' COMMENT '值班子类' AFTER `type`;







-- ----------------------------
--迭代二十-已发布组件 2022-11-28 shixuyang
-- ----------------------------
CREATE TABLE `zt_component_release` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `name` varchar(60) DEFAULT NULL COMMENT '组件名称',
                                        `latestVersion` varchar(255) DEFAULT NULL COMMENT '公共组件-最新版本',
                                        `recommendVersion` varchar(255) DEFAULT NULL COMMENT '第三方组件-推荐版本',
                                        `type` varchar(32) DEFAULT NULL COMMENT '组件类型：thirdParty-第三方组件；public-公共组件',
                                        `level` varchar(32) DEFAULT NULL COMMENT '级别：company-公司级；dept-部门级',
                                        `chineseClassify` varchar(32) DEFAULT NULL COMMENT '第三方组件-中文分类',
                                        `englishClassify` varchar(32) DEFAULT NULL COMMENT '第三方组件-英文分类',
                                        `licenseType` varchar(40) DEFAULT NULL COMMENT '第三方组件-许可证类型',
                                        `functionDesc` text DEFAULT NULL COMMENT '公共组件-功能说明',
                                        `location` varchar(255) DEFAULT NULL COMMENT '公共组件-获取位置',
                                        `maintainer` varchar(30) DEFAULT NULL COMMENT '公共组件-联系人',
                                        `maintainerDept` int(11) DEFAULT NULL COMMENT '公共组件-维护部门',
                                        `developLanguage` varchar(8) DEFAULT NULL COMMENT '开发语言',
                                        `status` varchar(32) DEFAULT NULL COMMENT '状态',
                                        `usedNum` int(6) DEFAULT NULL COMMENT '使用数量',
                                        `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除；1-删除',
                                        `componentId` int(11) DEFAULT NULL COMMENT '组件引用申请Id',
                                        `category` varchar(32) DEFAULT NULL COMMENT '公共组件-类别',
                                        PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
--迭代二十-已发布组件-版本 2022-11-28 shixuyang
-- ----------------------------
CREATE TABLE `zt_component_version` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `version` varchar(255) DEFAULT NULL COMMENT '组件版本',
                                        `updatedDate` datetime DEFAULT NULL COMMENT '更新日期',
                                        `usedNum` int(6) DEFAULT NULL COMMENT '使用数量',
                                        `desc` text DEFAULT NULL COMMENT '版本说明',
                                        `vulnerabilityLevel` varchar(8) DEFAULT NULL COMMENT '漏洞级别',
                                        `componentReleaseId` int(11) DEFAULT NULL COMMENT '组件Id',
                                        `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除；1-删除',
                                        PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
--迭代二十-已发布组件-台账 2022-11-28 shixuyang
-- ----------------------------
CREATE TABLE `zt_component_account` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `appId` varchar(50) DEFAULT NULL COMMENT '系统Id',
                                        `productId` varchar(50) DEFAULT NULL COMMENT '产品Id',
                                        `productVersionId` varchar(50) DEFAULT NULL COMMENT '产品版本Id',
                                        `comment` varchar(100) DEFAULT NULL COMMENT '备注',
                                        `componentReleaseId` int(11) DEFAULT NULL COMMENT '组件Id',
                                        `componentVersionId` int(11) DEFAULT NULL COMMENT '组件版本Id',
                                        `deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除；1-删除',
                                        PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
--迭代二十-自定义类别 2022-11-28 shixuyang
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'publicframework', '公共技术框架', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'publicproduct', '公共技术产品', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'shelltool', '脚本及工具类', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'publictest', '公共测试组件', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'codeexample', '代码样例', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'engineeringexample', '工程样例', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'categoryList', 'solution', '解决方案', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'thirdcategoryList', 'basic', '基础软件', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'thirdcategoryList', 'technology', '技术框架', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'thirdStatusList', 'introduce', '引入', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'thirdStatusList', 'signout', '退出', '0');
-- ----------------------------
--迭代二十-增加发布类别 2022-11-28 shixuyang
-- ----------------------------
ALTER TABLE zt_component ADD COLUMN category varchar(32) DEFAULT NULL COMMENT '类别';
-- ----------------------------
--迭代二十-自定义类别 2022-11-28 shixuyang
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'publishStatusList', 'construct', '在建', '0');
-- ----------------------------
--迭代二十-增加发布状态 2022-11-28 shixuyang
-- ----------------------------
ALTER TABLE zt_component ADD COLUMN publishStatus varchar(32) DEFAULT NULL COMMENT '发布状态';
-- ----------------------------
--迭代二十-增加纳入现有组件 2022-11-29 shixuyang
-- ----------------------------
ALTER TABLE `zt_reviewnode` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','incorporate','delaystopped');
ALTER TABLE `zt_reviewer` MODIFY COLUMN `status` enum('wait','pass','reject','pending','ignore','externalsendback','approvesuccess','closed','suspend','feedbacked','firstpassed','finalpassed','syncfail','syncsuccess','jxsyncfail','jxsyncsuccess','feedbacksuccess','feedbackfail','secondlineapproved','appoint','confirming','incorporate','delaystopped');
-- ----------------------------
--迭代二十-中文分类 2022-12-7 shixuyang
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'chineseClassifyList', 'frontlibrary', '前端库', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'englishClassifyList', 'webasset', 'Web Assets', '0');
-- ----------------------------
--迭代二十-内部接口 2022-12-19 shixuyang
-- ----------------------------
INSERT INTO `zt_entry` (`name`, `account`, `code`, `key`, `freePasswd`, `ip`, `desc`, `createdBy`, `createdDate`, `calledTime`, `editedBy`, `editedDate`, `deleted`) VALUES ('内部对接', 'admin', 'jinke', 'e205432c0214606a952122437d149129', '0', '*', '', 'admin', '2022-12-19 17:17:30', 0, 'admin', '2022-12-19 15:05:02', '0');/*2022-11-30 迭代二十清总软件著作权管理表*/
-- ----------------------------
--平台需求收集-1457 2022-12-21 shixuyang
-- ----------------------------
ALTER TABLE `zt_outwarddelivery` ADD `manufacturer` varchar(255) default null comment '厂商支持人员';
ALTER TABLE `zt_outwarddelivery` ADD `manufacturerConnect` varchar(255) default null comment '厂商支持人员联系方式';
-- ----------------------------
--uat新增自定义组件和版本 2022-12-22 shixuyang
-- ----------------------------
ALTER TABLE `zt_component_account` ADD `customComponent` varchar(255) default null comment '自定义组件';
ALTER TABLE `zt_component_account` ADD `customComponentVersion` varchar(255) default null comment '自定义组件版本';
ALTER TABLE `zt_component_account` ADD `type` varchar(32) default null comment '组件台账类型：third-第三方，public-公共';





create table zt_copyrightqz
(
    id                      int auto_increment comment 'id'
        primary key,
    code                    varchar(30)                 null comment '单号',
    emisCode                varchar(30)                 null comment '产品emiscode',
    copyrightRegistrationId varchar(255)                null comment '著作权登记唯一标识（清总反馈）',
    productenrollId         bigint                      null comment '关联产品登记id',
    productenrollCode       varchar(255)                null comment '关联产品登记号',
    fullname                varchar(255)                null comment '软件全称',
    shortName               varchar(255)                null comment '软件简称',
    version                 varchar(255)                null comment '软件版本号',
    `system`                varchar(255)                null comment '业务系统',
    descType                varchar(30)                 null comment '软件作品说明选择(0-原创；1-修改(含翻译软件、合成软件))',
    description             text                        null comment '软件作品说明(多行文本框)',
    devFinishedTime         datetime                    null comment '开发完成日期',
    publishStatus           varchar(30)                 null comment '发表状态（0：已发表，1：未发表）',
    firstPublicTime         datetime                    null comment '首次发表日期',
    firstPublicCountry      varchar(255)                null comment '首次发表国家',
    firstPublicPlace        varchar(255)                null comment '首次发表地点',
    devMode                 varchar(30)                 null comment '开发方式（0：独立开发，1：合作开发，2：委托开发，3：下达任务开发）',
    rightObtainMethod       varchar(30)                 null comment '权利取得方式（0：原始取得，1：继受取得）',
    isRegister              varchar(30)                 null comment '软件著作权是否已登记（0：是，1：否）',
    oriRegisNum             varchar(255)                null comment '原登记号',
    isOriRegisNumChanged    varchar(30)                 null comment '原登记是否做过变更或补充（0：是，1：否）',
    proveNum                varchar(255)                null comment '变更或补充证明编号',
    rightRange              varchar(30)                 null comment '权利范围（0：部分，1：全部）',
    sourceProgramAmount     text                        null comment '源程序量',
    identityMaterial        varchar(255)                null comment '软件鉴别材料（0：一般交存，1：例外交存）',
    generalDeposit          varchar(255)                null comment '一般交存(0：一种文档，1：多种文档）(多个英文逗号间隔)',
    generalDepositType      varchar(255)                null comment '一般交存文档种类',
    exceptionalDeposit      varchar(255)                null comment '例外交存（0：使用黑色宽斜线覆盖，1：前10页和任选连续50页，2：目标程序的连续的前、后各30页和源程序任选连续的20页）(多个英文逗号间隔)',
    pageNum                 varchar(255)                null comment '例外交存，页码为',
    softwareType            varchar(30)                 null comment '软件分类（0：应用软件，1：嵌入式软件，2:中间件，3：操作系统）',
    devHardwareEnv          text                        null comment '开发的硬件环境',
    opsHardwareEnv          text                        null comment '运行的硬件环境',
    devOS                   text                        null comment '开发该软件的操作系统',
    devEnv                  text                        null comment '软件开发环境/开发工具',
    operatingPlatform       text                        null comment '该软件的运行平台/操作系统',
    operationSupportEnv     text                        null comment '软件运行支撑环境/支持软件',
    devLanguage             varchar(255)                null comment '编程语言',
    devPurpose              text                        null comment '开发目的',
    industryOriented        text                        null comment '面向领域行业',
    mainFunction            text                        null comment '软件的主要功能',
    techFeatureType         varchar(255)                null comment '软件的技术特点',
    techFeature             varchar(255)                null comment '软件的技术特点（文本）',
    others                  text                        null comment '其他',
    applicant               varchar(30)                 null comment '申请人/创建人',
    applicantDept           int                         null comment '申请人/创建人所在部门',
    createdTime             datetime                    null comment '创建时间',
    editedBy                varchar(30)                 null comment '编辑人',
    editedTime              datetime                    null comment '编辑时间',
    status                  varchar(32)                 null comment '状态',
    dealUser                varchar(1025)               null comment '待处理人',
    reviewStage             varchar(8)                  null comment '评审阶段',
    changeVersion           int(8)                      null comment '变更版本',
    deleted                 enum ('0', '1') default '0' not null comment '是否删除：0-未删除；1-删除',
    outsideReviewResult     varchar(30)                 null comment '外部审批结果（pass-通过, reject-不通过）',
    reason                  varchar(255)                null comment '退回原因/外部审批意见',
    approverName            varchar(30)                 null comment '退回人/外部审批人',
    outsideReviewTime       datetime                    null comment '外部审批时间',
    synStatus               int             default 0   null comment '0-未推送；1-推送成功；2-推送失败',
    synFailedTimes          int             default 0   null comment '推送失败的次数',
    synFailedReason         varchar(255)                null comment '同步清总失败的原因',
    synDate                 datetime                    null comment '同步清总时间'
)
    comment '清总软件著作权管理';

/*2022-12-12 liuyuhan 迭代二十自主软件著作权管理表*/
create table zt_copyright
(
    id                   int auto_increment comment 'id'
        primary key,
    code                 varchar(30)                 null comment '单号',
    modifyId             bigint                      null comment '关联生产变更id',
    modifyCode           varchar(255)                null comment '关联生产变更号',
    fullname             varchar(255)                null comment '软件全称',
    softwareInfo         text                        null comment '软件全称fullname软件简称shortName软件版本号version,json保存',
    `system`             varchar(255)                null comment '业务系统',
    descType             varchar(30)                 null comment '软件作品说明选择(0-原创；1-修改(含翻译软件、合成软件))',
    description          text                        null comment '软件作品说明(多行文本框)',
    devFinishedTime      datetime                    null comment '开发完成日期',
    publishStatus        varchar(30)                 null comment '发表状态（0：已发表，1：未发表）',
    firstPublicTime      datetime                    null comment '首次发表日期',
    firstPublicCountry   varchar(255)                null comment '首次发表国家',
    firstPublicPlace     varchar(255)                null comment '首次发表地点',
    devMode              varchar(30)                 null comment '开发方式（0：独立开发，1：合作开发，2：委托开发，3：下达任务开发）',
    rightObtainMethod    varchar(30)                 null comment '权利取得方式（0：原始取得，1：继受取得）',
    isRegister           varchar(30)                 null comment '软件著作权是否已登记（0：是，1：否）',
    oriRegisNum          varchar(255)                null comment '原登记号',
    isOriRegisNumChanged varchar(30)                 null comment '原登记是否做过变更或补充（0：是，1：否）',
    proveNum             varchar(255)                null comment '变更或补充证明编号',
    rightRange           varchar(30)                 null comment '权利范围（0：部分，1：全部）',
    sourceProgramAmount  text                        null comment '源程序量',
    identityMaterial     varchar(255)                null comment '软件鉴别材料（0：一般交存，1：例外交存）',
    generalDeposit       varchar(255)                null comment '一般交存(0：一种文档，1：多种文档）(多个英文逗号间隔)',
    generalDepositType   text                        null comment '一般交存文档种类',
    exceptionalDeposit   varchar(255)                null comment '例外交存（0：使用黑色宽斜线覆盖，1：前10页和任选连续50页，2：目标程序的连续的前、后各30页和源程序任选连续的20页）(多个英文逗号间隔)',
    pageNum              varchar(255)                null comment '例外交存，页码为',
    softwareType         varchar(30)                 null comment '软件分类（0：应用软件，1：嵌入式软件，2:中间件，3：操作系统）',
    devHardwareEnv       text                        null comment '开发的硬件环境',
    opsHardwareEnv       text                        null comment '运行的硬件环境',
    devOS                text                        null comment '开发该软件的操作系统',
    devEnv               text                        null comment '软件开发环境/开发工具',
    operatingPlatform    text                        null comment '该软件的运行平台/操作系统',
    operationSupportEnv  text                        null comment '软件运行支撑环境/支持软件',
    devLanguage          varchar(255)                null comment '编程语言',
    devPurpose           text                        null comment '开发目的',
    industryOriented     text                        null comment '面向领域行业',
    mainFunction         text                        null comment '软件的主要功能',
    techFeatureType      varchar(255)                null comment '软件的技术特点',
    techFeature          text                        null comment '软件的技术特点（文本）',
    others               text                        null comment '其他',
    buildDept            varchar(255)                null comment '承建部门,平台中【系统管理】中【承建单位】',
    createdBy            varchar(30)                 null comment '申请人/创建人',
    createdDept          int                         null comment '申请人/创建人所在部门',
    createdTime          datetime                    null comment '创建时间',
    editedBy             varchar(30)                 null comment '编辑人',
    editedTime           datetime                    null comment '编辑时间',
    status               varchar(32)                 null comment '状态',
    dealUser             varchar(1025)               null comment '待处理人',
    reviewStage          varchar(8)                  null comment '评审阶段',
    changeVersion        int(8)                      null comment '变更版本',
    deleted              enum ('0', '1') default '0' not null comment '是否删除：0-未删除；1-删除'
)
    comment '自主软件著作权管理';

/*2022-12-26 liuyuhan 迭代二十自主软件著作权 产创审核人*/
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'copyright', 'innovateReviewerList', 'zhangyun', '张蕴（总行）', '0');

/*2022-12-29  迭代二十缺陷管理*/
CREATE TABLE `zt_defect` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `code` varchar(50) COMMENT '单号',
                             `resolution` varchar(30) COMMENT '解决方案',
                             `resolvedBuild` varchar(30)COMMENT '解决版本',
                             `resolvedDate` datetime  COMMENT '解决日期',
                             `dealUser` varchar(50)  COMMENT '指派给',
                             `analysis` text default NULL COMMENT '问题分析',
                             `linkProduct` text  COMMENT '涉及产品',
                             `ifTest` varchar(50) DEFAULT NULL COMMENT '是否集中测试',
                             `dealSuggest` varchar(30)  COMMENT '处置建议',
                             `dealComment` mediumtext  COMMENT '处置说明',
                             `changeDate` datetime  COMMENT '计划变更日期',
                             `submitChangeDate` datetime  COMMENT '计划提交变更日期',
                             `EditorImpactscope` mediumtext  COMMENT '影响范围',
                             `ifHisIssue` char(5) DEFAULT NULL COMMENT '是否历史遗留问题',
                             `app` int unsigned  COMMENT '所属系统',
                             `product` int unsigned  COMMENT '所属产品',
                             `project` int unsigned  COMMENT '所属项目',
                             `CBPproject` varchar(255) DEFAULT NULL COMMENT '所属CBP项目',
                             `projectManager` varchar(30)  COMMENT '项目经理',
                             `title` varchar(255) DEFAULT NULL COMMENT '缺陷标题',
                             `reportUser` varchar(50) DEFAULT NULL COMMENT '报告人',
                             `reportDate` datetime DEFAULT NULL COMMENT '报告日期',
                             `pri` char(5)  COMMENT '问题优先级',
                             `issues` text  COMMENT '问题描述',
                             `type` char(30)  COMMENT '缺陷类型',
                             `childType` char(30)  COMMENT '缺陷子类',
                             `status` char(30)  COMMENT '流程状态',
                             `frequency` char(30)  COMMENT '缺陷频次',
                             `developer` char(30)  COMMENT '开发人员',
                             `tester` char(30)  COMMENT '测试工程师',
                             `testOwner` char(30)  COMMENT '测试经理',
                             `rounds` char(50)  COMMENT '轮次',
                             `testEnvironment` char(30)  COMMENT '测试环境',
                             `dept` char(30)  COMMENT '所属部门',
                             `verification` char(30)  COMMENT '缺陷验证结果',
                             `testrequestId` char(255)  COMMENT '关联测试申请',
                             `testrequestCode` char(255)  COMMENT '关联测试申请code编码',
                             `productenrollId` char(255)  COMMENT '关联产品登记',
                             `productenrollCode` char(255)  COMMENT '关联产品登记code编码',
                             `modifycnccId` char(255)  COMMENT '关联生产变更',
                             `testrequestCreatedBy` char(255)  COMMENT '测试申请单创建人',
                             `productenrollCreatedBy` char(255)  COMMENT '产品登记单创建人',
                             `bugId` char(30)  COMMENT '相关bugID',
                             `testType` char(30)  COMMENT '测试类型',
                             `severity` int  COMMENT '问题严重性',
                             `reviewer` mediumtext DEFAULT NULL COMMENT '审批人',
                             `reviewComment` mediumtext DEFAULT NULL COMMENT '审批意见',
                             `reviewDate` datetime DEFAULT NULL COMMENT '审批时间',
                             `feedbackNum` int DEFAULT NULL COMMENT '反馈次数',
                             `createdBy` char(30) NOT NULL COMMENT '由谁创建',
                             `createdDate` datetime NOT NULL COMMENT '创建时间',
                             `confirmedBy` char(30) DEFAULT NULL COMMENT '由谁确认',
                             `confirmedDate` datetime DEFAULT NULL COMMENT '确认时间',
                             `dealedBy` char(30) DEFAULT NULL COMMENT '由谁处理',
                             `dealedDate` datetime DEFAULT NULL COMMENT '处理时间',
                             `submitedBy` char(30) DEFAULT NULL COMMENT '由谁提交',
                             `cc` char(30) DEFAULT NULL COMMENT '通知人员',
                             `submitedDate` datetime DEFAULT NULL COMMENT '提交时间',
                             `activedBy` char(30) DEFAULT NULL COMMENT '由谁激活',
                             `activedDate` datetime DEFAULT NULL COMMENT '激活时间',
                             `deleted` varchar(5) DEFAULT '0',
                             `source` tinyint(1) DEFAULT NULL COMMENT '数据来源 1:内部bug转缺陷 2：总中心uat同步',
                             `remark` text DEFAULT NULL COMMENT '本次操作备注',
                             `testCase` text DEFAULT NULL COMMENT '测试案例',
                             `testAdvice` text DEFAULT NULL COMMENT '测试建议',
                             `Dropdown_suspensionreason` text DEFAULT NULL COMMENT '挂起原因',
                             `uatId` varchar(30) DEFAULT NULL COMMENT '清总UAT同步过来的ID',
                             `syncStatus` int DEFAULT 0 COMMENT '本次已同步状态 1已同步',
                             `testrequestGiteeId` char(255)  COMMENT '关联测试申请gitee单号',
                             `productenrollGiteeId` char(255)  COMMENT '关联产品登记gitee单号',
                             `modifycnccGiteeId` char(255)  COMMENT '关联生产变更gitee单号',
                             `outwarddeliveryId` char(255)  COMMENT '关联对外交付',
                             `changeStatus` char(255)  DEFAULT NULL COMMENT '外部审批结果',
                             `approverName` char(255)  COMMENT '外部审批人',
                             `approverComment` char(255)  COMMENT '外部审批意见',
                             `sampleVersionNumber` char(255)  COMMENT '样品版本号',
                             `approverDate` datetime  DEFAULT NULL COMMENT '外部审批时间',
                             `realtedApp` varchar(30)  DEFAULT NULL COMMENT '涉及业务系统',
                             `progress` text DEFAULT NULL COMMENT '当前进展',
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `zt_testingrequest`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

ALTER TABLE `zt_modifycncc`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

ALTER TABLE `zt_productenroll`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

ALTER TABLE `zt_outwarddelivery`
    ADD `leaveDefect` varchar(255) DEFAULT NULL COMMENT '遗留缺陷',
    ADD `fixDefect` varchar(255) DEFAULT NULL COMMENT '修复缺陷';

/*2022-12-29  接口配置*/
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'defectFeedbackUrl', 'http://plcm.cncc.cn:30080/api/project/app/osc/yinqing_jinke_sync/webhooks/test-defect-receive');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'defectReFeedbackUrl', 'http://plcm.cncc.cn:30080/api/project/app/osc/yinqing_jinke_sync/webhooks/test-defect-receive-feedback');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushDefectAppId', 'jinke');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushDefectAppSecret', '482733936f2e45eaba0cc5768e5541eb');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'pushDefectEnable', 'enable');

/*2022-12-06  新增*/
ALTER TABLE `zt_product` ADD COLUMN `piplinePath` text DEFAULT NULL COMMENT '流水线地址' after `deleted`,
               ADD COLUMN `skipBuild` varchar(5) DEFAULT NULL COMMENT '跳过制版' after `piplinePath`;

/*2022-12-07  新增*/
ALTER TABLE `zt_build` ADD COLUMN `code` varchar(45) DEFAULT  NULL COMMENT '产品编号' after `product`;

/*2022-12-20 修改字段类型*/
ALTER TABLE `zt_build` MODIFY COLUMN `taskName`  text;

/*2022-12-20  新增*/
ALTER TABLE `zt_project` ADD COLUMN `source` MEDIUMINT DEFAULT 0 NOT NULL COMMENT '数据来源（0：用户创建 1：系统创建）' after `deleted`;

/*2022-12-20 系统创建阶段更新来源*/
update zt_project set source ='1' where name like '%二线研发管理%' and type ='stage' and deleted = '0';
update zt_project set source ='1' where parent in (select id from (select id from zt_project where name like '%二线研发管理%' and type ='stage' and deleted = '0') t1);

update zt_project set source ='1' where name like '%二线工单管理%' and type ='stage' and deleted = '0';
update zt_project set source ='1' where parent in (select id from (select id from zt_project where name like '%二线工单管理%' and type ='stage' and deleted = '0') t1);

/*2022-12-21 发布新增字段*/
ALTER TABLE `zt_release`
     ADD COLUMN `productVersion` varchar(255) COMMENT '产品版本' after `product`,
     ADD COLUMN `app` varchar(255)  COMMENT '应用系统' after `productVersion`;

/*2022-12-29  新增*/
ALTER TABLE `zt_secondorder` ADD COLUMN `desc` text DEFAULT NULL COMMENT '详细描述' after `deleted`;

-- ----------------------------
-- 迭代十九 2022-11-25 项目评审与变更功能修改
-- ----------------------------

-- ----------------------------
-- 迭代十九 2022-11-25 项目打基线相关表
-- ----------------------------

ALTER TABLE zt_baseline ADD objectType varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '对象标识' AFTER project;
ALTER TABLE zt_baseline ADD objectID INT(10) DEFAULT 0 NOT NULL COMMENT '对象ID' AFTER objectType;
ALTER TABLE zt_baseline ADD version MEDIUMINT DEFAULT 0 NOT NULL COMMENT '对象版本'  AFTER objectID;
CREATE INDEX objectType USING BTREE ON zt_baseline (objectType);
CREATE INDEX objectID USING BTREE ON zt_baseline (objectID);

-- ----------------------------
-- 迭代十九 2022-11-25 项目归档信息相关表
-- ----------------------------

CREATE TABLE `zt_archive` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `project` int(11) NOT NULL COMMENT '项目ID',
  `objectType` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '对象标识',
  `objectID` int(10) NOT NULL DEFAULT '0' COMMENT '对象ID',
  `version` mediumint(9) NOT NULL DEFAULT '0' COMMENT '对象版本',
  `svnUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'svn路径',
  `svnVersion` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'svn版本',
  `createdBy` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `createdTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否删除 0-有效 1已删除',
  PRIMARY KEY (`id`),
  KEY `objectType` (`objectType`) USING BTREE,
  KEY `objectID` (`objectID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- 迭代十九 2022-12-01 项目变更字段修改
-- ----------------------------
ALTER TABLE zt_change DROP INDEX reviewer;
ALTER TABLE zt_change MODIFY COLUMN reviewer TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '节点评审人';

-- ----------------------------
-- 迭代十九 2022-12-01 项目变更历史数据修复
-- ----------------------------
update zt_reviewnode set nodeCode = 'qa' where 1 and objectType = 'change' and stage = 1;
update zt_reviewnode set nodeCode = 'pm' where 1 and objectType = 'change' and stage = 2;
update zt_reviewnode set nodeCode = 'deptManage' where 1 and objectType = 'change' and stage = 3;
update zt_reviewnode set nodeCode = 'pdManage' where 1 and objectType = 'change' and stage = 4;
update zt_reviewnode set nodeCode = 'frameworkManage' where 1 and objectType = 'change' and stage = 5;
update zt_reviewnode set nodeCode = 'deptLeader' where 1 and objectType = 'change' and stage = 6;
update zt_reviewnode set nodeCode = 'owner' where 1 and objectType = 'change' and stage = 7;
update zt_reviewnode set nodeCode = 'baseline' where 1 and objectType = 'change' and stage = 8;
-- ----------------------------
-- 迭代十九 2022-12-01 审核节点控制是否显示
-- ----------------------------
ALTER TABLE zt_reviewnode ADD isShow TINYINT(1) DEFAULT 1 NOT NULL COMMENT '是否显示 1显示 2不显示主要是历史数据修复';

-- ----------------------------
-- 迭代十九 2022-12-09 工时表添加创建时间索引
-- ----------------------------

CREATE INDEX createdDate USING BTREE ON zt_consumed (createdDate);

-- ----------------------------
-- 迭代十九 2022-12-09 实际参会专家版本
-- ----------------------------

ALTER TABLE zt_review_meeting ADD realExportVersion TINYINT(1) DEFAULT 1 NULL COMMENT '实际参会专家版本  1升级前版本 2升级后版本' AFTER realExport;
ALTER TABLE zt_review_meeting_detail  ADD realExportVersion TINYINT(1) DEFAULT 1 NULL COMMENT '实际参会专家版本  1升级前版本 2升级后版本' AFTER realExport;


-- ----------------------------
-- 迭代十九 2022-12-19 日志新增部门字段, 历史部门字段数据处理
-- ----------------------------
ALTER TABLE `zt_effort` ADD COLUMN  `deptID` mediumint(9) NULL DEFAULT '' COMMENT '部门id';
update `zt_effort` inner join `zt_user` on `zt_effort`.`account` = `zt_user`.`account` set `zt_effort`.`deptID` = `zt_user`.`dept`;


-- ----------------------------
-- 迭代十九 2023-01-03 日志表部门ID字段历史数据处理
-- ----------------------------
UPDATE `zt_effort` SET `deptID` = 5 WHERE `account` = 'suwang' AND `date` < '2022-03-01';
UPDATE `zt_effort` SET `deptID` = 8 WHERE `account` = 'zhengyi' AND `date` < '2022-07-01';
UPDATE `zt_effort` SET `deptID` = 10 WHERE `account` = 'zhoulifeng' AND `date` < '2022-04-01';
UPDATE `zt_effort` SET `deptID` = 10 WHERE `account` = 'yangshun' AND `date` < '2022-09-01';
UPDATE `zt_effort` SET `deptID` = 10 WHERE `account` = 'zhongyuquan' AND `date` < '2022-04-11';
UPDATE `zt_effort` SET `deptID` = 7 WHERE `account` = 't_gaopeng' AND `date` < '2021-12-07';
UPDATE `zt_effort` SET `deptID` = 6 WHERE `account` = 't_guoyong' AND `date` < '2022-03-08';
UPDATE `zt_effort` SET `deptID` = 6 WHERE `account` = 'yuteng' AND `date` < '2021-12-06';
UPDATE `zt_effort` SET `deptID` = 2 WHERE `account` = 'pengxungui' AND `date` < '2022-02-15';

-- ----------------------------
-- 迭代十九 2023-01-05 项目评审打基线字段支持时分秒
-- ----------------------------
ALTER TABLE zt_review MODIFY COLUMN baseLineTime DATETIME DEFAULT '0000-00-00 00:00:00' NULL COMMENT '基线时间';
ALTER TABLE zt_change  MODIFY COLUMN baseLineTime DATETIME DEFAULT '0000-00-00 00:00:00' NULL COMMENT '基线时间';
-- ----------------------------
-- 迭代十九 2023-01-06 项目评审关闭时间字段默认值修改
-- ----------------------------
ALTER TABLE zt_review MODIFY COLUMN closeDate date DEFAULT '0000-00-00' NOT NULL COMMENT '关闭日期';
ALTER table zt_review MODIFY COLUMN closeTime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '关闭时间';
ALTER TABLE zt_review MODIFY COLUMN createdDate DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT '创建时间';

update zt_review set closeTime = concat(closeDate,' 00:00:00')  where  closeDate  is not NULL and closeDate   != '0000-00-00' and closeTime = '0000-00-00 00:00:00';

-- ----------------------------
-- 迭代十九 2023-01-05 项目评审打基线字段支持时分秒
-- ----------------------------
ALTER TABLE zt_consumed
ADD projectId INT(10) DEFAULT 0 NOT NULL COMMENT '关联的项目ID' AFTER id,
ADD deptId INT(10) NOT NULL COMMENT '部门ID' AFTER consumed;
CREATE INDEX objectType_objectId USING BTREE ON zt_consumed (objectType,objectID);
CREATE INDEX projectId USING BTREE ON zt_consumed (projectId);
update `zt_consumed` inner join `zt_user` on `zt_consumed`.`account` = `zt_user`.`account` set `zt_consumed`.`deptId` = `zt_user`.`dept`;



-- 2022-1-4 系统管理新增字段
ALTER TABLE `zt_application`
    ADD `isBasicLine` varchar(8) DEFAULT NULL COMMENT '是否属于基线',
    ADD `isSyncJinx` varchar(8) DEFAULT NULL COMMENT '是否同步金信';

-- 2022-1-5 关联二线工单
ALTER TABLE `zt_modify` ADD `secondorderId` varchar(128) DEFAULT '' COMMENT '二线工单id 逗号分隔';
ALTER TABLE `zt_info` ADD `secondorderId` varchar(128) DEFAULT '' COMMENT '二线工单id 逗号分隔';




-- ----------------------------
--需求收集-1527 2022-12-27 shixuyang
-- ----------------------------
ALTER TABLE `zt_modify` ADD `preChange` text default null comment '前置变更';
ALTER TABLE `zt_modify` ADD `postChange` text default null comment '后置变更';
ALTER TABLE `zt_modify` ADD `synImplement` text default null comment '同步实施';
ALTER TABLE `zt_modify` ADD `pilotChange` text default null comment '试点变更';
ALTER TABLE `zt_modify` ADD `promotionChange` text default null comment '推广变更';

-- ----------------------------
--需求收集1581-邮件抄送人配置 2023-1-4 shixuyang
-- ----------------------------
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'zhoujie', '周杰', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'sunjianzhi', '孙建智', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'zhanghailiang', '张海亮', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'songbingyu', '宋炳玉', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'tanxun', '谭勋', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'lishiming', '李适明', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'jinke', '金科', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'liangxiaoxing', '梁晓星', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'zhaojigang', '赵继刚', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'wangyong', '王永', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'fenghuan', '冯欢', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'component', 'carbonCopyList', 'hesong', '合松', '0');
-- ----------------------------
--需求收集-1601-增加二线工单 2023-1-5 shixuyang
-- ----------------------------
ALTER TABLE `zt_outwarddelivery` ADD `secondorderId` varchar(1024) default null comment '二线工单Id';
ALTER TABLE `zt_testingrequest` ADD `secondorderId` varchar(1024) default null comment '二线工单Id';
ALTER TABLE `zt_productenroll` ADD `secondorderId` varchar(1024) default null comment '二线工单Id';
ALTER TABLE `zt_modifycncc` ADD `secondorderId` varchar(1024) default null comment '二线工单Id';
ALTER TABLE `zt_infoqz` ADD `secondorderId` varchar(1024) default null comment '二线工单Id';

-- 平台需求收集 1544 chendongcheng
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '1', '生产中心>业务运行部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '2', '支付系统事业部>网络与平台部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '3', '支付系统事业部>系统一部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '4', '支付系统事业部>系统二部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '5', '支付系统事业部>运维系统部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '6', '支付系统事业部>生产调度部', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '11', '总行系统事业部>基础设施部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '12', '总行系统事业部>网络平台部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '13', '总行系统事业部>信息安全部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '14', '总行系统事业部>运行三部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '15', '总行系统事业部>运行二部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '16', '总行系统事业部>运行一部', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '101', '人事司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '102', '货金局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '103', '成方金信综合部', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '105', '科技司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '106', '办公厅', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '107', '研究局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '108', '党委宣传部', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '110', '派驻纪检组', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '111', '机关服务中心', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '112', '消保局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '113', '国际司', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '115', '机关党委', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '116', '条法司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '117', '征信局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '118', '巡视办', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '119', '会计司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '120', '离退休干部局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '121', '集中采购中心', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '122', '国库局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '123', '支付司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '124', '内审司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '125', '反洗钱局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '126', '调查统计司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '127', '支付结算司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '128', '宏观审慎局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '129', '调统司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '130', '金融稳定局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '131', '市场司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '132', '货政司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '133', '稳定局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '134', '会计财务司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '135', '机关服务局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'cooperateDepNameListList', '136', '金信中心', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '1', '中志公司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '2', '业务运行部', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '101', '人事司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '102', '货金局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '103', '成方金信综合部', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '105', '科技司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '106', '办公厅', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '107', '研究局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '108', '党委宣传部', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '110', '派驻纪检组', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '111', '机关服务中心', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '112', '消保局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '113', '国际司', '0');

INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '115', '机关党委', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '116', '条法司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '117', '征信局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '118', '巡视办', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '119', '会计司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '120', '离退休干部局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '121', '集中采购中心', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '122', '国库局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '123', '支付司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '124', '内审司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '125', '反洗钱局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '126', '调查统计司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '127', '支付结算司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '128', '宏观审慎局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '129', '调统司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '130', '金融稳定局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '131', '市场司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '132', '货政司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '133', '稳定局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '134', '会计财务司', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '135', '机关服务局', '0');
INSERT INTO zt_lang (`lang`, `module`, `section`, `key`, `value`, `system`) VALUES ('zh-cn', 'modify', 'judgeDepList', '136', '金信中心', '0');

-- 2023.1.10驻场支持-填写日志设置默认抄送人
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`)
VALUES('zh-cn', 'residentsupport', 'setCcList', '2', '', '1');
VALUES('zh-cn', 'residentsupport', 'setCcList', '2', '', '1');


/*迭代二十二 项目评审二期 工时表添加评审阶段 2023-01-09 wangjiurong*/
ALTER TABLE zt_consumed ADD reviewStage varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT '项目评审的评审阶段' AFTER account;
-- ----------------------------

--迭代二十二- 项目发布添加版本和处理人 2023-01-10 wangjiurong
-- ----------------------------
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

-- ----------------------------
--迭代二十二- 金信交付同步发布状态 2023-01-11 wangjiurong
-- ----------------------------
ALTER TABLE zt_modify ADD releaseSyncStatus TINYINT(1) DEFAULT 1 NULL COMMENT '是否同步到发布状态 1未同步 2已同步'  after `release`;
-- ----------------------------
--迭代二十二- 清总交付同步发布状态 2023-01-11 wangjiurong
-- ----------------------------
ALTER TABLE zt_modifycncc ADD releaseSyncStatus TINYINT(1) DEFAULT 1 NULL COMMENT '是否同步到发布状态 1未同步 2已同步'  after `release`;
-- ----------------------------
--迭代二十二- 项目变更操作基线时间 2023-01-11 wangjiurong
-- ----------------------------
ALTER TABLE zt_change ADD addBaseLineTime datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打基线时间'  after `baseLineTime`;
-- ----------------------------
--迭代二十二- 项目变更操作基线时间历史数据修正 2023-01-11 wangjiurong
-- ----------------------------
update zt_change Set addBaseLineTime = baseLineTime where 1 and baseLineTime is not null ;

-- ----------------------------
--迭代二十二- 添加基线类型  2023-02-14 wangjiurong
-- ----------------------------
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`) VALUES('zh-cn', 'cm', 'typeList', 'code', 'AC-代码基线', '1');

-- ----------------------------
-- 迭代22
-- 2023-1-29  xiangyang 度量三张报表
-- ----------------------------
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


-- ----------------------------
-- 迭代22
-- 2023-02-06  xiangyang 处理评审创建时间时分秒历史数据
-- ----------------------------
update zt_review as t1 left join zt_action as t2 on t1.id = t2.objectID set t1.createdDate = t2.date where t2.objectType = 'review' and t2.action = 'created';

-- 2023.1.18 迭代21看板管理功能建表sql liuyuhan
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


-- ----------------------------
--需求收集-1404-修改产品线字段长度 2023-1-9 shixuyang
-- ----------------------------
alter table zt_productenroll modify productLine varchar(255) default null;
alter table zt_outwarddelivery modify productLine varchar(255) null comment '产品线';

-- ----------------------------
--需求收集-996-新增评审信息字段 2023-1-11 shixuyang
-- ----------------------------
alter table zt_modifycncc add isReview tinyint(4) null comment '是否评审方案：2-否，1-是';
alter table zt_modifycncc add reviewReport varchar(255) null comment '方案评审报告';

-- ----------------------------
--需求收集-996-新增评审信息字段 2023-1-16 chendongchen
-- ----------------------------
alter table zt_modify add isReview tinyint(4) null comment '是否评审方案：2-否，1-是';
alter table zt_modify add isReviewPass tinyint(4) null comment '方案评审结果：2-未通过，1-通过';
alter table zt_modify add reviewReport varchar(255) null comment '方案评审报告';

-- ----------------------------
--需求收集-1587-增加记录已审批节点 2023-1-30 shixuyang
-- ----------------------------
alter table zt_outwarddelivery add approvedNode varchar(255) null comment '已审批节点';
alter table zt_outwarddelivery add modifyLevel varchar(2) default '1' comment '是否修改级别：1-否；2-是';


-- ----------------------------
-- 迭代22 2023-02-17  wangjiurong 其他常用服务配置
-- ----------------------------
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'resetPasswordIp', 'http://10.128.28.212');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'jenkinsServerIP', 'http://10.128.28.210:8080');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'sonarcubeServerIP', 'http://10.128.28.191:9090');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'common', 'global', 'networkDiskServerIP', 'http://10.128.27.21');


-- ----------------------------
-- 2023-02-20 清总同步需求变更单
-- ----------------------------
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

-- ----------------------------
-- 2023-02-21 清总同步需求变更单
-- ----------------------------
ALTER TABLE `zt_requirement`
     ADD COLUMN `canceled` enum('0','1') NOT NULL DEFAULT '0'  COMMENT '是否已取消,0:否,1:是',
     ADD COLUMN `changeOrderNumber` varchar(100)  COMMENT '变更单号' after `canceled`,
     ADD COLUMN `nextDealuser` varchar(100)  COMMENT '下一节点处理人,待发布回显' after `changeOrderNumber`,
     ADD INDEX `changeOrderNumber` (`changeOrderNumber`) USING BTREE ;

-- 20230128 问题表字段更新
ALTER TABLE `zt_problem`
    MODIFY COLUMN `product`  varchar(255) NOT NULL DEFAULT '0' COMMENT '产品 逗号分割' AFTER `editedBy`,
    MODIFY COLUMN `productPlan`  varchar(255) NOT NULL DEFAULT '0' COMMENT '产品版本 逗号分割' AFTER `product`;

-- 20220224 项目表增加字段
alter table zt_project add allowEnd date null comment '允许结束日期' after `realEnd`,
                       add allowBegin date null comment '允许开始日期' after `realEnd`,
                       add maintenanceStaff varchar(255)  null comment '允许报工维护人员' after `realEnd`;

-- 20230301 问题单新增字段
alter table zt_problem add repeatProblem Text null comment '重复问题单' after `type`;



/*迭代22 2023-1-17  新增冗余字段，用于创建任务*/
ALTER TABLE `zt_demand`
    ADD COLUMN `application` varchar(255) COMMENT '用于创建任务的判断依据';

-- ----------------------------
-- 2022-12-23 tongyanqi 问题表字段
-- ----------------------------
ALTER TABLE `zt_problem`
    MODIFY COLUMN `product`  varchar(255) NOT NULL DEFAULT '0' COMMENT '产品 逗号分割' AFTER `editedBy`,
    MODIFY COLUMN `productPlan`  varchar(255) NOT NULL DEFAULT '0' COMMENT '产品版本 逗号分割' AFTER `product`;




-- ----------------------------
-- 2023-01-10 tongyanqi 需求池 问题池改造
-- ----------------------------
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

-- ----------------------------
-- 迭代24 2023-03-02  wangjiurong 审核节点添加备注信息
-- ----------------------------

ALTER table zt_reviewnode ADD extra TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '扩展信息';