<?php
$config->demandcollection = new stdclass();
$config->demandcollection->create = new stdclass();
$config->demandcollection->edit   = new stdclass();
$config->demandcollection->deal   = new stdclass();
$config->demandcollection->closed   = new stdclass();

$config->demandcollection->create->requiredFields='submitter,title,type,productmanager';
$config->demandcollection->edit->requiredFields=$config->demandcollection->create->requiredFields;
$config->demandcollection->deal->requiredFields='title,submitter,type,product,productmanager';
$config->demandcollection->closed->requiredFields='state';

$config->demandcollection->editor = new stdclass();
$config->demandcollection->editor->create = array('id' => 'desc,commConfirmRecord', 'tools' => 'simpleTools');
$config->demandcollection->editor->deal = array('id' => 'desc,commConfirmRecord,analysis,scheme', 'tools' => 'simpleTools');
$config->demandcollection->editor->confirmed = array('id' => 'desc,analysis,scheme', 'tools' => 'simpleTools');
$config->demandcollection->editor->edit = array('id' => 'desc,analysis,scheme,commConfirmRecord', 'tools' => 'simpleTools');
$config->demandcollection->editor->syncdemand = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools');
$config->demandcollection->editor->view = array('id' => 'lastComment', 'tools' => 'simpleTools');

$config->demandcollection->list = new stdclass();
$config->demandcollection->list->exportFields = 'id,title,desc,analysis,storyId,dept,submitter,belongModel,belongPlatform,Implementation,priority,type,state,feedbackResult,developstate,productmanager,dealUser,processingDate,handoverDate,feedbackDate,scheduledDate,launchDate,Expected,Actual,Developer,createBy,createDate,updateBy,updateDate,handoverBy,handoverDate,confirmBy,confirmDate,closedBy,closedDate,responseDate,correctionReason,commConfirmBy,commConfirmRecord,demandId';

$config->demandcollection->datatable = new stdclass();
$config->demandcollection->datatable->defaultField = array('id', 'title', 'dept', 'type', 'priority', 'feedbackResult', 'submitter', 'belongPlatform' ,'belongModel', 'createDate','responseDate', 'processingDate', 'handoverDate', 'feedbackDate','scheduledDate','developstate','launchDate','Expected','Actual','Implementation','Developer','state','dealuser','actions');

$config->demandcollection->datatable->fieldList['id']['title']    	= 'id';
$config->demandcollection->datatable->fieldList['id']['fixed']    	= 'left';
$config->demandcollection->datatable->fieldList['id']['width']    	= '40';
$config->demandcollection->datatable->fieldList['id']['required'] 	= 'yes';

$config->demandcollection->datatable->fieldList['title']['title']    = 'title';
$config->demandcollection->datatable->fieldList['title']['fixed']    = 'left';
$config->demandcollection->datatable->fieldList['title']['width']    = '240';
$config->demandcollection->datatable->fieldList['title']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['dept']['title']    = 'dept';
$config->demandcollection->datatable->fieldList['dept']['fixed']    = 'left';
$config->demandcollection->datatable->fieldList['dept']['width']    = '120';
$config->demandcollection->datatable->fieldList['dept']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['type']['title']    = 'type';
$config->demandcollection->datatable->fieldList['type']['fixed']    = 'left';
$config->demandcollection->datatable->fieldList['type']['width']    = '90';
$config->demandcollection->datatable->fieldList['type']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['priority']['title']    = 'priority';
$config->demandcollection->datatable->fieldList['priority']['fixed']    = 'left';
$config->demandcollection->datatable->fieldList['priority']['width']    = '60';
$config->demandcollection->datatable->fieldList['priority']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['feedbackResult']['title']    = 'feedbackResult';
$config->demandcollection->datatable->fieldList['feedbackResult']['fixed']    = 'left';
$config->demandcollection->datatable->fieldList['feedbackResult']['width']    = '100';
$config->demandcollection->datatable->fieldList['feedbackResult']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['submitter']['title']    = 'submitter';
$config->demandcollection->datatable->fieldList['submitter']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['submitter']['width']    = '90';
$config->demandcollection->datatable->fieldList['submitter']['required'] = 'no';

