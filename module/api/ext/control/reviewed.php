<?php
include '../../control.php';
class myApi extends api
{
    public function reviewed()
    {
        $this->loadModel('requirement');

        /* 保存请求日志并检查请求参数。 */
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('requirement' , 'review');
        $this->requestlog->judgeRequestMode($logID);

        /* 判断所需字段是否存在。*/
        $data = fixer::input('post')
            ->stripTags($this->config->requirement->editor->view['id'], $this->config->allowedTags)
            ->get();
            
        $audit_opinion = $data->audit_opinion;
        foreach($this->config->api->reviewedParams as $param)
        {
            if(!isset($data->{$param}))
            {
                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
                $this->requestlog->response('fail', $errorMessage, array(), $logID);
            }
        }

        /* 判断是否存在反馈单。*/
        $requirement = $this->requirement->getByFeedbackCode($_POST['feedback_id']);
        if(empty($requirement))
        {
            $feedbackEmpty = sprintf($this->lang->api->feedbackEmpty, $_POST['feedback_id']);
            $this->requestlog->response('fail', $feedbackEmpty, array(), $logID);
        }

        if($requirement->status != 'changeReviewing' and $requirement->feedbackStatus != 'toexternalapproved')
        {
            $this->requestlog->response('fail', $this->lang->api->illegalStatus, array(), $logID);
        }

        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('requirement')
            ->andWhere('objectID')->eq($requirement->id)
            ->andWhere('version')->eq($requirement->version)
            ->andWhere('stage')->eq('4')
            ->orderBy('stage,id')
            ->fetch();
        $updateStatus = '';
        $updateComment = '';
        // 需要判断是变更审核通过还是反馈单提交审核通过。
        if($requirement->status == 'changeReviewing')
        {
            /* 判断评审结果是否通过。*/
            if($data->audit_result == true)
            {
                $changes = $this->requirement->review($requirement->id, $requirement->changeVersion, 'pass', $data->audit_opinion);

                $changes[] = array(
                    'field' => 'status',
                    'old'   => 'changeReviewing',
                    'new'   => 'changeApproved',
                    'diff'  => ''
                );

                $changes[] = array(
                    'field' => 'reviewComments',
                    'old'   => $requirement->reviewComments,
                    'new'   => $data->audit_opinion,
                    'diff'  => ''
                );

                $updateData = new stdClass();
                $updateData->status = 'changeApproved';
                $updateData->reviewComments = $data->audit_opinion;
                $this->dao->update(TABLE_REQUIREMENT)->data($updateData)->where('id')->eq($requirement->id)->exec();

                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'reviewed', $audit_opinion, $requirement->version);
                $this->action->logHistory($actionID, $changes);

                //$this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirement->id), $logID);
            }
            else
            {
                $changes = $this->requirement->review($requirement->id, $requirement->changeVersion, 'reject', $data->audit_opinion);

                $changes[] = array(
                    'field' => 'status',
                    'old'   => 'changeReviewing',
                    'new'   => 'changeFailed',
                    'diff'  => ''
                );

                $changes[] = array(
                    'field' => 'reviewComments',
                    'old'   => $requirement->reviewComments,
                    'new'   => $data->audit_opinion,
                    'diff'  => ''
                );

                $updateData = new stdClass();
                $updateData->status = 'changeFailed';
                $updateData->reviewComments = $data->audit_opinion;
                $this->dao->update(TABLE_REQUIREMENT)->data($updateData)->where('id')->eq($requirement->id)->exec();

                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'reviewed', $audit_opinion, $requirement->version);
                $this->action->logHistory($actionID, $changes);

                //$this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirement->id), $logID);
            }
        }
        elseif($requirement->feedbackStatus == 'toexternalapproved')
        {
            /* 判断评审结果是否通过。*/
            if($data->audit_result == true)
            {
                /* 更新需求条目状态和记录操作日志。*/
                $updateData = new stdClass();
                $updateData->feedbackStatus = 'feedbacksuccess';
                $updateData->reviewComments = $data->audit_opinion;
                $updateData->feedbackDate = helper::now();
                $updateData->planEnd = $requirement->end;
                $this->dao->update(TABLE_REQUIREMENT)->data($updateData)->where('id')->eq($requirement->id)->exec();

                /* 删除需求条目所属的产品记录，重新计算需求条目属于那些产品。*/
                $this->dao->delete()->from(TABLE_PRODUCTREQUIREMENT)->where('requirement')->eq($requirement->id)->exec();
                if(isset($requirement->product) and $requirement->product)
                {
                    foreach(explode(',', $requirement->product) as $product)
                    {
                        if(!$product) continue;

                        $data = new stdClass();
                        $data->requirement = $requirement->id;
                        $data->product     = $product;
                        $this->dao->insert(TABLE_PRODUCTREQUIREMENT)->data($data)->exec();
                    }
                }

                $changes = common::createChanges($requirement, array('feedbackStatus' => 'feedbacksuccess'));
                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'syncstatus', $audit_opinion,'','guestcn');
                $this->action->logHistory($actionID, $changes);
                $updateStatus = 'feedbacksuccess';
                $updateComment = $updateData->reviewComments;
                $this->loadModel('consumed')->record('requirement', $requirement->id, 0, 'guestcn', $requirement->feedbackStatus, 'feedbacksuccess', array());
            }
            else
            {
                /* 更新需求条目状态和记录操作日志。*/
                $updateData = new stdClass();
                $updateData->feedbackDealUser = $requirement->feedbackBy;
                $updateData->feedbackStatus = 'feedbackfail';
                $updateData->reviewComments = "打回人：".$data->approverName."<br>审批意见：".$data->audit_opinion;
                $updateData->feedbackDate = helper::now();
                $updateData->approverName = $data->approverName;
                $this->dao->update(TABLE_REQUIREMENT)->data($updateData)->where('id')->eq($requirement->id)->exec();

                $changes = common::createChanges($requirement, $updateData);
                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'syncstatus', $audit_opinion,'','guestcn');
                $this->action->logHistory($actionID, $changes);
                $updateStatus = 'feedbackfail';
                $updateComment = $updateData->reviewComments;
                $this->loadModel('consumed')->record('requirement', $requirement->id, 0, 'guestcn', $requirement->feedbackStatus, 'feedbackfail', array());
                //$this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirement->id), $logID);
            }
        }
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($updateStatus)
            ->set('comment')->eq($updateComment)
            ->set('reviewTime')->eq(helper::now())
            ->where('node')->eq($node->id)
            ->andWhere('reviewer')->eq('guestcn') //当前审核人
            ->exec();

        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateStatus)
            ->where('id')->eq($node->id)
            ->exec();

        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirement->id), $logID);
    }
}
