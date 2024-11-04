-- DROP TABLE IF EXISTS `zt_feedback`;
CREATE TABLE IF NOT EXISTS `zt_feedback` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product` mediumint(8) unsigned NOT NULL,
  `module` mediumint(8) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `subStatus` varchar(30) NOT NULL default '',
  `public` enum('0','1') NOT NULL DEFAULT '0',
  `notify` enum('0','1') NOT NULL DEFAULT '0',
  `likes` text NOT NULL,
  `result` mediumint(8) unsigned NOT NULL,
  `faq` mediumint(8) unsigned NOT NULL,
  `openedBy` char(30) NOT NULL,
  `openedDate` datetime NOT NULL,
  `reviewedBy` varchar(255) NOT NULL,
  `reviewedDate` datetime NOT NULL,
  `processedBy` char(30) NOT NULL,
  `processedDate` datetime NOT NULL,
  `closedBy` char(30) NOT NULL,
  `closedDate` datetime NOT NULL,
  `closedReason` varchar(30) NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  `assignedTo` varchar(255) NOT NULL,
  `assignedDate` datetime NOT NULL,
  `mailto` varchar(255) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_bug`   ADD `feedback` mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER `caseVersion`;
ALTER TABLE `zt_story` ADD `feedback` mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER `fromBug`;
ALTER TABLE `zt_user`  ADD `feedback` enum('0', '1') NOT NULL DEFAULT '0' AFTER `locked`;
ALTER TABLE `zt_group` ADD `developer` enum('0', '1') NOT NULL DEFAULT '1' AFTER `acl`;

INSERT INTO `zt_group` (`name`, `role`, `desc`, `acl`, `developer`) VALUES ('FEEDBACK', 'feedback', 'Feedback', '', '0');

REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'feedback', 'admin'),
(4, 'feedback', 'admin'),
(5, 'feedback', 'admin'),
(8, 'feedback', 'admin'),
(1, 'feedback', 'adminview'),
(4, 'feedback', 'adminview'),
(5, 'feedback', 'adminview'),
(8, 'feedback', 'adminview'),
(1, 'feedback', 'close'),
(4, 'feedback', 'close'),
(5, 'feedback', 'close'),
(8, 'feedback', 'close'),
(1, 'faq', 'browse'),
(4, 'faq', 'browse'),
(5, 'faq', 'browse'),
(8, 'faq', 'browse'),
(1, 'host', 'browse'),
(1, 'host', 'create'),
(1, 'host', 'edit'),
(1, 'host', 'view'),
(1, 'host', 'delete'),
(1, 'host', 'treemap'),
(4, 'host', 'browse'),
(4, 'host', 'create'),
(4, 'host', 'edit'),
(4, 'host', 'view'),
(4, 'host', 'delete'),
(4, 'host', 'treemap'),
(6, 'host', 'browse'),
(6, 'host', 'create'),
(6, 'host', 'edit'),
(6, 'host', 'view'),
(6, 'host', 'delete'),
(6, 'host', 'treemap'),
(8, 'host', 'browse'),
(8, 'host', 'create'),
(8, 'host', 'edit'),
(8, 'host', 'view'),
(8, 'host', 'delete'),
(8, 'host', 'treemap'),
(1, 'tree', 'browsehost'),
(4, 'tree', 'browsehost'),
(6, 'tree', 'browsehost'),
(8, 'tree', 'browsehost'),
(13,'attend','edit'),
(13,'attend','personal'),
(13,'doc','alllibs'),
(13,'doc','browse'),
(13,'doc','create'),
(13,'doc','createLib'),
(13,'doc','delete'),
(13,'doc','diff'),
(13,'doc','edit'),
(13,'doc','editLib'),
(13,'doc','showFiles'),
(13,'doc','view'),
(13,'effort','batchCreate'),
(13,'effort','batchEdit'),
(13,'effort','calendar'),
(13,'effort','createForObject'),
(13,'effort','delete'),
(13,'effort','edit'),
(13,'effort','export'),
(13,'effort','view'),
(13,'feedback','browse'),
(13,'feedback','close'),
(13,'feedback','comment'),
(13,'feedback','create'),
(13,'feedback','delete'),
(13,'feedback','edit'),
(13,'feedback','index'),
(13,'feedback','view'),
(13,'faq','browse'),
(13,'faq','create'),
(13,'faq','edit'),
(13,'faq','delete'),
(13,'file','delete'),
(13,'file','download'),
(13,'file','edit'),
(13,'holiday','browse'),
(13,'index','index'),
(13,'leave','back'),
(13,'leave','create'),
(13,'leave','delete'),
(13,'leave','edit'),
(13,'leave','personal'),
(13,'leave','switchstatus'),
(13,'leave','view'),
(13,'lieu','create'),
(13,'lieu','delete'),
(13,'lieu','edit'),
(13,'lieu','personal'),
(13,'lieu','switchstatus'),
(13,'lieu','view'),
(13,'makeup','create'),
(13,'makeup','delete'),
(13,'makeup','edit'),
(13,'makeup','personal'),
(13,'makeup','switchstatus'),
(13,'makeup','view'),
(13,'my','changePassword'),
(13,'my','editProfile'),
(13,'my','effort'),
(13,'my','profile'),
(13,'my','todo'),
(13,'overtime','create'),
(13,'overtime','delete'),
(13,'overtime','edit'),
(13,'overtime','personal'),
(13,'overtime','switchstatus'),
(13,'overtime','view'),
(13,'search','buildForm'),
(13,'search','buildQuery'),
(13,'search','deleteQuery'),
(13,'search','saveQuery'),
(13,'search','select'),
(13,'todo','batchCreate'),
(13,'todo','batchEdit'),
(13,'todo','batchFinish'),
(13,'todo','calendar'),
(13,'todo','create'),
(13,'todo','delete'),
(13,'todo','edit'),
(13,'todo','export'),
(13,'todo','finish'),
(13,'todo','import2Today'),
(13,'todo','view'),
(13,'tree','browse'),
(13,'tree','delete'),
(13,'tree','manageChild'),
(13,'tree','updateOrder');

