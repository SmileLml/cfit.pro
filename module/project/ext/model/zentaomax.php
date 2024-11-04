<?php
public function getReviewProject()
{
    return $this->dao->select('*')->from(TABLE_PROJECT)
        ->where('template')->ne('')
        ->andWhere('status')->ne('colsed')
        ->andWhere('deleted')->eq(0)
        ->orderBy('id_desc')
        ->fetchAll('id');
}

/**
 * Get projects to import
 *
 * @param  array  $projectIds
 * @access public
 * @return array
 */
public function getProjectsToImport($projectIds)
{
    $projects = $this->dao->select('*')->from(TABLE_PROJECT)
        ->where('id')->in($projectIds)
        ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->projects)->fi()
        ->andWhere('deleted')->eq(0)
        ->orderBy('id desc')
        ->fetchAll('id');

    $pairs = array();
    $now   = date('Y-m-d');
    foreach($projects as $id => $project) $pairs[$id] = 'S' . ':' . $project->name;
    return $pairs;
}

/**  
 * Set menu of project module.
 *
 * @param  int    $objectID
 * @access public
 * @return void
 */
public function setMenu($objectID, $params = array())
{
    return $this->loadExtension('zentaomax')->setMenu($objectID, $params);
}
