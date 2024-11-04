<?php
$config->search->fields->issue = new stdclass();
$config->search->fields->issue->id         = 'id';
$config->search->fields->issue->title      = 'title';
$config->search->fields->issue->content    = 'desc';
$config->search->fields->issue->addedDate  = 'createdDate';
$config->search->fields->issue->editedDate = 'editedDate';

$config->search->fields->risk = new stdclass();
$config->search->fields->risk->id         = 'id';
$config->search->fields->risk->title      = 'name';
$config->search->fields->risk->content    = 'remedy,prevention';
$config->search->fields->risk->addedDate  = 'createdDate';
$config->search->fields->risk->editedDate = 'editedDate';
