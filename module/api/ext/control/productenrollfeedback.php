<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function productenrollfeedback()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('productenroll' , 'productenrollfeedback');
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
          $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        $productenroll = $this->loadModel('productenroll')->getByCode($this->post->id);
        $outwardDeliveryId = $this->loadModel('outwarddelivery')->getOutwardDeliveryByTypeId('productEnroll',$productenroll->id);
        $outwardDelivery   = $this->loadModel('outwarddelivery')->getByID($outwardDeliveryId);
        //判断数据库是否存在记录
        if(!empty($productenroll->id)){
          //更新数据库
          $this->loadModel('productenroll')->updateStatus($outwardDelivery,$productenroll->id, $this->post->cardStatus, $this->post->returnPerson
            , $this->post->returnCase, $this->post->emisRegisterNumber);
        }else{
          $errMsg = '该ID不存在';
          $this->requestlog->response('fail', $errMsg, [], $logID, self::FAIL_CODE);
        }

        //清总对外交付-产品登记外部退回，记录退回原因
        $newOutwardDelivery   = $this->loadModel('outwarddelivery')->getByID($outwardDeliveryId);
        $reviewFailReason = $this->loadModel('outwarddelivery')->getHistoryReview($newOutwardDelivery, 2);
        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('reviewFailReason')->eq($reviewFailReason)->where('id')->eq($outwardDelivery->id)->exec();
        if(dao::isError()) {
          $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }

        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($productenroll->id)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $this->post->id), $logID);
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput(){
      $errMsg = [];
      $this->loadModel('productenroll');
      //校验是否存在异常字段
      foreach ($_POST as $key => $v)
      {
        if(!isset($this->lang->productenroll->apiItems[$key])){
          $errMsg[] = $key."不是协议字段";
        }
      }

      foreach ($this->lang->productenroll->apiItems as $k => $v)
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