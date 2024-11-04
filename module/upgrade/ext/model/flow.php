<?php
/**  
*  Import zentao module for workflow.
*  
*  @access public
*  @return bool
*/
public function importBuildinModules()
{
    return $this->loadExtension('zentaobiz')->importBuildinModules();
}

/**
 * Add sub status for built-in modules.
 *
 * @access public
 * @return bool
 */
public function addSubStatus()
{
    return $this->loadExtension('zentaobiz')->addSubStatus();
}

/**
 * Add batch create action and batch edit action for exist flows.
 *
 * @access public
 * @return bool
 */
public function addDefaultActions()
{
    return $this->loadExtension('zentaobiz')->addDefaultActions();
}

/**
 * Import caselib module for workflow.
 *
 * @access public
 * @return bool
 */
public function importCaseLibModule()
{
    return $this->loadExtension('zentaobiz')->importCaseLibModule();
}

/**
 * Delete buildin fields.
 * 
 * @access public
 * @return void
 */
public function deleteBuildinFields()
{
    return $this->loadExtension('zentaobiz')->deleteBuildinFields();
}

/**
 * processSubTables 
 * 
 * @access public
 * @return void
 */
public function processSubTables()
{
    return $this->loadExtension('zentaobiz')->processSubTables();
}

/**
 * Add workflow actions.
 * 
 * @access public
 * @return void
 */
public function addWorkflowActions()
{
    return $this->loadExtension('zentaobiz')->addWorkflowActions();
}

/**
 * Process workflow layout.
 * 
 * @access public
 * @return void
 */
public function processWorkflowLayout()
{
    return $this->loadExtension('zentaobiz')->processWorkflowLayout();
}

/**
 * Process workflow label.
 * 
 * @access public
 * @return void
 */
public function processWorkflowLabel()
{
    return $this->loadExtension('zentaobiz')->processWorkflowLabel();
}

/**
 * Process workflow condition.
 * 
 * @access public
 * @return void
 */
public function processWorkflowCondition()
{
    return $this->loadExtension('zentaobiz')->processWorkflowCondition();
}

/**
 * Process workflow fields.
 *
 * @access public
 * @return bool
 */
public function processWorkflowFields()
{
    return $this->loadExtension('zentaobiz')->processWorkflowFields();
}

public function addMailtoFields()
{
    return $this->loadExtension('zentaobiz')->addMailtoFields();
}

public function initView4WorkflowDatasource()
{
    return $this->loadExtension('zentaobiz')->initView4WorkflowDatasource();
}
