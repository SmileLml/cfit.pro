<?php
$config->projectdoc = new stdclass();

$config->projectdoc->client   = '/usr/bin/svn';
$config->projectdoc->account  = '';
$config->projectdoc->password = '';


$config->projectdoc->editor = new stdclass();
$config->projectdoc->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->projectdoc->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');

$config->projectdoc->create = new stdclass();
$config->projectdoc->create->requiredFields = 'SCM,name,path,encoding,client';

$config->projectdoc->edit = new stdclass();
$config->projectdoc->edit->requiredFields = 'SCM,name,path,encoding,client';

$config->projectdoc->svn = new stdclass();
$config->projectdoc->svn->requiredFields = 'account,password';

$config->projectdoc->suffix['c']    = "cpp";
$config->projectdoc->suffix['cpp']  = "cpp";
$config->projectdoc->suffix['asp']  = "asp";
$config->projectdoc->suffix['php']  = "php";
$config->projectdoc->suffix['cs']   =  "cs";
$config->projectdoc->suffix['sh']   = "bash";
$config->projectdoc->suffix['jsp']  = "java";
$config->projectdoc->suffix['lua']  = "lua";
$config->projectdoc->suffix['sql']  = "sql";
$config->projectdoc->suffix['js']   = "javascript";
$config->projectdoc->suffix['ini']  = "ini";
$config->projectdoc->suffix['conf'] = "apache";
$config->projectdoc->suffix['bat']  = "dos";
$config->projectdoc->suffix['py']   = "python";
$config->projectdoc->suffix['rb']   = "ruby";
$config->projectdoc->suffix['as']   = "actionscript";
$config->projectdoc->suffix['html'] = "xml";
$config->projectdoc->suffix['xml']  = "xml";
$config->projectdoc->suffix['htm']  = "xml";
$config->projectdoc->suffix['pl']   = "perl";

$config->projectdoc->cacheTime = 10;
$config->projectdoc->syncTime  = 10;
$config->projectdoc->batchNum  = 100;
$config->projectdoc->images    = '|png|gif|jpg|ico|jpeg|bmp|';
$config->projectdoc->binary    = '|pdf|';
