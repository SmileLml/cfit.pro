set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';


ALTER TABLE `zt_modifycncc` ADD COLUMN `changeForm` varchar(10) NOT NULL DEFAULT '' COMMENT '变更形式' AFTER `actualDeliveryTime`;
ALTER TABLE `zt_modifycncc` ADD COLUMN `automationTools` varchar(10) NOT NULL DEFAULT '' COMMENT '自动化工具' AFTER `changeForm`;
-- 添加变更形式枚举值
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'changeFormList', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'changeFormList', '1', '1.版本变更', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'changeFormList', '2', '2.脚本变更', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'changeFormList', '3', '3.配置变更', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'changeFormList', '4', '4.其他变更', '0', 0,'');

-- 添加新的实施方式
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'implementModalityNewList', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'implementModalityNewList', '4', '1.自动化部署+自动化验证', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'implementModalityNewList', '5', '2.仅自动化部署', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'implementModalityNewList', '6', '3.非自动化', '0', 0,'');

-- 添加自动化工具枚举值
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '0', '', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '1', 'AADS', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '2', 'AERS', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '3', '精卫变更流程（CMDB录入）', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '4', 'IBM CDC控制台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '5', 'TDSQL赤兔管理台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '6', '数据传输DBbridge管理控制台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '7', 'PMTS控制台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '8', 'DSG Web工具', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '9', 'TLQ TongGTP Web控制台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '10', 'ACS内容管理平台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '11', 'ACS协同管理平台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '12', 'ACS流程平台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '13', '华为大数据平台FusionInsight Web界面', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '14', 'TDSQL ansible工具', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '15', 'PMTS一体机管理界面', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '16', 'Xsky多集群管理平台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '17', '百度AI天牛平台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '18', 'PAMS客户端', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '19', 'MMM统一采集平台', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '20', 'IOPS统一运维门户', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '21', '华三云管Web界面', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '22', '华为云管Web界面', '0', 0,'');
INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES ('zh-cn', 'modifycncc', 'automationToolsList', '23', 'JWOPS精卫', '0', 0,'');

INSERT INTO `zt_lang` ( `lang`, `module`, `section`, `key`, `value`, `system`, `order`,`extendInfo`) VALUES
('zh-cn', 'modifycncc', 'automationToolsList', '24', 'DIEP数据集成平台一键部署工具', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '25', 'DIEPEM数据开发平台一键部署工具', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '26', 'VMware vSphere虚拟化平台', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '27', 'VMware Horizon云桌面', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '28', 'H3C CAS虚拟化平台', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '29', 'H3C Access云桌面', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '30', '浪潮InCloud Rail超融合平台', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '31', '浪潮InCloudSphere 虚拟化平台', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '32', 'XSKY 分布式存储', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '33', '主机IBM ITM客户端', '0', 0,''),
('zh-cn', 'modifycncc', 'automationToolsList', '34', '精卫作业平台', '0', 0,'');


-- ------------------------------ ----------------------------
-- 清总缺陷 接口字段枚举值 调整 wangjiurong
-- ------------------------------ ----------------------------
INSERT INTO zt_lang (lang, module, `section`, `key`, value, `system`, `order`, extendInfo) VALUES ('zh-cn', 'bug', 'typeList', 'other', '其他缺陷', '0', 0, '');

UPDATE zt_lang
SET  value='{"security":{"a1":"\\u6e90\\u7801\\u5b89\\u5168\\u95ee\\u9898","a2":"\\u7ec4\\u4ef6\\u5b89\\u5168\\u95ee\\u9898","a3":"\\u4e3b\\u673a\\u5b89\\u5168\\u95ee\\u9898","a4":"\\u6e17\\u900f\\u5b89\\u5168\\u95ee\\u9898","a5":"\\u7b49\\u4fdd\\u5b89\\u5168\\u95ee\\u9898","a6":"\\u5176\\u4ed6"},"funcdetect":{"b1":"\\u5b9e\\u73b0\\u9519\\u8bef","b2":"\\u5b9e\\u73b0\\u9057\\u6f0f","b3":"\\u4ea7\\u54c1\\u8bbe\\u8ba1","b4":"\\u4ea7\\u54c1\\u4f53\\u9a8c","b5":"\\u7cfb\\u7edf\\u5f02\\u5e38","b6":"\\u5176\\u4ed6","b7":"\\u7a0b\\u5e8f\\u8bbe\\u8ba1"},"other":{"c1":"\\u5176\\u4ed6"},"requiredect":{"d1":"\\u6587\\u6863\\u7f3a\\u9677"}}'
where 1 and module = 'bug' and `section` = 'childTypeList';