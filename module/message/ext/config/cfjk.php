<?php
//$config->message->objectTypes['opinion']        = array('created', 'deleted', 'edited','assigned','review','synccreated','syncupdated','editassignedto','changed','editchanged','reviewchange');
$config->message->objectTypes['opinion']        = array('created', 'deleted', 'edited','assigned','review','synccreated','syncupdated','editassignedto','changed','editchanged','reviewchange','deleteout');
$config->message->objectTypes['requirement']    = array('createfeedbacked','created', 'edited', 'assigned', 'reviewed', 'deleted','syncstatus','subdivided','sendmailByOutTimeInside','sendmailByOutTimeOutSide','changed','editchanged','reviewchange','deleteout');
$config->message->objectTypes['processimprove'] = array('created', 'edited');
$config->message->objectTypes['duty']           = array('created', 'edited');
$config->message->objectTypes['component']           = array('reviewed', 'submit', 'changeteamreviewer','publish');
$config->message->objectTypes['datamanagement']           = array('destroyreview','destroyexecution','delayed','delay','destroyreviewed','destroy');
$config->message->objectTypes['copyrightqz']    = array('createdsubmit','editedsubmit','review','reject','copyrightqzsyncfeedback');
$config->message->objectTypes['copyright']    = array('createdsubmit','editedsubmit','review','reject');

$config->message->available['mail']['opinion']        = $config->message->objectTypes['opinion'];
$config->message->available['mail']['requirement']    = $config->message->objectTypes['requirement'];
$config->message->available['mail']['processimprove'] = $config->message->objectTypes['processimprove'];
$config->message->available['mail']['duty']           = $config->message->objectTypes['duty'];

$config->message->objectTypes['projectplan'] = array('yearreview', 'yearreviewing', 'submitapproval', 'planapproval','yearreject','changereview','planchange','yearbatchreviewing');
$config->message->objectTypes['problem']     = array('created', 'deal', 'started','edited','review','createfeedback','syncfail','syncstatus','update','jxsyncfail','closed','problemdelay','reviewdelay','insideFeedback','sendmailBySolvingOutTime','outsideFeedback','problemchange','problemreviewchange','secondeal', 'assigned', 'toEndRemindMailFirst', 'toEndRemindMailSecond');
$config->message->objectTypes['demand']      = array('created', 'deal', 'started','edited','assigned','deleted','demanddelay','reviewdelay','sendmailByOutTime','toEndRemindMailFirst', 'toEndRemindMailSecond');
$config->message->objectTypes['modify']      = array('review', 'submit','reject','jxsyncmodifyfail','jxsynccancelmodifyfail','feedbacksynedit','feedbacksyn','modifysyncstatus','cancelchange','isdiskdelivery','canceled','deal','linkrelease');  //金信交付-生产变更
$config->message->objectTypes['info']        = array('created', 'review', 'linkrelease', 'reject', 'deal');  //金信交付-数据获取/数据修正
$config->message->objectTypes['infoqz']      = array('review', 'created', 'reject', 'syncstatus', 'editstatus','infoqzsynfailed','deal'); //清总交付-数据获取
$config->message->objectTypes['modifycncc']  = array('created', 'reject', 'syncstatus', 'editstatus','modifycnccsyncstatus'); //清总交付-生产变更
$config->message->objectTypes['residentsupport'] = array('submit', 'reviewed', 'reviewedconfirm'); //驻场支持
$config->message->objectTypes['putproduction'] = array('reviewed','created','assigned','edited','submited','syncstatus','deal','canceld'); //金信投产移交
$config->message->objectTypes['productionchange'] = array('submitreview','reviewed'); //内部自建投产变更
$config->message->objectTypes['cmdbsync'] = array('synccreated'); //cmdb同步
$config->message->objectTypes['credit'] = array('created','edited', 'submited','reviewed','canceled','deleted'); //征信交付
$config->message->objectTypes['localesupport'] = array('submited','reviewed'); //现场支持
$config->message->objectTypes['qualitygate'] = array('created', 'edited', 'assigned'); //安全门禁