$config->demandcollection->datatable->fieldList['belongModel']['title']    = 'bModel';
$config->demandcollection->datatable->fieldList['belongModel']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['belongModel']['width']    = '90';
$config->demandcollection->datatable->fieldList['belongModel']['required'] = 'no';

$config->demandcollection->datatable->fieldList['belongPlatform']['title']    = 'bPlatform';
$config->demandcollection->datatable->fieldList['belongPlatform']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['belongPlatform']['width']    = '90';
$config->demandcollection->datatable->fieldList['belongPlatform']['required'] = 'no';

$config->demandcollection->datatable->fieldList['createDate']['title']    = 'createDate';
$config->demandcollection->datatable->fieldList['createDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['createDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['createDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['responseDate']['title']    = 'responseDate';
$config->demandcollection->datatable->fieldList['responseDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['responseDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['responseDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['processingDate']['title']    = 'processingDate';
$config->demandcollection->datatable->fieldList['processingDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['processingDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['processingDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['handoverDate']['title']    = 'handoverDate';
$config->demandcollection->datatable->fieldList['handoverDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['handoverDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['handoverDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['feedbackDate']['title']    = 'feedbackDate';
$config->demandcollection->datatable->fieldList['feedbackDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['feedbackDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['feedbackDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['scheduledDate']['title']    = 'scheduledDate';
$config->demandcollection->datatable->fieldList['scheduledDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['scheduledDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['scheduledDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['developstate']['title']    = 'developstate';
$config->demandcollection->datatable->fieldList['developstate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['developstate']['width']    = '80';
$config->demandcollection->datatable->fieldList['developstate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['launchDate']['title']    = 'launchDate';
$config->demandcollection->datatable->fieldList['launchDate']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['launchDate']['width']    = '110';
$config->demandcollection->datatable->fieldList['launchDate']['required'] = 'no';

$config->demandcollection->datatable->fieldList['Expected']['title']    = 'Expected';
$config->demandcollection->datatable->fieldList['Expected']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['Expected']['width']    = '150';
$config->demandcollection->datatable->fieldList['Expected']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['Actual']['title']    = 'Actual';
$config->demandcollection->datatable->fieldList['Actual']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['Actual']['width']    = '150';
$config->demandcollection->datatable->fieldList['Actual']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['Implementation']['title']    = 'Implementation';
$config->demandcollection->datatable->fieldList['Implementation']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['Implementation']['width']    = '80';
$config->demandcollection->datatable->fieldList['Implementation']['required'] = 'no';

$config->demandcollection->datatable->fieldList['Developer']['title']    = 'Developer';
$config->demandcollection->datatable->fieldList['Developer']['fixed']    = 'no';
$config->demandcollection->datatable->fieldList['Developer']['width']    = '80';
$config->demandcollection->datatable->fieldList['Developer']['required'] = 'no';

$config->demandcollection->datatable->fieldList['state']['title']    = 'state';
$config->demandcollection->datatable->fieldList['state']['fixed']    = 'right';
$config->demandcollection->datatable->fieldList['state']['width']    = '80';
$config->demandcollection->datatable->fieldList['state']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['dealuser']['title']    = 'dealuser';
$config->demandcollection->datatable->fieldList['dealuser']['fixed']    = 'right';
$config->demandcollection->datatable->fieldList['dealuser']['width']    = '80';
$config->demandcollection->datatable->fieldList['dealuser']['required'] = 'yes';

$config->demandcollection->datatable->fieldList['actions']['title']    = 'actions';
$config->demandcollection->datatable->fieldList['actions']['fixed']    = 'right';
$config->demandcollection->datatable->fieldList['actions']['width']    = '160';
$config->demandcollection->datatable->fieldList['actions']['required'] = 'yes';


/* Search. */
global $lang;
$config->demandcollection->search['module']                          = 'demandcollection';
$config->demandcollection->search['fields']                            = array();
$config->demandcollection->search['fields']['title']                   = $lang->demandcollection->title;
$config->demandcollection->search['fields']['id']                      = $lang->demandcollection->id;
$config->demandcollection->search['fields']['dept']                  = $lang->demandcollection->dept;
$config->demandcollection->search['fields']['submitter']           = $lang->demandcollection->submitter;
$config->demandcollection->search['fields']['Implementation']  = $lang->demandcollection->Implementation;
$config->demandcollection->search['fields']['priority']              = $lang->demandcollection->priority;
$config->demandcollection->search['fields']['type']                   = $lang->demandcollection->type;
$config->demandcollection->search['fields']['state']                 = $lang->demandcollection->state;
$config->demandcollection->search['fields']['productmanager'] = $lang->demandcollection->productmanager;
$config->demandcollection->search['fields']['dealUser']            = $lang->demandcollection->dealUser;
$config->demandcollection->search['fields']['processingDate']   = $lang->demandcollection->processingDate;
$config->demandcollection->search['fields']['handoverDate']     = $lang->demandcollection->handoverDate;
$config->demandcollection->search['fields']['feedbackDate']     = $lang->demandcollection->feedbackDate;
$config->demandcollection->search['fields']['scheduledDate']    = $lang->demandcollection->scheduledDate;
$config->demandcollection->search['fields']['launchDate']         = $lang->demandcollection->launchDate;
$config->demandcollection->search['fields']['Expected']            = $lang->demandcollection->Expected;
$config->demandcollection->search['fields']['Actual']                = $lang->demandcollection->Actual;
$config->demandcollection->search['fields']['Developer']          = $lang->demandcollection->Developer;
$config->demandcollection->search['fields']['desc']                  = $lang->demandcollection->desc;
$config->demandcollection->search['fields']['analysis']             = $lang->demandcollection->analysis;
$config->demandcollection->search['fields']['belongPlatform']             = $lang->demandcollection->bPlatform;
$config->demandcollection->search['fields']['belongModel']             = $lang->demandcollection->bModel;
$config->demandcollection->search['fields']['commConfirmBy']             = $lang->demandcollection->commConfirmBy;
$config->demandcollection->search['fields']['commConfirmRecord']             = $lang->demandcollection->commConfirmRecord;
$config->demandcollection->search['fields']['correctionReason']        = $lang->demandcollection->correctionReason;
$config->demandcollection->search['fields']['demandId']        = $lang->demandcollection->demandId;

$config->demandcollection->search['params']['title']                = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandcollection->search['params']['id']                   = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->demandcollection->search['params']['dept']                 = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->demandcollection->search['params']['submitter']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demandcollection->search['params']['Implementation'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->demandcollection->search['params']['priority']    = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->demandcollection->search['params']['type']                 = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandcollection->typeList);
$config->demandcollection->search['params']['state']                = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandcollection->statusList);
$config->demandcollection->search['params']['productmanager'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->demandcollection->search['params']['dealUser']             = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->demandcollection->search['params']['processingDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandcollection->search['params']['handoverDate']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandcollection->search['params']['feedbackDate']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandcollection->search['params']['scheduledDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandcollection->search['params']['launchDate']       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandcollection->search['params']['Expected']          = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->demandcollection->search['params']['Actual']               = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->demandcollection->search['params']['Developer']         = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->demandcollection->search['params']['desc']                = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandcollection->search['params']['analysis']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandcollection->search['params']['belongModel']         = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandcollection->belongModel);
$config->demandcollection->search['params']['belongPlatform']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandcollection->belongPlatform);
$config->demandcollection->search['params']['commConfirmBy'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->demandcollection->search['params']['commConfirmRecord']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->demandcollection->search['params']['correctionReason']          = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandcollection->correctionReasonList);
$config->demandcollection->search['params']['demandId']          = array('operator' => '=', 'control' => 'select', 'values' => ['0' => '']);