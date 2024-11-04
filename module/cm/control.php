<?php
/**
 * The control file of cm module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     cm
 * @version     $Id: control.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class cm extends control
{
    /**
     * Project: chengfangjinke
     * Method: commonAction
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:08
     * Desc: This is the code comment. This method is called commonAction.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function commonAction($projectID)
    {
        $this->app->loadLang('baseline');
        $this->loadModel('project')->setMenu($projectID);
    }

    /**
     * Browse cm.
     * 
     * @param  int    $projectID 
     * @param  string $orderBy 
     * @param  int    $recTotal 
     * @param  int    $recPerPage 
     * @param  int    $pageID 
     * @access public
     * @return void
     */
    public function browse($projectID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->commonAction($projectID);

        $this->app->loadLang('review');
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title     = $this->lang->cm->browse;
        $this->view->projectID = $projectID;
        $this->view->pager     = $pager;
        $this->view->orderBy   = $orderBy;
        $this->view->users     = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->baselines = $this->cm->getList($projectID, $orderBy, $pager);
        $this->display();
    }

    /**
     * Cm report.
     * 
     * @param  int    $projectID 
     * @access public
     * @return void
     */
    public function report($projectID)
    {
        $this->commonAction($projectID);

        $this->app->loadLang('reviewissue');
        $this->app->loadLang('review');
        $this->app->loadLang('project');

        $this->view->title     = $this->lang->cm->report;
        $this->view->baselines = $this->cm->getReportInfo($projectID);
        $this->view->project   = $this->loadModel('project')->getByID($projectID);
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->currentStage = $this->dao->select('name')->from(TABLE_PROJECT)
            ->where('project')->eq($projectID)
            ->andWhere('type')->eq('stage')
            ->andWhere('status')->eq('doing')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')->limit(1)->fetch('name');
        $this->view->changes   = array('' => '') + $this->dao->select('id,code')->from(TABLE_CHANGE)
            ->where('project')->eq($projectID)
            ->andWhere('status')->eq('success')
            ->orderBy('id_desc')
            ->fetchPairs('id');

        $this->display();
    }

    /**
     * Create a cm.
     * 
     * @param  int    $projectID 
     * @param  int    $reviewID 
     * @access public
     * @return void
     */
    public function create($projectID, $reviewID = 0)
    {
        $this->commonAction($projectID);

        if($_POST)
        {
            $baselineID = $this->cm->create($projectID);

            if(dao::isError())
            {
                $result['result']  = 'fail';
                $result['message'] = dao::getError();
                $this->send($result);
            }

            $this->loadModel('action')->create('cm', $baselineID, 'Opened', '');

            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = inlink('browse', "project=$projectID");
            $this->send($result);
        }

        $this->view->title   = $this->lang->cm->create;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->project = $this->loadModel('project')->getByID($projectID);
        $this->view->changes = array('' => '') + $this->dao->select('id,code')->from(TABLE_CHANGE)
            ->where('project')->eq($projectID)
            ->andWhere('status')->eq('success')
            ->orderBy('id_desc')
            ->fetchPairs('id');
        $this->display();
    }

    /**
     * Edit a baseline.
     * 
     * @param  int    $baselineID 
     * @access public
     * @return void
     */
    public function edit($baselineID)
    {
        $baseline = $this->cm->getByID($baselineID);
        $this->commonAction($baseline->project);

        if($_POST)
        {
            $changes = $this->cm->update($baselineID);
            if(!empty($changes))
            {
                $actionID = $this->loadModel('action')->create('cm', $baselineID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }

            if(dao::isError())
            {
                $result['result']  = 'fail';
                $result['message'] = dao::getError();
                $this->send($result);
            }

            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = inlink('view', "baselineID=$baselineID");
            $this->send($result);
        }

        $this->view->title    = $this->lang->cm->view;
        $this->view->baseline = $baseline;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->project  = $this->loadModel('project')->getByID($baseline->project);
        $this->view->changes  = array('' => '') + $this->dao->select('id,code')->from(TABLE_CHANGE)
            ->where('project')->eq($baseline->project)
            ->andWhere('status')->eq('success')
            ->orderBy('id_desc')
            ->fetchPairs('id');
        $this->display();
    }

    /**
     * View a baseline. 
     * 
     * @param  int    $baselineID 
     * @access public
     * @return void
     */
    public function view($baselineID)
    {
        $baseline = $this->cm->getByID($baselineID);
        $this->commonAction($baseline->project);

        $this->view->title     = $this->lang->cm->view;
        $this->view->baseline  = $baseline;
        $this->view->actions   = $this->loadModel('action')->getList('cm', $baselineID);
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->projectID = $baseline->project;
        $this->view->changes   = array('' => '') + $this->dao->select('id,code')->from(TABLE_CHANGE)
            ->where('project')->eq($baseline->project)
            ->andWhere('status')->eq('success')
            ->orderBy('id_desc')
            ->fetchPairs('id');
        $this->display();
    }

    /**
     * Set data to view.
     * 
     * @param  int    $baseline 
     * @access public
     * @return void
     */
    public function setViewData($baseline)
    {
        $this->loadModel('review');
        if($baseline->category == 'PP') 
        {
            $this->view->plans = $this->loadModel('programplan')->getDataForGantt($baseline->project, $baseline->product, $baseline->id);
        }
        else
        {
            if(!$baseline->template) return;
            $template = $this->loadModel('doc')->getByID($baseline->template);

            if($template->type == 'book')
            {   
                $this->view->bookID = $template->id;
                $this->view->book   = $template;
            }   

            $this->view->template = $template;
        }
    }

    /**
     * Delete a baseline.
     * 
     * @param  int    $baselineID 
     * @param  string $confirm 
     * @access public
     * @return void
     */
    public function delete($baselineID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->cm->confirmDelete, $this->createLink('cm', 'delete', "baselineID=$baselineID&confirm=yes"), '');
            exit;
        }
        else
        {
            $this->cm->delete(TABLE_BASELINE, $baselineID);
            die(js::reload('parent.parent'));
        }
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetReviews
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called ajaxGetReviews.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $category
     */
    public function ajaxGetReviews($category = '')
    {
        $reviews = $this->dao->select('t2.id, t1.title, t2.version')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')->on('t1.object=t2.id')
            ->where('t2.category')->eq($category)
            ->andWhere('t1.status')->eq('done')
            ->fetchAll();
        $pairs = array('' => '');
        foreach($reviews as $review) $pairs[$review->id] = $review->title . '-' . $review->version;

        die(html::select('from', $pairs, '', "class='form-control chosen' onchange=getProduct(this.value)"));
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetProduct
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called ajaxGetProduct.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID
     */
    public function ajaxGetProduct($objectID)
    {
        $productID = $this->dao->findByID($objectID)->from(TABLE_OBJECT)->fetch('product');
        die($productID);
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:09
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function export($projectID)
    {
        $project = $this->loadModel('project')->getByID($projectID);

        if($_POST)
        {
            $this->loadModel('file');
            $cmLang   = $this->lang->cm;
            $cmConfig = $this->config->cm;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $cmConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($cmLang->$fieldName) ? $cmLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get cms. */
            $cms = array();
            if($this->session->cmOnlyCondition)
            {
                $cms = $this->dao->select('*')->from(TABLE_BASELINE)->where($this->session->cmQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy('id_desc')->fetchAll('id');
            }
            else
            {
                $stmt  = $this->dbh->query($this->session->cmQueryCondition . ($this->post->exportType == 'selected' ? " AND $field IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr('id_desc', '_', ' '));
                while($row = $stmt->fetch()) $cms[$row->id] = $row;
            }

            $cmIdList = array_keys($cms);

            /* Get users, products and executions. */
            $users   = $this->loadModel('user')->getPairs('noletter');
            $changes = array('' => '') + $this->dao->select('id,code')->from(TABLE_CHANGE)
                ->where('project')->eq($projectID)
                ->andWhere('status')->eq('success')
                ->orderBy('id_desc')
                ->fetchPairs('id');

            $data    = array();
            $rowspan = array();
            $i       = 0;
            foreach($cms as $cm)
            {
                $items = $this->dao->select('*')->from(TABLE_CMITEM)->where('baseline')->eq($cm->id)->fetchAll('id');
                if(count($items) > 1)
                {
                    $rowspan[$i]['rows']['title']           = count($items);
                    $rowspan[$i]['rows']['type']            = count($items);
                    $rowspan[$i]['rows']['cmAndDate']       = count($items);
                    $rowspan[$i]['rows']['reviewerAndDate'] = count($items);
                }

                if(!empty($items))
                {
                    foreach($items as $item)
                    {
                        if(!isset($data[$i])) $data[$i] = new stdclass();

                        $data[$i]->title           = $cm->title;
                        $data[$i]->type            = zget($cmLang->typeList, $cm->type, '');
                        $data[$i]->cmAndDate       = zget($users, $cm->cm) . ' ' . $cm->cmDate == '0000-00-00' ? '' : $cm->cmDate;
                        $data[$i]->reviewerAndDate = zget($users, $cm->reviewer) . ' ' . $cm->reviewerDate == '0000-00-00' ? '' : $cm->reviewerDate;
                        $data[$i]->itemname        = $item->title; 
                        $data[$i]->itemcode        = $item->code; 
                        $data[$i]->version         = $item->version; 
                        $data[$i]->changed         = $item->changedID ? $cmLang->changeList['yes'] : $cmLang->changeList['no']; 
                        $data[$i]->changedID       = zget($changes, $item->changedID, '\\'); 
                        $data[$i]->changedDate     = $item->changedDate == '0000-00-00' ? '\\' : $item->changedDate; 
                        $data[$i]->path            = $item->path; 
                        $data[$i]->comment         = $item->comment; 

                        $i ++;
                    }
                }
                else
                {
                    $data[$i]->title           = '';
                    $data[$i]->type            = '';
                    $data[$i]->cmAndDate       = '';
                    $data[$i]->reviewerAndDate = '';
                    $data[$i]->itemname        = '';
                    $data[$i]->itemcode        = '';
                    $data[$i]->version         = '';
                    $data[$i]->changed         = '';
                    $data[$i]->changedID       = '';
                    $data[$i]->changedDate     = '';
                    $data[$i]->path            = '';
                    $data[$i]->comment         = '';
                    $i ++;
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('rowspan', $rowspan);
            $this->post->set('project', $project);
            $this->post->set('kind', 'cm');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $project->name . '-' . $this->lang->cm->baselineReport;
        $this->view->allExportFields = $this->config->cm->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
}
