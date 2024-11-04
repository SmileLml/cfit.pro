<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function copyrightreviewfeedback()
    {
        $this->loadModel('copyrightqz');
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('copyrightqz' , 'copyrightreviewfeedback');
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
          $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        // 清总的copyrightRegistrationId对应我们的id,我们的copyrightRegistrationId对应清总的key
        $copyrightqz = $this->dao->select('*')->from(TABLE_COPYRIGHTQZ)->where('id')->eq($this->post->copyrightRegistrationId)->fetch();
        //判断数据库是否存在记录
        if(!empty($copyrightqz->id)){
            //更新数据库
            if($this->post->changeStatus=='gitee审核通过'){
                $data['status'] = 'done';
                $data['outsideReviewResult'] = 'pass';
                $data['dealUser'] = '';
            }else{
                $data['status'] = 'feedbackFailed';
                $data['outsideReviewResult'] = 'reject';
                $data['dealUser'] = implode(',',array_keys($this->lang->copyrightqz->secondLineReviewList));
            }
            $data['outsideReviewTime'] = helper::now();
            $data['reason']                  = $this->post->reason;
            $data['approverName']            = $this->post->approverName;
            $this->dao->update(TABLE_COPYRIGHTQZ)->data($data)->where('id')->eq((int)$copyrightqz->id)->exec();  
            $this->loadModel('action')->create('copyrightqz', $copyrightqz->id, 'copyrightqzsyncfeedback', $data['reason'],'','guestcn');
            $this->loadModel('consumed')->record('copyrightqz', $copyrightqz->id, '0', 'guestcn', $copyrightqz->status, $data['status'], array(),'著作登记');
        }else{
          $errMsg = '该ID不存在';
          $this->requestlog->response('fail', $errMsg, [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
          $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($copyrightqz->id)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('copyrightRegistrationId' => $copyrightqz->id), $logID);
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput(){
      $errMsg = [];
      $this->loadModel('copyrightqz');
      //校验是否存在异常字段
      foreach ($_POST as $key => $v)
      {
        if(!isset($this->lang->copyrightqz->apiItems[$key])){
          $errMsg[] = $key."不是协议字段";
        }
      }

      foreach ($this->lang->copyrightqz->apiItems as $k => $v)
      {
        if($v['required'] && $this->post->$k === ''){
          $errMsg[] = $k.$v['name'].$this->post->$k.'不可以空';
        }
        if($v['target'] != $k)
        {
          $_POST[$v['target']] = $this->post->$k;
          unset($_POST[$k]);
        }
      }
      return $errMsg;
    }
}