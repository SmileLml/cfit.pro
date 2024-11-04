<?php

/**
 * 20220208 重写方法，解决  『待我处理』 显示已删除状态的数据
 * Get opinion list.
 * @param  string  $browseType
 * @param  string  $orderBy
 * @param  object  $pager
 * @access public
 * @return void
 */
public function getList($browseType, $queryID, $orderBy, $pager = null, $extra = '', $begin = '', $end = '')
{
    /* 获取搜索条件的查询SQL。*/
    $opinionQuery = '';
    if($browseType == 'bysearch')
    {
        $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
        if($query)
        {
            $this->session->set('opinionQuery', $query->sql);
            $this->session->set('opinionForm', $query->form);
        }

        if($this->session->opinionQuery == false) $this->session->set('opinionQuery', ' 1 = 1');

        $opinionQuery = $this->session->opinionQuery;
    }
    $assigntomeQuery = '(( 1  AND  ((FIND_IN_SET("'.$this->app->user->account.'",dealUser) OR FIND_IN_SET("'.$this->app->user->account.'",changeDealUser)) AND `status` NOT IN ("delivery","online","deleteout","closed"))))';

    /* 创建SQL查询数据。*/
    $opinions = $this->dao->select('*')->from(TABLE_OPINION)
        ->where(1)
        ->andWhere('sourceOpinion')->eq(1)
        ->andWhere('status')->ne('deleted')
        ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'assigntome' and $browseType != 'noclosed' and $browseType != 'ignore')->andWhere('status')->eq($browseType)->fi()
        ->beginIF($browseType == 'ignore')->andWhere('`ignore`')->like("%{$this->app->user->account}%")->andWhere('status')->ne('closed')->eq(1)->fi()
        ->beginIF($browseType == 'noclosed')->andWhere('status')->ne('closed')->fi()
        ->beginIF($browseType == 'assigntome')->andWhere($assigntomeQuery)->fi()
        ->beginIF($browseType == 'bysearch')->andWhere($opinionQuery)->fi()
        ->beginIF($begin)->andWhere('createdDate')->ge($begin)->fi()
        ->beginIF($end)->andWhere('createdDate')->le($end)->fi()
        ->beginIF($browseType == 'assigntome')->orderBy('ignore_asc,'.$orderBy)->fi()
        ->beginIF($browseType != 'assigntome')->orderBy($orderBy)->fi()
        ->page($pager)
        ->fetchAll('id');
    /* 保存查询条件并查询子需求条目。*/
    $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'opinion', $browseType != 'bysearch');
    return $this->getChildren($opinions);
}