set session sql_mode = ''ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'';

CREATE TABLE `zt_need_deal_message`
(
    `id`              int(11) NOT NULL AUTO_INCREMENT,
    `desc`            mediumtext COMMENT '' 标题/摘要 '',
    `code`            varchar(30) DEFAULT NULL COMMENT '' 编号 '',
    `objectType`      varchar(30) DEFAULT NULL COMMENT '' 模块类型 '',
    `objectId`        int(11) DEFAULT NULL COMMENT '' 单子id '',
    `createdBy`       varchar(30) DEFAULT NULL COMMENT '' 创建人（上一节点审核人）'',
    `createdDate`     datetime    DEFAULT CURRENT_TIMESTAMP COMMENT '' 创建时间（上一节点审批时间）'',
    `updatedDate`     datetime    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deptId`          int(11) DEFAULT NULL COMMENT '' 表单创建人部门 '',
    `formCreatedBy`   varchar(30) DEFAULT NULL COMMENT '' 表单创建人 '',
    `formCreatedDate` datetime    DEFAULT NULL COMMENT '' 表单创建时间 '',
    `formStatus`      varchar(30) DEFAULT NULL COMMENT '' 表单状态 '',
    `status`          int(11) DEFAULT '' 1 '' COMMENT '' 1 待处理 2已处理 3跳过、无需处理 '',
    `extra`           text COMMENT '' 扩展字段 json '',
    `reviewer`        varchar(30) DEFAULT NULL COMMENT '' 待处理人 '',
    `version`         int(11) DEFAULT NULL COMMENT '' 版本 '',
    `deleted`         enum('' 0 '','' 1 '') DEFAULT '' 0 '' COMMENT '' 1 已删除 '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
