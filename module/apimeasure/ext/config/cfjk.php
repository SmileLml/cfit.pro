<?php
$config->api = new stdClass();
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
    'Requirement_documents'
);

$config->api->demandFields      = array(
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
//    'ChangeOrder_number',
//    'Canceled'
);

$config->api->entriesFields = array(
    'Demand_item_name' => 'name',
    'Demand_item_number' => 'entriesCode',
    'Demand_number' => 'parentCode',
    'Requirement_item_description' => 'desc',
    'Product' => 'product',
    'Product_Line' => 'line',
    'ChangeOrder_number'=>'changeOrderNumber',
    'Canceled' => 'canceled'
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

$config->api->getProjectReviewFields = array(
    'projectNumber' => 'projectNumber',
);



