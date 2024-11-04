<?php
/**
 * The control file of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     execution
 * @version     $Id$
 * @link        http://www.zentao.net
 */
public function createRelationOfTasks($executionID)
{
    $this->loadExtension('gantt')->createRelationOfTasks($executionID);
}

public function editRelationOfTasks($executionID)
{
    $this->loadExtension('gantt')->editRelationOfTasks($executionID);
}

public function getRelationsOfTasks($executionID)
{
    return $this->loadExtension('gantt')->getRelationsOfTasks($executionID);
}

public function getDataForGantt($executionID, $type)
{
    return $this->loadExtension('gantt')->getDataForGantt($executionID, $type);
}

public function deleteRelation($id)
{
    $this->loadExtension('gantt')->deleteRelation($id);
}
