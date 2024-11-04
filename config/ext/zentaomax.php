<?php
$config->version    = 'max2.0';
$config->maxVersion = '2.0';

if(!defined('TABLE_AUDITCL'))            define('TABLE_AUDITCL', '`' . $config->db->prefix . 'auditcl`');
if(!defined('TABLE_AUDITPLAN'))          define('TABLE_AUDITPLAN', '`' . $config->db->prefix . 'auditplan`');
if(!defined('TABLE_AUDITRESULT'))        define('TABLE_AUDITRESULT', '`' . $config->db->prefix . 'auditresult`');
if(!defined('TABLE_ACTIVITY'))           define('TABLE_ACTIVITY', '`' . $config->db->prefix . 'activity`');
if(!defined('TABLE_BUDGET'))             define('TABLE_BUDGET', '`' . $config->db->prefix . 'budget`');
if(!defined('TABLE_BASICMEAS'))          define('TABLE_BASICMEAS', '`' . $config->db->prefix . 'basicmeas`');
if(!defined('TABLE_HOLIDAY'))            define('TABLE_HOLIDAY', '`' . $config->db->prefix . 'holiday`');
if(!defined('TABLE_DESIGN'))             define('TABLE_DESIGN', '`' . $config->db->prefix . 'design`');
if(!defined('TABLE_DESIGNSPEC'))         define('TABLE_DESIGNSPEC', '`' . $config->db->prefix . 'designspec`');
if(!defined('TABLE_PROGRAMPROCESS'))     define('TABLE_PROGRAMPROCESS', '`' . $config->db->prefix . 'programprocess`');
if(!defined('TABLE_PROGRAMACTIVITY'))    define('TABLE_PROGRAMACTIVITY', '`' . $config->db->prefix . 'programactivity`');
if(!defined('TABLE_PROGRAMOUTPUT'))      define('TABLE_PROGRAMOUTPUT', '`' . $config->db->prefix . 'programoutput`');
if(!defined('TABLE_PROGRAMPLAN'))        define('TABLE_PROGRAMPLAN', '`'   . $config->db->prefix . 'programplan`');
if(!defined('TABLE_NC'))                 define('TABLE_NC', '`' . $config->db->prefix . 'nc`');
if(!defined('TABLE_RELATION'))           define('TABLE_RELATION', '`' . $config->db->prefix . 'relation`');
if(!defined('TABLE_TASKSPEC'))           define('TABLE_TASKSPEC', '`' . $config->db->prefix . 'taskspec`');
if(!defined('TABLE_OBJECT'))             define('TABLE_OBJECT', '`' . $config->db->prefix . 'object`');
if(!defined('TABLE_REVIEW'))             define('TABLE_REVIEW', '`' . $config->db->prefix . 'review`');
if(!defined('TABLE_REVIEWCL'))           define('TABLE_REVIEWCL', '`' . $config->db->prefix . 'reviewcl`');
if(!defined('TABLE_REVIEWRESULT'))       define('TABLE_REVIEWRESULT', '`' . $config->db->prefix . 'reviewresult`');
if(!defined('TABLE_REVIEWISSUE'))        define('TABLE_REVIEWISSUE', '`' . $config->db->prefix . 'reviewissue`');
if(!defined('TABLE_RELATIONOFTASKS'))    define('TABLE_RELATIONOFTASKS', '`' . $config->db->prefix . 'relationoftasks`');
if(!defined('TABLE_CMCL'))               define('TABLE_CMCL', '`' . $config->db->prefix . 'cmcl`');
if(!defined('TABLE_ISSUE'))              define('TABLE_ISSUE', '`' . $config->db->prefix . 'issue`');
if(!defined('TABLE_SOLUTIONS'))          define('TABLE_SOLUTIONS', '`' . $config->db->prefix . 'solutions`');
if(!defined('TABLE_STAGE'))              define('TABLE_STAGE', '`' . $config->db->prefix . 'stage`');
if(!defined('TABLE_PROJECT'))            define('TABLE_PROJECT', '`' . $config->db->prefix . 'project`');
if(!defined('TABLE_PROJECTPLANRELATION'))            define('TABLE_PROJECTPLANRELATION', '`' . $config->db->prefix . 'projectplanrelation`'); //管理产品计划表名
if(!defined('TABLE_PROCESS'))            define('TABLE_PROCESS', '`' . $config->db->prefix . 'process`');
if(!defined('TABLE_MEASTEMPLATE'))       define('TABLE_MEASTEMPLATE', '`' . $config->db->prefix . 'meastemplate`');
if(!defined('TABLE_DERIVEMEAS'))         define('TABLE_DERIVEMEAS', '`' . $config->db->prefix . 'derivemeas`');
if(!defined('TABLE_PROGRAMREPORT'))      define('TABLE_PROGRAMREPORT', '`' . $config->db->prefix . 'programreport`');
if(!defined('TABLE_MEASRECORDS'))        define('TABLE_MEASRECORDS', '`' . $config->db->prefix . 'measrecords`');
if(!defined('TABLE_MEASQUEUE'))          define('TABLE_MEASQUEUE', '`' . $config->db->prefix . 'measqueue`');
if(!defined('TABLE_TASK'))               define('TABLE_TASK', '`' . $config->db->prefix . 'task`');
if(!defined('TABLE_ISSUE'))              define('TABLE_ISSUE', '`' . $config->db->prefix . 'issue`');
if(!defined('TABLE_RISK'))               define('TABLE_RISK', '`' . $config->db->prefix . 'risk`');
if(!defined('TABLE_EFFORT'))             define('TABLE_EFFORT', '`' . $config->db->prefix . 'effort`');
if(!defined('TABLE_INTERVENTION'))       define('TABLE_INTERVENTION', '`' . $config->db->prefix . 'intervention`');
if(!defined('TABLE_WEEKLYREPORT'))       define('TABLE_WEEKLYREPORT', '`' . $config->db->prefix . 'weeklyreport`');
if(!defined('TABLE_DURATIONESTIMATION')) define('TABLE_DURATIONESTIMATION', '`' . $config->db->prefix . 'durationestimation`');
if(!defined('TABLE_WORKESTIMATION'))     define('TABLE_WORKESTIMATION', '`' . $config->db->prefix . 'workestimation`');
if(!defined('TABLE_ZOUTPUT'))            define('TABLE_ZOUTPUT', '`' . $config->db->prefix . 'zoutput`');
if(!defined('TABLE_FLOW_PUBLISH'))       define('TABLE_FLOW_PUBLISH', '`' . $config->db->prefix . 'flow_publish`');
if(!defined('TABLE_PUBLISHRECORD'))      define('TABLE_PUBLISHRECORD', '`' . $config->db->prefix . 'publishrecord`');
if(!defined('TABLE_DEMANDCOLLECTION'))   define('TABLE_DEMANDCOLLECTION', '`' . $config->db->prefix . 'demandcollection`');
if(!defined('TABLE_MODIFYCNCC'))         define('TABLE_MODIFYCNCC', '`' . $config->db->prefix . 'modifycncc`');
if(!defined('TABLE_SECONDORDER'))        define('TABLE_SECONDORDER', '`' . $config->db->prefix . 'secondorder`');
if(!defined('TABLE_REVIEWQZ'))           define('TABLE_REVIEWQZ', '`' . $config->db->prefix . 'reviewqz`');
if(!defined('TABLE_DEPTORDER'))          define('TABLE_DEPTORDER', '`' . $config->db->prefix . 'deptorder`');
if(!defined('TABLE_WHOLE_REPORT'))          define('TABLE_WHOLE_REPORT', '`' . $config->db->prefix . 'whole_report`');
if(!defined('TABLE_DETAIL_REPORT'))          define('TABLE_DETAIL_REPORT', '`' . $config->db->prefix . 'detail_report`');
if(!defined('TABLE_SECONDMONTHHISTORYDATA'))          define('TABLE_SECONDMONTHHISTORYDATA', '`' . $config->db->prefix . 'secondmonthhistorydata`');

