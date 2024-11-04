<?php
$config->api->demandParams = array(
    'Demand_number',
    'Demand_Department',
    'Demand_contact',
    'Contact_telephone',
    'Proposed_date',
    'Expected_realization_date',
    'Demand_name',
    'User_demand_background',
    'Feasibility_study_report',
    'RequirementType', //需求类型
    'Requirement_documents',
    'User_demand_backgrounds',
    'Degree_of_urgency',
    'Demand_category'
);

$config->api->demandFields      = array(
    'Demand_number'             => 'demandCode',
    'Demand_Department'         => 'sourceName',
    'Demand_contact'            => 'contact',
    'Contact_telephone'         => 'contactInfo',
    'Proposed_date'             => 'date',
    'Expected_realization_date' => 'deadline',
    'Demand_name'               => 'name',
    'User_demand_background'    => 'overview',
    'Feasibility_study_report'  => 'downloadFile',
    'RequirementType'            => 'type',
    'Requirement_documents'     => 'downloadFile',
    'File_qitawenjian'          => 'downloadFile',
    'User_demand_backgrounds'   => 'background',
    'Degree_of_urgency'         => 'urgency',
    'Demand_category'           => 'category'
);

$config->api->changeParams = array(
    'Change_number',
    'Demand_number',
    'Change_background',
    'Change_content',
    'General_manager',
    'Product_manager',
    'Change_entry',
    'Circumstance',
    'Missed_demolition'
);

$config->api->changeFields = array(
    'Change_number'        =>'changeNumber',
    'Demand_number'        =>'demandNumber',
    'Change_background'    =>'changeBackground',
    'Change_content'       =>'changeContent',
    'General_manager'      =>'generalManager',
    'Product_manager'      =>'productManager',
    'Change_entry'         =>'changeEntry',
    'Circumstance'         =>'circumstance',
    'Missed_demolition'    =>'missedDemolition',
);

$config->api->entriesParams = array(
    'Demand_item_name',
    'Demand_item_number',
    'Demand_number',
    'Requirement_item_description',
    'Product',
    'Product_Line',
    'ChangeOrder_number',
    'Canceled',
    'ChildName',
    'RequirementType', //需求类型
    'ProductRequireStartTime', //需求启动时间
//    'isImprovementServices',
//    'estimateWorkload'

);

$config->api->entriesFields = array(
    'Demand_item_name' => 'name',
    'Demand_item_number' => 'entriesCode',
    'Demand_number' => 'parentCode',
    'Requirement_item_description' => 'desc',
    'Product' => 'product',
    'Product_Line' => 'line',
    'ChangeOrder_number'=>'changeOrderNumber',
    'Canceled' => 'canceled',
    'isImprovementServices' => 'isImprovementServices',
    'ChildName' => 'ChildName',
    'end' => 'end',
    'RequirementType' => 'type',
    'ProductRequireStartTime' => 'requireStartTime'
);
$config->api->entriesNoEdit = array(
    'Demand_item_number',
    'Demand_number',
    'ChangeOrder_number',
    'ChildName',
    'end'
);
$config->api->problemParams = array(
    'acceptUserDepName',
    'businessFunctionAffect',
    'businessSystemIdList',
    'createUserContact',
    'dataCenterNameList',
    'occurTime',
    'problemAppearance',
    'problemLevel',
    'problemSource',
    'problemSummary',
    'problemType',
    'recoverTime',
    'reportTime',
    'IssueId'
);

$config->api->problemFields = array(
    'acceptUserDepName' => 'DepIdofIssueCreator',
    'businessFunctionAffect' => 'EffectOfService',
    'businessSystemIdList' => 'app',
    'createUserContact' => 'TeleNoOfCreator',
    'dataCenterNameList' => 'NodeIdOfIssue',
    'occurTime' => 'occurDate',
    'problemAppearance' => 'desc',
    'problemLevel' => 'severity',
    'problemSource' => 'source',
    'problemSummary' => 'abstract',
    'problemType' => 'type',
    'recoverTime' => 'RecoveryTime',
    'reportTime' => 'TimeOfReport',
    'changeIdUniqueCausedBy' => 'ChangeIdRelated',
    'drillIdUniqueCausedBy' => 'DrillCausedBy',
    'eventIdUniqueCausedBy' => 'IncidentIdRelated',
    'improveSuggestion' => 'Optimization',
    'IssueId' => 'IssueId'
);

$this->config->api->reviewedParams = array(
    'audit_result',
    'audit_opinion',
    'feedback_id'
);

$this->config->api->deletedParams = array(
    'itemId',
    'itemType'
);

$config->api->changeordertimeParams = array(
    'changeOrderId',
    'expectedStartTime',
    'expectedEndTime',
    'backspaceExpectedEndTime',
    'backspaceExpectedStartTime',

);

$config->api->changeordertimeFields = array(
    'code' => 'changeOrderId',
    'planBegin' => 'expectedStartTime',
    'planEnd' => 'expectedEndTime',
    'backspaceExpectedEndTime' => 'backspaceExpectedEndTime',
    'backspaceExpectedStartTime' => 'backspaceExpectedStartTime',
);

$config->api->getprojectFields = array(
    'deleted' => 'deleted',
);

$config->api->getProjectReviewFields = array(
    'projectNumber' => 'projectNumber',
);

$config->api->getapplicationFields = array(
    'deleted' => 'deleted',
);

$config->api->getproductFields = array(
    'deleted' => 'deleted',
    'appIdList' => 'appIdList',
);
$this->config->partitionFields = array(
    'partition_name'        => '',
    'application'           => '',
    'application_cn_name'   => '',
    'manage_ip'             => '',
    'location'              => '',
    'ciKey'                 => '',
    'deleted'               => '',
);

