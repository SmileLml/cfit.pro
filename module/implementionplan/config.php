<?php
$config->implementionplan = new stdclass();
$config->implementionplan->uploadplan  = new stdclass();

$config->implementionplan->uploadplan->requiredFields   = 'file,level';
$config->implementionplan->uploadplan->delete           = 'comment';

$config->implementionplan->editor = new stdclass();
$config->implementionplan->editor->uploadplan         = array('id' => '', 'tools' => 'simpleTools','height'=>'50px;');
$config->implementionplan->editor->delete             = array('id' => 'comment', 'tools' => 'simpleTools');