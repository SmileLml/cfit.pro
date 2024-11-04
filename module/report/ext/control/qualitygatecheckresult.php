<?php
include '../../control.php';
class myReport extends report
{
    /**
     * Export the bug discovery rate report.
     *
     * @access public
     * @return void
     */
    public function qualityGateCheckResult($projectID = 0, $getProductId = 0, $productVersion = '0', $buildId = 0)
    {
        $this->app->loadLang('bug');
        $this->loadModel('project')->setMenu($projectID);
        // 获取搜索条件。
        if(isset($_POST['productId'])){
            $productId = $_POST['productId'];
            $productVersion = 0;
            $buildId = 0;
        }else{
            $productId = $getProductId;
        }
        $bugSummary = $this->report->getQualityGateBugSummary($projectID, $productId, $productVersion, $buildId);
        $bugData = zget($bugSummary, 'data');
        $bugDataSource = zget($bugSummary, 'dataSource');
        $this->view->title      = $this->lang->report->qualityGateCheckResult;
        $this->view->position[] = $this->lang->report->qualityGateCheckResult;
        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;
        $this->view->productId = $productId;
        $this->view->productVersion  = $productVersion;
        $this->view->buildId  = $buildId;
        $this->view->bugData   = $bugData;
        $this->view->bugDataSource = $bugDataSource;
        $productList  =  $this->loadModel('project')->getProductList($projectID);
        $this->view->productList = array('0' => '') + $productList;
        $productIds = array_keys($productList);

        $productPlanList  =  $this->loadModel('productplan')->getProductPlanNameList($productIds);
        $this->view->productPlanList = array('1' => '默认') + $productPlanList;
        //二级分类
        $childTypeList = zget(json_decode($this->lang->bug->childTypeList['all'], true), 'security');
        $this->view->childTypeList = $childTypeList;
        $param = json_encode(array('projectID' => $projectID, 'productId' => $productId, 'productVersion' => $productVersion, 'buildId' => $buildId));
        $this->view->param = helper::safe64Encode($param);
        $this->display();
    }
}
