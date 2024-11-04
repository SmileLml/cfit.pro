set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 2023.06.21 创建系统安全评分表：
CREATE TABLE `zt_safety_score` (
   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
   `appId` int(11) NOT NULL COMMENT '应用系统ID',
   `details` text COLLATE utf8mb4_bin NOT NULL COMMENT '应用系统bug数量和评分详情',
   `riskValue` float NOT NULL COMMENT '系统风险总值',
   `score` float NOT NULL COMMENT '系统总合评分',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='系统安全评分表';
-- 2023.06.21 应用研发安全性基础参数表：
CREATE TABLE `zt_safety_param` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` tinyint(4) NOT NULL COMMENT '权重类型  1 指标权重；2 标定值',
    `targetOne` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '一级指标',
    `targetTwo` char(20) COLLATE utf8mb4_bin NOT NULL COMMENT '二级指标',
    `targetThree` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '三级指标',
    `weightNum` double NOT NULL COMMENT '权重',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='应用研发安全性基础参数表';
-- 2023.06.21 添加系统安全性基础参数：
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (1, 1, 'static', 'source', 'severity', 0.0383);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (2, 1, 'static', 'source', 'ordinary', 0.0158);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (3, 1, 'static', 'source', 'slight', 0.0058);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (4, 1, 'static', 'source', 'suggest', 0.0024);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (5, 1, 'static', 'module', 'severity', 0.0641);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (6, 1, 'static', 'module', 'ordinary', 0.024);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (7, 1, 'static', 'module', 'slight', 0.0065);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (8, 1, 'static', 'module', 'suggest', 0.0041);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (9, 1, 'static', 'master', 'severity', 0.0237);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (10, 1, 'static', 'master', 'ordinary', 0.0109);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (11, 1, 'static', 'master', 'slight', 0.0029);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (12, 1, 'static', 'master', 'suggest', 0.0017);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (13, 1, 'dynamic', 'permeate', 'severity', 0.2578);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (14, 1, 'dynamic', 'permeate', 'ordinary', 0.0967);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (15, 1, 'dynamic', 'permeate', 'slight', 0.028);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (16, 1, 'dynamic', 'permeate', 'suggest', 0.0174);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (17, 1, 'standard', 'cip', 'severity', 0.2578);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (18, 1, 'standard', 'cip', 'ordinary', 0.0967);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (19, 1, 'standard', 'cip', 'slight', 0.028);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (20, 1, 'standard', 'cip', 'suggest', 0.0174);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (21, 2, '', 'source', '', 0.06);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (22, 2, '', 'module', '', 0.15);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (23, 2, '', 'master', '', 0.16);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (24, 2, '', 'permeate', '', 0.19);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (25, 2, '', 'cip', '', 0.86);
INSERT INTO `zt_safety_param` (`id`, `type`, `targetOne`, `targetTwo`, `targetThree`, `weightNum`) VALUES (26, 2, '', 'composite', '', 18);

