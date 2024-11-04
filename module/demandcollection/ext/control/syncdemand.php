<?php

include '../../control.php';
class myDemandcollection extends demandcollection
{
    public function syncDemand($id)
    {
        if ($_POST) {
            if('created' == $this->post->syncType){
                $demandID = $this->demandcollection->syncCreated();
            }else{
                $demandID = $this->post->demandId;
                if(empty($demandID)){
                    $collectionInfo = $this->demandcollection->getByID($id);
                    $demandID = $collectionInfo->demandId;
                }
                $changes = $this->demandcollection->syncUpdate($demandID);

                if($changes || $this->post->comment) {
                    $actionID = $this->loadModel('action')->create('demand', $demandID, 'edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $demandInfo = $this->dao->select('id,code')->from(TABLE_DEMAND)->where('id')->eq($demandID)->fetch();
            $this->loadModel('action')->create('demandcollection', $id, 'syncDemand', '同步需求条目：' . (!empty($demandInfo) ? $demandInfo->code : $demandID));
            if('created' == $this->post->syncType){
                $this->loadModel('action')->create('demand', $demandID, 'created');
                $this->loadModel('action')->create('requirement', $_POST['requirementID'], 'createdemand');
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }

        //同步方式默认值
        //如果关联条目ID为空默认新生成；如果已关联条目默认并入已有条目，并且不能选择其他条目
        $collection = $this->demandcollection->getByID($id);
        /**
         * @var requirementinsideModel $requirementInsideModel
         * @var demandinsideModel $demandInsideModel
         */
        $requirementInsideModel = $this->loadModel('requirementinside');
        $demandInsideModel = $this->loadModel('demandinside');

        $this->view->title        = $this->lang->demandcollection->syncDemand;
        $this->view->users        = $this->loadModel('user')->getPairs('noclosed');
        $this->view->dept         = $this->loadModel('dept')->getByID($this->app->user->dept);
        $this->view->productList  = ['' => ''];
        $this->view->opinions     = ['0' => ''] + $this->demandcollection->getPairsByOpinion(); //需求意向
        $this->view->requirements = ['0' => ''] + $requirementInsideModel->getRequirementByUser(); //需求任务 倒挂需求任务时，登录人为需求任务的创建人和待处理人可选
        $this->view->demands      = ['0' => ''] + $this->demandcollection->getPairsByDemand();
        $this->view->apps         = ['0' => ''] + $this->loadModel('application')->getapplicationNameCodePairs(); //所属系统
        $this->view->plans        = ['0' => ''] + $this->loadModel('projectplan')->getAliveProjects(false); //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->executions   = ['' => ''];
        $this->view->fixType      = '';
        $this->view->collection   = $collection;
        $this->view->syncType     = empty($collection->demandId) ? 'created' : 'edit';

        $this->display();
    }
}