-- DROP TABLE IF EXISTS `zt_feedbackproduct`;
CREATE TABLE IF NOT EXISTS `zt_feedbackview` (
  `account` char(30) NOT NULL,
  `product` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `account_product` (`account`,`product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_serverroom`;
CREATE TABLE IF NOT EXISTS `zt_serverroom` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `line` varchar(20) NOT NULL,
  `bandwidth` varchar(128) NOT NULL,
  `provider` varchar(128) NOT NULL,
  `owner` varchar(30) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_account`;
CREATE TABLE IF NOT EXISTS `zt_account` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `adminURI` varchar(255) NOT NULL,
  `account` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `extra` text NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  `status` varchar(30) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  key `name` (`name`),
  key `provider` (`provider`),
  key `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_asset`;
CREATE TABLE IF NOT EXISTS `zt_asset` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `group` varchar(128) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_host`;
CREATE TABLE IF NOT EXISTS `zt_host` (
  `id` mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  `assetID`      mediumint(8) UNSIGNED NOT NULL,
  `admin`        smallint(5)  UNSIGNED NOT NULL DEFAULT 0,
  `serverRoom`   mediumint(8) UNSIGNED NOT NULL,
  `cabinet`      varchar(128) NOT NULL,
  `serverModel`  varchar(256) NOT NULL,
  `hardwareType` varchar(64)  NOT NULL,
  `hostType`     enum('physical','virtual') NOT NULL,
  `cpuBrand`     varchar(128) NOT NULL,
  `cpuModel`     varchar(128) NOT NULL,
  `cpuNumber`    varchar(16)  NOT NULL,
  `cpuCores`     varchar(30)  NOT NULL,
  `cpuRate`      varchar(30)  NOT NULL,
  `memory`       varchar(30)  NOT NULL,
  `diskType`     varchar(30)  NOT NULL,
  `diskSize`     varchar(30)  NOT NULL,
  `unit`         enum('GB','TB') NOT NULL DEFAULT 'GB',
  `privateIP`    varchar(128) NOT NULL,
  `publicIP`     varchar(128) NOT NULL,
  `nic`          varchar(128) NOT NULL,
  `mac`          varchar(128) NOT NULL,
  `osName`       varchar(64)  NOT NULL,
  `osVersion`    varchar(64)  NOT NULL,
  `webserver`    varchar(128) NOT NULL,
  `database`     varchar(128) NOT NULL,
  `language`     varchar(16)  NOT NULL,
  `status`       enum('online','offline') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_service`;
CREATE TABLE IF NOT EXISTS `zt_service` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `version` varchar(64) NOT NULL,
  `color` char(7) NOT NULL,
  `desc` text,
  `dept` varchar(128) NOT NULL,
  `devel` varchar(30) NOT NULL,
  `qa` varchar(30) NOT NULL,
  `ops` varchar(30) NOT NULL,
  `hosts` text,
  `softName` varchar(128) NOT NULL,
  `softVersion` varchar(128) NOT NULL,
  `type` varchar(20) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  `parent` mediumint(8) unsigned NOT NULL default '0',
  `path` char(255) NOT NULL default '',
  `grade` tinyint(3) unsigned NOT NULL default '0',
  `order` smallint(5) unsigned NOT NULL default '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES (1, 'tree', 'editHost');

-- DROP TABLE IF EXISTS `zt_attend`;
CREATE TABLE IF NOT EXISTS `zt_attend` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account` char(30) NOT NULL,
  `date` date NOT NULL,
  `signIn` time NOT NULL,
  `signOut` time NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL,
  `device` varchar(30) NOT NULL,
  `client` varchar(20) NOT NULL,
  `manualIn` time NOT NULL,
  `manualOut` time NOT NULL,
  `reason` varchar(30) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `reviewStatus` varchar(30) NOT NULL DEFAULT '',
  `reviewedBy` char(30) NOT NULL,
  `reviewedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account` (`account`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `reason` (`reason`),
  KEY `reviewStatus` (`reviewStatus`),
  KEY `reviewedBy` (`reviewedBy`),
  UNIQUE KEY `attend` (`date`,`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_attendstat`;
CREATE TABLE IF NOT EXISTS `zt_attendstat` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account` char(30) NOT NULL,
  `month` char(10) NOT NULL DEFAULT '',
  `normal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `late` decimal(12,2) NOT NULL DEFAULT 0.00,
  `early` decimal(12,2) NOT NULL DEFAULT 0.00,
  `absent` decimal(12,2) NOT NULL DEFAULT 0.00,
  `trip` decimal(12,2) NOT NULL DEFAULT 0.00,
  `egress` decimal(12,2) NOT NULL DEFAULT 0.00,
  `lieu` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paidLeave` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unpaidLeave` decimal(12,2) NOT NULL DEFAULT 0.00,
  `timeOvertime` decimal(12,2) NOT NULL DEFAULT 0.00,
  `restOvertime` decimal(12,2) NOT NULL DEFAULT 0.00,
  `holidayOvertime` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deserve` decimal(12,2) NOT NULL DEFAULT 0.00,
  `actual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `account` (`account`),
  KEY `month` (`month`),
  KEY `status` (`status`),
  UNIQUE KEY `attend` (`month`,`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_overtime`;
CREATE TABLE IF NOT EXISTS `zt_overtime` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `year` char(4) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `start` time NOT NULL,
  `finish` time NOT NULL,
  `hours` float(4,1) unsigned NOT NULL DEFAULT '0.0',
  `leave` varchar(255) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT '',
  `rejectReason` varchar(100) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `reviewedBy` char(30) NOT NULL,
  `reviewedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_holiday`;
CREATE TABLE IF NOT EXISTS `zt_holiday` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `type` enum('holiday', 'working') NOT NULL DEFAULT 'holiday',
  `desc` text NOT NULL,
  `year` char(4) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_leave`;
CREATE TABLE IF NOT EXISTS `zt_leave` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `year` char(4) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `start` time NOT NULL,
  `finish` time NOT NULL,
  `hours` float(4,1) unsigned NOT NULL DEFAULT '0.0',
  `backDate` datetime NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT '',
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `reviewedBy` char(30) NOT NULL,
  `reviewedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_lieu`;
CREATE TABLE IF NOT EXISTS `zt_lieu` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `year` char(4) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `start` time NOT NULL,
  `finish` time NOT NULL,
  `hours` float(4,1) unsigned NOT NULL DEFAULT '0.0',
  `overtime` char(255) NOT NULL,
  `trip` char(255) NOT NULL,
  `desc` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT '',
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `reviewedBy` char(30) NOT NULL,
  `reviewedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`),
  KEY `status` (`status`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_trip`;
CREATE TABLE IF NOT EXISTS `zt_trip` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('trip', 'egress') NOT NULL DEFAULT 'trip',
  `customers` varchar(20) NOT NULL,
  `name` char(30) NOT NULL,
  `desc` text NOT NULL,
  `year` char(4) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `start` time NOT NULL,
  `finish` time NOT NULL,
  `from` char(50) NOT NULL,
  `to` char(50) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_deploy`;
CREATE TABLE IF NOT EXISTS `zt_deploy` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `owner` char(30) NOT NULL,
  `members` text NOT NULL,
  `notify` text NOT NULL,
  `cases` text NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `result` varchar(20) NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_deployproduct`;
CREATE TABLE IF NOT EXISTS `zt_deployproduct` (
  `deploy` mediumint(8) unsigned NOT NULL,
  `product` mediumint(8) unsigned NOT NULL,
  `release` mediumint(8) unsigned NOT NULL,
  `package` varchar(255) NOT NULL,
  UNIQUE KEY `deploy_product_release` (`deploy`,`product`,`release`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_deploystep`;
CREATE TABLE IF NOT EXISTS `zt_deploystep` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `deploy` mediumint(8) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `stage` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `assignedTo` char(30) NOT NULL,
  `assignedDate` datetime NOT NULL,
  `finishedBy` char(30) NOT NULL,
  `finishedDate` datetime NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `zt_testresult` ADD `deploy` mediumint(8) unsigned NOT NULL;
-- DROP TABLE IF EXISTS `zt_deployscope`;
CREATE TABLE IF NOT EXISTS `zt_deployscope` (
  `deploy` mediumint(8) unsigned NOT NULL,
  `service` mediumint(8) unsigned NOT NULL,
  `hosts` text NOT NULL,
  `remove` text NOT NULL,
  `add` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_doc`
ADD `parent` smallint(5) unsigned NOT NULL DEFAULT '0' AFTER `type`,
ADD `path` char(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `parent`,
ADD `grade` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `path`,
ADD `order` smallint(5) unsigned NOT NULL DEFAULT '0' AFTER `grade`;

ALTER TABLE `zt_product` ADD `feedback` varchar(30) COLLATE 'utf8_general_ci' NOT NULL AFTER `RD`;

ALTER TABLE `zt_leave`       ADD `level` tinyint(3) NOT NULL;
ALTER TABLE `zt_leave`       ADD `assignedTo` varchar(30) NOT NULL;
ALTER TABLE `zt_leave`       ADD `reviewers` text NOT NULL;
ALTER TABLE `zt_leave`       ADD `backReviewers` text NOT NULL;
ALTER TABLE `zt_lieu`        ADD `level` tinyint(3) NOT NULL;
ALTER TABLE `zt_lieu`        ADD `assignedTo` varchar(30) NOT NULL;
ALTER TABLE `zt_lieu`        ADD `reviewers` text NOT NULL;
ALTER TABLE `zt_overtime`    ADD `level` tinyint(3) NOT NULL;
ALTER TABLE `zt_overtime`    ADD `assignedTo` varchar(30) NOT NULL;
ALTER TABLE `zt_overtime`    ADD `reviewers` text NOT NULL;

-- DROP TABLE IF EXISTS `zt_faq`;
CREATE TABLE IF NOT EXISTS `zt_faq` (
`id` mediumint(9) NOT NULL AUTO_INCREMENT,
`module` mediumint(9) NOT NULL,
`product` mediumint(9) NOT NULL,
`question` varchar(255) NOT NULL,
`answer` text NOT NULL,
`addedtime` datetime NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_task` ADD `feedback` mediumint(8) unsigned NOT NULL AFTER `fromBug`;
ALTER TABLE `zt_todo` ADD `feedback` mediumint(8) unsigned NOT NULL AFTER `end`;

-- DROP TABLE IF EXISTS `zt_domain`;
CREATE TABLE IF NOT EXISTS `zt_domain`(
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `adminURI` varchar(255) NOT NULL,
  `resolverURI` varchar(255) NOT NULL,
  `register` varchar(255) NOT NULL,
  `expiredDate` datetime NOT NULL,
  `renew` varchar(255) NOT NULL,
  `account` varchar(255) NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  key `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- DROP TABLE IF EXISTS `zt_workflow`;
CREATE TABLE IF NOT EXISTS `zt_workflow` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent` varchar(30) NOT NULL, 
  `child` varchar(30) NOT NULL, 
  `type` varchar(10) NOT NULL DEFAULT 'flow',
  `app` varchar(20) NOT NULL,
  `position` varchar(30) NOT NULL,
  `module` varchar(30) NOT NULL,
  `table` varchar(50) NOT NULL,
  `name` varchar(30) NOT NULL,
  `flowchart` text NOT NULL,
  `js` text NOT NULL,
  `css` text NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `administrator` text NOT NULL,
  `desc` text NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '1.0',
  `status` varchar(10) NOT NULL DEFAULT 'wait',
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `type` (`type`),
  KEY `app` (`app`),
  KEY `module` (`module`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`app`, `module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowaction`;
CREATE TABLE IF NOT EXISTS `zt_workflowaction` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` enum('single', 'batch') NOT NULL DEFAULT 'single',
  `batchMode` enum('same', 'different') NOT NULL DEFAULT 'different',
  `extensionType` varchar(10) NOT NULL DEFAULT 'override' COMMENT 'none | extend | override',
  `open` varchar(20) NOT NULL,
  `position` enum('menu', 'browseandview', 'browse', 'view') NOT NULL DEFAULT 'browseandview',
  `layout` char(20) NOT NULL,
  `show` enum('dropdownlist', 'direct') NOT NULL DEFAULT 'dropdownlist',
  `order` smallint(5) unsigned NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `virtual` tinyint(1) unsigned NOT NULL,
  `conditions` text NOT NULL,
  `verifications` text NOT NULL,
  `hooks` text NOT NULL,
  `linkages` text NOT NULL,
  `js` text NOT NULL,
  `css` text NOT NULL,
  `toList` char(255) NOT NULL,
  `blocks` text NOT NULL,
  `desc` text NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'enable',
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `action` (`action`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`module`, `action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowdatasource`;
CREATE TABLE IF NOT EXISTS `zt_workflowdatasource` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('system', 'sql', 'func', 'option', 'lang', 'category') NOT NULL DEFAULT 'option',
  `name` varchar(30) NOT NULL,
  `datasource` text NOT NULL, 
  `view` varchar(20) NOT NULL,
  `keyField` varchar(50) NOT NULL,
  `valueField` varchar(50) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowfield`;
CREATE TABLE IF NOT EXISTS `zt_workflowfield` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `field`  varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'varchar',
  `length` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `control` varchar(20) NOT NULL,
  `expression` text NOT NULL,
  `options` text NOT NULL,
  `default` varchar(100) NOT NULL,
  `rules` varchar(255) NOT NULL,
  `placeholder` varchar(100) NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `searchOrder` smallint(5) unsigned NOT NULL DEFAULT '0', 
  `exportOrder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `canExport` enum('0', '1') NOT NULL DEFAULT '0',
  `canSearch` enum('0', '1') NOT NULL DEFAULT '0',
  `isValue` enum('0', '1') NOT NULL DEFAULT '0',
  `readonly` enum('0', '1') NOT NULL DEFAULT '0',
  `buildin` tinyint(1) unsigned NOT NULL,
  `desc` text NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `field` (`field`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`module`, `field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowlayout`;
CREATE TABLE IF NOT EXISTS `zt_workflowlayout` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `field`  varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `width` smallint(5) NOT NULL,
  `position` text NOT NULL,
  `readonly` enum('0', '1') NOT NULL DEFAULT '0',
  `mobileShow` enum('0', '1') NOT NULL DEFAULT '1',
  `summary` varchar(20) NOT NULL,
  `defaultValue` text NOT NULL,
  `layoutRules` varchar(255) NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `action` (`action`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`module`, `action`, `field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowlabel`;
CREATE TABLE IF NOT EXISTS `zt_workflowlabel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `code` varchar(30) NOT NULL,
  `label` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `orderBy` text NOT NULL,
  `order` tinyint(3) NOT NULL,
  `buildin` tinyint(1) unsigned NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowlinkdata`;
CREATE TABLE IF NOT EXISTS `zt_workflowlinkdata` (
  `objectType` varchar(30) NOT NULL,
  `objectID` mediumint(8) unsigned NOT NULL,
  `linkedType` varchar(30) NOT NULL,
  `linkedID` mediumint(8) unsigned NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  UNIQUE KEY `unique` (`objectType`, `objectID`, `linkedType`, `linkedID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowrelation`;
CREATE TABLE IF NOT EXISTS `zt_workflowrelation` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `prev` varchar(30) NOT NULL, 
  `next` varchar(30) NOT NULL,
  `field` varchar(50) NOT NULL,
  `actions` varchar(20) NOT NULL,
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowrelationlayout`;
CREATE TABLE IF NOT EXISTS `zt_workflowrelationlayout` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `prev` varchar(30) NOT NULL,
  `next` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `field` varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `prev` (`prev`),
  KEY `next` (`next`),
  KEY `action` (`action`),
  KEY `order` (`order`),
  UNIQUE KEY `unique` (`prev`, `next`, `action`, `field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowrule`;
CREATE TABLE IF NOT EXISTS `zt_workflowrule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('system', 'regex', 'func') NOT NULL DEFAULT 'regex',
  `name` varchar(30) NOT NULL,
  `rule` text NOT NULL, 
  `createdBy` char(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` char(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowsql`;
CREATE TABLE IF NOT EXISTS `zt_workflowsql` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `field` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `sql` text NOT NULL,
  `vars` text NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `editedBy` varchar(30) NOT NULL,
  `editedDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `module` (`module`),
  KEY `field` (`field`),
  KEY `action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowversion`;
CREATE TABLE IF NOT EXISTS `zt_workflowversion` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `version` varchar(10) NOT NULL,
  `fields` text NOT NULL,
  `actions` text NOT NULL,
  `layouts` text NOT NULL,
  `sqls` text NOT NULL,
  `labels` text NOT NULL,
  `table` text NOT NULL,
  `datas` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `moduleversion` (`module`, `version`),
  KEY `module` (`module`),
  KEY `version` (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `zt_workflowreport`;
CREATE TABLE IF NOT EXISTS `zt_workflowreport` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL COMMENT 'module name',
  `name` varchar(100) NOT NULL COMMENT 'report name',
  `type` enum('pie', 'line', 'bar') NOT NULL DEFAULT 'pie' COMMENT 'report type',
  `countType` enum('sum', 'count') NOT NULL DEFAULT 'sum' COMMENT 'report count method',
  `displayType` enum('value', 'percent') NOT NULL DEFAULT 'value' COMMENT 'report display method',
  `dimension` varchar(130) NOT NULL COMMENT 'dimension field code of zt_workflowfield',
  `fields` text NOT NULL COMMENT 'count fileds code of zt_workflowfield,use comma split',
  `order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `zt_action` CHANGE `action` `action` varchar(80) NOT NULL DEFAULT '';
ALTER TABLE `zt_action` CHANGE `extra` `extra` text;
ALTER TABLE `zt_file` CHANGE `objectType` `objectType` char(30) NOT NULL;

INSERT INTO `zt_workflowrule` VALUES (1,'system','必填','notempty','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00'),(2,'system','唯一','unique','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00'),(3,'system','日期','date','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00'),(4,'system','email','email','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00'),(5,'system','数字','float','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00'),(6,'system','电话','phone','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00'),(7,'system','IP','ip','admin','2020-10-14 14:06:14','','0000-00-00 00:00:00');

REPLACE INTO `zt_grouppriv` VALUES
(1,'apppriv','flow'),
(1,'workflow','activate'),
(1,'workflow','backup'),
(1,'workflow','browseDB'),
(1,'workflow','browseFlow'),
(1,'workflow','copy'),
(1,'workflow','create'),
(1,'workflow','deactivate'),
(1,'workflow','delete'),
(1,'workflow','edit'),
(1,'workflow','flowchart'),
(1,'workflow','setCSS'),
(1,'workflow','setJS'),
(1,'workflow','release'),
(1,'workflow','ui'),
(1,'workflow','upgrade'),
(1,'workflow','view'),
(1,'workflowaction','browse'),
(1,'workflowaction','create'),
(1,'workflowaction','delete'),
(1,'workflowaction','edit'),
(1,'workflowaction','setCSS'),
(1,'workflowaction','setJS'),
(1,'workflowaction','setNotice'),
(1,'workflowaction','setVerification'),
(1,'workflowaction','sort'),
(1,'workflowaction','view'),
(1,'workflowcondition','browse'),
(1,'workflowcondition','create'),
(1,'workflowcondition','delete'),
(1,'workflowcondition','edit'),
(1,'workflowdatasource','browse'),
(1,'workflowdatasource','create'),
(1,'workflowdatasource','delete'),
(1,'workflowdatasource','edit'),
(1,'workflowfield','browse'),
(1,'workflowfield','create'),
(1,'workflowfield','delete'),
(1,'workflowfield','edit'),
(1,'workflowfield','export'),
(1,'workflowfield','exportTemplate'),
(1,'workflowfield','setValue'),
(1,'workflowfield','setExport'),
(1,'workflowfield','setSearch'),
(1,'workflowfield','showImport'),
(1,'workflowfield','sort'),
(1,'workflowfield','import'),
(1,'workflowhook','browse'),
(1,'workflowhook','create'),
(1,'workflowhook','delete'),
(1,'workflowhook','edit'),
(1,'workflowlabel','browse'),
(1,'workflowlabel','create'),
(1,'workflowlabel','delete'),
(1,'workflowlabel','edit'),
(1,'workflowlabel','sort'),
(1,'workflowlayout','admin'),
(1,'workflowlayout','block'),
(1,'workflowlinkage','browse'),
(1,'workflowlinkage','create'),
(1,'workflowlinkage','delete'),
(1,'workflowlinkage','edit'),
(1,'workflowrelation','admin'),
(1,'workflowreport','browse'),
(1,'workflowreport','create'),
(1,'workflowreport','edit'),
(1,'workflowreport','delete'),
(1,'workflowreport','sort'),
(1,'workflowrule','browse'),
(1,'workflowrule','create'),
(1,'workflowrule','delete'),
(1,'workflowrule','edit'),
(1,'workflowrule','view');

ALTER TABLE `zt_workflow` ADD `navigator` varchar(10) NOT NULL AFTER `type`;

INSERT INTO `zt_workflowdatasource` (`type`, `name`, `datasource`, `view`, `keyField`, `valueField`) VALUES
('system',      '产品',           '{\"app\":\"system\",\"module\":\"product\",\"method\":\"getPairs\",\"methodDesc\":\"Get product pairs.\",\"params\":[{\"name\":\"mode\",\"type\":\"string\",\"desc\":\"\",\"value\":\"all\"}]}',       '',     '',     ''),
('system',      '项目',           '{\"app\":\"system\",\"module\":\"execution\",\"method\":\"getPairs\",\"methodDesc\":\"Get execution pairs.\",\"params\":[{\"name\":\"mode\",\"type\":\"string\",\"desc\":\"all|noclosed or empty\",\"value\":\"all\"}]}',  '',     '',     ''),
('system',      '产品线',         '{\"app\":\"system\",\"module\":\"product\",\"method\":\"getLinePairs\",\"methodDesc\":\"Get line pairs.\",\"params\":[{\"name\":\"useShort\",\"type\":\"bool\",\"desc\":\"\",\"value\":\"\"}]}',  '',     '',     ''),
('sql',         '需求',           'select id,title from zt_story where deleted=\"0\"',    'view_datasource_4',    'id',   'title'),
('sql',         '任务',           'select id,name from zt_task where deleted=\"0\"',      'view_datasource_5',    'id',   'name'),
('sql',         'Bug',            'select id,title from zt_bug where deleted=\"0\"',      'view_datasource_6',    'id',   'title'),
('system',      '权限分组',       '{\"app\":\"system\",\"module\":\"group\",\"method\":\"getPairs\",\"methodDesc\":\"\",\"params\":[]}',  '',     '',     ''),
('system',      '用户',           '{\"app\":\"system\",\"module\":\"user\",\"method\":\"getPairs\",\"methodDesc\":\"\",\"params\":[{\"name\":\"params\",\"type\":\"\",\"desc\":\"\",\"value\":\"noclosed|noletter\"},{\"name\":\"usersToAppended\",\"type\":\"\",\"desc\":\"\",\"value\":\"\"}]}',        '',     '',     ''),
('system',      '产品分支',       '{\"app\":\"system\",\"module\":\"branch\",\"method\":\"getAllPairs\",\"methodDesc\":\"Get pairs.\",\"params\":[{\"name\":\"params\",\"type\":\"string\",\"desc\":\"\",\"value\":\"\"}]}',      '',     '',     ''),
('sql',         '版本',           'select id,name from zt_build where deleted=\"0\"',     'view_datasource_10',   'id',   'name'),
('sql',         '模块',           'select id,name from zt_module where deleted=\"0\"',    'view_datasource_11',   'id',   'name'),
('sql',         '计划',           'select id,title from zt_productplan where deleted=\"0\"',      'view_datasource_12',   'id',   'title'),
('lang',        '产品类型',       'productType',    '',     '',     ''),
('lang',        '产品状态',       'productStatus',  '',     '',     ''),
('lang',        '产品访问控制',   'productAcl',     '',     '',     ''),
('lang',        '项目类型',       'projectType',    '',     '',     ''),
('lang',        '项目状态',       'projectStatus',  '',     '',     ''),
('lang',        '项目访问控制',   'projectAcl',     '',     '',     ''),
('lang',        '发布状态',       'releaseStatus',  '',     '',     ''),
('lang',        '需求来源',       'storySource',    '',     '',     ''),
('lang',        '需求优先级',     'storyPri',       '',     '',     ''),
('lang',        '需求状态',       'storyStatus',    '',     '',     ''),
('lang',        '需求阶段',       'storyStage',     '',     '',     ''),
('lang',        'Bug严重程度',    'bugSeverity',    '',     '',     ''),
('lang',        'Bug优先级',      'bugPri',         '',     '',     ''),
('lang',        'Bug类型',        'bugType',        '',     '',     ''),
('lang',        'Bug操作系统',    'bugOs',          '',     '',     ''),
('lang',        'Bug浏览器',      'bugBrowser',     '',     '',     ''),
('lang',        'Bug状态',        'bugStatus',      '',     '',     ''),
('lang',        '任务类型',       'taskType',       '',     '',     ''),
('lang',        '任务优先级',     'taskPri',        '',     '',     ''),
('lang',        '任务状态',       'taskStatus',     '',     '',     ''),
('lang',        '测试用例优先级', 'testcasePri',    '',     '',     ''),
('lang',        '测试用例类型',   'testcaseType',   '',     '',     ''),
('lang',        '测试用例阶段',   'testcaseStage',  '',     '',     ''),
('lang',        '测试用例状态',   'testcaseStatus', '',     '',     ''),
('lang',        '测试单优先级',   'testtaskPri',    '',     '',     ''),
('lang',        '测试单状态',     'testtaskStatus', '',     '',     ''),
('lang',        '反馈状态',       'feedbackStatus', '',     '',     ''),
('lang',        'Bug解决方案',    'bugResolution',  '',     '',     ''),
('sql',         '用例',           'select id,title from zt_case where deleted=\"0\"',     'view_datasource_41',   'id',   'title'),
('system',      '反馈分支',       '{\"app\":\"system\",\"module\":\"tree\",\"method\":\"getOptionMenu\",\"methodDesc\":\"Create an option menu in html.\",\"params\":[{\"name\":\"rootID\",\"type\":\"int\",\"desc\":\"\",\"value\":\"0\"},{\"name\":\"type\",\"type\":\"string\",\"desc\":\"\",\"value\":\"feedback\"},{\"name\":\"startModule\",\"type\":\"int\",\"desc\":\"\",\"value\":\"0\"},{\"name\":\"branch\",\"type\":\"\",\"desc\":\"\",\"value\":\"0\"}]}',   '',     '',     ''),
('lang',        '需求类型',       'storyType',    '',     '',     '');

DROP VIEW IF EXISTS `view_datasource_4`;
DROP VIEW IF EXISTS `view_datasource_5`;
DROP VIEW IF EXISTS `view_datasource_6`;
DROP VIEW IF EXISTS `view_datasource_10`;
DROP VIEW IF EXISTS `view_datasource_11`;
DROP VIEW IF EXISTS `view_datasource_12`;
DROP VIEW IF EXISTS `view_datasource_41`;

CREATE VIEW `view_datasource_4`  AS select `id`,`title` from `zt_story` where `deleted` = '0';
CREATE VIEW `view_datasource_5`  AS select `id`,`name` from `zt_task` where `deleted` = '0';
CREATE VIEW `view_datasource_6`  AS select `id`,`title` from `zt_bug` where `deleted` = '0';
CREATE VIEW `view_datasource_10` AS select `id`,`name` from `zt_build` where `deleted` = '0';
CREATE VIEW `view_datasource_11` AS select `id`,`name` from `zt_module` where `deleted` = '0';
CREATE VIEW `view_datasource_12` AS select `id`,`title` from `zt_productplan` where `deleted` = '0';
CREATE VIEW `view_datasource_41` AS select `id`,`title` from `zt_case` where `deleted` = '0';