$config->message->available['mail']['projectplan'] = $config->message->objectTypes['projectplan'];
$config->message->available['mail']['problem']     = $config->message->objectTypes['problem'];
$config->message->available['mail']['demand']      = $config->message->objectTypes['demand'];
$config->message->available['mail']['modify']      = $config->message->objectTypes['modify'];
$config->message->available['mail']['info']        = $config->message->objectTypes['info'];
$config->message->available['mail']['infoqz']      = $config->message->objectTypes['infoqz'];
$config->message->available['mail']['modifycncc']  = $config->message->objectTypes['modifycncc'];
$config->message->available['mail']['component']  = $config->message->objectTypes['component'];
$config->message->available['mail']['datamanagement']  = $config->message->objectTypes['datamanagement'];
$config->message->available['mail']['residentsupport']  = $config->message->objectTypes['residentsupport'];
$config->message->available['mail']['putproduction']  = $config->message->objectTypes['putproduction'];
$config->message->available['mail']['productionchange']  = $config->message->objectTypes['productionchange'];
$config->message->available['mail']['credit']         = $config->message->objectTypes['credit'];
$config->message->available['mail']['copyrightqz']  = $config->message->objectTypes['copyrightqz'];
$config->message->available['mail']['copyright']  = $config->message->objectTypes['copyright'];
$config->message->available['mail']['cmdbsync']      = $config->message->objectTypes['cmdbsync'];
$config->message->available['mail']['localesupport'] = $config->message->objectTypes['localesupport'];
$config->message->available['mail']['qualitygate'] = $config->message->objectTypes['qualitygate'];


/*$config->message->objectTypes['review'] = array('opened', 'reloadsubmit', 'edited');*/
$config->message->objectTypes['review']        = array('created', 'edited','closed', 'autoclosed','applyreview', 'assigning', 'reviewed', 'suspend', 'renew');
$config->message->available['mail']['review']  = $config->message->objectTypes['review'];

$config->message->objectTypes['reviewmeeting'] = array('reviewed', 'finishmeetingsummary','autoupdatestatus');
$config->message->available['mail']['reviewmeeting'] = $config->message->objectTypes['reviewmeeting'];

$config->message->objectTypes['change'] = array( 'reviewed', 'applychange','appoint');
$config->message->available['mail']['change'] = $config->message->objectTypes['change'];

$config->message->objectTypes['outwarddelivery']       = array('created','modifycncceditstatus', 'edited','modifycnccsyncstatus','canceled','productenrollsyncfeedback', 'reject', 'testrequestfeedback','review', 'modifycnccsyncfeedback','submitexamine',
                                                        'testrequesteditfeedback','linkrelease','productenrolleditfeedback','qingzongsynfailed','modifycncceditfeedback','deal');
$config->message->available['mail']['outwarddelivery'] = $config->message->objectTypes['outwarddelivery'];

$config->message->objectTypes['build']        = array('opened','edited','deal','batchdeal');
$config->message->available['mail']['build']  = $config->message->objectTypes['build'];

$config->message->objectTypes['secondorder'] = array('created','edited','deal','statusedit','reviewedconfirm','sync','syncstatus','dealReturned','assigned');
$config->message->available['mail']['secondorder'] = $config->message->objectTypes['secondorder'];

$config->message->objectTypes['deptorder'] = array('created','edited','deal','statusedit');
$config->message->available['mail']['deptorder'] = $config->message->objectTypes['deptorder'];

$config->message->objectTypes['defect'] = array('created','edited','deal','reviewedconfirm','syncfail','applychange','feedbacked');
$config->message->available['mail']['defect'] = $config->message->objectTypes['defect'];

$config->message->objectTypes['requirementChange'] = array('changeorder');
$config->message->available['mail']['requirementChange'] = $config->message->objectTypes['requirementChange'];

$config->message->objectTypes['sectransfer'] = array('reviewed','deal','syncstatus','dealed','syncfail','reject');
$config->message->available['mail']['sectransfer'] = $config->message->objectTypes['sectransfer'];

$config->message->objectTypes['closingitem'] = array('created','submitexamine','reviewed');
$config->message->available['mail']['closingitem'] = $config->message->objectTypes['closingitem'];

$config->message->objectTypes['closingadvise'] = array('assigned','reviewed');
$config->message->available['mail']['closingadvise'] = $config->message->objectTypes['closingadvise'];

$config->message->objectTypes['osspchange'] = array('created','submited','confirm','reviewed','closed','edited');
$config->message->available['mail']['osspchange'] = $config->message->objectTypes['osspchange'];

$config->message->objectTypes['issue'] = array('assigned','assignedtoframeworked');
$config->message->available['mail']['issue'] = $config->message->objectTypes['issue'];

$config->message->objectTypes['risk'] = array('assigned','assignedtoframeworked');
$config->message->available['mail']['risk'] = $config->message->objectTypes['risk'];

$config->message->objectTypes['environmentorder'] = array('submited','assigned','deal');
$config->message->available['mail']['environmentorder'] = $config->message->objectTypes['environmentorder'];

$config->message->objectTypes['authorityapply'] = array('submited','deal');
$config->message->available['mail']['authorityapply'] = $config->message->objectTypes['authorityapply'];