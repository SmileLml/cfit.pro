<?php
/**
 * 需求意向接口
 */
include '../../control.php';
class myApi extends api
{
    public function demand()
    {
        // 请求中断继续执行代码
        ignore_user_abort(true);
        set_time_limit(0);

        /* 保存请求日志并检查请求参数。 */
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('demand' , 'createDemand');
        $this->requestlog->judgeRequestMode($logID);

        /* 判断所需字段是否存在。*/
        $data = fixer::input('post')
            ->stripTags('User_demand_background', $this->config->allowedTags)
            ->stripTags('User_demand_backgrounds', $this->config->allowedTags)
            ->get();
        foreach($this->config->api->demandParams as $param)
        {
            if(!isset($data->{$param}))
            {
                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
                $this->requestlog->response('fail', $errorMessage, array(), $logID);
            }
        }

        $this->loadModel('opinion');
        $opinion = $this->opinion->getByCode($_POST['Demand_number']);
        /* 判断需求是否已同步。*/
        if(empty($opinion))
        {
            /* 对必填字段做处理。*/
            unset($_POST);
            $this->config->opinion->create->requiredFields = 'name,sourceMode,sourceName,union,date,contact,contactInfo,deadline,background,overview,urgency,category,type';

            /* 设置参数到post中。*/
            foreach($this->config->api->demandFields as $paramName => $field)
            {
                /* 对时间戳做处理。*/
                if($paramName == 'Proposed_date' or $paramName == 'Expected_realization_date')
                {
                    $timeStamp = substr($data->{$paramName}, 0, 10);
                    $processingTime = date('Y-m-d', $timeStamp);
                    $this->post->set($field, $processingTime);
                    continue;
                }
                if($field != 'downloadFile') $this->post->set($field, $data->{$paramName});
            }
            //处理需求类别映射 禅道类型 business业务类、structure技术类、businesstechnology业务类+技术类
            $category = isset($data->Demand_category) ? $data->Demand_category : ''; //单选形式，但是数组形式传递，且数组只包含一项
            if(!empty($category[0]))
            {
                $this->mappingField($category[0]);
                //字段映射不到db需存空
                $this->config->opinion->create->requiredFields = 'name,sourceMode,sourceName,union,date,contact,contactInfo,deadline,background,overview,urgency,type';
            }else{
                $this->post->set('category', '');
            }

            //紧急程度
            if(isset($data->Degree_of_urgency) && !empty($data->Degree_of_urgency))
            {
                $urgency = $data->Degree_of_urgency; //单选形式，但是数组形式传递，且数组只包含一项
                $this->post->set('urgency', $urgency[0]);
            }

            //需求类型
            if(isset($data->RequirementType) && !empty($data->RequirementType))
            {
                $requirementType = $data->RequirementType; //单选形式，但是数组形式传递，且数组只包含一项
                $this->post->set('type', $requirementType[0]);
            }

            $this->post->set('receiveDate', helper::today());// 创建日期作为接受日期
            $this->post->set('sourceMode', 8);
            $this->post->set('union', 2);
            $this->post->set('workload', 0);
            /* 调用创建方法，判罚是否成功创建。*/
            $opinionID = $this->opinion->create(true);
            if(dao::isError())
            {
                $errors = dao::getError();
                $this->requestlog->response('fail', $errors, array(), $logID);
            }

            /* 更新附件。*/
            foreach($this->config->api->demandFields as $paramName => $field)
            {
                if($field == 'downloadFile')
                {
                    $files = $data->{$paramName};
                    if(!empty($files))
                    {
                        foreach($files as $file)
                        {
                            $this->requestlog->downloadApiFile($file['url'], $file['name'], 'opinion', $opinionID);
                        }
                    }
                }
            }

            $this->loadModel('action')->create('opinion', $opinionID, 'syncCreated', $this->lang->api->syncCreate,'','guestcn');
            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $opinionID), $logID);
        }
        else
        {
            /* 对必填字段做处理。*/
            unset($_POST);
            $this->config->opinion->edit->requiredFields = 'name,sourceMode,sourceName,union,date,contact,contactInfo,deadline,background,overview,urgency,category,type';

            /* 设置参数到post中。*/
            foreach($this->config->api->demandFields as $paramName => $field)
            {
                /* 对时间戳做处理。*/
                if($paramName == 'Proposed_date' or $paramName == 'Expected_realization_date')
                {
                    $timeStamp = substr($data->{$paramName}, 0, 10);
                    $processingTime = date('Y-m-d', $timeStamp);
                    $this->post->set($field, $processingTime);
                    continue;
                }
                if($field != 'downloadFile') $this->post->set($field, $data->{$paramName});
            }

            $category = isset($data->Demand_category) ? $data->Demand_category : ''; //单选形式，但是数组形式传递，且数组只包含一项
            if(!empty($category[0]))
            {
                //处理需求类别映射 禅道类型 business业务类、structure技术类、businesstechnology业务类+技术类
                $this->mappingField($category[0]);
                //字段映射不到db需存空
                $this->config->opinion->edit->requiredFields = 'name,sourceMode,sourceName,union,date,contact,contactInfo,deadline,background,overview,urgency,type';
            }else{
                $this->post->set('category', '');
            }

            //紧急程度
            if(isset($data->Degree_of_urgency) && !empty($data->Degree_of_urgency))
            {
                $urgency = $data->Degree_of_urgency; //单选形式，但是数组形式传递，且数组只包含一项
                $this->post->set('urgency', $urgency[0]);
            }

            //需求类型
            if(isset($data->RequirementType) && !empty($data->RequirementType))
            {
                $requirementType = $data->RequirementType; //单选形式，但是数组形式传递，且数组只包含一项
                $this->post->set('type', $requirementType[0]);
            }

            $this->post->set('sourceMode', 8);
            $this->post->set('union', 2);
            $this->post->set('workload', $opinion->workload);
            /* 调用创建方法，判罚是否成功创建。*/
            $changes = $this->opinion->update($opinion->id,true);
            if(dao::isError())
            {
                $errors = dao::getError();
                $this->requestlog->response('fail', $errors, $logID);
            }

            /* 删除原来的附件。*/
            $this->requestlog->deleteOldFile('opinion', $opinion->id);

            /* 更新附件。*/
            foreach($this->config->api->demandFields as $paramName => $field)
            {
                if($field == 'downloadFile')
                {
                    $files = $data->{$paramName};
                    if(!empty($files))
                    {
                        foreach($files as $file)
                        {
                            $this->requestlog->downloadApiFile($file['url'], $file['name'], 'opinion', $opinion->id);
                        }
                    }
                }
            }

            $actionID = $this->loadModel('action')->create('opinion', $opinion->id, 'syncUpdated', $this->lang->api->syncUpdate,'','guestcn');
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);

            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $opinion->id), $logID);
        }

        die();
    }

    /**
     * @Notes:
     * @Date: 2023/12/11
     * @Time: 17:31
     * @Interface mappingField
     * @param $category
     */
    public function mappingField($category)
    {

        //处理需求类别映射 禅道类型 business业务类、structure技术类、businesstechnology业务类+技术类
        $str = mb_substr($category,0,4);
        switch ($str)
        {
            case "自主技术";
                $this->post->set('category', 'structure');
                break;
            case "自主业务";
                $this->post->set('category', 'business');
                break;
            default:
                if(in_array($category,$this->lang->api->categoryTechnologyList)){
                    $this->post->set('category', 'structure');
                }else if(in_array($category,$this->lang->api->categoryBusinessList)){
                    $this->post->set('category', 'business');
                }else{
                    $this->post->set('category', '');
                }
                break;
        }

    }


}
