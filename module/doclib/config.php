<?php
$config->doclib = new stdclass();

$config->doclib->client   = '/usr/bin/svn';
$config->doclib->account  = '';
$config->doclib->password = '';


$config->doclib->editor = new stdclass();
$config->doclib->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->doclib->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');

$config->doclib->create = new stdclass();
$config->doclib->create->requiredFields = 'SCM,name,path,encoding,client';

$config->doclib->edit = new stdclass();
$config->doclib->edit->requiredFields = 'SCM,name,path,encoding,client';

$config->doclib->svn = new stdclass();
$config->doclib->svn->requiredFields = 'account,password';

$config->doclib->suffix['c']    = "cpp";
$config->doclib->suffix['cpp']  = "cpp";
$config->doclib->suffix['asp']  = "asp";
$config->doclib->suffix['php']  = "php";
$config->doclib->suffix['cs']   =  "cs";
$config->doclib->suffix['sh']   = "bash";
$config->doclib->suffix['jsp']  = "java";
$config->doclib->suffix['lua']  = "lua";
$config->doclib->suffix['sql']  = "sql";
$config->doclib->suffix['js']   = "javascript";
$config->doclib->suffix['ini']  = "ini";
$config->doclib->suffix['conf'] = "apache";
$config->doclib->suffix['bat']  = "dos";
$config->doclib->suffix['py']   = "python";
$config->doclib->suffix['rb']   = "ruby";
$config->doclib->suffix['as']   = "actionscript";
$config->doclib->suffix['html'] = "xml";
$config->doclib->suffix['xml']  = "xml";
$config->doclib->suffix['htm']  = "xml";
$config->doclib->suffix['pl']   = "perl";

$config->doclib->cacheTime = 10;
$config->doclib->syncTime  = 10;
$config->doclib->batchNum  = 100;
$config->doclib->images    = '|png|gif|jpg|ico|jpeg|bmp|';
$config->doclib->binary    = '|pdf|';
