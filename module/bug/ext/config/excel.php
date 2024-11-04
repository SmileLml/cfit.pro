<?php
$config->bug->export                 = new stdclass();
$config->bug->import                 = new stdclass();
$config->bug->export->listFields     = explode(',', "module,project,story,severity,pri,type,childType,os,browser,openedBuild");
$config->bug->export->sysListFields  = explode(',', "module,story");
$config->bug->export->templateFields = explode(',', "applicationID,product,module,project,execution,story,title,keywords,severity,pri,type,childType,os,browser,steps,deadline,linkPlan,openedBuild");
$config->bug->import->ignoreFields   = explode(',', "status,activatedCount,confirmed,mailto,openedBy,openedDate,assignedDate,resolvedBy,resolution,resolvedBuild,resolvedDate,closedBy,closedDate,duplicateBug,linkBug,case,lastEditedBy,lastEditedDate,files");
