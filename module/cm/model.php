<?php
/**
 * The model file of cm module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     cm
 * @version     $Id: model.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class cmModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param string $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($projectID, $orderBy = 'id_desc', $pager = null)
    {
        $baselines = $this->dao->select('*')->from(TABLE_BASELINE)
            ->where('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
            $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'cm', true);
        if($baselines){
            foreach ($baselines as $baseline)
            {
                $baseline->items = $this->dao->select('*')->from(TABLE_CMITEM)->where('baseline')->eq($baseline->id)->fetch();
            }
        }
        return $baselines;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $baselineID
     * @return mixed
     */
    public function getByID($baselineID)
    {
        $baseline = $this->dao->select('*')->from(TABLE_BASELINE)->where('id')->eq($baselineID)->fetch();
        $baseline->items = $this->dao->select('*')->from(TABLE_CMITEM)->where('baseline')->eq($baselineID)->fetchAll('id');

        return $baseline;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairsForGantt
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called getPairsForGantt.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $productID
     * @return mixed
     */
    public function getPairsForGantt($projectID, $productID)
    {
        return $this->dao->select('id, version')->from(TABLE_OBJECT)
            ->where('project')->eq($projectID)
            ->andWhere('product')->eq($productID)
            ->andWhere('type')->eq('taged')
            ->andWhere('category')->eq('PP')
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @return mixed
     */
    public function create($projectID)
    {
        $baseline = fixer::input('post')
            ->add('project', $projectID)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->get();

        $items = array();
        foreach($baseline->itemname as $key => $itemname)
        {
            if(!$itemname) continue;
            $items[$key]['title']       = $itemname;
            $items[$key]['code']        = $baseline->itemcode[$key];
            $items[$key]['version']     = $baseline->version[$key];
            $items[$key]['changedID']   = $baseline->changedID[$key];
            $items[$key]['changedDate'] = $baseline->changedDate[$key];
            $items[$key]['path']        = $baseline->path[$key];
            $items[$key]['comment']     = $baseline->comment[$key];
            $items[$key]['changed']     = $baseline->changedID[$key] ? 1 : 0;
        }

        $project = new stdclass();
//        $project->PM = $baseline->PM;
        $project->QA = $baseline->QA;
        $project->PO = $baseline->PO;

        unset($baseline->itemname);
        unset($baseline->itemcode);
        unset($baseline->version);
        unset($baseline->changedID);
        unset($baseline->changedDate);
        unset($baseline->path);
        unset($baseline->PM);
        unset($baseline->PO);
        unset($baseline->QA);
        unset($baseline->comment);

        $this->lang->baseline->title = $this->lang->cm->title;
        $this->dao->insert(TABLE_BASELINE)->data($baseline)->autoCheck()->batchCheck($this->config->cm->create->requiredFields, 'notempty')->exec();
        $baselineID = $this->dao->lastInsertID();

        foreach($items as $item)
        {
            $item['baseline'] = $baselineID;
            $this->dao->insert(TABLE_CMITEM)->data($item)->exec();
        }

        $this->dao->update(TABLE_PROJECT)->data($project)->where('id')->eq($projectID)->exec();

        return $baselineID;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:10
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $baselineID
     * @return array|false
     */
    public function update($baselineID)
    {
        $oldBaseline = $this->getByID($baselineID);
        $baseline = fixer::input('post')->get();

        $items = array();
        foreach($baseline->itemname as $key => $itemname)
        {
            if(!$itemname) continue;
            $items[$key]['title']       = $itemname;
            $items[$key]['baseline']    = $baselineID;
            $items[$key]['code']        = $baseline->itemcode[$key];
            $items[$key]['version']     = $baseline->version[$key];
            $items[$key]['changedID']   = $baseline->changedID[$key];
            $items[$key]['changedDate'] = $baseline->changedDate[$key];
            $items[$key]['path']        = $baseline->path[$key];
            $items[$key]['comment']     = $baseline->comment[$key];
            $items[$key]['changed']     = $baseline->changedID[$key] ? 1 : 0;
        }

        $project = new stdclass();
//        $project->PM = $baseline->PM;
        $project->QA = $baseline->QA;
        $project->PO = $baseline->PO;

        unset($baseline->itemname);
        unset($baseline->itemcode);
        unset($baseline->version);
        unset($baseline->changedID);
        unset($baseline->changedDate);
        unset($baseline->path);
        unset($baseline->PM);
        unset($baseline->PO);
        unset($baseline->QA);
        unset($baseline->comment);

        $this->lang->baseline->title = $this->lang->cm->title;
        $this->dao->update(TABLE_BASELINE)->data($baseline)->where('id')->eq($baselineID)->autoCheck()->batchCheck($this->config->cm->create->requiredFields, 'notempty')->exec();

        if(!dao::isError())
        {
            $this->dao->delete()->from(TABLE_CMITEM)->where('baseline')->eq($baselineID)->exec();
            foreach($items as $key => $item) $this->dao->insert(TABLE_CMITEM)->data($item)->exec();

            $this->dao->update(TABLE_PROJECT)->data($project)->where('id')->eq($oldBaseline->project)->exec();
            return common::createChanges($oldBaseline, $baseline);
        }
        return false;
    }

    /**
     * Project: chengfangjinke
     * Method: getDataByObject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:10
     * Desc: This is the code comment. This method is called getDataByObject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $productID
     * @param $objectType
     * @param $range
     * @return array
     */
    public function getDataByObject($projectID, $productID, $objectType, $range)
    {
        $data = array();
        $this->loadModel('review');
        $checkedItem = $range;
        if($objectType == 'PP') $data = $this->review->getDataFromPP($projectID, $objectType, $productID);
        if($objectType == 'SRS' || $objectType == 'URS')  $data = $this->review->getDataFromStory($projectID, $objectType, $productID, $range, $checkedItem);
        if(in_array($objectType, array('HLDS', 'DDS', 'DBDS', 'ADS'))) $data = $this->review->getDataFromDesign($projectID, $objectType, $productID, $range, $checkedItem);
        if($objectType == 'ITTC' || $objectType == 'STTC') $data = $this->review->getDataFromCase($projectID, $objectType, $productID, $range, $checkedItem);

        return $data;
    }

    /**
     * Project: chengfangjinke
     * Method: getReportInfo
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:10
     * Desc: This is the code comment. This method is called getReportInfo.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @return mixed
     */
    public function getReportInfo($projectID)
    {
        $baselines = $this->getList($projectID);
        foreach($baselines as $baselineID => $baseline) $baseline->items = $this->dao->select('*')->from(TABLE_CMITEM)->where('baseline')->eq($baselineID)->fetchAll('id');

        return $baselines;
    }
}