$config->objectTables['review'] = TABLE_REVIEW;
$config->objectTables['budget'] = TABLE_BUDGET;
$config->objectTables['risk']   = TABLE_RISK;
$config->objectTables['issue']  = TABLE_ISSUE;
$config->objectTables['design'] = TABLE_DESIGN;
$config->objectTables['reviewqz'] = TABLE_REVIEWQZ;

$config->projectModules = 'story,product,bug,task,project,flow,repo,productplan,release,testcase,testtask,testreport,testsuite,deploy,doc';

$filter->project->burn = new stdclass();
$filter->project->burn->cookie['burnBy'] = 'code';

$filter->product->burn = new stdclass();
$filter->product->burn->cookie['leftProjects'] = 'code';

$config->excludeFlows = array();

$config->hourPointCommonList['zh-cn'][2] = '功能点';
$config->hourPointCommonList['zh-tw'][2] = '功能點';
$config->hourPointCommonList['en'][2]    = 'function point';
$config->hourPointCommonList['de'][2]    = 'function point';
$config->hourPointCommonList['fr'][2]    = 'function point';

$config->hourPointCommonList['zh-cn'][3] = '代码行';
$config->hourPointCommonList['zh-tw'][3] = '代码行';
$config->hourPointCommonList['en'][3]    = 'loc';
$config->hourPointCommonList['de'][3]    = 'loc';
$config->hourPointCommonList['fr'][3]    = 'loc';

$filter->custom->setcmmi->cookie['systemModel'] = 'code';
$filter->custom->setscrum->cookie['sytemModel'] = 'code';

$filter->product->submit = new stdclass();
$filter->story->submit = new stdclass();
$filter->testcase->submit = new stdclass();
$filter->default->cookie['hideMenu']             = 'equal::true';
$filter->product->submit->cookie['checkedItem']  = 'reg::checked';
$filter->story->submit->cookie['checkedItem']    = 'reg::checked';
$filter->testcase->submit->cookie['checkedItem'] = 'reg::checked';
