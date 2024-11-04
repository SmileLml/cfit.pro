<?php
$config->cm = new stdclass();

$config->cm->create = new stdclass();
$config->cm->edit   = new stdclass();

$config->cm->create->requiredFields = 'title';
$config->cm->edit->requiredFields   = 'title';

$config->cm->list = new stdclass();
$config->cm->list->exportFields = 'title,type,cmAndDate,reviewerAndDate,itemname,itemcode,version,changed,changedID,changedDate,path,comment';
