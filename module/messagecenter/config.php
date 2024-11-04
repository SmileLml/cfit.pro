<?php
$config->messagecenter = new stdClass();
$config->messagecenter->objectTypes = array();//模块类型
/*completedAction 已办动作（只在消息中心增加已办消息）*/
$config->messagecenter->objectTypes['modify']['completedAction']          = array('reject','submit','cancelreview','canceled','runresult','cancelchange','repush','isdiskdelivery');
$config->messagecenter->objectTypes['outwarddelivery']['completedAction'] = array('reject','submitexamine','repush','canceled');
$config->messagecenter->objectTypes['sectransfer']['completedAction']     = array('created','repush','reject');
$config->messagecenter->objectTypes['problem']['completedAction']         = array();
$config->messagecenter->objectTypes['info']['completedAction']            = array('createdandsubmitexamine','submitexamine','reject');
$config->messagecenter->objectTypes['putproduction']['completedAction']    = array('submited','deal');
$config->messagecenter->objectTypes['infoqz']['completedAction']    = array();
$config->messagecenter->objectTypes['requirement']['completedAction']     = array();
$config->messagecenter->objectTypes['credit']['completedAction']     = array();
$config->messagecenter->objectTypes['change']['completedAction']          = array('recall','deleted');

/*completedAndIncompletedAction 消息中心加待办及已办*/
$config->messagecenter->objectTypes['modify']['completedAndIncompletedAction']          = array('linkrelease','deal','review');
$config->messagecenter->objectTypes['outwarddelivery']['completedAndIncompletedAction'] = array('linkrelease','deal','review');
$config->messagecenter->objectTypes['sectransfer']['completedAndIncompletedAction']     = array('reviewed','workloadedit','syncstatus','dealed');
$config->messagecenter->objectTypes['info']['completedAndIncompletedAction']            = array('linkrelease','deal','review');
$config->messagecenter->objectTypes['putproduction']['completedAndIncompletedAction']     = array('reviewed');
$config->messagecenter->objectTypes['infoqz']['completedAndIncompletedAction']     = array('linkrelease','review');
$config->messagecenter->objectTypes['requirement']['completedAndIncompletedAction']     = array('reviewed','createfeedbacked');
$config->messagecenter->objectTypes['problem']['completedAndIncompletedAction']         = array('review','createfeedback','secondeal');
$config->messagecenter->objectTypes['credit']['completedAndIncompletedAction']     = array('reviewed');
$config->messagecenter->objectTypes['change']['completedAndIncompletedAction']          = array('reviewed','applychange');

/*审批动作对应单子状态*/
$config->messagecenter->objectTypes['modify']['completedAndIncompletedAction']['review']           = array('cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','gmsuccess');
$config->messagecenter->objectTypes['outwarddelivery']['completedAndIncompletedAction']['review']  = array('cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','gmsuccess');
$config->messagecenter->objectTypes['sectransfer']['completedAndIncompletedAction']['review']      = array('waitOwnApprove','waitLeaderApprove','waitMaxLeaderApprove','waitSecApprove','centerReject');
$config->messagecenter->objectTypes['problem']['completedAndIncompletedAction']['createfeedback']  = array('todeptapprove');
$config->messagecenter->objectTypes['problem']['completedAndIncompletedAction']['review']          = array('deptapproved');
$config->messagecenter->objectTypes['info']['completedAndIncompletedAction']['review']             = array('cmconfirmed','groupsuccess','managersuccess','posuccess','gmsuccess');
$config->messagecenter->objectTypes['putproduction']['completedAndIncompletedAction']['review']      = array('waitdept','waitleader','waitgm','waitproduct','waitdelivery');
$config->messagecenter->objectTypes['infoqz']['completedAndIncompletedAction']['review']      = array('cmconfirmed','groupsuccess','managersuccess','systemsuccess','posuccess','leadersuccess');
$config->messagecenter->objectTypes['requirement']['completedAndIncompletedAction']['createfeedback']  = array('todepartapproved');
$config->messagecenter->objectTypes['credit']['completedAndIncompletedAction']['review']      = array('waitdept','waitleader','waitgm','waitproductsecond');
$config->messagecenter->objectTypes['change']['completedAndIncompletedAction']['review']      = array('waitcountersign','wait','waitmasterpropm','qasuccess','cmconfirmed','managersuccess','productmanagersuccess','frameworkmanagersuccess','leadersuccess');

/*countersignCompletedAndIncompletedAction 会签消息中心加待办及已办*/
$config->messagecenter->objectTypes['change']['countersignCompletedAndIncompletedAction']          = array('reviewed');

/*会签审批动作对应单子状态*/
$config->messagecenter->objectTypes['change']['countersignCompletedAndIncompletedAction']['review']           = array('waitcountersign');

/*$config->messagecenter->available = array();
$config->messagecenter->available['message']['modify']['completedAction']                         = $config->message->objectTypes['modify']['completedAction'];
$config->messagecenter->available['message']['modify']['completedAndIncompletedAction']           = $config->message->objectTypes['modify']['completedAndIncompletedAction'];
$config->messagecenter->available['message']['outwarddelivery']['completedAction']                = $config->message->objectTypes['outwarddelivery']['completedAction'];
$config->messagecenter->available['message']['outwarddelivery']['completedAndIncompletedAction']  = $config->message->objectTypes['outwarddelivery']['completedAndIncompletedAction'];
$config->messagecenter->available['message']['sectransfer']['completedAction']                    = $config->message->objectTypes['sectransfer']['completedAction'];
$config->messagecenter->available['message']['sectransfer']['completedAndIncompletedAction']      = $config->message->objectTypes['sectransfer']['completedAndIncompletedAction'];*/

/*各模块和消息中心字段印射*/
$config->messagecenter->modifyFields      = array(
    'desc'             => 'desc',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'dealUser',
    'version'          => 'version'
);

$config->messagecenter->outwarddeliveryFields      = array(
    'desc'             => 'outwardDeliveryDesc',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'dealUser',
    'version'          => 'version'
);

$config->messagecenter->sectransferFields      = array(
    'desc'             => 'protransferDesc',
    'code'             => 'id',
    'objectId'         => 'id',
    'deptId'           => 'dept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'approver',
    'version'          => 'version'
);

$config->messagecenter->problemFields     = array(
    'desc'             => 'abstract',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'ReviewStatus',
    'reviewer'         => 'feedbackToHandle',
    'version'          => 'version'
);

$config->messagecenter->infoFields     = array(
    'desc'             => 'desc',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'reviewers',
    'version'          => 'version'
);
$config->messagecenter->putproductionFields     = array(
    'desc'             => 'desc',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'dealUser',
    'version'          => 'version'
);
$config->messagecenter->infoqzFields     = array(
    'desc'             => 'desc',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'dealUsers',
    'version'          => 'version'
);
$config->messagecenter->requirementFields     = array(
    'desc'             => 'name',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'feedbackStatus',
    'reviewer'         => 'feedbackDealUser',
    'version'          => 'version'
);
$config->messagecenter->creditFields     = array(
    'desc'             => 'summary',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'dealUsers',
    'version'          => 'version'
);
$config->messagecenter->changeFields     = array(
    'desc'             => 'reason',
    'code'             => 'code',
    'objectId'         => 'id',
    'deptId'           => 'createdDept',
    'formCreatedBy'    => 'createdBy',
    'formCreatedDate'  => 'createdDate',
    'formStatus'       => 'status',
    'reviewer'         => 'dealUsers',
    'version'          => 'version'
);