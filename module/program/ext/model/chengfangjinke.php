<?php
/**
 * Project: chengfangjinke
 * Method: getProjectList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:12
 * Desc: This is the code comment. This method is called getProjectList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $programID
 * @param string $browseType
 * @param int $queryID
 * @param string $orderBy
 * @param null $pager
 * @param int $programTitle
 * @param int $involved
 * @return mixed
 */
public function getProjectList($programID = 0, $browseType = 'all', $queryID = 0, $orderBy = 'id_desc', $pager = null, $programTitle = 0, $involved = 0)
{
    $path = '';
    if($programID)
    {
        $program = $this->getByID($programID);
        $path    = $program->path;
    }

    $projectQuery = '';
    if($browseType == 'bysearch')
    {
        $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
        if($query)
        {
            $this->session->set('projectQuery', $query->sql);
            $this->session->set('projectForm', $query->form);
        }

        if($this->session->projectQuery == false) $this->session->set('projectQuery', ' 1 = 1');

        $projectQuery = $this->session->projectQuery;
    }

    if(strpos($projectQuery,'`code`')) { //搜索项目代号特殊处理
        $queryArray = explode('`code`', $projectQuery);
        $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
        $projectQuery = $queryArray[0] . ' (id in (select project from ' . TABLE_PROJECTPLAN . ' where mark'. $subQuery;
    }
    if(strpos($projectQuery,'`projectId`')) { //搜索项目编号特殊处理
        $queryArray = explode('`projectId`', $projectQuery);
        $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
        $projectQuery = $queryArray[0] . ' (id in (select project from ' . TABLE_PROJECTPLAN . ' where code'. $subQuery;

    }
    $projectList = $this->dao->select('*')->from(TABLE_PROJECT)
        ->where('deleted')->eq('0')
        ->beginIF($this->config->systemMode == 'new')->andWhere('type')->eq('project')->fi()
        ->beginIF($browseType == 'bysearch')->andWhere($projectQuery)->fi()
        ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
        ->beginIF($path)->andWhere('path')->like($path . '%')->fi()
        ->beginIF(!$this->app->user->admin and $this->config->systemMode == 'new')->andWhere('id')->in($this->app->user->view->projects)->fi()
        ->beginIF(!$this->app->user->admin and $this->config->systemMode == 'classic')->andWhere('id')->in($this->app->user->view->sprints)->fi()
        ->beginIF($this->cookie->involved or $involved)
        ->andWhere('openedBy', true)->eq($this->app->user->account)
        ->orWhere('PM')->eq($this->app->user->account)
        ->markRight(1)
        ->fi()
        ->orderBy($orderBy)
        ->page($pager)
        ->fetchAll('id');

    $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'project');
    /* Determine how to display the name of the program. */
    if($programTitle and $this->config->systemMode == 'new')
    {
        $programList = $this->getPairs();
        foreach($projectList as $id => $project)
        {
            $path = explode(',', $project->path);
            $path = array_filter($path);
            array_pop($path);
            $programID = $programTitle == 'base' ? current($path) : end($path);
            if(empty($path) || $programID == $id) continue;

            $programName = isset($programList[$programID]) ? $programList[$programID] : '';

            $projectList[$id]->name = $programName . '/' . $projectList[$id]->name;
        }
    }
//    die(json_encode($projectList));
    return $projectList;
}

/**
 * Get the product associated with the program.
 *
 * @param  int     $programID
 * @param  string  $mode       all|assign
 * @param  string  $status     all|noclosed
 * @access public
 * @return array
 */
public function getProductCodeNamePairs($programID = 0, $mode = 'assign', $status = 'all')
{
    /* Get the top programID. */
    if($programID)
    {
        $program   = $this->getByID($programID);
        $path      = explode(',', $program->path);
        $path      = array_filter($path);
        $programID = current($path);
    }

    /* When mode equals assign and programID equals 0, you can query the standalone product. */
    $products = $this->dao->select('id,concat(concat(code,"_"),name) as name')->from(TABLE_PRODUCT)
        ->where('deleted')->eq(0)
        ->beginIF($mode == 'assign')->andWhere('program')->eq($programID)->fi()
        ->beginIF(strpos($status, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
        ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
        ->fetchPairs('id', 'name');
    return $products;
}

function str_replace_first($from, $to, $content)
{
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $content, 1);
}