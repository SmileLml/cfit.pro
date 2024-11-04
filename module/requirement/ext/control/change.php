<?php
include '../../control.php';
class myRequirement  extends requirement
{    
    /**
     * Change a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function change($requirementID)
    {
        /**
         * @var requirementModel $requirementModel
         * @var deptModel $deptModel
         * @var demandModel $demandModel
         */
        $this->app->loadlang('opinion');
        //如果存在审批中的单子不允许发起变更
        $requirementModel = $this->loadModel('requirement');
        $demandModel = $this->loadModel('demand');
        $requirement = $requirementModel->getByID($requirementID);
        $pendingChangeOrderInfo = $requirementModel->getPendingOrderByRequirementId($requirementID);
        $allowChange = true;
        if(!empty($pendingChangeOrderInfo)){
            $allowChange = false;//提示已存在变更中的单子，不允许再次发起
        }
        //所属意向如果发起变更且该任务受影响则不允许再次发起
        $followOpinion = $requirementModel->followChange($requirementID,$requirement->opinion);

        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->requirement->change($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'changed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $deptModel = $this->loadModel('dept');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        //产创部产品经理
        $poUsers = $deptModel->getPoUserByDeptId(1);
        foreach ($poUsers as $name)
        {
            $poUsers[$name] = zget($users,$name,'');
        }

        $demand = $demandModel->getByRequirementID('id,`code`,title,`status`',$requirementID);
        $affectDemands = array();
        $selectDemandIds = '';
        if(!empty($demand))
        {
            foreach ($demand as $value)
            {
                //受影响条目范围 已录入、开发中、变更单退回
                if(in_array($value->status,['wait','feedbacked','chanereturn']))
                {
                    $demandStatus = zget($this->lang->demand->statusList,$value->status);
                    $affectDemands[$value->id] = $value->code. "(" .$value->title. "_". $demandStatus .")";
                }

            }
            $selectDemandIds = implode(',',array_column($demand,'id'));
        }
        //部门负责人
        $deptLeaderCN = $deptModel->getFieldByDeptId('id,manager',$this->app->user->dept);
        $define = $this->lang->demand->deptReviewList['reviewer'];
        if(!empty($define))
        {
            $manager =  implode(',',array_unique(array_merge(explode(',',$define),explode(',',$deptLeaderCN->manager))));
        }else{
            $manager = $deptLeaderCN->manager;
        }
        $deptLeader = $deptModel->getRenameListByAccountStr($manager);
        //获取后台配置人员的数组下标
        $defineIndexInfo = array_flip(array_keys($deptLeader));
        $defineIndex = $defineIndexInfo[$define] + 1;

        //选中不可编辑人拼接后台配置人员
        $defaultChoose = false;
        if(!empty($define))
        {
            $defaultChoose = true;
        }
        $this->view->defaultChoose  = $defaultChoose;

        $this->view->defineIndex   = $defineIndex;
        $this->view->define        = $define;
        $this->view->deptLeader  = array('0' => '') + $deptLeader;
        $this->view->poUsers     = array('0' => '') + $poUsers;
        $this->view->requirement = $requirement;
        $this->view->users       = $users;
        $this->view->allowChange = $allowChange;
        $this->view->followOpinion = $followOpinion;
        $this->view->affectDemands = $affectDemands;
        $this->view->selectDemandIds = $selectDemandIds;
        //是否是清总单据
        $isGuestcn = $requirementModel->getIsGuestcn($requirement->createdBy);
        $this->view->isGuestcn  = $isGuestcn;
        $this->display();
    }
}
