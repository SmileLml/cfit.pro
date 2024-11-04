<?php
$config->mobileapi->objectTableList = [
    'modify'             => TABLE_MODIFY,
    'outwarddelivery'    => TABLE_OUTWARDDELIVERY,
    'sectransfer'        => TABLE_SECTRANSFER,
    'problem'            => TABLE_PROBLEM,
    'info'               => TABLE_INFO,
    'putproduction'      => TABLE_PUTPRODUCTION,
    'infoqz'             => TABLE_INFO_QZ,
    'requirement'        => TABLE_REQUIREMENT,
    'credit'        => TABLE_CREDIT,
    'change'        => TABLE_CHANGE
];
$config->mobileapi->objectFlieldsList = [
    'modify'             => 'status,type,planBegin,planEnd,applyUsercontact,`desc`',
    'outwarddelivery'    => 'status,isNewModifycncc,modifycnccId,contactTel as applyUsercontact,outwardDeliveryDesc as `desc`',
    'sectransfer'        => 'status,protransferDesc as `desc`',
    'problem'            => 'ReviewStatus as status,acceptUser,acceptDept,abstract as `desc`',
    'info'               => 'status,`desc`',
    'putproduction'      => 'status,createdBy,`desc`',
    'infoqz'             => 'status,createdBy,`desc`,createUserPhone',
    'requirement'        => 'feedbackStatus as status,`desc`,feedbackBy,end',
    'credit'        =>  'status,createdBy,summary as `desc`',
    'change'        =>  'status,createdBy,reason as `desc`'
];
$config->mobileapi->objectStatusList = [
    'modify'             => 'statusList',
    'outwarddelivery'    => 'statusList',
    'sectransfer'        => 'statusListName',
    'problem'            => 'feedbackStatusList',
    'info'               => 'statusList',
    'putproduction'      => 'statusList',
    'infoqz'             => 'statusList',
    'requirement'        => 'feedbackStatusList',
    'credit'        => 'statusList',
    'change'        => 'statusList'
];
