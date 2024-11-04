<?php
$config->workflow->require->create .= ',navigator';
$config->workflow->require->edit   .= ',navigator';

$config->workflow->buildin = new stdclass();
$config->workflow->buildin->modules = new stdclass();
$config->workflow->buildin->modules->product = new stdclass();
$config->workflow->buildin->modules->product->product     = array('table' => TABLE_PRODUCT,     'navigator' => 'primary');
$config->workflow->buildin->modules->product->productplan = array('table' => TABLE_PRODUCTPLAN, 'navigator' => 'secondary');
$config->workflow->buildin->modules->product->release     = array('table' => TABLE_RELEASE,     'navigator' => 'secondary');
$config->workflow->buildin->modules->product->story       = array('table' => TABLE_STORY,       'navigator' => 'secondary');

$config->workflow->buildin->modules->project = new stdclass();
$config->workflow->buildin->modules->project->program   = array('table' => TABLE_PROJECT, 'navigator' => 'primary');
$config->workflow->buildin->modules->project->project   = array('table' => TABLE_PROJECT, 'navigator' => 'primary');
$config->workflow->buildin->modules->project->execution = array('table' => TABLE_PROJECT, 'navigator' => 'primary');
$config->workflow->buildin->modules->project->build     = array('table' => TABLE_BUILD,   'navigator' => 'secondary');
$config->workflow->buildin->modules->project->task      = array('table' => TABLE_TASK,    'navigator' => 'secondary');

$config->workflow->buildin->modules->qa = new stdclass();
$config->workflow->buildin->modules->qa->bug       = array('table' => TABLE_BUG,       'navigator' => 'secondary');
$config->workflow->buildin->modules->qa->testcase  = array('table' => TABLE_CASE,      'navigator' => 'secondary');
$config->workflow->buildin->modules->qa->testtask  = array('table' => TABLE_TESTTASK,  'navigator' => 'secondary');
$config->workflow->buildin->modules->qa->testsuite = array('table' => TABLE_TESTSUITE, 'navigator' => 'secondary');
$config->workflow->buildin->modules->qa->caselib   = array('table' => TABLE_TESTSUITE, 'navigator' => 'secondary');

$config->workflow->buildin->modules->feedback = new stdclass();
$config->workflow->buildin->modules->feedback->feedback = array('table' => TABLE_FEEDBACK, 'navigator' => 'primary');

$config->workflow->buildin->subStatus = new stdclass();
$config->workflow->buildin->subStatus->modules = array('product', 'release', 'story', 'project', 'task', 'bug', 'testcase', 'testtask', 'feedback');
