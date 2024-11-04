<?php
include '../../control.php';
class myApi extends api
{
    public function deleteObject()
    {
        /* 保存请求日志并检查请求参数。 */
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('deleted' , 'deleted');
        $this->requestlog->judgeRequestMode($logID);

        /**
         * @var requirementModel $requirementModel
         * @var opinionModel $opinionModel
         */
        $requirementModel = $this->loadModel('requirement');
        $opinionModel = $this->loadModel('opinion');
        //迭代三十三 清总同步的删除状态，将删除置为外部删除
        /* 判断所需字段是否存在。*/
        $data = fixer::input('post')->get();
        foreach($this->config->api->deletedParams as $param)
        {
            if(!isset($data->{$param}))
            {
                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
                $this->requestlog->response('fail', $errorMessage, array(), $logID);
            }
        }

        if($data->itemType == 'demand')
        {
            /* 判断是否存在需求意向。*/
            $opinion = $opinionModel->getByCode($_POST['itemId']);
            if(empty($opinion))
            {
                $opinionEmpty = sprintf($this->lang->api->opinionEmpty, $_POST['itemId']);
                $this->requestlog->response('fail', $opinionEmpty, array(), $logID);
            }
            if($opinion->status != 'deleteout')
            {
                $this->post->set('mailto', array($this->app->user->account));
//            $this->post->set('comment', $this->lang->api->syncUpdate . ',' . $this->lang->api->deleteOpinion);
                $this->post->set('comment', $this->lang->api->deleteOpinion);

                $opinionID = $opinion->id;
                //挂起需求意向
                $this->dao->update(TABLE_OPINION)->set('status')->eq('deleteout')->where('id')->eq($opinionID)->exec();
                $this->loadModel('action')->create('opinion', $opinionID, 'deleteout', $this->post->comment,'','guestcn');
                $this->loadModel('consumed')->record('opinion', $opinionID, 0, 'guestcn', $opinion->status, 'deleteout', array());
                $requirements = $this->dao->select('id, name, status')->from(TABLE_REQUIREMENT)->where('opinion')->eq($opinionID)->fetchAll();
                //挂起需求任务
                if($requirements)
                {
                    $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('deleteout')->where('opinion')->eq($opinionID)->exec();
                    foreach($requirements as $requirement)
                    {
                        if($requirement->status != 'deleteout')
                        {
                            $this->loadModel('action')->create('requirement', $requirement->id, 'deleteout', $this->post->comment,'','guestcn');
                            $this->loadModel('consumed')->record('requirement', $requirement->id, 0, 'guestcn', $requirement->status, 'deleteout', array());
                            //挂起需求条目 数据量不多，故套入循环中逐条更新
                            $this->dealDemandData($requirement->id);
                        }

                    }
                }
            }
            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $opinion->id), $logID);
        }
        elseif($data->itemType == 'demandItem')
        {
            /* 判断是否存在需求条目。*/
            $requirement = $requirementModel->getByCode($_POST['itemId']);
            if(empty($requirement))
            {
                $requirementEmpty = sprintf($this->lang->api->requirementEmpty, $_POST['itemId']);
                $this->requestlog->response('fail', $requirementEmpty, array(), $logID);
            }
            if($requirement->status != 'deleteout')
            {
                $this->post->set('mailto', array($this->app->user->account));
    //            $this->post->set('comment', $this->lang->api->syncUpdate . ',' . $this->lang->api->deleteRequirement);
                $this->post->set('comment', $this->lang->api->deleteOpinion);

                $requirementID = $requirement->id;
                //挂起需求任务
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('deleteout')->where('id')->eq($requirementID)->exec();
                $this->loadModel('action')->create('requirement', $requirementID, 'deleteout', $this->post->comment,'','guestcn');
                $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestcn', $requirement->status, 'deleteout', array());
                //挂起需求条目
                $this->dealDemandData($requirementID);
            }

            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirement->id), $logID);
        }
    }

    /**
     * @Notes:挂起需求条目处理
     * @Date: 2024/1/12
     * @Time: 16:10
     * @Interface dealDemandData
     * @param $requirementID
     */
    public function dealDemandData($requirementID)
    {
        /* @var demandModel $demandModel*/
        $demandModel = $this->loadModel('demand');
        $demands = $demandModel->getBrowesByRequirementID($requirementID);
        $data = new stdclass();
        foreach($demands as $demand)
        {
            //挂起需求任务时，该任务下的需求条目已交付、上线成功、变更单退回、变更单异常状态下不改变需求条目状态。开发中的条目已发起变更（且变更审批中）改成经直接挂起
            if(!in_array($demand->status,$this->lang->api->demandStautsList))
            {
                if($demand->status != 'deleteout')
                {
                    $data->status = 'deleteout';
                    $this->dao->update(TABLE_DEMAND)->data($data)->where('id')->eq($demand->id)->exec();
                    if(!dao::isError())
                    {
                        $this->loadModel('action')->create('demand', $demand->id, 'deleteout', $this->post->comment,'','guestcn');
                        $this->loadModel('consumed')->record('demand', $demand->id, 0, 'guestcn', $demand->status, 'deleteout');
                    }
                }
            }
        }

    }
}
