<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function syncthird()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('componentthirdaccount' , 'syncthird'); //todo 测试不加日志
        $this->checkApiToken();

        $this->loadModel('componentthirdaccount');

        $errorData = array();
        $successNum = 0;
        //遍历数据并入库
        foreach ($_POST as $key => $v){
            $isError = false;
            $iscomponentVersion = 'false';
            foreach ($this->lang->componentthirdaccount->apiItems as $k => $item1)
            {
                if($item1['required'] && $v[$k] == ''){
                    $error = new stdClass();
                    $error->id = $v;
                    $error->errorMsg = '缺少必填字段';
                    array_push($errorData, $error);
                    $isError = true;
                    break 1;
                }
            }
            $insertData = new stdClass();
            foreach ($v as $i => $item){
                if($i == 'componentName'){
                    $componentObj = $this->dao->select('*')->from(TABLE_COMPONENT_RELEASE)->where('type')->eq('third')->andWhere('name')->eq($item)->fetch();
                    if(empty($componentObj)){
                        $insertData->customComponent = $item;
                        foreach ($v as $x => $y){
                            if($x == 'componentVersion'){
                                $iscomponentVersion = 'true';
                                $insertData->customComponentVersion = $y;
                            }
                        }
                    }else{
                        $insertData->componentReleaseId = $componentObj->id;
                        foreach ($v as $x => $y){
                            if($x == 'componentVersion'){
                                $componentVersion = $this->dao->select('*')->from(TABLE_COMPONENT_VERSION)->where('version')->eq($y)->andWhere('componentReleaseId')->eq($componentObj->id)->fetch();
                                if(empty($componentVersion)){
                                    $error = new stdClass();
                                    $error->id = $v;
                                    $error->errorMsg = '组件版本不存在';
                                    array_push($errorData, $error);
                                    $isError = true;
                                    break 2;
                                }
                                $iscomponentVersion = 'true';
                                $insertData->componentVersionId = $componentVersion->id;
                            }
                        }
                    }
                }
                if($i == 'appName'){
                    $appObj = $this->dao->select('*')->from(TABLE_APPLICATION)->where('code')->eq($item)->fetch();
                    if(empty($appObj)){
                        $error = new stdClass();
                        $error->id = $v;
                        $error->errorMsg = '系统不存在';
                        array_push($errorData, $error);
                        $isError = true;
                        break 1;
                    }
                    $insertData->appId = $appObj->id;
                }
                if($i == 'productName'){
                    $productObj = $this->dao->select('*')->from(TABLE_PRODUCT)->where('code')->eq($item)->fetch();
                    if(empty($productObj)){
                        $error = new stdClass();
                        $error->id = $v;
                        $error->errorMsg = '产品不存在';
                        array_push($errorData, $error);
                        $isError = true;
                        break 1;
                    }
                    $insertData->productId = $productObj->id;
                    foreach ($v as $x => $y){
                        if($x == 'productVersion'){
                            $productVersion = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('title')->eq($y)->andWhere('product')->eq($productObj->id)->fetch();
                            if(empty($productVersion)){
                                $error = new stdClass();
                                $error->id = $v;
                                $error->errorMsg = '产品版本不存在';
                                array_push($errorData, $error);
                                $isError = true;
                                break 2;
                            }
                            $insertData->productVersionId = $productVersion->id;
                        }
                    }
                }
            }
            if(!$isError){
                $insertData->type = 'third';
                if(!empty($insertData->componentReleaseId)){
                    $dataObject = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('appId')->eq($insertData->appId)
                        ->andWhere('productId')->eq($insertData->productId)->andWhere('productVersionId')->eq($insertData->productVersionId)
                        ->andWhere('componentReleaseId')->eq($insertData->componentReleaseId)
                        ->beginIF($iscomponentVersion == 'true')->andWhere('componentVersionId')->eq($insertData->componentVersionId)->fi()
                        ->fetch();
                }else{
                    $dataObject = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('appId')->eq($insertData->appId)
                        ->andWhere('productId')->eq($insertData->productId)->andWhere('productVersionId')->eq($insertData->productVersionId)
                        ->andWhere('customComponent')->eq($insertData->customComponent)
                        ->beginIF($iscomponentVersion == 'true')->andWhere('customComponentVersion')->eq($insertData->customComponentVersion)->fi()
                        ->fetch();
                }

                if(empty($dataObject)){
                    $this->dao->insert(TABLE_COMPONENT_ACCOUNT)
                        ->data($insertData)->autoCheck()
                        ->exec();
                    $successNum = $successNum+1;
                }else{
                    $error = new stdClass();
                    $error->id = $v;
                    $error->errorMsg = '数据库存在相同数据';
                    array_push($errorData, $error);
                }
            }
        }


        $this->requestlog->response('success', '成功接收：'.$successNum, $errorData, $logID);
    }
}