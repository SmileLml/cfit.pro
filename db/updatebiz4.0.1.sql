UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'product' AND `field` = 'PO';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'product' AND `field` = 'QD';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'product' AND `field` = 'RD';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'product' AND `field` = 'feedback';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'product' AND `field` = 'createdBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'openedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'closedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'canceledBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'PO';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'PM';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'QD';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'project' AND `field` = 'RD';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'build' AND `field` = 'builder';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'story' AND `field` = 'openedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'story' AND `field` = 'assignedTo';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'story' AND `field` = 'lastEditedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'story' AND `field` = 'reviewedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'story' AND `field` = 'closedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'bug' AND `field` = 'openedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'bug' AND `field` = 'assignedTo';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'bug' AND `field` = 'resolvedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'bug' AND `field` = 'closedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'bug' AND `field` = 'lastEditedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'task' AND `field` = 'openedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'task' AND `field` = 'assignedTo';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'task' AND `field` = 'finishedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'task' AND `field` = 'canceledBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'task' AND `field` = 'closedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'task' AND `field` = 'lastEditedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testcase' AND `field` = 'openedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testcase' AND `field` = 'reviewedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testcase' AND `field` = 'lastEditedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testcase' AND `field` = 'lastRunner';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testtask' AND `field` = 'owner';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testsuite' AND `field` = 'addedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'testsuite' AND `field` = 'lastEditedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'caselib' AND `field` = 'addedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'caselib' AND `field` = 'lastEditedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'feedback' AND `field` = 'openedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'feedback' AND `field` = 'reviewedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'feedback' AND `field` = 'processedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'feedback' AND `field` = 'closedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'feedback' AND `field` = 'editedBy';
UPDATE `zt_workflowfield` SET `options` = 'user' WHERE `module` = 'feedback' AND `field` = 'assignedTo';

UPDATE `zt_workflowfield` SET `options` = 'user', `control` = 'multi-select' WHERE `field` = 'mailto';
