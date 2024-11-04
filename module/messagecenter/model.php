<?php
/**
 * The model file of common module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class messagecenterModel extends model
{
    public function pushMessageTo($objectType,$objectID,$actionType){
        $action = $this->config->messagecenter->objectTypes;
        $fields = $objectType."Fields";
        if (!isset($this->config->messagecenter->$fields)){
            return true;
        }
        $messageFields = $this->config->messagecenter->$fields;
        if(isset($action["$objectType"])){
            //查询具体单子详情，后续新增模块如无 getByID方法，建议新增此方法
            $info = $this->loadModel("$objectType")->getByID($objectID);
            //已办
            if(isset($action["$objectType"]['completedAction']) && in_array($actionType,$action["$objectType"]['completedAction'])){
                //只在消息中心增加已办消息
                $this->noUpdateMessageToInsert($objectType,$objectID);
                //清总和金信提交动作，状态在待办中，需要加待办
                if(in_array($actionType,array('submitexamine','submit')) && in_array($info->{$messageFields['formStatus']},$action["$objectType"]['completedAndIncompletedAction']['review'])){
                   $this->addDealMessage($objectType,$objectID);
                }
            }
            //已办 or 待办
            if(isset($action["$objectType"]['completedAndIncompletedAction']) && in_array($actionType,$action["$objectType"]['completedAndIncompletedAction'])){
                if(in_array($info->{$messageFields['formStatus']},$action["$objectType"]['completedAndIncompletedAction']['review']) || in_array($info->{$messageFields['formStatus']},$action["$objectType"]['completedAndIncompletedAction']['createfeedback'])){
                    //当前处理用户加已办
//                    if($action["$objectType"]['completedAndIncompletedAction'] != 'syncstatus'){
                    if($info->{$messageFields['formStatus']} != 'syncstatus'){
                        //会签的已办和待办
                        if(isset($action["$objectType"]['countersignCompletedAndIncompletedAction']) && in_array($actionType,$action["$objectType"]['countersignCompletedAndIncompletedAction']) && in_array($info->{$messageFields['formStatus']},$action["$objectType"]['countersignCompletedAndIncompletedAction']['review'])){
                            $this->updateDealMessage($objectType,$objectID,null,true);
                        }else{
                            $this->updateDealMessage($objectType,$objectID);
                        }
                    }
                    //下一个用户加待办
                    if ($info->{$messageFields['formStatus']} != 'sendback'){
                        //会签的已办和待办
                        if(!isset($action["$objectType"]['countersignCompletedAndIncompletedAction']) || !in_array($actionType,$action["$objectType"]['countersignCompletedAndIncompletedAction']) || !in_array($info->{$messageFields['formStatus']},$action["$objectType"]['countersignCompletedAndIncompletedAction']['review'])){
                            $this->addDealMessage($objectType,$objectID);
                        }
                    }
                }

                $fields = $objectType."Fields";
                $messageFields = $this->config->messagecenter->$fields;
                $beforeStatus = $this->loadModel('consumed')->getObjectByIDToMax($objectID, $objectType,$info->{$messageFields['formStatus']});
                // 前一个状态 审批状态中包含，处理后不包含。则消息中的待办必须按以下逻辑更新为已办
                if((in_array($beforeStatus->before,$action["$objectType"]['completedAndIncompletedAction']['review']) && !in_array($beforeStatus->after,$action["$objectType"]['completedAndIncompletedAction']['review']))
                    || (in_array($beforeStatus->before,$action["$objectType"]['completedAndIncompletedAction']['createfeedback']) && !in_array($beforeStatus->after,$action["$objectType"]['completedAndIncompletedAction']['review']))){
                    $this->updateDealMessage($objectType,$objectID);
                }
            }
        }
    }

    /**
     * zt_need_deal_message 消息表 新增
     * @param $objectType
     * @param $objectID
     */
    public function addDealMessage($objectType,$objectID,$updatestatus = null ){
       //查询具体单子详情，后续新增模块如无 getByID方法，建议新增此方法
       $info = $this->loadModel("$objectType")->getByID($objectID);
       if($info){
           $fields = $objectType."Fields";
           $messageFields = $this->config->messagecenter->$fields;
           $this->app->loadLang('outwarddelivery');
           if($updatestatus && in_array($objectType,array('modify','outwarddelivery'))){
               //清总和金信涉及授权管理
               $dealUsers = $this->loadModel('common')->getAuthorizer("$objectType",  $info->{$messageFields['reviewer']},$info->{$messageFields['formStatus']}, $this->lang->outwarddelivery->authorizeStatusList);
           }else if($objectType == 'change'){
               $reviewers = $info->reviewers;
               $reviewersArray = explode(',', $reviewers);
               $appiontUsers = $info->appiontUsers;
               $appiontUsersArray = explode(',', $appiontUsers);
               //所有审核人
               $dealUsers = implode(',',array_filter(array_merge($reviewersArray, $appiontUsersArray)));
           }else {
               $dealUsers = $info->{$messageFields['reviewer']};
           }
           $messageId = array();
           $data = new stdClass();
           $data->desc           = $info->{$messageFields['desc']};
           $data->code           = $info->{$messageFields['code']};
           $data->objectType     = "$objectType";
           $data->objectId       = $objectID;
           $data->createdBy      = $this->app->user->account;
           $data->deptId         = isset($info->{$messageFields['deptId']}) ? $info->{$messageFields['deptId']} : 0;
           $data->formCreatedBy   = $info->{$messageFields['formCreatedBy']};
           $data->formCreatedDate = $info->{$messageFields['formCreatedDate']};
           $data->formStatus     = $info->{$messageFields['formStatus']};
           $data->status         = $updatestatus ? 2 : 1; //待办
          // $data->reviewer       = $info[$messageFields['reviewer']];
           $data->version        = $info->{$messageFields['version']};
           $dealUsersArr = !$updatestatus ? array_filter(explode(',',trim($dealUsers,''))) : $this->app->user->account;
           if($dealUsersArr){
               if(is_array($dealUsersArr)){
                   foreach ($dealUsersArr as $item) {
                       $data->reviewer = $item;
                       $this->dao->insert(TABLE_NEED_DEAL_MESSAGE)->data($data)->exec();
                       $id = $this->dao->lastInsertID();
                       $messageId[] = $id;
                       $this->loadModel('action')->create('messagecenter', $id, 'created');
                   }
               }else{
                   $data->reviewer = $dealUsersArr;
                   $this->dao->insert(TABLE_NEED_DEAL_MESSAGE)->data($data)->exec();
                   $id = $this->dao->lastInsertID();
                   $messageId[] = $id;
                   $this->loadModel('action')->create('messagecenter', $id, 'created');
               }
               //查询是否有待办，直接忽略
              /* if($updatestatus){
                   $this->updateDealMessage($objectType,$objectID,1);
               }*/
           }
           //消息接口
           if($messageId && !$updatestatus){
              $this->action->pushMessages($messageId);
           }
           return $messageId;
       }
    }

    /**
     * 更新消息不存在，则新增
     * @param $objectType
     * @param $objectID
     * @return array
     */
    public function noUpdateMessageToInsert($objectType,$objectID){
        $info = $this->loadModel("$objectType")->getByID($objectID);
        if($info){
            $fields = $objectType."Fields";
            $messageFields = $this->config->messagecenter->$fields;
            $this->app->loadLang('outwarddelivery');
            if(in_array($objectType,array('modify','outwarddelivery'))){
                //清总和金信涉及授权管理
                $dealUsers = $this->loadModel('common')->getAuthorizer("$objectType",  $info->{$messageFields['reviewer']},$info->{$messageFields['formStatus']}, $this->lang->outwarddelivery->authorizeStatusList);
            }else if($objectType == 'change'){
                $reviewers = $info->reviewers;
                $reviewersArray = explode(',', $reviewers);
                $appiontUsers = $info->appiontUsers;
                $appiontUsersArray = explode(',', $appiontUsers);
                //所有审核人
                $dealUsers = implode(',',array_filter(array_merge($reviewersArray, $appiontUsersArray)));
            }else{
                $dealUsers = $info->{$messageFields['reviewer']};
            }
            $messageId = array();
            $data = new stdClass();
            $data->desc           = $info->{$messageFields['desc']};
            $data->code           = $info->{$messageFields['code']};
            $data->objectType     = "$objectType";
            $data->objectId       = $objectID;
            $data->createdBy      = $this->app->user->account;
            $data->deptId         = $info->{$messageFields['deptId']};
            $data->formCreatedBy   = $info->{$messageFields['formCreatedBy']};
            $data->formCreatedDate = $info->{$messageFields['formCreatedDate']};
            $data->formStatus     = $info->{$messageFields['formStatus']};
           // $data->status         = $updatestatus ? 2 : 1; //待办
            $data->version        = $info->{$messageFields['version']};
            $dealUsersArr = $this->app->user->account;//array_filter(explode(',',trim($dealUsers,'')));
            if($dealUsersArr){
                $data->status         = 2 ;//已办
                $data->reviewer = $dealUsersArr;
                $this->dao->insert(TABLE_NEED_DEAL_MESSAGE)->data($data)->exec();
                $id = $this->dao->lastInsertID();
                $messageId[] = $id;
                $this->loadModel('action')->create('messagecenter', $id, 'created');
                //查询是否有待办，直接忽略
                $this->ignoreDealMessage($objectType,$objectID);
            }
            //消息接口
           /* if($messageId){
              $this->action->pushMessages($messageId);
            }*/
            return $messageId;
        }
    }
    /**
     * 更新消息状态
     * @param $objectType
     * @param $objectID
     * @param $ignore
     */
    public function updateDealMessage($objectType,$objectID,$ignore = null,$isCountersign = false){
        $fields = $objectType."Fields";
        $messageFields = $this->config->messagecenter->$fields;
        $info = $this->loadModel("$objectType")->getByID($objectID);
        $code           = $info->{$messageFields['code']};
        $objectType     = $objectType;
        $objectId       = $objectID;
        $reviewer       = $info->{$messageFields['reviewer']};
        $messages = $this->getByMessageToWhere($code,$objectType,$objectId);//当前单子消息表中所有的待办
        if($messages){
            $messageID = array();
            foreach ($messages as $message){
                //待办人和当前用户一致，更新为已办
                $data = new stdClass();
                $data->formStatus = $info->{$messageFields['formStatus']};
               // $data->objectId         = $message->objectId;
                if($message->reviewer == $this->app->user->account && !$ignore){
                    $data->status     = '2'; //已办
                }else{
                    if(!$isCountersign){
                        $data->status     = '3';//忽略
                    }
                }
                $this->dao->update(TABLE_NEED_DEAL_MESSAGE)->data($data)->where('id')->eq($message->id)->exec();
                $messageID[] = $message->id;
                $changes =  common::createChanges($message, $data);
                $actionID = $this->loadModel('action')->create('messagecenter', $message->id, 'edited');
                if($changes) $this->action->logHistory($actionID, $changes);
            }
            //消息接口
            $this->loadModel('action')->pushMessages($messageID);
        }else{
            $this->noUpdateMessageToInsert($objectType,$objectID);
        }
    }
   /**
     * 忽略消息状态
     * @param $objectType
     * @param $objectID
     */
    public function ignoreDealMessage($objectType,$objectID){
        $fields = $objectType."Fields";
        $messageFields = $this->config->messagecenter->$fields;
        $info = $this->loadModel("$objectType")->getByID($objectID);
        $code           = $info->{$messageFields['code']};
        $objectType     = $objectType;
        $objectId       = $objectID;
        $reviewer       = $info->{$messageFields['reviewer']};
        $messages = $this->getByMessageToWhere($code,$objectType,$objectId);//当前单子消息表中所有的待办
        $messageID = array();
        if($messages){
            foreach ($messages as $message){
                $data = new stdClass();
                $data->formStatus = $info->{$messageFields['formStatus']};
                $data->status     = '3';//忽略
                $this->dao->update(TABLE_NEED_DEAL_MESSAGE)->data($data)->where('id')->eq($message->id)->exec();
                $messageID[] = $message->id;
                $changes =  common::createChanges($message, $data);
                $actionID = $this->loadModel('action')->create('messagecenter', $message->id, 'edited');
                if($changes) $this->action->logHistory($actionID, $changes);
            }
            //消息接口
            if($messageID){
                $this->loadModel('action')->pushMessages($messageID);
            }
        }
    }
    /**
     * 查询消息表
     * @param $code
     * @param $type
     * @param $objectId
     * @param null $reviewer
     * @return mixed
     */
    public function getByMessageToWhere($code,$type,$objectId,$reviewer = null){
        $message = $this->dao->select('id,`desc`,code,objectType,objectId,createdBy,deptId,formCreatedDate,formStatus,status,reviewer ,`version`')
            ->from(TABLE_NEED_DEAL_MESSAGE)
            ->where('code')->eq($code)
            ->andWhere('objectType')->eq($type)
            ->andWhere('objectId')->eq($objectId)
            ->beginIF($reviewer)->andWhere('reviewer')->eq($reviewer)->fi()
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->eq('1')
            ->orderBy('id desc')
            ->fetchAll('id');

        return $message;
    }

}

