<?php

class safetystatistics extends control
{
    /**
     * 系统安全评分列表
     * @param $browseType
     * @param $param
     * @param $orderBy
     * @param $recTotal
     * @param $recPerPage
     * @param $pageID
     */
    public function browse($orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->list = $this->dao
            ->select('*')
            ->from(TABLE_SAFETY_SCORE)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $this->view->pager = $pager;
        $this->view->orderBy    = $orderBy;
        $this->view->apps  = $this->safetystatistics->getAppPairs();
        $this->view->title = $this->lang->safetystatistics->common;
        $this->display();
    }

    /**
     * 设置安全评分基础参数
     */
    public function params()
    {
        if ($_POST) {
            $res = $this->safetystatistics->editParams();

            if (dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $list                        = $this->safetystatistics->getParams();
        $this->view->calibrationList = $list['calibration'] ?? [];
        $this->view->targetOneList   = $list['targetOne']   ?? [];
        $this->view->targetTwoList   = $list['targetTwo']   ?? [];
        $this->view->selected        = 1;
        $this->view->title           = $this->lang->safetystatistics->common;

        $this->display('safetystatistics', 'create');
    }

    /**
     * 获取系统安全评分
     */
    public function createScore()
    {
        if ($_POST) {
            $res = $this->safetystatistics->createScore();

            if(!$res){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->safetystatistics->errorMsg;
                $response['locate']  = 'parent';
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title = $this->lang->safetystatistics->common;

        $this->display('safetystatistics', 'score');
    }
}
