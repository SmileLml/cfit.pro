<?php
$lang->conference->common         = 'Conference';
$lang->conference->admin          = 'Settings';
$lang->conference->notset         = 'Not set';
$lang->conference->settings       = 'Conference params';
$lang->conference->enabled        = 'Conference';
$lang->conference->enabledTip     = 'Enable conference';
$lang->conference->serverAddr     = 'Server Address';
$lang->conference->serverAddrTip  = '';
$lang->conference->apiPort        = 'API Port';
$lang->conference->apiPortTip     = 'For OWT, if HTTPS is enabled 3004 by default, otherwise 3001 by default';
$lang->conference->mgmtPort       = 'OWT Management Port';
$lang->conference->mgmtPortTip    = '3300 by default';
$lang->conference->rtcPort        = 'SRS Signaling Port';
$lang->conference->rtcPortTip     = '1989 by default';
$lang->conference->https          = 'Enable HTTPS';
$lang->conference->httpsTip       = 'Enabled by default, make sure OWT server has the same setting';
$lang->conference->serviceId      = 'OWT ID';
$lang->conference->serviceIdTip   = '';
$lang->conference->serviceKey     = 'OWT Key';
$lang->conference->serviceKeyTip  = '';
$lang->conference->configGuideTip = '';

$lang->conference->backend = new stdclass();
$lang->conference->backend->type  = '后端类型';
$lang->conference->backend->types = array('owt' => 'OWT', 'srs' => 'SRS');

$lang->conference->inputError = new stdClass();
$lang->conference->inputError->serviceId        = 'Service ID cannot be empty';
$lang->conference->inputError->serviceKey       = 'Service Key cannot be empty';
$lang->conference->inputError->serverAddr       = 'Server address cannot be empty';
$lang->conference->inputError->apiPort          = 'API port cannot be empty';
$lang->conference->inputError->mgmtPort         = 'Management port cannot be empty';
$lang->conference->inputError->rtcPort          = 'Signaling port cannot be empty';
$lang->conference->inputError->resolutionWidth  = 'Resolution width can not be empty';
$lang->conference->inputError->resolutionHeight = 'Resolution height can not be empty';

$lang->conference->server = 'Server settings';
$lang->conference->video  = 'Video settings';

$lang->conference->resolutionWidth     = 'Resolution Width';
$lang->conference->resolutionWidthTip  = 'px, recommended value 320-1280';
$lang->conference->resolutionHeight    = 'Resolution Height';
$lang->conference->resolutionHeightTip = 'px, recommended value 240-720';

$lang->conference->placeholder                   = new stdClass();
$lang->conference->placeholder->resolutionWidth  = '640';
$lang->conference->placeholder->resolutionHeight = '480';
