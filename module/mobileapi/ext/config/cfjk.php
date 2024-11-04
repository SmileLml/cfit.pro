<?php
$config->mobileapi->demandParams = array(
    'Demand_number',
    'Demand_Department',
    'Demand_contact',
    'Contact_telephone',
    'Proposed_date',
    'Expected_realization_date',
    'Demand_name',
    'User_demand_background',
    'Feasibility_study_report',
    'Requirement_documents'
);

$config->mobileapi->demandFields      = array(
    'Demand_number'             => 'demandCode',
    'Demand_Department'         => 'sourceName',
    'Demand_contact'            => 'contact',
    'Contact_telephone'         => 'contactInfo',
    'Proposed_date'             => 'date',
    'Expected_realization_date' => 'deadline',
    'Demand_name'               => 'name',
    'User_demand_background'    => 'background',
    'Feasibility_study_report'  => 'downloadFile',
    'Requirement_documents'     => 'downloadFile',
    'File_qitawenjian'     => 'downloadFile'
);

$config->mobileapi->changeParams = array(
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

$config->mobileapi->changeFields = array(
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

$config->mobileapi->entriesParams = array(
    'Demand_item_name',
    'Demand_item_number',
    'Demand_number',
    'Requirement_item_description',
    'Product',
    'Product_Line',
    'ChangeOrder_number',
    'Canceled',
    'ChildName',
//    'isImprovementServices',
//    'estimateWorkload'

);

$config->mobileapi->entriesFields = array(
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
//    'estimateWorkload' => 'estimateWorkload'
);
$config->mobileapi->entriesNoEdit = array(
    'Demand_item_number',
    'Demand_number',
    'ChangeOrder_number',
    'ChildName'
);
$config->mobileapi->problemParams = array(
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

$config->mobileapi->problemFields = array(
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

$this->config->mobileapi->reviewedParams = array(
    'audit_result',
    'audit_opinion',
    'feedback_id'
);

$this->config->mobileapi->deletedParams = array(
    'itemId',
    'itemType'
);

$config->mobileapi->changeordertimeParams = array(
    'changeOrderId',
    'expectedStartTime',
    'expectedEndTime',
    'backspaceExpectedEndTime',
    'backspaceExpectedStartTime',

);

$config->mobileapi->changeordertimeFields = array(
    'code' => 'changeOrderId',
    'planBegin' => 'expectedStartTime',
    'planEnd' => 'expectedEndTime',
    'backspaceExpectedEndTime' => 'backspaceExpectedEndTime',
    'backspaceExpectedStartTime' => 'backspaceExpectedStartTime',
);

$config->mobileapi->getprojectFields = array(
    'deleted' => 'deleted',
);

$config->mobileapi->getProjectReviewFields = array(
    'projectNumber' => 'projectNumber',
);

$config->mobileapi->getapplicationFields = array(
    'deleted' => 'deleted',
);

$config->mobileapi->getproductFields = array(
    'deleted' => 'deleted',
    'appIdList' => 'appIdList',
);

