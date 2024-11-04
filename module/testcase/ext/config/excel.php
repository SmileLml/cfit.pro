<?php
$config->testcase->export               = new stdclass();
$config->testcase->import               = new stdclass();
$config->testcase->export->listFields   = array('module', 'project', 'types', 'stage', 'pri', 'story', 'status', 'type');
$config->testcase->import->ignoreFields = array('openedBy', 'openedDate', 'lastEditedBy', 'lastEditedDate', 'version');
$config->testcase->exportFields = '
    id, applicationID, product, module, project, execution, story,
    title, precondition, stepDesc, stepExpect, real, keywords,
    pri, type, stage, status, categories, bugsAB, resultsAB, stepNumberAB, lastRunResult, openedBy, openedDate,
    lastEditedBy, lastEditedDate, version, linkCase, files, intro';

