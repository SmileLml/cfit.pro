<?php
$config->requirementspec          = new stdclass();
$config->requirementspec->change   = new stdclass();

$config->requirementspec->change->requiredFields   = 'reviewer,dept,end,owner,contact,method,analysis,handling';

$config->requirementspec->editor = new stdclass();
$config->requirementspec->editor->change   = array('id' => 'analysis,handling,implement,desc', 'tools' => 'simpleTools');
