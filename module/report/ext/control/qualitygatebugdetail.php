<?php
include '../../control.php';
class myReport extends report
{

    /**
     * a安全门禁bug明细
     *
     * @param $dataSource
     * @param int $projectID
     * @param int $productId
     * @param int $productVersion
     * @param int $build
     * @param string $childType
     * @param string $sourceType
     * @param int $severity
     */
    public function qualityGateBugDetail($dataSource = 'bug', $projectID = 0, $productId = 0, $productVersion = 0,  $build = 0,$childType = '', $sourceType = '', $severity = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $this->app->loadLang('bug');
        $this->view->title      = $this->lang->report->qualityGateBugDetail;
        $this->view->position[] = $this->lang->report->qualityGateBugDetail;
        $this->loadModel('project')->setMenu($projectID);
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $bugData = $this->report->getQualityGateBugList($dataSource, $projectID, $productId, $productVersion, $build, $childType, $sourceType, $severity, $orderBy, $pager);
        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;
        $this->view->productId = $productId;
        $this->view->productVersion = $productVersion;
        $this->view->childType = $childType;
        $this->view->sourceType = $sourceType;
        $this->view->severity = $severity;
        $this->view->orderBy = $orderBy;
        $this->view->pager = $pager;
        $this->view->data = $bugData;
        //二级分类
        $childTypeList = zget(json_decode($this->lang->bug->childTypeList['all'], true), 'security');
        $this->view->childTypeList = $childTypeList;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $childTypeList = json_decode($this->lang->bug->childTypeList['all'], true);
        $this->view->childTypeList = $childTypeList;
        $this->display();
    }
}
