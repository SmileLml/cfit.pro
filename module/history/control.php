<?php
class history extends control
{
    // 处理生产变更页面需求任务丢失的问题
    public function fixModifyRequirementId(){
        $data = $this->dao->select('id,demandId')->from(TABLE_MODIFY)->orderBy('id')->fetchAll('id');
        $demandData = $this->dao->select('id,requirementID')->from(TABLE_DEMAND)->fetchAll('id');
        foreach($data as $id => $item){
            if(empty($item)||$item->demandId=='') continue;
            $requirementId = [];
            foreach(explode(',',trim($item->demandId,',')) as $demand){
                if(empty($demand)||!isset($demandData[$demand])) continue;
                if($demandData[$demand]->requirementID==0) continue;
                $requirementId[] = $demandData[$demand]->requirementID;
            }
            $requirementId = array_unique($requirementId);
            if($requirementId==[]) continue;
            var_dump('变更单id（'.$id.'）需求条目id（'.$item->demandId.'）需求任务id（'.implode(',',$requirementId).'）');
            $this->dao->update(TABLE_MODIFY)->set('requirementId')->eq(implode(',',$requirementId))->where('id')->eq($id)->exec();
        }
    }
    //处理生产变更产品编号的历史数据
    public function fixModifyProductCode(){
        $data = $this->dao->select('id,productCode')->from(TABLE_MODIFY)->orderBy('id')->fetchAll('id');
        foreach($data as $id => $item){
            if(empty($item)) continue;
            $newitem =  json_decode($item->productCode);
            if(empty($newitem)) continue;
            $productInfoCode = '';
            $productId       = '';
            foreach($newitem as $product){
                $productObj  =  $this->loadModel('product')->getById($product->assignProduct);
                if(!empty($productObj)){
                    if(!empty($productInfoCode)){
                        $productInfoCode = $productInfoCode.',';
                    }
                    $productInfoCode = $productInfoCode.$productObj->code;
                    $productInfoCode = $productInfoCode.'-'.'V'.trim(trim($product->versionNumber,'v'),'V').'-for-'.$product->supportPlatform;
                    if(!empty($productObj->os)){
                        $productInfoCode = $productInfoCode.'-'.$productObj->os;
                    }
                    if(!empty($productObj->arch)){
                        $productInfoCode = $productInfoCode.'-'.$productObj->arch;
                    }
                }
                if(empty($productId)){
                    $productId = $product->assignProduct;
                }else{
                    $productId = $productId.','.$product->assignProduct;
                }
            }

            $this->dao->update(TABLE_MODIFY)
            ->set('productId')->eq($productId)
            ->set('productInfoCode')->eq($productInfoCode)
            ->where('id')->eq($id)->exec();
            
            var_dump($id,$productId,$productInfoCode.PHP_EOL);
        }
    }
    // 因为执行现在无法手动关联产品，只能把项目关联的产品都关联到执行上。处理所有项目下的执行重新关联项目关联的产品。
    public function executionRelatedProducts()
    {
        $projectIdList = $this->dao->select('id, name')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();

        // 查询项目下的执行，为这些执行同步关联产品。
        $this->loadModel('execution');
        foreach($projectIdList as $projectID => $projectName)
        {
            // 查询项目关联的产品。
            $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
            $_POST['products'] = $products;

            // 查询项目下的所有执行。
            $executionIdList = $this->dao->select('id')->from(TABLE_EXECUTION)->where('project')->eq($projectID)->andWhere('deleted')->eq('0')->fetchAll();
            foreach($executionIdList as $execution)
            {
                $this->execution->updateProducts($execution->id);
            }
        }
        echo '处理了' . count($projectIdList) . '个项目。';
    }

    // 去除对外交付的单的待产品经理审批节点
    public function eliminatePO()
    {
        //处理对外交付历史纪录
        $this->app->loadLang('outwarddelivery');
        $ODList= $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)
            ->where('deleted')->ne(1)
            ->andWhere('status')->eq('systemsuccess')
            ->fetchAll('id');
        foreach($ODList as $outwarddeliveryID => $outwarddelivery)
        {
           //正处于待产品经理审批的状态，需要判断跳过后的状态
            if($outwarddelivery->isNewModifycncc == 1){
                $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[ $outwarddelivery->level ] ;  //不同表单类型的审批节点不同
            }
            elseif($outwarddelivery->isNewProductEnroll == 1){
                $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[ 5 ] ;
            }
            elseif($outwarddelivery->isNewTestingRequest == 1){
                $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[ 4 ] ;
            }

            $afterStage = $outwarddelivery->reviewStage + 1;
            while($afterStage < 7){
                if ( $afterStage == 3 and $this->post->isNeedSystem == 'no') { $afterStage += 1; }  //如果跳过系统部审批，则再前进一步
                if ( ! in_array($afterStage, $requiredStage )) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                }
                else{  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }

            $current = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')   //将当前产品经理节点设置为跳过
            ->andWhere('objectID')->eq($outwarddelivery->id)
                ->andWhere('version')->eq($outwarddelivery->version)
                ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch('id');

            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')->where('node')->eq($current)->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('outwarddelivery')   //将当前产品经理节点设置为跳过
            ->andWhere('objectID')->eq($outwarddelivery->id)
                ->andWhere('version')->eq($outwarddelivery->version)
                ->andWhere('id')->eq($current)->orderBy('stage,id')->limit(1)->exec();

            if($afterStage - $outwarddelivery->reviewStage > 1){
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('outwarddelivery')   //将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($outwarddelivery->id)
                    ->andWhere('version')->eq($outwarddelivery->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $outwarddelivery->reviewStage - 1)->exec();
            }

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')   //查找下一节点的状态
            ->andWhere('objectID')->eq($outwarddelivery->id)
                ->andWhere('version')->eq($outwarddelivery->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('outwardDelivery', $outwarddelivery->id, $outwarddelivery->version, $afterStage);
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($outwarddelivery->id)->exec();
            }

            //更新状态
            if(isset($this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage])){
                $status = $this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage];
            }

            $lastDealDate = date('Y-m-d');

            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddeliveryID)->exec();
            $this->loadModel('consumed')->remove('outwarddelivery', $outwarddeliveryID, $this->app->user->account, $status); //逻辑删除原有状态 只保留最新的
            $this->loadModel('consumed')->record('outwarddelivery', $outwarddeliveryID, $this->post->consumed, $this->app->user->account, $outwarddelivery->status, $status, array());

            //如果状态为”待外部审批“,更新当前审批字段
            if($status == 'withexternalapproval'){
                //修改待处理人为清总
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq('guestcn')->where('id')->eq($outwarddeliveryID)->exec();

                //是否有测试申请单
                if($outwarddelivery->testingRequestId != 0){
                    $status2 = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch()->status;
                    if($status2 == 'testingrequestpass'){
                        //是否有产品登记单
                        if($outwarddelivery->productEnrollId != 0){
                            $status2 = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch()->status;
                            if($status2 == 'emispass' or $status2 == 'giteepass'){
                                //只剩下生产变更
                                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                            }else{
                                //产品登记单未处于外部审批通过
                                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="3"')->where('id')->eq($outwarddeliveryID)->exec();
                            }
                        }else{
                            //只剩下生产变更
                            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                        }
                    }else{
                        //测试申请未处于外部审批通过
                        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="2"')->where('id')->eq($outwarddeliveryID)->exec();
                    }

                }else{
                    //没有测试申请，是否有产品登记单
                    if($outwarddelivery->productEnrollId != 0){
                        $status2 = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch()->status;
                        if($status2 == 'emispass' or $status2 == 'giteepass'){
                            //只剩下生产变更
                            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                        }else{
                            //产品登记单未处于外部审批通过
                            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="3"')->where('id')->eq($outwarddeliveryID)->exec();
                        }
                    }else{
                        //没有产品登记，只剩下生产变更
                        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                    }
                }
            }

            //更新子表单的状态
            if($status == 'withexternalapproval') $status = 'waitqingzong';
            if($outwarddelivery->isNewModifycncc == 1){
                $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->modifycnccId)->exec();
                $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'review', $this->post->comment);
            }
            if($outwarddelivery->isNewProductEnroll == 1 and in_array($outwarddelivery->reviewStage,explode(',','0,1,2,4,7')) == 1){
//                if($outwarddelivery->reviewStage == 2) $status = 'systemsuccess';
                if($outwarddelivery->reviewStage == 2) $status = 'gmsuccess';
                $productenroll  = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch();
                if($productenroll->status != 'giteepass' and $productenroll->status != 'emispass'){
                    $this->dao->update(TABLE_PRODUCTENROLL)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->productEnrollId)->exec();
                    $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, 'review', $this->post->comment);
                }
            }
            if($outwarddelivery->isNewTestingRequest == 1 and in_array($outwarddelivery->reviewStage,explode(',','0,1,2,7')) == 1){
                if($outwarddelivery->reviewStage == 2) $status = 'gmsuccess';
                $testingrequest = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch();
                if($testingrequest->status != 'testingrequestpass')  {
                    $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->testingRequestId)->exec();
                    $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'review', $this->post->comment);
                }
            }
        }
        echo '处理了' . count($ODList) . '个对外交付。';

        //处理金信交付-生产变更历史纪录
        $this->app->loadLang('modify');
        $modifyList= $this->dao->select('*')->from(TABLE_MODIFY)
            ->Where('status')->eq('systemsuccess')
            ->fetchAll('id');
        foreach($modifyList as $modifyID => $modify)
        {
            //正处于待产品经理审批的状态，需要判断跳过后的状态
            $requiredStage = $this->lang->modify->requiredReviewerList[ $modify->level ] ;  //不同变更级别类型的审批节点不同

            $afterStage = $modify->reviewStage + 1;
            while($afterStage < 7){
                if ( $afterStage == 3 and $this->post->isNeedSystem == 'no') { $afterStage += 1; }  //如果跳过系统部审批，则再前进一步
                if ( ! in_array($afterStage, $requiredStage )) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                }
                else{  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }

            $current = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')   //将当前产品经理节点设置为跳过
            ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch('id');

            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')->where('node')->eq($current)->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modify')   //将当前产品经理节点设置为跳过
            ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('id')->eq($current)->orderBy('stage,id')->limit(1)->exec();

            if($afterStage - $modify->reviewStage > 1){
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modify')   //将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($modify->id)
                    ->andWhere('version')->eq($modify->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $modify->reviewStage - 1)->exec();
            }

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')   //查找下一节点的状态
            ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('modify', $modify->id, $modify->version, $afterStage);
            }

            //更新状态
            if(isset($this->lang->modify->reviewBeforeStatusList[$afterStage])){
                $status = $this->lang->modify->reviewBeforeStatusList[$afterStage];
            }

            $lastDealDate = date('Y-m-d');

            $this->dao->update(TABLE_MODIFY)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($modifyID)->exec();
            $this->loadModel('consumed')->remove('modify', $modifyID, $this->app->user->account, $status); //逻辑删除原有状态 只保留最新的
            $this->loadModel('consumed')->record('modify', $modifyID, $this->post->consumed, $this->app->user->account, $modify->status, $status, array());

        }

        echo '处理了' . count($modifyList) . '个生产变更。';

        //处理金信交付-数据获取和数据修正 历史纪录
        $this->app->loadLang('info');
        $infoList= $this->dao->select('*')->from(TABLE_INFO)
            ->Where('status')->eq('systemsuccess')
            ->fetchAll('id');
        foreach($infoList as $infoID => $info)
        {
            //正处于待产品经理审批的状态，需要判断跳过后的状态
            $requiredStage = $this->lang->info->requiredReviewerList ;  //不同变更级别类型的审批节点不同

            $afterStage = $info->reviewStage + 1;
            while($afterStage < 7){
                if ( $afterStage == 3 and $this->post->isNeedSystem == 'no') { $afterStage += 1; }  //如果跳过系统部审批，则再前进一步
                if ( ! in_array($afterStage, $requiredStage )) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                }
                else{  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }

            $current = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('info')   //将当前产品经理节点设置为跳过
            ->andWhere('objectID')->eq($info->id)
                ->andWhere('version')->eq($info->version)
                ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch('id');

            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')->where('node')->eq($current)->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('info')   //将当前产品经理节点设置为跳过
            ->andWhere('objectID')->eq($info->id)
                ->andWhere('version')->eq($info->version)
                ->andWhere('id')->eq($current)->orderBy('stage,id')->limit(1)->exec();

            if($afterStage - $info->reviewStage > 1){
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('info')   //将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($info->id)
                    ->andWhere('version')->eq($info->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $info->reviewStage - 1)->exec();
            }

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('info')   //查找下一节点的状态
            ->andWhere('objectID')->eq($info->id)
                ->andWhere('version')->eq($info->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('info', $info->id, $info->version, $afterStage);
            }

            //更新状态
            if(isset($this->lang->info->reviewBeforeStatusList[$afterStage])){
                $status = $this->lang->info->reviewBeforeStatusList[$afterStage];
            }

            $lastDealDate = date('Y-m-d');

            $this->dao->update(TABLE_INFO)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($infoID)->exec();
            $this->loadModel('consumed')->remove('info', $infoID, $this->app->user->account, $status); //逻辑删除原有状态 只保留最新的
            $this->loadModel('consumed')->record('info', $infoID, $this->post->consumed, $this->app->user->account, $info->status, $status, array());

        }

        echo '处理了' . count($infoList) . '个数据获取/修正。';
    }

    //处理对外交付-退回原因历史数据
    public function processRevertReason(){
        $this->app->loadLang('outwarddelivery');
        $ODList= $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)
            ->where('deleted')->ne(1)
            ->fetchAll('id');
        foreach($ODList as $outwarddeliveryID => $outwarddelivery) {
            $revertReasonArray = array();
            if(!empty($outwarddelivery->revertReason)){
                $revertReasonArray[]=array('RevertDate'=> $outwarddelivery->revertDate,'RevertReason'=>$outwarddelivery->revertReason);
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('revertReason')->eq(json_encode($revertReasonArray))->where('id')->eq($outwarddeliveryID)->exec();
            }
        }
        echo '处理了' . count($ODList) . '个对外交付。';
    }

    // 处理需求池所有需求[系统分类]历史字段数据。
    public function demandRelatedApps()
    {
        $demandPairs = $this->dao->select('id,app,isPayment')->from(TABLE_DEMAND)->where('status')->ne('deleted')->orderBy('id_desc')->fetchAll();

        foreach($demandPairs as $demand)
        {
            if(empty($demand->app)) continue;
            $appIdList = explode(',', $demand->app);

            // 根据[所属应用系统]处理[系统分类]字段的值。
            $paymentIdList = array();
            $isPayment = '';
            foreach($appIdList as $appID)
            {
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
                $isPayment = implode(',', $paymentIdList);
            }
            $this->dao->update(TABLE_DEMAND)->set('isPayment')->eq($isPayment)->where('id')->eq($demand->id)->exec();
        }
        echo '处理了' . count($demandPairs) . '个需求。';
    }

    // 处理问题[系统分类]历史字段数据。
    public function problemRelatedApps()
    {
        $problemPairs = $this->dao->select('id,app,isPayment')->from(TABLE_PROBLEM)->where('status')->ne('deleted')->orderBy('id_desc')->fetchAll();

        foreach($problemPairs as $problem)
        {
            if(empty($problem->app)) continue;
            $appIdList = explode(',', $problem->app);

            // 根据[所属应用系统]处理[系统分类]字段的值。
            $paymentIdList = array();
            $isPayment = '';
            foreach($appIdList as $appID)
            {
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
                $isPayment = implode(',', $paymentIdList);
            }
            $this->dao->update(TABLE_PROBLEM)->set('isPayment')->eq($isPayment)->where('id')->eq($problem->id)->exec();
        }
        echo '处理了' . count($problemPairs) . '个问题。';
    }

    // 处理生产变更单[系统分类]历史字段数据。
    public function modifyRelatedApps()
    {
        $modifyPairs = $this->dao->select('id,app,isPayment')->from(TABLE_MODIFY)->where('status')->ne('deleted')->orderBy('id_desc')->fetchAll();

        foreach($modifyPairs as $modify)
        {
            if(empty($modify->app)) continue;
            $appIdList = explode(',', $modify->app);

            // 根据[所属应用系统]处理[系统分类]字段的值。
            $paymentIdList = array();
            $isPayment = '';
            foreach($appIdList as $appID)
            {
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
                $isPayment = implode(',', $paymentIdList);
            }
            $this->dao->update(TABLE_MODIFY)->set('isPayment')->eq($isPayment)->where('id')->eq($modify->id)->exec();
        }
        echo '处理了' . count($modifyPairs) . '个生产变更单。';
    }

    // 处理info[系统分类]历史字段数据。
    public function infoRelatedApps()
    {
        $infoPairs = $this->dao->select('id,app,isPayment')->from(TABLE_INFO)->where('status')->ne('deleted')->orderBy('id_desc')->fetchAll();

        foreach($infoPairs as $info)
        {
            if(empty($info->app)) continue;
            $appIdList = explode(',', $info->app);

            // 根据[所属应用系统]处理[系统分类]字段的值。
            $paymentIdList = array();
            $isPayment = '';
            foreach($appIdList as $appID)
            {
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
                $isPayment = implode(',', $paymentIdList);
            }
            $this->dao->update(TABLE_INFO)->set('isPayment')->eq($isPayment)->where('id')->eq($info->id)->exec();
        }
        echo '处理了' . count($infoPairs) . '个info。';
    }

    // 处理问题池问题[制版次数]历史字段数据。
    public function problemReleases()
    {
        $problemPairs = $this->dao->select('id')->from(TABLE_PROBLEM)->orderBy('id_desc')->fetchAll();
        $this->loadModel('problem');
        foreach($problemPairs as $problem)
        {
            $buildTimes = $this->problem->getBuild($problem->id);
            if($buildTimes)
            {
                $this->dao->update(TABLE_PROBLEM)->set('buildTimes')->eq($buildTimes)->where('id')->eq($problem->id)->exec();
            }
        }
        echo '处理了' . count($problemPairs) . '个问题。';
    }

    //根据外部计划关联的内部计划，处理projectplan表新增字段
    public function projectplanOutsideProject()
    {
        $this->loadModel('projectPlan');
        $outsideplan = $this->dao->select('id,linkedPlan')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->fetchPairs();
        $j=0;
        foreach ($outsideplan as $id => $linkedPlan) {
            $j++;
            if(!empty($linkedPlan)){
                $this->projectPlan->maintain($id,$linkedPlan);
            }
        }
        echo '处理了:' . $j . '个外部信息计划。';
    }

    // 处理年度计划[状态]字段。
    public function projectplanStatus()
    {
        // 由于增加了流程状态，原流程变更，导致需要重新对旧数据状态进行梳理。
        $plans = $this->dao->select('id,status')->from(TABLE_PROJECTPLAN)->where('status')->eq('wait')->orderBy('id_desc')->fetchAll();
        foreach($plans as $plan)
        {
            $this->dao->update(TABLE_PROJECTPLAN)->set('status')->eq('start')->where('id')->eq($plan->id)->exec();
        }
        echo '处理了' . count($plans) . '个年度计划。';
    }

    // 判断带拆分的需求意向是否已经拆分了需求，对其更新[状态]字段。
    public function opinionStatus()
    {
        $opinions = $this->dao->select('id,status')->from(TABLE_OPINION)->where('status')->eq('created')->orderBy('id_desc')->fetchAll();
        $processTotal = 0;
        foreach($opinions as $opinion)
        {
            $demand = $this->dao->select('id,status')->from(TABLE_DEMAND)->where('opinionID')->eq($opinion->id)->orderBy('id_desc')->fetch();
            if(!empty($demand))
            {
                $this->dao->update(TABLE_OPINION)->set('status')->eq('subdivided')->where('id')->eq($opinion->id)->exec();
                $processTotal++;
            }
        }
        echo '处理了' . $processTotal . '个需求意向。';
    }

    /**
     * 处理二线管理中的数据修正和数据获取历史数据审核节点
     *
     * @author wangjiurong
     * @param int $infoID
     */
    function repairHistoryInfoReviewData($infoID = 0, $num = 20){
        $objectType = 'info';
        $sql = "SELECT zm.id, zm.version, zm.status, zm.reviewStage, count(zro.id) AS nodeCount 
                from zt_info  zm
                LEFT JOIN zt_reviewnode zro on zm.id = zro.objectID and zro.objectType = '{$objectType}' and zm.version = zro.version
                where 1";
        if($infoID){
            $sql .= " AND zm.id='{$infoID}'";
        }
        $sql .= " GROUP BY zro.objectID HAVING nodeCount =7  LIMIT {$num}";
        $data = $this->dao->query($sql)->fetchAll();
        echo '<pre>';
        print_r($data);
        echo '</pre>';

        if(!empty($data)){
            $stage = 2;
            foreach ($data as $val){
                //处理历史数据
                if($val->nodeCount == 7){
                    //更新历史节点
                    $this->dao->update(TABLE_REVIEWNODE)->set('stage = stage+1')
                        ->where('objectID')->eq($val->id)
                        ->andWhere('objectType')->eq($objectType)
                        ->andWhere('version')->eq($val->version)
                        ->andWhere('stage')->gt(1)
                        ->exec();
                    //新增组长审核节点(并忽略)
                    $reviewNodeInfo = new stdClass();
                    $reviewNodeInfo->status   = 'ignore';
                    $reviewNodeInfo->objectID = $val->id;
                    $reviewNodeInfo->objectType = $objectType;
                    $reviewNodeInfo->stage    = $stage;
                    $reviewNodeInfo->version  = $val->version;
                    $reviewNodeInfo->createdBy   = $this->app->user->account;
                    $reviewNodeInfo->createdDate = helper::today();
                    $this->dao->insert(TABLE_REVIEWNODE)->data($reviewNodeInfo)->autoCheck()->exec();
                    if($val->status == 'cmconfirmed'){ //忽略掉组长审核
                        $this->dao->update(TABLE_INFO)->set('reviewStage = reviewStage+1')->set('status')->eq('groupsuccess')
                            ->where('id')->eq($val->id)
                            ->andWhere('status')->eq('cmconfirmed')
                            ->exec();
                    }
                }
            }
        }

        //处理历史日志
        $initDateTime = '2022-02-21 00:00:00';
        $sql1 = "SELECT * from zt_consumed 
                    where 1 
                    AND objectType = '{$objectType}'
                    AND `before` = 'cmconfirmed'
                    AND (`after` in ('managersuccess', 'posuccess')
                    OR (`after` = 'reject'  AND createdDate < '{$initDateTime}')
                    ) ";
        if($infoID){
            $sql1 = "SELECT * from zt_consumed 
                    where 1 
                    AND objectType = '{$objectType}'
                    AND `before` = 'cmconfirmed'
                    AND `after` in ('managersuccess', 'posuccess', 'reject')
                    AND objectID = '{$infoID}'";
        }
        $data1 = $this->dao->query($sql1)->fetchAll();
        echo '<pre>';
        print_r($data1);
        echo '</pre>';

        if(!empty($data1)){
            foreach ($data1 as $val){
                $id = $val->id;
                $objectID = $val->objectID;

                //更新历史节点
                $this->dao->update(TABLE_CONSUMED)->set('before')->eq('groupsuccess')
                    ->where('id')->eq($val->id)
                    ->exec();
                //查询前一个节点
                $consumedInfo = $this->dao->select('id')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('`before`')->eq('wait')
                    ->andWhere('`after`')->eq('cmconfirmed')
                    ->andWhere('id')->lt($id)
                    ->orderBy('id_desc')->fetch();
                if($consumedInfo){
                    $this->dao->update(TABLE_CONSUMED)->set('after')->eq('groupsuccess')
                        ->where('id')->eq($consumedInfo->id)
                        ->exec();
                }
            }
        }

        if(empty($data) && empty($data1)){
            echo '处理成功';
            return true;
        }
        return true;
    }

    /**
     * 处理二线管理中的生产变更历史数据审核节点
     * @author wangjiurong
     * @param int $modifyID
     */
    function repairHistoryModifyReviewData($modifyID = 0, $num = 20){
        $objectType = 'modify';
        $sql = "SELECT zm.id, zm.version, zm.status, zm.reviewStage, count(zro.id) AS nodeCount 
                from zt_modify zm
                LEFT JOIN zt_reviewnode zro on zm.id = zro.objectID and zro.objectType = '{$objectType}' and zm.version = zro.version
                where 1";
        if($modifyID){
            $sql .= " AND zm.id='{$modifyID}'";
        }
        $sql .= " GROUP BY zro.objectID HAVING nodeCount =7  LIMIT {$num}";
        $data = $this->dao->query($sql)->fetchAll();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if(!empty($data)){
            $stage = 2;
            foreach ($data as $val){
                //处理历史数据
                if($val->nodeCount == 7){
                    //更新历史节点
                    $this->dao->update(TABLE_REVIEWNODE)->set('stage = stage+1')
                        ->where('objectID')->eq($val->id)
                        ->andWhere('objectType')->eq($objectType)
                        ->andWhere('version')->eq($val->version)
                        ->andWhere('stage')->gt(1)
                        ->exec();
                    //新增组长审核节点(并忽略)
                    $reviewNodeInfo = new stdClass();
                    $reviewNodeInfo->status   = 'ignore';
                    $reviewNodeInfo->objectID = $val->id;
                    $reviewNodeInfo->objectType = $objectType;
                    $reviewNodeInfo->stage    = $stage;
                    $reviewNodeInfo->version  = $val->version;
                    $reviewNodeInfo->createdBy   = $this->app->user->account;
                    $reviewNodeInfo->createdDate = helper::today();
                    $this->dao->insert(TABLE_REVIEWNODE)->data($reviewNodeInfo)->autoCheck()->exec();
                    if($val->status == 'cmconfirmed'){ //忽略掉组长审核
                        $this->dao->update(TABLE_MODIFY)->set('reviewStage = reviewStage+1')->set('status')->eq('groupsuccess')
                            ->where('id')->eq($val->id)
                            ->andWhere('status')->eq('cmconfirmed')
                            ->exec();
                    }
                }
            }
        }

        //处理历史日志
        $initDateTime = '2022-02-21 00:00:00';
        $sql1 = "SELECT * from zt_consumed 
                    where 1 
                    AND objectType = '{$objectType}'
                    AND `before` = 'cmconfirmed'
                    AND (`after` in ('managersuccess', 'posuccess')
                    OR (`after` = 'reject'  AND createdDate < '{$initDateTime}')
                    ) ";
        if($modifyID){
            $sql1 = "SELECT * from zt_consumed 
                    where 1 
                    AND objectType = '{$objectType}'
                    AND `before` = 'cmconfirmed'
                    AND `after` in ('managersuccess', 'posuccess', 'reject')
                    AND objectID = '{$modifyID}'";
        }
        $data1 = $this->dao->query($sql1)->fetchAll();
        echo '<pre>';
        print_r($data1);
        echo '</pre>';

        if(!empty($data1)){
            foreach ($data1 as $val){
                $id = $val->id;
                $objectID = $val->objectID;

                //更新历史节点
                $this->dao->update(TABLE_CONSUMED)->set('before')->eq('groupsuccess')
                    ->where('id')->eq($val->id)
                    ->exec();
                //查询前一个节点
                $consumedInfo = $this->dao->select('id')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('`before`')->eq('wait')
                    ->andWhere('`after`')->eq('cmconfirmed')
                    ->andWhere('id')->lt($id)
                    ->orderBy('id_desc')->fetch();
                if($consumedInfo){
                    $this->dao->update(TABLE_CONSUMED)->set('after')->eq('groupsuccess')
                        ->where('id')->eq($consumedInfo->id)
                        ->exec();
                }
            }
        }

        if(empty($data) && empty($data1)){
            echo '处理成功';
            return true;
        }
        return true;
    }

    function updateHistoryModifyAndInfo(){
        //历史数据修正，数据获取
        $sql = "SELECT zi.id, zi.`status` as info_status, zrn.id as node  
from zt_info zi
LEFT JOIN  zt_reviewnode zrn on zrn.objectID = zi.id and zrn.objectType = 'info' AND zi.version = zrn.version
where 1 
and zi.status = 'cmconfirmed'
AND zrn.stage = 2 
and zrn.status = 'ignore'";
        $data = $this->dao->query($sql)->fetchAll();
        echo '数据信息';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if(!empty($data)){
            foreach ($data as $val){
                $id = $val->id;
                $node = $val->node;

                $sql1 = "SELECT zi.id, zi.`status` as info_status, zrn.id as node  
from zt_info zi
LEFT JOIN  zt_reviewnode zrn on zrn.objectID = zi.id and zrn.objectType = 'info' AND zi.version = zrn.version
where 1 
and zi.id = '{$id}'
AND zrn.stage != 2 
and zrn.status = 'pending'";
                $tempData = $this->dao->query($sql1)->fetch();
                if(!empty($tempData)){
                    $tempNode = $tempData->node;
                    //修改原来的审核节点
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')
                        ->where('id')->eq($tempNode)
                        ->exec();

                    //修改原来的审人节点
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')
                        ->where('node')->eq($tempNode)
                        ->exec();
                }

                //修改组长审核节点
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')
                    ->where('id')->eq($node)
                    ->exec();
                //修改组长审核节点的审核人
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')
                    ->where('node')->eq($node)
                    ->exec();
            }
        }


        //修改数据变更
        $sql = "SELECT zm.id, zm.`status` as modify_status, zrn.id as node
from zt_modify zm
LEFT JOIN  zt_reviewnode zrn on zrn.objectID = zm.id and zrn.objectType = 'modify' AND zm.version = zrn.version
where 1 
and zm.status = 'cmconfirmed'
AND zrn.stage = 2 
and zrn.status = 'ignore'";
        $data = $this->dao->query($sql)->fetchAll();
        echo '生产变更';
        echo '<pre>';
        print_r($data);
        echo '</pre>';

        if(!empty($data)){
            foreach ($data as $val){
                $id   = $val->id;
                $node = $val->node;

                $sql1 = "SELECT zm.id, zm.`status` as modify_status, zrn.id as node  
from zt_modify zm
LEFT JOIN  zt_reviewnode zrn on zrn.objectID = zm.id and zrn.objectType = 'modify' AND zm.version = zrn.version
where 1 
and zm.id = '{$id}'
AND zrn.stage != 2 
and zrn.status = 'pending'";
                $tempData = $this->dao->query($sql1)->fetch();
                if(!empty($tempData)) {
                    $tempNode = $tempData->node;
                    //修改原来的审核节点
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')
                        ->where('id')->eq($tempNode)
                        ->exec();

                    //修改原来的审人节点
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')
                        ->where('node')->eq($tempNode)
                        ->exec();
                }

                //修改组长审核节点
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')
                    ->where('id')->eq($node)
                    ->exec();
                //修改组长审核节点的审核人
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')
                    ->where('node')->eq($node)
                    ->exec();

            }
        }
        echo '处理成功';
    }

    function repairHistoryModifyAadInfoReviewStatus(){
        $sql = "SELECT zm.id, zm.`code`,  zm.`status`, zm.reviewStage,
                zrn.id as node, zrn.stage, zrn.`status` as review_status
                from zt_info zm
                LEFT JOIN  zt_reviewnode zrn on zrn.objectID = zm.id and zrn.objectType = 'info' AND zm.version = zrn.version
                where 1 
                and zm.`status` != 'deleted'
                AND zrn.`status` = 'pending'
                AND zm.reviewStage < zrn.stage -1";
        $data = $this->dao->query($sql)->fetchAll();

        echo '数据获取,数据修正';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if(!empty($data)){
            $this->app->loadLang('info');
            foreach ($data as $val){
                $id = $val->id;
                $stage = $val->stage;
                $newReviewStage = $stage - 1;
                $newStatus = $this->lang->info->reviewBeforeStatusList[$newReviewStage];
                //修改审核节点状态和审核流程步骤
                $this->dao->update(TABLE_INFO)->set('status')->eq($newStatus)->set('reviewStage')->eq($newReviewStage)
                    ->where('id')->eq($id)
                    ->exec();
            }
        }

        //处理生产变更
        $sql = "SELECT zm.id, zm.`code`, zm.`status`, zm.reviewStage,
                    zrn.id as node, zrn.stage, zrn.`status` as revie_status
                    from zt_modify zm
                    LEFT JOIN  zt_reviewnode zrn on zrn.objectID = zm.id and zrn.objectType = 'modify' AND zm.version = zrn.version
                    where 1 
                    and zm.`status` != 'deleted'
                    AND zrn.`status` = 'pending'
                    AND zm.reviewStage < zrn.stage -1";
        $data = $this->dao->query($sql)->fetchAll();

        if(!empty($data)){
            $this->app->loadLang('modify');
            foreach ($data as $val){
                $id = $val->id;
                $stage = $val->stage;
                $newReviewStage = $stage - 1;
                $newStatus = $this->lang->modify->reviewBeforeStatusList[$newReviewStage];
                //修改审核节点状态和审核流程步骤
                $this->dao->update(TABLE_MODIFY)->set('status')->eq($newStatus)->set('reviewStage')->eq($newReviewStage)
                    ->where('id')->eq($id)
                    ->exec();

            }
        }
        echo '处理成功';
    }

    /**
     *立项报告发送邮件
     *
     * @param int $projectPlanId
     */
    function sendProjectPlanMails($projectPlanId = 0){
        $statusArray = ['pass', 'projected'];
        $minId = 114;
        $maxId = 189;
        $data = $this->dao->select('id')->from(TABLE_PROJECTPLAN)
            ->where('status')->in($statusArray)
            ->beginIF($projectPlanId != 0 )
            ->andWhere('id')->eq($projectPlanId)
            ->fi()
            ->beginIF($projectPlanId == 0 )
            ->andWhere('id')->ge($minId)
            ->andWhere('id')->le($maxId)
            ->fi()
            ->fetchAll();
        if(!empty($data)){
            $this->loadModel('projectPlan');
            foreach ($data as $val){
                $planID = $val->id;
                $actionID = 0;
                $isReissueMail = true;
                $this->projectPlan->oldSendmail($planID, $actionID, $isReissueMail);

            }
        }
        echo '补发邮件完成';
    }

    /**
     * 处理审核节点错误数据
     */
    function updateModifyReviewStatus(){
        //info 21
        $this->dao->update(TABLE_INFO)->set('status')->eq('reject')
            ->where('id')->eq('21')
            ->exec();

        //修改328
        $modifyId = 328;
        $this->dao->update(TABLE_MODIFY)->set('status')->eq('gmsuccess')->set('reviewStage')->eq('7')
            ->where('id')->eq($modifyId)
            ->exec();

        //修改324
        $modifyId = 324;
        $this->dao->update(TABLE_MODIFY)->set('status')->eq('productsuccess')->set('reviewStage')->eq('8')
            ->where('id')->eq($modifyId)
            ->exec();
        $this->dao->update(TABLE_CONSUMED)->set('`after`')->eq('productsuccess')
            ->where('objectType')->eq('modify')
            ->andWhere('objectID')->eq($modifyId)
            ->andWhere('`before`')->eq('leadersuccess')
            ->exec();

        //修改7
        $modifyId = 7;
        $this->dao->update(TABLE_MODIFY)->set('status')->eq('productsuccess')->set('reviewStage')->eq('8')
            ->where('id')->eq($modifyId)
            ->exec();
        $this->dao->update(TABLE_CONSUMED)->set('`after`')->eq('productsuccess')
            ->where('objectType')->eq('modify')
            ->andWhere('objectID')->eq($modifyId)
            ->andWhere('`before`')->eq('leadersuccess')
            ->exec();

        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
            ->where('id')->eq('3733')
            ->exec();

        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->set('node')->eq('3983')
            ->where('id')->eq('3735')
            ->exec();

        echo '处理成功';
    }
    //处理processimprove表新增字段createdDept
    public function processimproveCreatedDept()
    {
        $processimproves = $this->dao->select('id,createdDept,createdBy')->from(TABLE_PROCESSIMPROVE)->fetchAll();
        $j=0;
        foreach ($processimproves as $processimprove) {

            if($processimprove->createdDept == '-1'){
                $j++;
                $createdDept = $this->dao->select('dept')->from(TABLE_USER)
                    ->where('account')->eq($processimprove->createdBy)
                    ->fetch('dept');
                // echo $createdDept.'-------';
                $this->dao->update(TABLE_PROCESSIMPROVE)
                    ->set('createdDept')->eq($createdDept)
                    ->where('id')->eq($processimprove->id)
                    ->exec();
            }
        }
        echo '处理了:' . $j . '个';
    }

    // 2022年2月21日 重新计算所有任务的层级路径。
    public function processTaskPath($begin = 0, $end = 200)
    {
        $tasks = $this->dao->query("SELECT id,parent,path FROM `zt_task` wHeRe deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            if($task->parent == 0)
            {
                $task->path = $task->id;
            }
            else
            {
                $parentTask = $this->dao->query("SELECT path FROM `zt_task` wHeRe deleted = '0' and id = {$task->parent}")->fetch();
                $task->path = $parentTask->path . ',' . $task->id;
            }

            $this->dao->update(TABLE_TASK)->set('path')->eq($task->path)->where('id')->eq($task->id)->exec();
        }
        echo '处理了' . count($tasks) . '个任务。3秒后自动跳转，请等待处理完毕。';
        $location = $this->createLink('history', 'processTaskPath', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    // 2022年2月22日 重新处理项目阶段数据，将原来挂在一级阶段下的最顶层任务处理成阶段，并将该任务挂载新建阶段下。
    public function processTaskStage($begin = 0, $end = 20)
    {
        /*
         获取所有的阶段数据
         获取阶段下顶级的任务
         建立任务同名的子阶段
         更新任务所属于子阶段
         */
        $this->loadModel('execution');
        $this->loadModel('task');

        // 记录第一次查询出来的阶段数据，避免后续新增阶段的干扰。
        if($begin == 0) unset($_SESSION['processTaskStageIDList']);

        $processTaskStageIDList = $this->session->processTaskStageIDList;
        if(empty($processTaskStageIDList))
        {
            $allStageID = $this->dao->query("SELECT group_concat(id) as ids FROM `zt_project` wHeRe type = 'stage' AND deleted = '0' oRdEr bY `id` asc")->fetch();
            if(empty($allStageID->ids)) '无数据可执行！';
            $this->session->set('processTaskStageIDList', trim($allStageID->ids, ','));
            $processTaskStageIDList = $this->session->processTaskStageIDList;
        }

        $stages = $this->dao->query("SELECT id,project,grade,path,deleted FROM `zt_project` where id in ($processTaskStageIDList) and type = 'stage' AND deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($stages))
        {
            $this->dao->query('update zt_project  set `order` = id * 5');
            echo '所有的阶段都执行完毕了!';
            die();
        }

        foreach($stages as $stage)
        {
            // 获取某阶段下的顶级任务数据。(之前的处理方式有问题，删除的任务不纳入处理才对)
            $tasks = $this->dao->select('id,name,execution,parent,estStarted,deadline,planDuration,finishedDate,realStarted,realDuration')->from(TABLE_TASK)->where('execution')->eq($stage->id)->andWhere('deleted')->eq('0')->orderBy('id_asc')->fetchAll();

            // 获取某阶段所属项目关联的产品数据。
            $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($stage->project)->fetchPairs();

            // 记录哪些任务是有子任务的。
            $taskChildren = array();
            foreach($tasks as $task) $taskChildren[$task->parent] = 1;

            foreach($tasks as $task)
            {
                if($task->parent) continue;

                // 一级阶段下的直属任务，有子任务的情况，也就是超过三层。
                if(isset($taskChildren[$task->id]) and $task->parent == 0)
                {
                    $execution = new stdClass();
                    $execution->project      = $stage->project;
                    $execution->parent       = $stage->id;
                    $execution->name         = $task->name;
                    $execution->type         = 'stage';
                    $execution->resource     = '';
                    $execution->begin        = $task->estStarted;
                    $execution->end          = $task->deadline;
                    $execution->realBegan    = $task->realStarted;
                    $execution->realEnd      = $task->finishedDate;
                    $execution->planDuration = $task->planDuration;
                    $execution->realDuration = $task->realDuration;
                    $execution->grade        = $stage->grade + 1;
                    $execution->openedBy     = $this->app->user->account;
                    $execution->openedDate   = helper::today();
                    $execution->status       = 'wait';
                    $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
                    $executionID = $this->dao->lastInsertID();

                    // 为执行关联项目的产品。
                    if(!empty($products))
                    {
                        $_POST['products'] = $products;
                        $this->execution->updateProducts($executionID);
                        unset($_POST['products']);
                    }

                    // 更新阶段path字段。
                    $stage->path = trim($stage->path, ',');
                    $path = ',' . $stage->path . ',' . $executionID . ',';
                    $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('deleted')->eq($stage->deleted)->where('id')->eq($executionID)->exec();

                    // 更新一级直属任务的所属阶段，并删除任务。
                    $this->dao->update(TABLE_TASK)->set('execution')->eq($executionID)->set('deleted')->eq('1')->where('id')->eq($task->id)->exec();

                    // 更新任务所有底层的子任务所属阶段。
                    $this->updateTaskChildrenExecution($task->id, $executionID, $level = 1);

                    // 更新操作日志。
                    $this->changeActionLog('task', $task->id, $executionID);

                    // 更新任务工时记录。
                    $this->changeEffortLog('task', $task->id, $executionID);

                    // 更新任务直属阶段的[实际开始、实际完成、实际工期、工期偏差]字段。
                    $this->task->computeConsumed($task->id);
                }
                elseif($task->parent == 0)
                {
                    // 判断无子任务的直属一级任务是否有工时消耗。
                    $taskEffort = $this->dao->select('id')->from(TABLE_EFFORT)->where('objectType')->eq('task')->andWhere('objectID')->eq($task->id)->andWhere('deleted')->eq('0')->fetch();
                    if(empty($taskEffort))
                    {
                        $execution = new stdClass();
                        $execution->project      = $stage->project;
                        $execution->parent       = $stage->id;
                        $execution->name         = $task->name;
                        $execution->type         = 'stage';
                        $execution->resource     = '';
                        $execution->begin        = $task->estStarted;
                        $execution->end          = $task->deadline;
                        $execution->realBegan    = $task->realStarted;
                        $execution->realEnd      = $task->finishedDate;
                        $execution->planDuration = $task->planDuration;
                        $execution->realDuration = $task->realDuration;
                        $execution->grade        = $stage->grade + 1;
                        $execution->openedBy     = $this->app->user->account;
                        $execution->openedDate   = helper::today();
                        $execution->status       = 'wait';
                        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
                        $executionID = $this->dao->lastInsertID();

                        // 为执行关联项目的产品。
                        if(!empty($products))
                        {
                            $_POST['products'] = $products;
                            $this->execution->updateProducts($executionID);
                            unset($_POST['products']);
                        }

                        // 更新阶段path字段。
                        $stage->path = trim($stage->path, ',');
                        $path = ',' . $stage->path . ',' . $executionID . ',';
                        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('deleted')->eq($stage->deleted)->where('id')->eq($executionID)->exec();

                        // 更新一级直属任务的所属阶段。
                        $this->dao->update(TABLE_TASK)->set('execution')->eq($executionID)->where('id')->eq($task->id)->exec();

                        // 更新操作日志。
                        $this->changeActionLog('task', $task->id, $executionID);

                        // 更新任务工时记录。
                        $this->changeEffortLog('task', $task->id, $executionID);

                        // 更新任务直属阶段的[实际开始、实际完成、实际工期、工期偏差]字段。
                        $this->task->computeConsumed($task->id);
                    }
                    else
                    {
                        $execution = new stdClass();
                        $execution->project      = $stage->project;
                        $execution->parent       = $stage->id;
                        $execution->name         = $task->name;
                        $execution->type         = 'stage';
                        $execution->resource     = '';
                        $execution->begin        = $task->estStarted;
                        $execution->end          = $task->deadline;
                        $execution->realBegan    = $task->realStarted;
                        $execution->realEnd      = $task->finishedDate;
                        $execution->planDuration = $task->planDuration;
                        $execution->realDuration = $task->realDuration;
                        $execution->grade        = $stage->grade + 1;
                        $execution->openedBy     = $this->app->user->account;
                        $execution->openedDate   = helper::today();
                        $execution->status       = 'wait';
                        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
                        $executionID = $this->dao->lastInsertID();

                        // 为执行关联项目的产品。
                        if(!empty($products))
                        {
                            $_POST['products'] = $products;
                            $this->execution->updateProducts($executionID);
                            unset($_POST['products']);
                        }

                        // 更新阶段path字段。
                        $stage->path = trim($stage->path, ',');
                        $path = ',' . $stage->path . ',' . $executionID . ',';
                        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('deleted')->eq($stage->deleted)->where('id')->eq($executionID)->exec();

                        // 更新一级直属任务的所属阶段。
                        $this->dao->update(TABLE_TASK)->set('execution')->eq($executionID)->where('id')->eq($task->id)->exec();

                        // 更新操作日志。
                        $this->changeActionLog('task', $task->id, $executionID);

                        // 更新任务工时记录。
                        $this->changeEffortLog('task', $task->id, $executionID);

                        // 更新任务直属阶段的[实际开始、实际完成、实际工期、工期偏差]字段。
                        $this->task->computeConsumed($task->id);
                    }
                }
            }
        }

        echo '本次处理了' . count($stages) . '个阶段。3秒后自动刷新页面，直到提示完全处理完为止，请不要关闭页面。';
        $location = $this->createLink('history', 'processTaskStage', array('begin' => $begin + 20));
        header("refresh:3;url=$location");
    }

    // 这是个被调用的方法，不要执行。
    public function updateTaskChildrenExecution($taskID, $executionID = 0, $level = 0)
    {
        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('parent')->in($taskID)->fetchAll('id');
        if(!empty($tasks))
        {
            foreach($tasks as $task)
            {
                if($level == 1)
                {
                    $this->dao->update(TABLE_TASK)
                        ->set('execution')->eq($executionID)
                        ->set('parent')->eq('0')
                        ->set('grade')->eq($level)
                        ->set('path')->eq($task->id)
                        ->where('id')->eq($task->id)
                        ->exec();
                }
                else
                {
                    $parentPath = $this->dao->select('path')->from(TABLE_TASK)->where('id')->eq($task->parent)->fetch('path');
                    $this->dao->update(TABLE_TASK)
                        ->set('execution')->eq($executionID)
                        ->set('grade')->eq($level)
                        ->set('path')->eq($parentPath . ',' . $task->id)
                        ->where('id')->eq($task->id)
                        ->exec();
                }

                // 更新操作日志。
                $this->changeActionLog('task', $task->id, $executionID);

                // 更新任务工时记录。
                $this->changeEffortLog('task', $task->id, $executionID);
            }
            $level ++;
            $this->updateTaskChildrenExecution(array_keys($tasks), $executionID, $level);
        }
    }

    // 2022年2月23日 重新处理所有任务的层级字段。
    public function processTaskGrade($begin = 0, $end = 200)
    {
        if($begin == 0)
        {
            $tasks = $this->dao->select('id')->from(TABLE_TASK)->where('grade')->eq(0)->orderBy('id_asc')->fetchAll('id');
            foreach($tasks as $task)
            {
                $this->dao->update(TABLE_TASK)->set('grade')->eq(1)->where('id')->eq($task->id)->exec();
            }
        }

        $gradeTasks = $this->dao->query("SELECT id,parent FROM `zt_task` wHeRe deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($gradeTasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($gradeTasks as $task)
        {
            if($task->parent)
            {
                $parentGrade = $this->dao->select('grade')->from(TABLE_TASK)->where('id')->eq($task->parent)->fetch('grade');
                $task->grade = $parentGrade + 1;
                $this->dao->update(TABLE_TASK)->set('grade')->eq($task->grade)->where('id')->eq($task->id)->exec();
            }
            else
            {
                $task->grade = 1;
                $this->dao->update(TABLE_TASK)->set('grade')->eq($task->grade)->where('id')->eq($task->id)->exec();
            }
        }

        echo '处理了' . count($gradeTasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskGrade', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    // 2022年3月4日 将已删除的阶段下面的任务修改为已删除。
    public function processExecutionTask($begin = 0, $end = 200)
    {
        //$stages = $this->dao->select('id')->from(TABLE_EXECUTION)->where('`type`')->eq('stage')->andWhere('deleted')->eq('1')->orderBy('id_asc')->fetchAll();
        $stages = $this->dao->query("SELECT id FROM `zt_project` wHeRe `type` = 'stage' and deleted = '1' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($stages))
        {
            echo '所有阶段处理完毕，无数据可执行！';
            die();
        }

        foreach($stages as $stage)
        {
            $this->dao->update(TABLE_TASK)->set('deleted')->eq('1')->where('execution')->eq($stage->id)->exec();
        }
        echo '处理了' . count($stages) . '个阶段。';
        $location = $this->createLink('history', 'processExecutionTask', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    // 2022年3月15日 重新计算所有阶段的层级路径。
    public function processStagePath($begin = 0, $end = 200)
    {
        //$stages = $this->dao->select('id,project,parent,path')->from(TABLE_EXECUTION)->where('`type`')->eq('stage')->orderBy('id_asc')->fetchAll('id');
        $stages = $this->dao->query("SELECT id,project,parent,path FROM `zt_project` wHeRe `type` = 'stage' and deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($stages))
        {
            echo '所有阶段处理完毕，无数据可执行！';
            die();
        }
        foreach($stages as $stage)
        {
            if($stage->parent == 0)
            {
                $stage->path = ',' . $stage->project . ',' . $stage->id . ',';
            }
            else
            {
                $parentStage = $this->dao->query("SELECT path FROM `zt_project` wHeRe id = $stage->parent")->fetch();
                $stage->path = $parentStage->path . $stage->id . ',';
            }

            $this->dao->update(TABLE_EXECUTION)->set('path')->eq($stage->path)->where('id')->eq($stage->id)->exec();
        }

        echo '处理了' . count($stages) . '个阶段。3秒后自动跳转，请等待处理完毕。';
        $location = $this->createLink('history', 'processStagePath', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    // 被调用的方法：更新之前记录在一级阶段的动态为新的阶段上。
    public function changeActionLog($objectType, $objectID, $newID)
    {
        $this->dao->update(TABLE_ACTION)->set('execution')->eq($newID)->where('objectType')->eq($objectType)->andWhere('objectID')->eq($objectID)->exec();
    }

    // 被调用的方法：更新工时消耗表。
    public function changeEffortLog($objectType, $objectID, $newID)
    {
        $this->dao->update(TABLE_EFFORT)->set('execution')->eq($newID)->where('objectType')->eq($objectType)->andWhere('objectID')->eq($objectID)->exec();
    }

    // 更新所有的用户默认增加项目工作量和延期任务区块。
    public function updateUserBlock()
    {
        $users = $this->dao->select('id,account')->from(TABLE_USER)->where('deleted')->eq('0')->orderBy('id_asc')->fetchAll();
        foreach($users as $user)
        {
            // 对项目仪表盘进行恢复默认。
            $this->dao->delete()->from(TABLE_BLOCK)->where('module')->eq('project')->andWhere('account')->eq($user->account)->exec();
            $this->dao->delete()->from(TABLE_CONFIG)->where('module')->eq('project')->andWhere('owner')->eq($user->account)->andWhere('`key`')->eq('blockInited')->exec();

            //$block          = new stdClass();
            //$block->account = $user->account;
            //$block->module  = 'project';
            //$block->type    = 'waterfall';
            //$block->title   = '项目工作量';
            //$block->source  = 'project';
            //$block->block   = 'projectworks';
            //$block->params  = '';
            //$block->order   = 8;
            //$block->grid    = 8;
            //$block->height  = 0;
            //$block->hidden  = 0;

            //$this->dao->insert(TABLE_BLOCK)->data($block)->exec();

            //$block          = new stdClass();
            //$block->account = $user->account;
            //$block->module  = 'project';
            //$block->type    = 'waterfall';
            //$block->title   = '延期任务';
            //$block->source  = 'project';
            //$block->block   = 'delaytask';
            //$block->params  = '';
            //$block->order   = 9;
            //$block->grid    = 8;
            //$block->height  = 0;
            //$block->hidden  = 0;

            //$this->dao->insert(TABLE_BLOCK)->data($block)->exec();
        }
        echo '处理了' . count($users) . '个用户。';
    }

    // 处理阶段版本数据
    public function processStageVersion($begin = 0, $end = 20)
    {
        if($begin == 0)
        {
            unset($_SESSION['processStageIDList']);

            //将现有的阶段version从0改为1
            $rowCount = $this->dao->update(TABLE_EXECUTION)->set('version')->eq('1')->where('`type`')->eq('stage')->andWhere('version')->eq('0')->exec();
            echo '共有' . $rowCount . '条阶段更新版本';
        }

        $processStageIDList = $this->session->processStageIDList;
        if(empty($processStageIDList))
        {
            $allStageID = $this->dao->query("SELECT group_concat(id) as ids FROM `zt_project` wHeRe type = 'stage' AND deleted = '0' oRdEr bY `id` asc")->fetch();
            if(empty($allStageID->ids)) '无数据可执行！';
            $this->session->set('processStageIDList', trim($allStageID->ids, ','));
            $processStageIDList = $this->session->processStageIDList;
        }

        $stages = $this->dao->query("SELECT * FROM `zt_project` where id in ($processStageIDList) and type = 'stage' AND deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($stages))
        {
            echo '所有的阶段都执行完毕了!';
            die();
        }

        $i = $j = 0;
        foreach($stages as $stage)
        {
            $spec = $this->dao->select('*')->from(TABLE_EXECUTIONSPEC)->where('execution')->eq($stage->id)->andWhere('version')->eq($stage->version)->fetch();
            if($spec)
            {
                $spec->name         = $stage->name;
                $spec->code         = $stage->code;
                $spec->milestone    = $stage->milestone;
                $spec->begin        = $stage->begin;
                $spec->end          = $stage->end;
                $spec->realBegan    = $stage->realBegan;
                $spec->realEnd      = $stage->realEnd;
                $spec->planDuration = $stage->planDuration;
                $spec->desc         = $stage->desc;
                $this->dao->update(TABLE_EXECUTIONSPEC)->data($spec)->where('execution')->eq($stage->id)->andWhere('version')->eq($stage->version)->exec();
                $i++;
            }
            else
            {
                $spec               = new stdClass();
                $spec->execution    = $stage->id;
                $spec->version      = $stage->version;
                $spec->name         = $stage->name;
                $spec->code         = $stage->code;
                $spec->milestone    = $stage->milestone;
                $spec->begin        = $stage->begin;
                $spec->end          = $stage->end;
                $spec->realBegan    = $stage->realBegan;
                $spec->realEnd      = $stage->realEnd;
                $spec->planDuration = $stage->planDuration;
                $spec->desc         = $stage->desc;
                $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();
                $j++;
            }
        }
        echo '插入' . $j . '条数据;' . '更新' . $i . '条数据;3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processStageVersion', array('begin' => $begin + 20));
        header("refresh:3;url=$location");
    }

    /**
     *修复项目变更的审核节点信息 20220315(此方法以迭代六为准）
     * @author wangjiruong
     *
     * @param int $changeId
     */
    public function repairProjectChangeReview($changeId = 0){
        $objectType = 'change';
        $sql = "select 
                zc.id, zc.project,  zc.`level`, zc.status, zc.reviewStage, zc.version
                ,count(zro.objectID) as reviewnode_count
                from zt_change zc
                left join zt_reviewnode zro on zro.objectType = '{$objectType}' and  zro.objectID = zc.id and zc.version = zro.version 
                where 1 ";
        if($changeId){
            $sql .= "  and zc.id = '{$changeId}'";
        }
        $sql .= " group by zc.id having reviewnode_count = 4";
        $sql .= " limit 0, 20";
        $data = $this->dao->query($sql)->fetchAll();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if(empty($data)){
            echo '修复审核信息成功';
            return true;
        }
        //批量修改
        $changeIds = [];
        //批量新增
        $batchData = [];
        $addNodes = [1, 4, 5, 8];

        //要新增的节点信息
        $createdDate = helper::today();
        foreach ($data as $val){
            $changeId = $val->id;
            $changeIds[] = $changeId;
            foreach ($addNodes as $nodeStage){
                $reviewNodeInfo = new stdClass();
                $reviewNodeInfo->status      = 'ignore';
                $reviewNodeInfo->objectType  = $objectType;
                $reviewNodeInfo->createdBy   = 'admin';
                $reviewNodeInfo->createdDate = $createdDate;
                $reviewNodeInfo->objectID = $changeId;
                $reviewNodeInfo->version = $val->version;
                $reviewNodeInfo->stage = $nodeStage;
                $batchData[] = $reviewNodeInfo;
            }
        }

        //批量修改
        if($changeIds){
            $this->dao->update(TABLE_REVIEWNODE)->set('`stage`')->eq('7')
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->in($changeIds)
                ->andWhere('stage')->eq('4')
                ->exec();
            $this->dao->update(TABLE_REVIEWNODE)->set('`stage`')->eq('6')
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->in($changeIds)
                ->andWhere('stage')->eq('3')
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('`stage`')->eq('3')
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->in($changeIds)
                ->andWhere('stage')->eq('2')
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('`stage`')->eq('2')
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->in($changeIds)
                ->andWhere('stage')->eq('1')
                ->exec();
        }

        //批量新增审核节点
        foreach ($batchData as $insetData){
            $this->dao->insert(TABLE_REVIEWNODE)->data($insetData)->exec();
        }

        foreach ($data as $val){
            $changeId = $val->id;
            $level    = $val->level;
            $status   = $val->status;

            $newStatus      = $val->status;
            $newReviewStage = $val->reviewStage;
            if($level == 1){ //一级变更单
                switch ($status){
                    case 'wait': //原来的已提交是新状态下的"待申请部门负责人审核"
                        $newStatus      = 'cmconfirmed';
                        $newReviewStage = '2';
                        break;

                    case 'managersuccess':
                        $newStatus = 'frameworkmanagersuccess';
                        $newReviewStage = '5';
                        break;

                    case 'leadersuccess':
                        $newStatus = 'leadersuccess';
                        $newReviewStage = '6';
                        break;
                }
            }elseif ($level == 2){//二级变更单
                switch ($status){
                    case 'wait': //原来的已提交是新状态下的"待申请部门负责人审核"
                        $newStatus      = 'cmconfirmed';
                        $newReviewStage = '2';
                        break;

                    case 'managersuccess':
                        $newStatus = 'frameworkmanagersuccess';
                        $newReviewStage = '5';
                        break;
                }

            }elseif($level == 3){//三级变更单
                switch ($status){
                    case 'wait': //原来的已提交是新状态下的"待项目经理审核"
                        $newStatus      = 'qasuccess';
                        $newReviewStage = '1';
                        break;
                }
            }
            //修改变更单信息
            if($newStatus != $val->status || $newReviewStage != $val->reviewStage){
                $this->dao->update(TABLE_CHANGE)->set('status')->eq($newStatus)->set('reviewStage')->eq($newReviewStage)
                    ->where('id')->eq($changeId)
                    ->exec();
            }
        }

        return true;
    }

    /**
     *修复项目变更的审核节点害工作量日志信息 20220315
     * @author wangjiurong
     *
     * @param int $changeId
     */
    public function repairProjectChangeReviewWork($changeId = 0){

        $objectType = 'change';
        $sql = "select 
                zc.id, zc.project, zc.`level`, zc.status, zc.reviewStage, zc.version
                from zt_change zc
                left join zt_reviewnode zro on zro.objectType = '{$objectType}' and  zro.objectID = zc.id and zc.version = zro.version 
                left join zt_consumed zcd on zcd.objectType = '{$objectType}' and  zcd.objectID = zc.id 
                where 1
                and zro.stage = '8'
                and zro.status = 'ignore'
                and zcd.`after` = 'wait'";
        if($changeId){
            $sql .= " and zc.id = '{$changeId}'";
        }
        $sql .= " limit 0, 20";
        $data = $this->dao->query($sql)->fetchAll();

        echo '<pre>';
        print_r($data);
        echo '</pre>';

        if(empty($data)){
            echo '修复审核信息成功';
            return true;
        }

        $changeDataList = [];
        $changeIds  = [];
        foreach ($data as $val){
            $id = $val->id;
            $changeDataList[$id] = $val;
            $changeIds[] = $id;
        }
        $changeWorkList =  $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->in($changeIds)
            ->fetchAll();
        foreach ($changeWorkList as $val){
            $changeId   = $val->objectID;
            $changeInfo = $changeDataList[$changeId];
            $level  = $changeInfo->level;
            $before = $val->before;
            $after  = $val->after;
            $updateParams = [];
            if($level == 1) { //一级变更单
                if($after == 'wait'){
                    $updateParams['after'] = 'cmconfirmed';
                }elseif ($after == 'managersuccess'){
                    $updateParams['after'] = 'frameworkmanagersuccess';
                }
                if($before == 'wait'){
                    $updateParams['before'] = 'cmconfirmed';
                }elseif ($before == 'managersuccess'){
                    $updateParams['before'] = 'frameworkmanagersuccess';
                }

            }elseif ($level == 2){//二级变更单
                if($after == 'wait'){
                    $updateParams['after'] = 'cmconfirmed';
                }elseif ($after == 'managersuccess'){
                    $updateParams['after'] = 'frameworkmanagersuccess';
                }
                if($before == 'wait'){
                    $updateParams['before'] = 'cmconfirmed';
                }elseif ($before == 'managersuccess'){
                    $updateParams['before'] = 'frameworkmanagersuccess';
                }
            }elseif ($level == 3){//三级变更单
                if($after == 'wait'){
                    $updateParams['after'] = 'qasuccess';
                }
                if($before == 'wait'){
                    $updateParams['before'] = 'qasuccess';
                }
            }
            if(!empty($updateParams)){
                $this->dao->update(TABLE_CONSUMED)->data($updateParams)
                    ->where('id')->eq($val->id)
                    ->exec();
            }
        }
        return true;
    }


    /**
     *处理需求池表历史数据，默认都需要系统部验证 填写 验证人员(verifyperson) 和 实验室验证人员(laboratorytest)
     *
     * 规则： 1、 验证人员：流程节点从 待验证 -> 待发布的处理人
     *       2、 测试人员：流程节点从 待测试 -> 待验证的处理人
     */
    public function updateDemand(){

        $demandPairs = $this->dao->select('id')->from(TABLE_DEMAND)->where('status')->ne('deleted')->andwhere('systemverify')->eq(1)->orderBy('id_desc')->fetchAll();
        $type =  'demand';
        $yzcount = 0;
        $cscount = 0;
        foreach ($demandPairs as $value){
            if(empty($value->id)) continue;
            //查询表中的历史操作人员详情
            $consumed = $this->dao->select('account,`before`,after,objectID')->from(TABLE_CONSUMED)
                ->where('objectID')->eq($value->id)
                ->andWhere('objectType')->eq($type)
                ->fetchAll();
            foreach ($consumed as $item) {
                //待验证 testsuccess    -> 待发布 verifysuccess
                if($item->before == 'testsuccess' && $item->after == 'verifysuccess'){
                    $this->dao->update(TABLE_DEMAND)->set('verifyperson')->eq($item->account)->where('id')->eq($value->id)->exec();
                    $yzcount ++;
                    // continue;
                }
            }

            foreach ($consumed as $item2) {
                //待测试 build -> 待验证 testsuccess
                if($item2->before == 'build' && $item2->after == 'testsuccess'){
                    $this->dao->update(TABLE_DEMAND)->set('laboratorytest')->eq($item2->account)->where('id')->eq($value->id)->exec();
                    $cscount ++;
                    // continue;
                }
            }
        }
        echo '填写了需求表' . $yzcount . '条验证人员数据。';
        echo '填写了需求表' . $cscount . '条测试人员数据。';

    }

    /**
     *处理问题池表历史数据，默认都需要系统部验证 填写 验证人员(verifyperson) 和 实验室验证人员(laboratorytest)
     *
     * 规则： 1、 验证人员：流程节点从 待验证 -> 待发布的处理人
     *       2、 测试人员：流程节点从 待测试 -> 待验证的处理人
     */
    public function updateProblem(){

        $problemPairs = $this->dao->select('id')->from(TABLE_PROBLEM)->where('status')->ne('deleted')->andwhere('systemverify')->eq(1)->orderBy('id_desc')->fetchAll();
        $type =  'problem';
        $yzcount = 0;
        $cscount = 0;
        foreach ($problemPairs as $value){
            if(empty($value->id)) continue;
            //查询表中的历史操作人员详情
            $consumed = $this->dao->select('account,`before`,after,objectID')->from(TABLE_CONSUMED)
                ->where('objectID')->eq($value->id)
                ->andWhere('objectType')->eq($type)
                ->fetchAll();
            foreach ($consumed as $item) {
                //待验证 testsuccess    -> 待发布 verifysuccess
                if($item->before == 'testsuccess' && $item->after == 'verifysuccess'){
                    $this->dao->update(TABLE_PROBLEM)->set('verifyperson')->eq($item->account)->where('id')->eq($value->id)->exec();
                    $yzcount ++;
                    // continue;
                }
            }

            foreach ($consumed as $item2) {
                //待测试 build -> 待验证 testsuccess
                if($item2->before == 'build' && $item2->after == 'testsuccess'){
                    $this->dao->update(TABLE_PROBLEM)->set('laboratorytest')->eq($item2->account)->where('id')->eq($value->id)->exec();
                    $cscount ++;
                    //  continue;
                }
            }

        }
        echo '填写了问题表' . $yzcount . '条验证人员数据。';
        echo '填写了问题表' . $cscount . '条测试人员数据。';

    }

    // (历史数据zt_task2处理，用于之后逻辑)将已删除的阶段下面的任务修改为已删除。
    public function processHistoryExecutionTask($begin = 0, $end = 200)
    {
        //$stages = $this->dao->select('id')->from(TABLE_EXECUTION)->where('`type`')->eq('stage')->andWhere('deleted')->eq('1')->orderBy('id_asc')->fetchAll();
        $stages = $this->dao->query("SELECT id FROM `zt_project2` wHeRe `type` = 'stage' and deleted = '1' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($stages))
        {
            echo '所有阶段处理完毕，无数据可执行！';
            die();
        }

        foreach($stages as $stage)
        {
            $this->dao->update('zt_task2')->set('deleted')->eq('1')->where('execution')->eq($stage->id)->exec();
        }
        echo '处理了' . count($stages) . '个阶段。';
        $location = $this->createLink('history', 'processHistoryExecutionTask', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    // 判断历史任务是否删除，如果删除则删除底层所有子任务。
    public function processTaskDelete($begin = 0, $end = 200)
    {
        $tasks = $this->dao->query("SELECT id,deleted FROM `zt_task2` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            if($task->deleted == 1) $this->deleteChildTask($task->id);
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskDelete', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    /* 被调用的方法，无需执行。*/
    public function deleteChildTask($taskID)
    {
        $tasks = $this->dao->select('id')->from('zt_task2')->where('parent')->in($taskID)->fetchAll();
        if(empty($tasks)) return false;

        $idList = array();
        foreach($tasks as $task)
        {
            $idList[] = $task->id;
            $this->dao->update('zt_task2')->set('deleted')->eq('1')->where('id')->eq($task->id)->exec();
        }
        $this->deleteChildTask($idList);
    }

    /* 处理之前子任务都删除但是没当做删除处理的任务进行恢复。*/
    public function processTaskRecovery($begin = 0, $end = 20)
    {
        /*
         获取所有的阶段数据
         获取阶段下顶级的任务
         建立任务同名的子阶段
         更新任务所属于子阶段
         */
        $this->loadModel('execution');
        $this->loadModel('task');

        // 记录第一次查询出来的阶段数据，避免后续新增阶段的干扰。
        if($begin == 0) unset($_SESSION['processTaskStageIDList']);

        $processTaskStageIDList = $this->session->processTaskStageIDList;
        if(empty($processTaskStageIDList))
        {
            $allStageID = $this->dao->query("SELECT group_concat(id) as ids FROM `zt_project2` wHeRe type = 'stage' AND deleted = '0' oRdEr bY `id` asc")->fetch();
            if(empty($allStageID->ids)) '无数据可执行！';
            $this->session->set('processTaskStageIDList', trim($allStageID->ids, ','));
            $processTaskStageIDList = $this->session->processTaskStageIDList;
        }

        $stages = $this->dao->query("SELECT id,project,grade,path,deleted FROM `zt_project2` where id in ($processTaskStageIDList) and type = 'stage' AND deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($stages))
        {
            unset($_SESSION['processTaskStageIDList']);
            echo '所有的阶段都执行完毕了!';
            die();
        }

        foreach($stages as $stage)
        {
            // 获取某阶段下的顶级任务数据。
            $tasks = $this->dao->select('id,name,execution,parent,estStarted,deadline,planDuration,finishedDate,realStarted,realDuration,deleted')->from('zt_task2')->where('execution')->eq($stage->id)->orderBy('id_asc')->fetchAll();

            // 记录哪些任务是有子任务的,(判断子任务是否都是删除的)。
            $taskChildren = array();

            // 假设父任务有子任务且都删除了。
            $deletedChildren = array();

            foreach($tasks as $task)
            {
                $taskChildren[$task->parent] = 1;
                $deletedChildren[$task->parent] = 0;
            }

            foreach($tasks as $task)
            {
                // 只要子任务有一个没删除就还算有子任务。
                if($task->deleted == '0' and $task->parent) $deletedChildren[$task->parent] = 1;
            }

            foreach($tasks as $task)
            {
                if($task->parent) continue;

                // 一级阶段下的直属任务，有子任务的情况，也就是超过三层。
                // 把之前父任务下子任务都删除但没有算删除的任务进行恢复，所有的子任务删除，应该按照建立同名阶段把任务挂在阶段下逻辑处理。
                if(isset($taskChildren[$task->id]) and $task->parent == 0 and $deletedChildren[$task->id] == 0)
                {
                    // 恢复之前删除任务。
                    $this->dao->update(TABLE_TASK)->set('deleted')->eq('0')->where('id')->eq($task->id)->exec();

                    // 更新任务直属阶段的[实际开始、实际完成、实际工期、工期偏差]字段。
                    $this->task->computeConsumed($task->id);
                }
                elseif($task->parent == 0)
                {
                    // 判断之前的任务是否删除，如果是删除的则把处理后的任务的所属阶段删除。(后续历史数据任务相关工时消耗会有方法处理)
                    if($task->deleted == '1')
                    {
                        $newTask = $this->dao->select('id,execution')->from(TABLE_TASK)->where('id')->eq($task->id)->fetch();
                        $this->dao->update(TABLE_EXECUTION)->set('deleted')->eq('1')->where('id')->eq($newTask->execution)->exec();
                    }
                }
            }
        }

        echo '本次处理了' . count($stages) . '个阶段。3秒后自动刷新页面，直到提示完全处理完为止，请不要关闭页面。';
        $location = $this->createLink('history', 'processTaskRecovery', array('begin' => $begin + 20));
        header("refresh:3;url=$location");
    }

    /* 处理之前父任务有子任务，且任务有工时消耗的，对其进行恢复。*/
    public function processParentTaskRecovery($begin = 0, $end = 20)
    {
        /*
         获取所有的阶段数据
         获取阶段下顶级的任务
         建立任务同名的子阶段
         更新任务所属于子阶段
         */
        $this->loadModel('execution');
        $this->loadModel('task');

        // 记录第一次查询出来的阶段数据，避免后续新增阶段的干扰。
        if($begin == 0) unset($_SESSION['processTaskStageIDList']);

        $processTaskStageIDList = $this->session->processTaskStageIDList;
        if(empty($processTaskStageIDList))
        {
            $allStageID = $this->dao->query("SELECT group_concat(id) as ids FROM `zt_project2` wHeRe type = 'stage' AND deleted = '0' oRdEr bY `id` asc")->fetch();
            if(empty($allStageID->ids)) '无数据可执行！';
            $this->session->set('processTaskStageIDList', trim($allStageID->ids, ','));
            $processTaskStageIDList = $this->session->processTaskStageIDList;
        }

        $stages = $this->dao->query("SELECT id,project,grade,path,deleted FROM `zt_project2` where id in ($processTaskStageIDList) and type = 'stage' AND deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($stages))
        {
            unset($_SESSION['processTaskStageIDList']);
            echo '所有的阶段都执行完毕了!';
            die();
        }

        foreach($stages as $stage)
        {
            // 获取某阶段下的顶级任务数据。
            $tasks = $this->dao->select('id,name,execution,parent,estStarted,deadline,planDuration,finishedDate,realStarted,realDuration,deleted')->from('zt_task2')->where('execution')->eq($stage->id)->orderBy('id_asc')->fetchAll();

            // 记录哪些任务是有子任务的,(判断子任务是否都是删除的)。
            $taskChildren = array();

            // 假设父任务有子任务且都删除了。
            $deletedChildren = array();

            foreach($tasks as $task)
            {
                $taskChildren[$task->parent] = 1;
                $deletedChildren[$task->parent] = 0;
            }

            foreach($tasks as $task)
            {
                // 只要子任务有一个没删除就还算有子任务。
                if($task->deleted == '0' and $task->parent) $deletedChildren[$task->parent] = 1;
            }

            foreach($tasks as $task)
            {
                if($task->parent) continue;

                // 一级阶段下的直属任务，有子任务的情况，也就是超过三层。
                // 父任务存在子任务的情况处理。
                if(isset($taskChildren[$task->id]) and $task->parent == 0 and $deletedChildren[$task->id] == 1)
                {
                    // 判断父任务是否有工时消耗记录，对其恢复。
                    $effort = $this->dao->select('id')->from('zt_effort2')->where('objectID')->eq($task->id)->andWhere('deleted')->eq('0')->fetch();
                    if(!empty($effort))
                    {
                        $this->dao->update(TABLE_TASK)->set('deleted')->eq('0')->where('id')->eq($task->id)->exec();

                        // 更新任务直属阶段的[实际开始、实际完成、实际工期、工期偏差]字段。
                        $this->task->computeConsumed($task->id);
                    }
                }
            }
        }

        echo '本次处理了' . count($stages) . '个阶段。3秒后自动刷新页面，直到提示完全处理完为止，请不要关闭页面。';
        $location = $this->createLink('history', 'processParentTaskRecovery', array('begin' => $begin + 20));
        header("refresh:3;url=$location");
    }

    /**
     * 处理问题和需求单的相关配合人员，相关配合人员都插入一条新的记录方便统计工作量 2022.4.6
     * @author tangfei
     */
    public function dealConsumedRelevantUser()
    {
        $demandProblems = $this->dao->query("select * from zt_consumed where (objectType = 'demand' or objectType = 'problem') AND 'deleted'= 0 AND details != ''")->fetchAll();

        $count = 0;
        foreach ($demandProblems as $demandProblem)
        {
            $data = new stdclass();
            $data->objectType  = $demandProblem->objectType;
            $data->objectID    = $demandProblem->objectID;
            $data->consumed    = 0;
            $data->account     = '';
            $data->before      = $demandProblem->before;
            $data->after       = $demandProblem->after;
            $data->createdBy   = $demandProblem->createdBy;
            $data->createdDate = $demandProblem->createdDate;
            $data->parentId    = $demandProblem->id;

            $demandProblem->details = $this->loadModel('consumed')->getConsumedDetailsArray($demandProblem->details);
            if(!empty($demandProblem->details))
            {
                foreach ($demandProblem->details as $detail)
                {
                    $consumed = $this->dao->select('*')->from(TABLE_CONSUMED)
                        ->where('account')->eq($detail->account)
                        ->andwhere('parentID')->eq($demandProblem->id)
                        ->fetch();

                    if($consumed == '')
                    {
                       $data->account  = $detail->account;
                       $data->consumed = $detail->workload;

                       $this->dao->insert(TABLE_CONSUMED)->data($data)->exec();
                       $count++;
                    }
                }
            }
        }
        echo '执行完成,新增了'.$count.'条配合人员数据';
    }

    // 重新计算任务所属阶段的进度和状态。
    public function processTaskProgress($begin = 0, $end = 150)
    {
        $tasks = $this->dao->query("SELECT id FROM `zt_task` wHeRe deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        $this->loadModel('task');
        foreach($tasks as $task) $this->task->computeConsumed($task->id);

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskProgress', array('begin' => $begin + 150));
        header("refresh:1;url=$location");
    }

    // 重新计算任务工时消耗表记录是否删除。
    public function processTaskConsumed($begin = 0, $end = 200)
    {
        // 从历史记录zt_effort2中读取数据。
        $tasks = $this->dao->query("SELECT id,deleted FROM `zt_effort2` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务消耗记录处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            $this->dao->update(TABLE_EFFORT)->set('deleted')->eq($task->deleted)->where('id')->eq($task->id)->exec();
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskConsumed', array('begin' => $begin + 200));
        header("refresh:3;url=$location");
    }

    // 判断任务是否删除，更新工时消耗表。
    public function changeEffortStatus($begin = 0, $end = 100)
    {
        $tasks = $this->dao->query("SELECT id,deleted FROM `zt_task` wHeRe deleted = '1' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有删除任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            $this->dao->update(TABLE_EFFORT)->set('deleted')->eq($task->deleted)->where('objectType')->eq('task')->andWhere('objectID')->eq($task->id)->exec();
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'changeEffortStatus', array('begin' => $begin + 100));
        header("refresh:3;url=$location");
    }

    /* 获取父任务删除但是子任务没有删除的数据。*/
    public function getAbnormalProject($begin = 0, $end = 100)
    {
        $tasks = $this->dao->query("SELECT id,deleted FROM `zt_task3` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            // 如果我(当前任务)被删除了，那么要查我下面所有底层子任务是否存在没删除的，如果存在则记录下来。
            if($task->deleted == 1)
            {
                $abnormalList = $this->getChildTask($task->id);
                foreach($abnormalList as $task => $project)
                {
                    // 判断任务是否已经记录过了，记录过则跳过。
                    $hasTask = $this->dao->query("SELECT id FROM `zt_abnormal` where task = {$task}")->fetch();
                    if(!empty($hasTask)) continue;

                    // 查询任务关联的工时消耗。
                    $efforts = $this->dao->query("SELECT id FROM `zt_effort3` where objectType = 'task' and objectID = {$task} and project = {$project} and deleted = '0'")->fetchAll();

                    // 如果没有工时则记录空的记录。
                    if(empty($efforts))
                    {
                        $abnormal = new stdClass();
                        $abnormal->task    = $task;
                        $abnormal->project = $project;
                        $abnormal->effort  = 0;
                        $this->dao->insert('zt_abnormal')->data($abnormal)->exec();
                    }
                    else
                    {
                        // 有工时的情况。
                        foreach($efforts as $effort)
                        {
                            $abnormal = new stdClass();
                            $abnormal->task    = $task;
                            $abnormal->project = $project;
                            $abnormal->effort  = $effort->id;
                            $this->dao->insert('zt_abnormal')->data($abnormal)->exec();
                        }
                    }
                }
            }
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'getAbnormalProject', array('begin' => $begin + 100));
        header("refresh:3;url=$location");
    }

    /* 被调用的方法，无需执行。*/
    public function getChildTask($taskID, $abnormalList = array())
    {
        $tasks = $this->dao->select('id,project,deleted')->from('zt_task3')->where('parent')->in($taskID)->fetchAll();
        if(empty($tasks)) return $abnormalList;

        $idList = array();
        foreach($tasks as $task)
        {
            $idList[] = $task->id;
            if($task->deleted == 0) $abnormalList[$task->id] = $task->project;
        }
        return $this->getChildTask($idList, $abnormalList);
    }

    /* 获取父任务删除但是子任务没有删除的数据。*/
    public function getAbnormalProjectTable($begin = 0, $end = 50)
    {
        $abnormalList = $this->dao->query("SELECT * FROM `zt_abnormal` oRdEr bY project asc,task asc,effort asc lImiT $begin,$end")->fetchAll();
        $path = 'view/viewabnormalprojecttable.html.php';
        if(empty($abnormalList))
        {
            $table = '</table>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);

            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        // 设置写入的文件，首次执行对数据进行清空。
        if(!file_exists('view')) mkdir("view");
        if($begin == 0) fopen($path, 'w');
        if (!file_exists($path)) fopen($path, 'w');

        if($begin == 0)
        {
            $table = '<table>' . PHP_EOL;
            $table .= '<tr>';
            $table .= '<th>项目ID</th>';
            $table .= '<th>项目名称</th>';
            $table .= '<th>任务ID</th>';
            $table .= '<th>任务名称</th>';
            $table .= '<th>工时ID</th>';
            $table .= '<th>工时日期</th>';
            $table .= '<th>工时消耗</th>';
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        foreach($abnormalList as $abnormal)
        {
            $project = $this->dao->select('id,name')->from('zt_project3')->where('id')->eq($abnormal->project)->fetch();
            $task    = $this->dao->select('id,name')->from('zt_task3')->where('id')->eq($abnormal->task)->fetch();
            $effort  = $this->dao->select('*')->from('zt_effort3')->where('id')->eq($abnormal->effort)->fetch();

            if(empty($effort))
            {
                $effort = new stdClass();
                $effort->id       = '-';
                $effort->consumed = '-';
                $effort->date     = '-';
            }

            $table = '<tr>';
            $table .= '<td>' . $project->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $project->name . '</td>' . PHP_EOL;
            $table .= '<td>' . $task->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $task->name . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->date . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->consumed . '</td>' . PHP_EOL;
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        echo '处理了' . count($abnormalList) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'getAbnormalProjectTable', array('begin' => $begin + 50));
        header("refresh:3;url=$location");
    }

    public function viewAbnormalProjectTable()
    {
        $this->display();
    }

    // 对于历史数据判断任务是否删除，更新工时消耗表(这个时候工时消耗还没有删除)。
    public function changeHistoryEffortStatus($begin = 0, $end = 100)
    {
        $tasks = $this->dao->query("SELECT id,deleted FROM `zt_task2` wHeRe deleted = '1' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有删除任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            $this->dao->update('zt_effort2')->set('deleted')->eq($task->deleted)->where('objectType')->eq('task')->andWhere('objectID')->eq($task->id)->exec();
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'changeHistoryEffortStatus', array('begin' => $begin + 100));
        header("refresh:3;url=$location");
    }

    // 获取项目工时在处理前后的数据差异。
    public function getProjectDataDiff($begin = 0, $end = 10)
    {
        // 以历史数据为基准(zt_project2、zt_task2、zt_effort2)，其中。
        $projectList = $this->dao->query("SELECT id,name FROM `zt_project2` where deleted = '0' oRdEr bY id asc lImiT $begin,$end")->fetchAll();
        $path = 'view/viewprojectdatadiff.html.php';
        if(empty($projectList))
        {
            $table = '</table>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);

            echo '所有项目处理完毕，无数据可执行！';
            die();
        }

        // 设置写入的文件，首次执行对数据进行清空。
        if(!file_exists('view')) mkdir("view");
        if($begin == 0) fopen($path, 'w');
        if (!file_exists($path)) fopen($path, 'w');

        if($begin == 0)
        {
            $table = '<table>' . PHP_EOL;
            $table .= '<tr>';
            $table .= '<th>项目ID</th>';
            $table .= '<th>项目名称</th>';
            $table .= '<th>处理后工时</th>';
            $table .= '<th>处理前工时</th>';
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        $maxID = $this->dao->select('max(id) as maxID')->from('zt_effort2')->fetch('maxID');
        foreach($projectList as $project)
        {
            // 查询处理之后的工时。
            $effort = $this->dao->select('cast(sum(consumed) as decimal(11,2)) as consumed')->from('zt_effort')
                ->where('objectType')->eq('task')
                ->andWhere('project')->eq($project->id)
                ->andWhere('deleted')->eq('0')
                ->andWhere('id')->le($maxID)
                ->fetch();

            // 查询历史数据的工时(已删除无效数据)。
            $effort2 = $this->dao->select('cast(sum(consumed) as decimal(11,2)) as consumed')->from('zt_effort2')
                ->where('objectType')->eq('task')
                ->andWhere('project')->eq($project->id)
                ->andWhere('deleted')->eq('0')
                ->fetch();

            $table = '<tr>';
            $table .= '<td>' . $project->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $project->name . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->consumed . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort2->consumed . '</td>' . PHP_EOL;
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        echo '处理了' . count($projectList) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'getProjectDataDiff', array('begin' => $begin + 10));
        header("refresh:3;url=$location");
    }

    public function viewProjectDataDiff()
    {
        $this->display();
    }

    /**
     * 2022-04-13 tongyanqi 年底计划 架构改造需求 历史数据处理
     */
    public function structureChanges()
    {
        $this->loadModel('projectplan');
        foreach($this->lang->projectplan->structureList as $k => $v )
        {
            $this->dao->update(TABLE_PROJECTPLAN)->set('structure')->eq($v)->where('structure')->eq($k)->exec();
        }
        echo "good";
    }
    /**
     *
     * （先不上线） tongyanqi 项目周边 外部计划数据处理
     */
    public function weeklyReportOutsidePlanChanges()
    {
        $this->loadModel('weeklyreport');
        $reports = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)->where('deleted')->eq(0)->fetchAll('id');
        $projectPlan = $outsidePlanRows = [];
        $i = 0;
        foreach ($reports as $report){ //查看每个报告
            if(!empty($report->outsidePlan)) continue; //已有新的外部计划内容不用处理
            $projectOutsidePlans = [];
            if(empty($projectPlan[$report->projectId])){ //获取报告对应的年度计划
                $projectPlan[$report->projectId] = $this->loadModel('projectPlan')->getByProjectID($report->projectId);
            }
            $outsideProjectIdArray = explode(',', $projectPlan[$report->projectId]->outsideProject);
            foreach ($outsideProjectIdArray as $outsideProjectId){ //获取每个年度计划对应的外部计划
                if(!is_numeric($outsideProjectId) || $outsideProjectId < 1) continue;
                if(empty($outsidePlanRows[$outsideProjectId])){
                    $outsidePlanRows[$outsideProjectId] = $this->loadModel('outsideplan')->getByID($outsideProjectId);
                }
                $projectOutsidePlans[] = $outsidePlanRows[$outsideProjectId];
            }
            $outsidePlans = [];
            foreach ($projectOutsidePlans as $outplan) //拼接主要的(外部)项目/任务信息
            {
                $outsidePlan['code'] = $outplan->code;
                $outsidePlan['name'] = $outplan->name;
                $outsidePlan['begin'] = $outplan->begin;
                $outsidePlan['end']  = $outplan->end;
                $outsidePlan['workload'] = $outplan->workload;
                $outsidePlans[] = $outsidePlan;
            }
            if(!empty($outsidePlans) && empty($report->outsidePlan))
            {
                $outsidePlanJson = base64_encode(json_encode($outsidePlans));
                $this->dao->update(TABLE_PROJECTWEEKLYREPORT)->set('outsidePlan')->eq($outsidePlanJson)->where('id')->eq($report->id)->exec(); //更新周报
                $i++;
            }
        }
        echo $i . "rows affected";
    }
    /**
     *  tongyanqi 2022-04-21
     * 处理历史项目白名单 添加部门领导
     */
    public function processWhiteList()
    {
        $this->loadModel('projectplan');
        $plans = $this->dao->select('id,bearDept,project')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('status')->ne('closed')->andWhere('project')->ne(null)->fetchAll('id'); //->andWhere('project')->eq(2646)
        $bossAccounts   = $this->loadModel('dept')->getDeptUserPairs(25); //取总经理们
        $bosses         = array_keys($bossAccounts);
        $depts = $this->dao->select('id,manager1,manager,leader,leader1')->from(TABLE_DEPT)->fetchAll('id');
        $managers = $whitelist_array= [];
        //取所有部门经理
        foreach ($depts as $dept){
            $managers[$dept->id] =  array_unique(explode(',', $dept->manager .','. $dept->manager1 .','. $dept->leader .','. $dept->leader1 ));
        }
        //循环所有年度计划
        foreach ($plans as $plan){
            if(empty($plan->project)) continue; //没立项
            $bearDeptIds = explode(',', $plan->bearDept); //年度计划对于的部门
            $whitelist_array[$plan->project] = $bosses; //白名单要有总经理们

            //整理白名单 更新project表
          foreach($bearDeptIds as $bearDeptId) //
            {
                $whitelist_array[$plan->project] = array_merge($whitelist_array[$plan->project], array_values($managers[$bearDeptId])); //还有对应部门经理们
            }
            $whitelist_array[$plan->project] = array_unique( $whitelist_array[$plan->project]);
            echo "项目id " . $plan->project . "::加入经理:" . json_encode($whitelist_array[$plan->project]) .PHP_EOL."<br>";
            $currentMembers = $this->loadModel('project')->getTeamMembers($plan->project);
            $projectMembers[$plan->project] = empty($currentMembers) ? [] : array_keys($this->loadModel('project')->getTeamMembers($plan->project)); //项目成员

            $projectWhitelist = $this->dao->select('whitelist')->from(TABLE_PROJECT)->where('id')->eq($plan->project)->fetch('whitelist'); //取现有白名单
            $projectWhitelist .= ','. implode(',', $whitelist_array[$plan->project]); //加入领导
            $projectWhitelist = array_unique(explode(',', $projectWhitelist )); //生成数组 去重
            $projectWhitelist = implode(',', $projectWhitelist)  ; //新的白名单串
            $this->dao->update(TABLE_PROJECT)->set('whitelist')->eq($projectWhitelist)->where('id')->eq($plan->project)->exec(); //更新项目表
        }
        unset($whitelist_array['']); //无效项目
        //处理对于项目的白名单 更新acl
        $i = 0;
        foreach ($whitelist_array as $key => $accounts)
        {
            if(empty($key)) continue;

            foreach ($accounts as $accountName)
            {
                if(isset($projectMembers[$key]) && in_array($accountName, $projectMembers[$key])) continue; //已经是项目成员
//                删除原白名单 避免重复
                $this->dao->delete()->from(TABLE_ACL)
                    ->where('objectType')->eq('project')
                    ->andWhere('objectID')->eq($key)
                    ->andWhere('type')->eq('whitelist')
                    ->andWhere('account')->eq($accountName)
                    ->andWhere('source')->eq('add')
                    ->exec();

                $acl             = new stdClass();
                $acl->account    = $accountName;
                $acl->objectType = 'project';
                $acl->objectID   = $key;
                $acl->type       = 'whitelist';
                $acl->source     = 'add';
                $acl->reason     = 1001; //立项领导白名单
                $this->dao->insert(TABLE_ACL)->data($acl)->autoCheck()->exec();

                $i++;
            }
        }
        echo $i . "rows processed";
    }

    // 获取任务工时task表的consumed和effort表数据差异数据。
    public function getTaskConsumedDiff($begin = 0, $end = 150)
    {
        // 记录那些项目存在异常。
        $projectIdList = $this->session->projectIdList;

        $tasks = $this->dao->query("SELECT id,project,name,consumed FROM `zt_task` where deleted = '0' oRdEr bY id asc lImiT $begin,$end")->fetchAll();
        $path = 'view/viewtaskconsumeddiff.html.php';
        if(empty($tasks))
        {
            $table = '</table>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);

            file_put_contents($path, '差异项目ID：' . $projectIdList, FILE_APPEND | LOCK_EX);
            echo '所有项目处理完毕，无数据可执行！';
            die();
        }

        // 设置写入的文件，首次执行对数据进行清空。
        if(!file_exists('view')) mkdir("view");
        if($begin == 0) fopen($path, 'w');
        if (!file_exists($path)) fopen($path, 'w');

        if($begin == 0)
        {
            $table = '<table>' . PHP_EOL;
            $table .= '<tr>';
            $table .= '<th>项目ID</th>';
            $table .= '<th>项目名称</th>';
            $table .= '<th>任务ID</th>';
            $table .= '<th>任务名称</th>';
            $table .= '<th>任务Consumed</th>';
            $table .= '<th>Effort消耗</th>';
            $table .= '<th>差值</th>';
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        $projectList = empty($projectIdList) ? array() : json_decode($projectIdList, true);
        foreach($tasks as $task)
        {
            $effort = $this->dao->select('cast(sum(consumed) as decimal(11,2)) as consumed')->from('zt_effort')
                ->where('objectType')->eq('task')
                ->andWhere('objectID')->eq($task->id)
                ->andWhere('deleted')->eq('0')
                ->fetch();

            $consumedDiff = $task->consumed - $effort->consumed;
            if($consumedDiff == 0) continue;

            $projectName = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($task->project)->fetch('name');

            $projectList[$task->project] = $task->project;

            $table = '<tr>';
            $table .= '<td>' . $task->project . '</td>' . PHP_EOL;
            $table .= '<td>' . $projectName . '</td>' . PHP_EOL;
            $table .= '<td>' . $task->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $task->name . '</td>' . PHP_EOL;
            $table .= '<td>' . $task->consumed . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->consumed . '</td>' . PHP_EOL;
            $table .= '<td>' . $consumedDiff . '</td>' . PHP_EOL;
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        $projectIdList = json_encode($projectList);
        $this->session->set('projectIdList', $projectIdList);

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'getTaskConsumedDiff', array('begin' => $begin + 150));
        header("refresh:1;url=$location");
    }

    public function viewTaskConsumedDiff()
    {
        $this->display();
    }

    // 获取个人任务工时task表的consumed和effort表数据差异数据。
    public function getPersonalConsumedDiff($account = '', $projectID = 0, $begin = 0, $end = 150)
    {
        if(empty($account))
        {
            echo '请输入用户账号';
            die();
        }

        $user = $this->dao->query("SELECT id FROM `zt_user` where deleted = '0' and account = '$account'")->fetch();
        if(empty($user))
        {
            echo '未查询到用户账号';
            die();
        }

        $effort1 = $this->dao->query("SELECT id,objectID,cast(consumed as decimal(11,2)) as consumed,date FROM `zt_effort` where project = $projectID and deleted = '0' oRdEr bY id asc lImiT $begin,$end")->fetchAll();

        $path = 'view/viewpersonalconsumeddiff.html.php';
        if(empty($effort1))
        {
            $table = '</table>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);

            echo '所有工时消耗处理完毕，无数据可执行！';
            die();
        }

        // 设置写入的文件，首次执行对数据进行清空。
        if(!file_exists('view')) mkdir("view");
        if($begin == 0) fopen($path, 'w');
        if (!file_exists($path)) fopen($path, 'w');

        if($begin == 0)
        {
            $table = '<table>' . PHP_EOL;
            $table .= '<tr>';
            $table .= '<th>处理后ID</th>';
            $table .= '<th>处理后objectID</th>';
            $table .= '<th>处理后Consumed</th>';
            $table .= '<th>处理后Date</th>';
            $table .= '<th>|</th>';
            $table .= '<th>处理前ID</th>';
            $table .= '<th>处理前objectID</th>';
            $table .= '<th>处理前Consumed</th>';
            $table .= '<th>处理前Date</th>';
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        foreach($effort1 as $effort)
        {
            $effort2 = $this->dao->select('id,objectID,cast(consumed as decimal(11,2)) as consumed,date')->from('zt_effort4')
                ->where('id')->eq($effort->id)
                ->fetch();

            $background = '';
            if($effort2->consumed != $effort->consumed) $background = ' style="background: #bfb2b2;"';

            $table = '<tr' . $background . '>';
            $table .= '<td>' . $effort->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->objectID . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->consumed . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort->date . '</td>' . PHP_EOL;
            $table .= '<td>|</td>' . PHP_EOL;
            $table .= '<td>' . $effort2->id . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort2->objectID . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort2->consumed . '</td>' . PHP_EOL;
            $table .= '<td>' . $effort2->date . '</td>' . PHP_EOL;
            $table .= '</tr>' . PHP_EOL;
            file_put_contents($path, $table, FILE_APPEND | LOCK_EX);
        }

        echo '处理了' . count($effort1) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'getPersonalConsumedDiff', array('account' => $account, 'projectID' => $projectID, 'begin' => $begin + 150, 'end' => $end));
        header("refresh:1;url=$location");
    }

    public function viewPersonalConsumedDiff()
    {
        $this->display();
    }

    // 清除所有任务的task表中consumed字段。
    public function processTaskConsumedEmpty()
    {
        $this->dao->query("update zt_task set `consumed` = 0 where deleted = '0'");
        echo '所有的任务都执行完毕了!';
        die();
    }

    // 对没有所属阶段的任务进行逻辑删除。
    public function processTaskExecutionEmpty()
    {
        $this->dao->query("update zt_task set `deleted` = '1' where execution = '0' and deleted = '0'");
        echo '所有的任务都执行完毕了!';
        die();
    }

    // 根据任务的effort表实际记录工时恢复task表的consumed字段。
    public function processTaskConsumedCompute($begin = 0, $end = 150)
    {
        $tasks = $this->dao->query("SELECT id,deadline,estStarted FROM `zt_task` where deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            $effort = $this->dao->query("SELECT cast(sum(consumed) as decimal(11,2)) as consumed FROM `zt_effort` where objectID = {$task->id} and objectType = 'task' and deleted = '0'")->fetch();
            if(empty($effort))
            {
                $effort = new stdClass();
                $effort->consumed = 0;
            }

            $planDuration = helper::diffDate3($task->deadline, $task->estStarted);
            $this->dao->update(TABLE_TASK)->set('consumed')->eq($effort->consumed)->set('planDuration')->eq($planDuration)->where('id')->eq($task->id)->exec();
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskConsumedCompute', array('begin' => $begin + 150));
        header("refresh:1;url=$location");
    }

    // 根据任务的effort表实际记录工时恢复task表的consumed字段。
    public function processParentTaskConsumed($begin = 0, $end = 100)
    {
        $tasks = $this->dao->query("SELECT * FROM `zt_task` where deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            // 判断任务是否为父任务，且该父任务有工时消耗。如果有，则创建同名的子任务，将工时消耗挂在子任务上。
            $effort = $this->dao->query("SELECT cast(sum(consumed) as decimal(11,2)) as consumed FROM `zt_effort` where objectID = {$task->id} and objectType = 'task' and deleted = '0'")->fetch();
            $subtask = $this->dao->select('id')->from(TABLE_TASK)->where('parent')->eq($task->id)->andWhere('deleted')->eq('0')->fetch();

            if(!empty($effort->consumed) and !empty($subtask))
            {
                $clonedTask = clone $task;
                unset($clonedTask->id);
                $clonedTask->parent = $task->id;
                $this->dao->insert(TABLE_TASK)->data($clonedTask)->autoCheck()->exec();
                $clonedTaskID = $this->dao->lastInsertID();

                $newPath = $task->path;
                $newPath = $newPath . ',' . $clonedTaskID;
                $grade   = $task->grade;
                $grade   = $grade +1;
                $this->dao->update(TABLE_TASK)->set('path')->eq($newPath)->set('grade')->eq($grade)->where('id')->eq($clonedTaskID)->exec();

                $this->dao->update(TABLE_EFFORT)->set('objectID')->eq($clonedTaskID)->where('objectID')->eq($task->id)->andWhere('objectType')->eq('task')->exec();
            }
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processParentTaskConsumed', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }


    /**
     * 2022-4-21 tongyanqi 处理历问题解决时间
     */
    public function processProblemClosedTime()
    {
        $delivers = $this->dao->select('*')->from(TABLE_CONSUMED)->where('after')->eq('delivery')->fetchAll();

        foreach ($delivers as $deliver)
        {
            if($deliver->objectType == 'demand') {$table = TABLE_DEMAND;}
            elseif($deliver->objectType == 'problem') {$table = TABLE_PROBLEM;}
            else {continue;}
            $this->dao->update($table)->set('solvedTime')->eq($deliver->createdDate)->where('id')->eq($deliver->objectID)->exec();
        }

        $delivers = $this->dao->select('*')->from(TABLE_CONSUMED)->where('after')->eq('closed')->fetchAll();
        foreach ($delivers as $deliver)
        {
            if($deliver->objectType == 'demand') {$table = TABLE_DEMAND;}
            elseif($deliver->objectType == 'problem') {$table = TABLE_PROBLEM;}
            else {continue;}
            $this->dao->update($table)->set('solvedTime')->eq($deliver->createdDate)->where('id')->eq($deliver->objectID)->exec();
        }
        echo "ok";
    }

    /**
     * todo 执行前需要检查生产环境自定义字段是否有变动
     * 2022-04-25 tongyanqi 年底计划 来源历史数据处理
     */
    public function projectCreationSourceChanges()
    {
        $this->loadModel('projectplan');
        if($this->session->projectCreationSourceChanges == '1') die("已经执行过了");
        $this->session->set('projectCreationSourceChanges', '1');
        $map[0] = '';  //0
        $map[1] = '1'; //1
        $map[2] = 'projectsource4'; //征信项目
        $map[3] = '8'; //金币总公司
        $map[4] = '9'; //印钞造币总公司
        $map[5] = '10'; //分支行
        $map[6] = '11'; //行内其他单位
        $map[7] = '6';  //内部项目
        $map[8] = '7';  //其他

        foreach($this->lang->projectplan->sourceList as $k => $v )
        {
            if(empty($v) || $k == 1 || empty($map[$k])) continue; //0，1不用改
            echo $k.'=>'.$v."= ". $this->lang->projectplan->basisList[$map[$k]] .'<br>';
            $this->dao->update(TABLE_PROJECTCREATION)->set('source')->eq($map[$k])->where('source')->eq($k)->exec();
        }
        echo "done";
    }
	
	    //0424 平台需求收集历史数据处理
    public function dealdemandcollections(){
        $infos = $this->dao->select('*')->from('zt_flow_userfb')->fetchAll();
        $plan = $this->dao->select('id,title')->from('zt_productplan')->where('product')->eq('1')->andWhere('deleted')->eq('0')->fetchAll();
        $plan = array_column($plan,'id','title');
        foreach($infos as $info)
        {
            $demandcollection = new stdClass();
            $demandcollection->title = $info->name;
            $demandcollection->dept = $info->dept;
            $demandcollection->submitter = $info->submitter;
            $demandcollection->type = $info->type;
            $demandcollection->desc = $info->content;
            $demandcollection->analysis = $info->feedback;
            if(strtotime($info->createdDate) > 0) $demandcollection->createDate = $info->createdDate;
            $demandcollection->createBy = $info->createdBy;
            if(strtotime($info->fxdate) > 0) $demandcollection->processingDate = $info->fxdate;
            if(strtotime($info->tjdate) > 0) $demandcollection->handoverDate = $info->tjdate;
            if(strtotime($info->csdate) > 0) $demandcollection->feedbackDate = $info->csdate;
            if(strtotime($info->online) > 0) $demandcollection->launchDate = $info->online;
            $demandcollection->Implementation = $info->basedept;
            if(strtotime($info->editedDate) > 0) $demandcollection->updateDate = $info->editedDate;
            $demandcollection->updateBy = $info->editedBy;
            $demandcollection->productmanager = $info->manager;
            $demandcollection->assignFor = $info->assignedTo;
            $demandcollection->copyFor = $info->mailto;
            $demandcollection->dealuser = $info->approver;
            $demandcollection->confirmBy = $info->confirmBy;
            $demandcollection->confirmDate = $info->confirmBy;
            $demandcollection->closedDate = strtotime($info->closeDate) >0 ? $info->closeDate : null;
            if(strtotime($info->fxdate) > 0) $demandcollection->processingDate = $info->fxdate;

            switch($info->version)
            {
                case '迭代1':
                    $demandcollection->Expected = isset($plan['V1.0.0.1']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代2':
                    $demandcollection->Expected = isset($plan['V1.0.0.2']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代3':
                    $demandcollection->Expected = isset($plan['V1.0.0.3']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代4':
                    $demandcollection->Expected = isset($plan['V1.0.0.4']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代5':
                    $demandcollection->Expected = isset($plan['V1.0.0.5']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代6':
                    $demandcollection->Expected = isset($plan['V1.0.0.6']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代7':
                    $demandcollection->Expected = isset($plan['V1.0.0.7']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代8':
                    $demandcollection->Expected = isset($plan['V1.0.0.8']) ? $plan['V1.0.0.1'] : '';
                    break;
                default:
                    $demandcollection->Expected = '';
            }

            switch($info->acversion)
            {
                case '迭代1':
                    $demandcollection->Actual = isset($plan['V1.0.0.1']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代2':
                    $demandcollection->Actual = isset($plan['V1.0.0.2']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代3':
                    $demandcollection->Actual = isset($plan['V1.0.0.3']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代4':
                    $demandcollection->Actual = isset($plan['V1.0.0.4']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代5':
                    $demandcollection->Actual = isset($plan['V1.0.0.5']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代6':
                    $demandcollection->Actual = isset($plan['V1.0.0.6']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代7':
                    $demandcollection->Actual = isset($plan['V1.0.0.7']) ? $plan['V1.0.0.1'] : '';
                    break;
                case '迭代8':
                    $demandcollection->Actual = isset($plan['V1.0.0.8']) ? $plan['V1.0.0.1'] : '';
                    break;
                default:
                    $demandcollection->Actual = '';
            }
            switch($info->status)
            {
                case '1':
                    $demandcollection->state = '1';
                    break;
                case '2':
                    $demandcollection->state = '2';
                    break;
                case '3':
                    $demandcollection->state = '8';
                    break;
                case '4':
                    $demandcollection->state = '4';
                    break;
                case '5':
                    $demandcollection->state = '5';
                    break;
                case '6':
                    $demandcollection->state = '9';
                    break;
                case '7':
                    $demandcollection->state = '10';
                    break;
                case '8':
                    $demandcollection->state = '7';
                    break;
                case '9':
                    $demandcollection->state = '4';
                    break;
                case '10':
                    $demandcollection->state = '6';
                    break;
                case '11':
                    $demandcollection->state = '11';
                    break;
                case '12':
                    $demandcollection->state = '4';
                    break;
                case '13':
                    $demandcollection->state = '4';
                    break;
                case '14':
                    $demandcollection->state = '4';
                    break;
                case '15':
                    $demandcollection->state = '4';
                    break;
                case '16':
                    $demandcollection->state = '4';
                    break;
                case '17':
                    $demandcollection->state = '4';
                    break;
                case '18':
                    $demandcollection->state = '4';
                    break;
            }
            switch($info->pri)
            {
                case '高':
                    $demandcollection->priority = '1';
                    break;
                case '中':
                    $demandcollection->priority = '2';
                    break;
                case '低':
                    $demandcollection->priority = '3';
                    break;
                default:
                    $demandcollection->priority = '';
            }

            $this->dao->insert(TABLE_DEMANDCOLLECTION)->data($demandcollection)->exec();
        }
        echo "ok";
    }

    /**
     * 2022-04-28 tongyanqi 更改年度机会basis
     */
    public function projectBasisChange()
    {
        $rs = $this->dao->update(TABLE_PROJECTPLAN)->set('basis')->eq(1)->where('basis')->eq('projectsource2')->exec();
        echo "更新项目数".$rs."<br>";
        $this->dao->delete()->from(TABLE_LANG)->where('module')->eq('projectplan')->andWhere('section')->eq('basisList')->andWhere('`key`')->eq('projectsource2')->exec();
        echo "已删除自定义字段projectsource2";
    }

    // 对取消状态的任务进行删除。
    public function processTaskCancelConsumed($begin = 0, $end = 200)
    {
        // 从历史记录zt_effort2中读取数据。
        $tasks = $this->dao->query("SELECT id,status,deleted FROM `zt_task` where status = 'cancel' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有取消任务记录处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            $this->dao->update(TABLE_TASK)->set('deleted')->eq('1')->where('id')->eq($task->id)->exec();
            $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('objectType')->eq('task')->andWhere('objectID')->eq($task->id)->exec();
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskCancelConsumed', array('begin' => $begin + 200));
        header("refresh:1;url=$location");
    }

    // 团队任务的真实消耗，重新计算。根据任务的effort表实际记录工时恢复task表的consumed字段。
    public function processTaskTeamConsumedCompute($begin = 0, $end = 150)
    {
        $tasks = $this->dao->query("SELECT id FROM `zt_task` where deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($tasks))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        $this->loadModel('effort');
        foreach($tasks as $task)
        {
            $task->team = $this->dao->select('*')->from(TABLE_TEAM)->where('root')->eq($task->id)->andWhere('type')->eq('task')->orderBy('order')->fetchAll('account');
            if(!empty($task->team))
            {
                foreach($task->team as $account => $teamUser)
                {
                    $realData = $this->effort->getRealConsumedAndLeftByTaskID($task->id, $account);
                    $teamUserLeft     = $realData->left;
                    $teamUserConsumed = $realData->consumed;
                    $this->dao->update(TABLE_TEAM)->set('left')->eq($teamUserLeft)->set('consumed')->eq($teamUserConsumed)
                         ->where('root')->eq($task->id)
                         ->andWhere('type')->eq('task')
                         ->andWhere('account')->eq($account)->exec();
                }
            }
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTaskTeamConsumedCompute', array('begin' => $begin + 150));
        header("refresh:1;url=$location");
    }

    /**
     * 2022/05/13
     * 处理评审表历史数据 没有创建部门的问题
     */
    public function updateReviewDept(){

        $reviewPairs = $this->dao->select('id,createdDept,createdBy')->from(TABLE_REVIEW)->where('deleted')->eq('0')->andwhere('createdDept')->eq(0)->orderBy('id_desc')->fetchAll();
        $yzcount = 0;
        foreach ($reviewPairs as $value){
            if(empty($value->id)) continue;
            //查询用户部门 id
            $user = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($value->createdBy)->fetch();
            $this->dao->update(TABLE_REVIEW)->set('createdDept')->eq($user->dept)->where('id')->eq($value->id)->exec();
            $yzcount ++;

        }
        echo '更新了' . $yzcount . '条部门数据';


    }

    // 阶段如果删除，则底层相关任务，任务消耗等也将删除。
    public function processStageTask($begin = 0, $end = 20)
    {
        // 从历史记录zt_effort2中读取数据。
        $stages = $this->dao->query("SELECT id,deleted FROM `zt_project` where `type` = 'stage' and deleted = '1' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        if(empty($stages))
        {
            echo '所有任务消耗记录处理完毕，无数据可执行！';
            die();
        }

        $this->loadModel('execution');
        foreach($stages as $stage)
        {
            // 查询子阶段。
            $stageList = $this->dao->select('id')->from(TABLE_EXECUTION)
                ->where('type')->eq('stage')
                ->andWhere('deleted')->eq(0)
                ->andWhere('parent')->eq($stage->id)
                ->fetchPairs();
            $stageList[$stage->id] = $stage->id;

            /* Delete execution. */
            $this->dao->update(TABLE_EXECUTION)->set('deleted')->eq('1')->where('id')->in($stageList)->exec();
            foreach($stageList as $execution)
            {
                $this->execution->updateUserView($execution);
            }

            $this->dao->delete()->from(TABLE_PROJECTPRODUCT)->where('project')->in($stageList)->exec();
            $this->dao->delete()->from(TABLE_EXECUTIONSPEC)->where('execution')->in($stageList)->exec();

            // 删除阶段及其子阶段下所有的任务。
            $taskList = $this->dao->select('id,execution')->from(TABLE_TASK)
                ->where('execution')->in($stageList)
                ->andWhere('deleted')->eq(0)
                ->fetchAll();
            $taskListID = array();
            foreach($taskList as $task)
            {
                $taskListID[] = $task->id;
                $this->execution->deleteTasks($task->execution, $task->id);
            }
            $this->dao->delete()->from(TABLE_TASKSPEC)->where('task')->in($taskListID)->exec();
        }

        echo '处理了' . count($stages) . '条数据; 1秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processStageTask', array('begin' => $begin + 20));
        header("refresh:1;url=$location");
    }

    /**
     * 1、变更状态为 '评审通过' 的已结束单子&&且 存在 '质量部CM' 节点 修改变更状态为  '待打基线'  待处理人为 '质量部CM'
     *
     */
    public function updateChangeBaseLine(){
        //查询变更表符合要求数据id
        $change = $this->dao->select('*')->from(TABLE_CHANGE)->where('status')->ne('deleted')->andwhere('createdDate')->ge('2022-01-01')->orderBy('id_desc')->fetchAll();
        $before = 'gmsuccess';
        $after = 'success';
        if($change){
            //处理需求 1、
            $changeid = array_column($change,'id');
            //查询工作量表符合要求的数据id
            $consumed = $this->dao->select('objectID')->from(TABLE_CONSUMED)->where('objectType')->eq('change')
                ->andWhere('objectID')->in($changeid)
                ->andwhere('deleted')->eq('0')
                ->andwhere('`before`')->eq($before)
                ->andwhere('`after`')->eq($after)
                ->orderBy('objectID')->fetchPairs();
            //查询创建变更人所属部门的质量部CM
            //$this->loadModel('dept')->getByID($app->user->dept);
            $consumedid = array_keys($consumed);

            $yzcount = 0;
            foreach ($consumedid as $id){
                // 查询reviewernode 版本
                $nodeversion = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectID')->eq($id)
                                ->andWhere('objectType')->eq('change')
                                ->andWhere('stage')->eq('8')
                                ->orderBy('id desc')->limit(1)
                                ->fetch();
                // 查询reviewernode id
                $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectID')->eq($id)
                                ->andWhere('objectType')->eq('change')
                                ->andWhere('stage')->eq('8')
                                ->andWhere('version')
                                ->eq($nodeversion->version)
                                ->fetch();

                //更新reviewernode 状态
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('objectID')->eq($id)->andWhere('stage')->eq('8')->andWhere('id')->eq($node->id)->exec();
                //更新reviewer 状态
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($node->id)->andWhere('status')->eq('pass')->exec();
                //更新 变更表状态
                $statusAndReviewStage  = new stdClass();
                $statusAndReviewStage->status = 'gmsuccess';

                $statusAndReviewStage->reviewStage = '7';
                $this->dao->update(TABLE_CHANGE)->data($statusAndReviewStage)->where('id')->eq($id)->exec();
                $yzcount ++;

            }
            echo '更新了' . $yzcount . '条评审通过的已结束单子&&且存在 质量部CM节点数据';
        }

    }
    /**
     *
     * 2、变更状态为 '评审通过' 的已结束单子&&且 不存在 '质量部CM' 节点 新增节点 ‘CM打基线’  修改变更状态为 '待打基线'  待处理人为 '质量部CM'
     */
    public function insertChangeBaseLine(){
        //查询变更表符合要求数据id
        $change = $this->dao->select('*')->from(TABLE_CHANGE)->where('status')->ne('deleted')->andwhere('createdDate')->ge('2022-01-01')->orderBy('id_desc')->fetchAll();
        $before = 'gmsuccess';
        $after = 'success';
        if($change){
            //处理需求 2、
            $changeid = array_column($change,'id');
            $createdDept = array_column($change,'createdDept','id');
            //查询工作量表符合要求的数据id
            $consumed = $this->dao->select('objectID')->from(TABLE_CONSUMED)->where('objectType')->eq('change')
                ->andWhere('objectID')->in($changeid)
                ->andwhere('deleted')->eq('0')
                ->andwhere('`before`')->ne($before)
                ->andwhere('`after`')->eq($after)
                ->orderBy('objectID')->fetchPairs();


            $consumedid = array_keys($consumed);
            $yzcount = 0;
            foreach ($consumedid as $id){
                //查询创建变更人所属部门的质量部CM
               $deptcm =  $this->loadModel('dept')->getByID($createdDept[$id]);
               $cm = $deptcm->cm;
                // 查询reviewernode 版本
                $nodeversion = $this->dao->select('version,createdBy')->from(TABLE_REVIEWNODE)->where('objectID')->eq($id)
                    ->andWhere('objectType')->eq('change')
                    ->orderBy('id desc')->limit(1)
                    ->fetch();
                $nodecount = $this->dao->select('count(*) count,id')->from(TABLE_REVIEWNODE)->where('objectID')->eq($id)
                    ->andWhere('objectType')->eq('change')
                    ->andWhere('stage')->eq('8')
                    ->andWhere('version')->eq($nodeversion->version)
                    ->fetch();
                if($nodecount->count){
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('objectID')->eq($id)
                        ->andWhere('objectType')->eq('change')
                        ->andWhere('version')->eq($nodeversion->version)
                        ->andWhere('stage')->eq('8')->exec();
                    $reviewNodeid = $nodecount->id;
                }else{
                    //新增节点到node 表
                    $reviewNodeInfo = new stdClass();
                    $reviewNodeInfo->status   = 'pending';
                    $reviewNodeInfo->objectID = $id;
                    $reviewNodeInfo->objectType = 'change';
                    $reviewNodeInfo->stage    = '8';
                    $reviewNodeInfo->version  = $nodeversion->version;
                    $reviewNodeInfo->createdBy   = $nodeversion->createdBy;
                    $reviewNodeInfo->createdDate = helper::today();
                    $this->dao->insert(TABLE_REVIEWNODE)->data($reviewNodeInfo)->autoCheck()->exec();
                    $reviewNodeid = $this->dao->lastInsertID();
                }


                 $cm = explode(',',$cm);
                 $grade = 0;
                 foreach ($cm as $item) {
                     if(!$item) continue;
                     $review = new stdClass();
                     $review->node = $reviewNodeid;
                     $review->reviewer = $item;
                     $review->status = 'pending';
                     $review->grade = $grade ;
                     $review->createdBy = $nodeversion->createdBy;
                     $review->createdDate = helper::today();
                     $grade ++;
                     $this->dao->insert(TABLE_REVIEWER)->data($review)->autoCheck()->exec();
                 }

                //更新 变更表状态
                $statusAndReviewStage  = new stdClass();
                $statusAndReviewStage->status = 'gmsuccess';

                $statusAndReviewStage->reviewStage = '7';
                $this->dao->update(TABLE_CHANGE)->data($statusAndReviewStage)->where('id')->eq($id)->exec();
                $yzcount ++;
            }
            echo '插入了' . $yzcount . '条评审通过的已结束单子&&且不存在 质量部CM 节点数据 ';
        }

    }

    /**
     * Desc:处理评审通过的数据增加待打基线流程节点
     * Date: 2022/6/29
     * Time: 18:22
     *
     *
     */
    public function dealReviewBaseline()
    {
        //review主表的dealUser和qualityCm更新，以及status更新为pass状态
        $reviewlist = $this->dao->select('id,status,qualityCm,createdDate,createdBy,version')->from(TABLE_REVIEW)
            ->where('createdDate')->ge('2022-01-01')
            ->andWhere('status')->eq('reviewpass')
            ->andwhere('deleted')->eq('0')
            ->orderBy('id asc')
            ->fetchAll();
        $count = 0;
        foreach ($reviewlist as $key => $value){
            //质量部CM
            $user = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($value->createdBy)->fetch();
            $deptcm =  $this->loadModel('dept')->getByID($user->dept);
            $cm = $deptcm->cm;
            if($cm == ''){
                $cm = 'admin';
            }
            if(strstr($cm,',')){
                $cmArr = explode(',',$cm);
                $cm = $cmArr[0];
            }
            //review表需要更新数据构造 $reviewData
            $reviewId = $value->id;
            $reviewData = new stdClass();
            $reviewData->status = 'baseline';
            $reviewData->dealUser = $cm;

            //reviewnode表需要更新数据构造$reviewNode
            $node = $this->dao->select('stage')->from(TABLE_REVIEWNODE)
                ->where('objectID')->eq($reviewId)
                ->andWhere('objectType')->eq('review')
                ->orderBy('id desc')->limit(1)
                ->fetch();

            $reviewNode = new stdClass();
            $reviewNode->status = 'pending';
            $reviewNode->subObjectType = 'baseline';
            $reviewNode->objectType = 'review';
            $reviewNode->objectID = $reviewId;
            $reviewNode->createdBy = $cm;
            $reviewNode->version = $value->version;
            $reviewNode->createdDate = helper::today();
            $reviewNode->nodeCode = 'baseline';
            if(!$node){
                $reviewNode->stage = 1;
            }else{
                $reviewNode->stage =($node->stage) + 1;
            }

            $this->dao->begin();

            $this->dao->update(TABLE_REVIEW)->data($reviewData)->where('id')->eq($reviewId)->exec();
            $this->dao->insert(TABLE_REVIEWNODE)->data($reviewNode)->autoCheck()->exec();
            //构建reviewer表需要更新数据构造$reviewer
            $nodeId = $this->dao->lastInsertID();
            $extra = [];
            $extra['reviewedDate'] = helper::today();
            $extra['isEditInfo'] = '';

            $reviewer = new stdClass();
            $reviewer->node = $nodeId;
            $reviewer->reviewer = $cm;
            $reviewer->status = 'pending';
            $reviewer->grade = 0;
            $reviewer->extra = json_encode($extra);
            $reviewer->createdBy = $cm;
            $reviewer->createdDate = helper::today();
            $this->dao->insert(TABLE_REVIEWER)->data($reviewer)->autoCheck()->exec();

            //获取consumed节点是否已存在关闭状态
            $isHadConsumed = $this->dao->select('`id`,`before`,`after`')->from(TABLE_CONSUMED)
                    ->where('`objectType`')->eq('review')
                    ->andWhere('`objectID`')->eq($reviewId)
                    ->andWhere('`before`')->eq('pass')
                    ->andWhere('`after`')->eq('reviewpass')
                    ->orderBy('id desc')->limit(1)
                    ->fetch();
            //获取不到添加
            if(!$isHadConsumed){
                $consumedData = new stdClass();
                $consumedData->objectType = 'review';
                $consumedData->objectID = $reviewId;
                $consumedData->consumed = 0;
                $consumedData->account = $cm;
                $consumedData->before = 'pass';
                $consumedData->after = 'reviewpass';
                $consumedData->createdBy = $cm;
                $consumedData->createdDate = helper::now();

                $this->dao->insert(TABLE_CONSUMED)->data($consumedData)->autoCheck()->exec();
            }else{
                $updateConsumed = new stdClass();
                $updateConsumed->after = 'baseline';
                $this->dao->update(TABLE_CONSUMED)->data($updateConsumed)->where('id')->eq($isHadConsumed->id)->exec();
            }

            $this->dao->commit();
            $count++;
        }
        echo '共处理' . $count . '条评审增加打基线流程节点数据！';
    }


    /**
     * Desc: 处理所有评审的CM和dept
     * Date: 2022/7/4
     * Time: 16:31
     *
     */
    public function dealReviewCmAndDept()
    {
        $reviewlist = $this->dao->select('id,status,qualityCm,createdDate,createdBy,version')->from(TABLE_REVIEW)
            ->orderBy('id asc')
            ->fetchAll();
        $count = 0;
        foreach ($reviewlist as $key => $value){
            //质量部CM
            $user = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($value->createdBy)->fetch();
            $deptcm =  $this->loadModel('dept')->getByID($user->dept);
            $cm = $deptcm->cm;
            if($cm == ''){
                $cm = 'admin';
            }
            if(strstr($cm,',')){
                $cmArr = explode(',',$cm);
                $cm = $cmArr[0];
            }

            //review表需要更新数据构造 $reviewData
            $reviewId = $value->id;
            $reviewData = new stdClass();
            $reviewData->qualityCm = $cm;
            $reviewData->createdDept = $user->dept;

            $this->dao->update(TABLE_REVIEW)->data($reviewData)->where('id')->eq($reviewId)->exec();

            $count++;
        }
        echo '共处理' . $count . '条评审质量部CM数据！';
    }

    /**
     * Desc: 处理非法关闭评审后的待处理人数据
     * Date: 2022/7/4
     * Time: 16:31
     *
     */
    public function dealErrorProcessData()
    {
        $review = $this->dao->select('id,dealUser,status,version')->from(TABLE_REVIEW)
            ->where('status')->in(array('baseline', 'drop', 'fail'))
            ->andWhere('deleted')->eq(0)
            ->fetchAll();
        $count = 0;
        foreach ($review as $key=>$value){
            $reviewNodes = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($value->id)
                ->andWhere('version')->eq($value->version)
                ->andWhere('status')->in(array('pending', 'wait'))
                ->andWhere('nodeCode')->ne('baseline')
                ->fetchAll();
            if($reviewNodes){
                $nodeIds = array_column($reviewNodes, 'id');

                //更新reviewer表
                $updateData = new stdClass();
                $updateData->status = 'ignore';
                $revieweNode = $this->dao->update(TABLE_REVIEWNODE)->data($updateData)->where('id')->in($nodeIds)->exec();

                //更新reviewer表
                $reviewer = $this->dao->update(TABLE_REVIEWER)->data($updateData)->where('node')->in($nodeIds)->exec();
                if($revieweNode && $reviewer){
                    $count++;
                }
            }
        }
        echo '共处理' . $count . '条非正常关闭评审数据！';
    }

    // 处理历史数据中的测试单创建日期。
    public function processTesttaskDate($begin = 0, $end = 50)
    {
        $tasks = $this->dao->query("SELECT * FROM `zt_testtask` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($tasks))
        {
            echo '所有测试单数据处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            $action = $this->dao->select('*')->from(TABLE_ACTION)->where('objectID')->eq($task->id)->andWhere('objectType')->eq('testtask')->andWhere('action')->eq('opened')->fetch();
            if(!empty($action))
            {
                $createdBy   = $action->actor;
                $createdDate = $action->date;
                $this->dao->update(TABLE_TESTTASK)->set('createdBy')->eq($createdBy)->set('createdDate')->eq($createdDate)->where('id')->eq($task->id)->exec();
            }
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTesttaskDate', array('begin' => $begin + 50));
        header("refresh:1;url=$location");
    }

    // 处理历史数据中的测试单单号。
    public function processTesttaskOddNumber($begin = 0, $end = 200)
    {
        $tasks = $this->dao->query("SELECT id,createdDate FROM `zt_testtask` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($tasks))
        {
            echo '所有测试单数据处理完毕，无数据可执行！';
            die();
        }

        foreach($tasks as $task)
        {
            // 更新测试单单号
            $time = strtotime($task->createdDate);
            $oddNumber = 'CFIT-TR-' . date('Ymd', $time) . '-' . sprintf('%02d', $task->id);
            $this->dao->update(TABLE_TESTTASK)->set('oddNumber')->eq($oddNumber)->where('id')->eq($task->id)->exec();
        }

        echo '处理了' . count($tasks) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processTesttaskOddNumber', array('begin' => $begin + 200));
        header("refresh:1;url=$location");
    }

    // 处理历史数据中的Bug分类与子类的对应关系。
    public function processBugType($begin = 0, $end = 200) {
        $bugs = $this->dao->query("SELECT * FROM `zt_bug` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($bugs))
        {
            echo '所有Bug数据处理完毕，无数据可执行！';
            die();
        }

        $typeList = array();
        $typeList['codeerror'] = array('funcdetect' => 'b1');
        $typeList['yilou']     = array('funcdetect' => 'b2');
        $typeList['tiyan']     = array('funcdetect' => 'b4');
        $typeList['config']    = array('funcdetect' => 'b6');
        $typeList['install']   = array('funcdetect' => 'b6');
        $typeList['security']  = array('security' => 'a6');
        $typeList['performance']  = array('performance' => '');
        $typeList['standard']  = array('funcdetect' => 'b6');
        $typeList['automation']  = array('funcdetect' => 'b1');
        $typeList['designdefect']  = array('funcdetect' => 'b3');
        $typeList['funcdetect']  = array('funcdetect' => 'b1');
        $typeList['requiredect']  = array('requiredect' => '');
        $typeList['codeimprovement']  = array('funcdetect' => 'b6');
        $typeList['security1']  = array('security' => 'a1');
        $typeList['security2']  = array('security' => 'a2');
        $typeList['security3']  = array('security' => 'a3');
        $typeList['security4']  = array('security' => 'a4');
        $typeList['security5']  = array('security' => 'a5');
        $typeList['others']     = array('funcdetect' => 'b5');

        foreach($bugs as $bug)
        {
            if(!empty($typeList[$bug->type]))
            {
                foreach($typeList[$bug->type] as $type => $childType)
                {
                    $this->dao->update(TABLE_BUG)->set('type')->eq($type)->set('childType')->eq($childType)->where('id')->eq($bug->id)->exec();
                }
            }
        }

        echo '处理了' . count($bugs) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processBugType', array('begin' => $begin + 200));
        header("refresh:1;url=$location");
    }
     /**
     * 更新需求解決时间
     */
    public function updateDemandSolvedTime(){
        //查询需求表
        $arr = array();
        $demandId =  $this->dao->select('id,solvedTime')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andwhere('status')->ne('closed')
            ->fetchAll('id');

        $demandIds = array_column($demandId,'id');
        $type = array('modify','fix','gain','modifycncc','gainQz','info','infoQz');//类型
        $infotype = array('fix','gain');
        $infoqztype = array('gainQz','info');
       // 查询二线表关联数据
        $sendline =  $this->dao->select('id,objectID,objectType,relationType,relationID,createdDate')->from(TABLE_SECONDLINE)
                ->where('deleted')->eq(0)
                ->andwhere('objectType')->eq('demand')
                ->andwhere('objectID')->in($demandIds)
                ->andwhere('relationType')->in($type)
                ->orderBy('id desc')
                ->fetchAll('id');

        $sendres = array();
        foreach ($sendline as $key=>$item) {
            $sendres[$item->objectID][$item->id] = $sendline[$key];
        }

        foreach ($sendres as $key=>$item) {
            $max = array_search(max($item),$item);//获取多个关联内容中最新的
            if(in_array($item[$max]->relationType,$infotype)){
                $objecttype  = 'info';
            }elseif(in_array($item[$max]->relationType,$infoqztype)){
                $objecttype  = 'infoQz';
            }else{
                $objecttype = $item[$max]->relationType;
            }

            //查询二线专员审批节点
            $before =  $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                ->where('deleted')->eq(0)
                ->andwhere('`after`')->eq('productsuccess')
                ->andwhere('`objectID`')->eq($item[$max]->relationID)
                ->andwhere('`objectType`')->eq($objecttype)
                ->orderBy('id desc')
                ->limit(1)
                ->fetch();
            if($before){
                //查询是否之后有退回的操作
                $reject =  $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                    ->where('deleted')->eq(0)
                    ->andwhere('`objectID`')->eq($item[$max]->relationID)
                    ->andwhere('`objectType`')->eq($objecttype)
                    ->andwhere('`after`')->eq('reject')
                    ->andwhere('id')->gt($before->id)
                    ->orderBy('id desc')
                    ->limit(1)
                    ->fetch();

                //没有退回获取时间
              if(!$reject){
                  //根据二线专员节点 获取前一个节点时间 并更新主表解决时间
                  $before2 =  $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                      ->where('deleted')->eq(0)
                      ->andwhere('`after`')->eq($before->before)
                      ->andwhere('`objectID`')->eq($item[$max]->relationID)
                      ->andwhere('`objectType`')->eq($objecttype)
                      ->orderBy('id desc')
                      ->limit(1)
                      ->fetch();
                  if(!$before2){
                      $before2 =  $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                          ->where('deleted')->eq(0)
                          ->andwhere('`objectID`')->eq($item[$max]->relationID)
                          ->andwhere('`objectType`')->eq($objecttype)
                          ->andwhere('id')->lt($before->id)
                          ->orderBy('id desc')
                          ->limit(1)
                          ->fetch();
                  }
                      //更新解决时间
                      $createdDate = isset($before2->createdDate) ? $before2->createdDate : '';
                      $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq($createdDate)->where('id')->eq($key)->exec();
                      $arr[] = $key;


                  }else{
                  $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq('')->where('id')->eq($key)->exec();
                  $arr[] = $key;
              }
              }else{
                $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq('')->where('id')->eq($key)->exec();
                $arr[] = $key;
            }
            }
        echo json_encode($arr);
    }
    /**
     * 更新问题解決时间
     */
    public function updateProblemSolvedTime(){
        $arr = array();
        //查询需求表
        $demandId =  $this->dao->select('id,solvedTime')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andwhere('status')->ne('closed')
            ->fetchAll('id');
        $demandIds = array_column($demandId,'id');
        $type = array('modify','fix','gain','modifycncc','gainQz','info','infoQz');//类型
        $infotype = array('fix','gain');
        $infoqztype = array('gainQz','info');
        // 查询二线表关联数据
        $sendline =  $this->dao->select('id,objectID,objectType,relationType,relationID,createdDate')->from(TABLE_SECONDLINE)
            ->where('deleted')->eq(0)
            ->andwhere('objectType')->eq('problem')
            ->andwhere('objectID')->in($demandIds)
            ->andwhere('relationType')->in($type)
            ->orderBy('id desc')
            ->fetchAll('id');

        $sendres = array();
        foreach ($sendline as $key=>$item) {
            $sendres[$item->objectID][$item->id] = $sendline[$key];
        }

        foreach ($sendres as $key=>$item) {
            $max = array_search(max($item), $item);//获取多个关联内容中最新的
            if (in_array($item[$max]->relationType, $infotype)) {
                $objecttype = 'info';
            } elseif (in_array($item[$max]->relationType, $infoqztype)) {
                $objecttype = 'infoQz';
            } else {
                $objecttype = $item[$max]->relationType;
            }
            //查询二线专员审批节点
            $before =  $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                ->where('deleted')->eq(0)
                ->andwhere('`after`')->eq('productsuccess')
                ->andwhere('`objectID`')->eq($item[$max]->relationID)
                ->andwhere('`objectType`')->eq($objecttype)
                ->orderBy('id desc')
                ->limit(1)
                ->fetch();
                if ($before) {
                    //查询是否之后有退回的操作
                    $reject = $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                        ->where('deleted')->eq(0)
                        ->andwhere('`objectID`')->eq($item[$max]->relationID)
                        ->andwhere('`objectType`')->eq($objecttype)
                        ->andwhere('`after`')->ne('reject')
                        ->andwhere('id')->gt($before->id)
                        ->orderBy('id desc')
                        ->limit(1)
                        ->fetch();
                    //没有退回获取时间
                    if (!$reject) {
                        //根据二线专员节点 获取前一个节点时间 并更新主表解决时间
                        $before2 = $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                            ->where('deleted')->eq(0)
                            ->andwhere('`after`')->eq($before->before)
                            ->andwhere('`objectID`')->eq($item[$max]->relationID)
                            ->andwhere('`objectType`')->eq($objecttype)
                            ->orderBy('id desc')
                            ->limit(1)
                            ->fetch();
                        if (!$before2) {
                            $before2 = $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                                ->where('deleted')->eq(0)
                                ->andwhere('`objectID`')->eq($item[$max]->relationID)
                                ->andwhere('`objectType`')->eq($objecttype)
                                ->andwhere('id')->lt($before->id)
                                ->orderBy('id desc')
                                ->limit(1)
                                ->fetch();
                        }

                        //更新解决时间
                        $createdDate = isset($before2->createdDate) ? $before2->createdDate : '';
                        $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq($createdDate)->where('id')->eq($key)->exec();
                        $arr[] = $key;


                    }else{
                        $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq('')->where('id')->eq($key)->exec();
                        $arr[] = $key;
                    }
                } else {
                    $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq('')->where('id')->eq($key)->exec();
                    $arr[] = $key;
                }
        }
        echo json_encode($arr);
    }

    /**
     *修改等待评审主席确定会议评审结论的数据
     *
     */
    public function repairWaitMeetingOwnerReview(){
        $sql = "select zr.*,
                zrm.id as meetingID, zrm.status as meetingStatus
                from zt_review zr
                left join zt_review_meeting zrm on zr.meetingCode = zrm.meetingCode
                where 1
                and zr.status = 'waitMeetingOwnerReview'
                and zr.meetingCode != ''
                and zrm.status != 'waitMeetingOwnerReview' limit 10";
        $data = $this->dao->query($sql)->fetchAll();
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        //修改历史数据
        $objectType = 'reviewmeeting';
        $version = 0;
        foreach ($data as $val){
            $reviewId  = $val->id;
            $meetingID = $val->meetingID;
            $updateParams = new stdClass();
            $updateParams->type = $val->type;
            $updateParams->allOwner = $val->owner. ','. $val->reviewer;
            $updateParams->owner = $val->owner;
            $updateParams->reviewer = $val->reviewer;
            $updateParams->dealUser = $val->owner;
            $updateParams->status   = $val->status;
            //修改会议评审表
            $this->dao->update(TABLE_REVIEW_MEETING)->data($updateParams)->where('id')->eq($meetingID)->exec();
            //修改会议评审详情表
            $updateDetailParams = new stdClass();
            $updateDetailParams->status = $val->status;
            $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateDetailParams)
                ->where('review_id')->eq($reviewId)
                ->andWhere('review_meeting_id')->eq($meetingID)
                ->exec();

            //增加评审专员审核节点
            $extra = [
                'reviewedDate' => $val->lastReviewedDate,
                'isEditInfo' => 2,
            ];
            $reviewers = [$val->reviewer];
            $status    = 'pass';
            $stage     = 1;
            $extParams = [
                'nodeCode'      => 'meetingReview',
                'reviewerExtParams' => array(
                    'extra'     => json_encode($extra)
                ),
            ];
            $this->loadModel('review')->addNode($objectType, $meetingID, $version, $reviewers, true, $status, $stage, $extParams);

            //增加评审主席审核节点
            $reviewers = [$val->owner];
            $status    = 'pending';
            $stage     = 2;
            $extParams = [
                'nodeCode' => 'meetingOwnerReview',
            ];
            $this->loadModel('review')->addNode($objectType, $meetingID, $version, $reviewers, true, $status, $stage, $extParams);
        }
        echo '继续执行';
    }

    /**
     * 修复历史会议的会议单号和评审纪要号
     */
    public function repairMeetingReviewData(){
        $sql = "select zr.*,
                zrm.id as meetingID, zrm.status as meetingStatus
                from zt_review_meeting zrm 
                left join zt_review zr on zr.meetingCode = zrm.meetingCode
                where 1
                and zrm.meetingCode like 'CFIT-REP-%'
                and zr.id > 0 
                limit 10";
        $data = $this->dao->query($sql)->fetchAll();

//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';

        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }

        foreach ($data as $val){
            $reviewId        = $val->id;
            $meetingID       =  $val->meetingID;
            $type            = $val->type;
            $updateParams           = new stdClass();
            $updateParams->type     = $val->type;
            $updateParams->allOwner = $val->owner. ','. $val->reviewer;
            $updateParams->owner    = $val->owner;
            $updateParams->reviewer = $val->reviewer;
            $updateParams->dealUser  = $val->owner;
            if($val->type == 'dept'){
                $meetingCodeSort = $this->loadModel('reviewmeeting')->setMeetingCodeSort($type, false);
                $newMeetingCode  = $this->loadModel('reviewmeeting')->setMeetingCode($type, $meetingCodeSort);
                $meetingSummarySortId   = $this->loadModel('reviewmeeting')->setMeetingSummaryCodeSort(false);
                $meetingSummaryCode     = $this->loadModel('reviewmeeting')->setMeetingSummaryCode($meetingSummarySortId);

                $updateParams->sortId   = $meetingCodeSort;
                $updateParams->meetingCode = $newMeetingCode; //会议编号
                $updateParams->meetingSummarySortId = $meetingSummarySortId;
                $updateParams->meetingSummaryCode   = $meetingSummaryCode; //会议纪要编号

            }

            if($val->status == 'waitMeetingOwnerReview'){
                $updateParams->status = $val->status;
            }else{
                $updateParams->status = 'pass';
            }
            //修改会议评审表
            $this->dao->update(TABLE_REVIEW_MEETING)->data($updateParams)->where('id')->eq($meetingID)->exec();
            if($val->type == 'dept'){
                //详情
                $detailUpdateParams = new stdClass();
                $detailUpdateParams->meetingCode = $newMeetingCode;
                $detailUpdateParams->status = $val->status;
                //修改会议评审表
                $this->dao->update(TABLE_REVIEW)->data($detailUpdateParams)->where('id')->eq($reviewId)->exec();
                //修改会议评审详情表
                $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($detailUpdateParams)
                    ->where('review_id')->eq($reviewId)
                    ->andWhere('review_meeting_id')->eq($meetingID)
                    ->exec();

            }
        }
        echo '继续执行';
    }

    /* 查询那些版本没有所属项目，需编辑为有所属项目。 */
    public function processBuildByProject()
    {
        $builds = $this->dao->query("SELECT * FROM `zt_build` where project = 0 and deleted = '0'")->fetchAll();

        echo '<table>';
        $i = 1;
        foreach($builds as $build)
        {
            if($i == 1)
            {
                echo '<tr>';
                foreach($build as $field => $value)
                {
                    echo '<th>';
                    echo $field;
                    echo '</th>';
                }
                echo '</tr>';
            }
            $i ++;

            echo '<tr>';
            foreach($build as $field => $value)
            {
                echo '<td>';
                echo $value;
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        if(empty($builds))
        {
            echo '所有版本均有所属项目，无须处理！';
            die();
        }
    }

    /**
     * 需求意向关联需求条目的项目
     */
    public function demandProjectsToOpinions()
    {
       $demands = $this->dao->select('id ,opinionID, projectPlan')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
//            ->andwhere('id')->eq('683')
            ->fetchall('projectPlan');
        $projectPlanIds = array_keys($demands);
        $list = $this->dao->select('id,project')->from(TABLE_PROJECTPLAN)
            ->where('id')->in($projectPlanIds)
            ->andwhere('status')->in('projected')
            ->fetchall('id');
       foreach ($list as $k => $projectPlan){
           if($demands[$k]){
               $opinion = $this->dao->select("project")->from(TABLE_OPINION)->where('id')->eq($demands[$k]->opinionID)->fetch();

               echo "意向原有的项目id string：".$opinion->project . "<br>";
               $opinionsProjectArray = explode(',', $opinion->project);
               echo "意向原有的项目id array:"; print_r($opinionsProjectArray); echo "<br>";
               echo "年度计划的项目id:"; echo  $projectPlan->project ; echo "<br>";
               if($projectPlan->project && !in_array($projectPlan->project, $opinionsProjectArray)) //如果原来的数组没有该项目id
               {
                   $projectString = rtrim($opinion->project, ',') . ',' . $projectPlan->project . ',';
                   $this->dao->update(TABLE_OPINION)->set('project')->eq($projectString)->where("id")->eq($demands[$k]->opinionID)->exec();
                   echo "***********更新 $demands[$k]->opinionID 的项目为 $projectString ********** <br>";
               } else {
                   echo "不用处理<br>";
               }
           }
       }
    }

    /**
     * 处理需求任务project字段从projectplanid换成projectid
     */
    public function requirementChangeProjectId()
    {
        $requirements = $this->dao->select('id ,project')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->fetchall('id');
        foreach ($requirements as $requirement){
            if(!empty($requirement->project)){
                $plan = $this->dao->select('`id`, `project`, `name`')->from(TABLE_PROJECTPLAN)->where('id')->eq($requirement->project)->fetch();
                $this->dao->update(TABLE_REQUIREMENT)->set('project')->eq($plan->project)->where('id')->eq($requirement->id)->exec();
            }
        }
        echo "结束";
    }

    /**
     * 将关联编号写进modify表
     * @author liugaoyang
     */
    public function modifyLinkedProductCodeUpdate()
    {

        //modify表历史数据处理
       $modifys = $this->dao->select('id ,productCode')->from(TABLE_MODIFY)
           ->where('productCode')->ne('')
           ->fetchall('id');
        foreach ( $modifys as $modify) {
            $res = "";
            $codeList = json_decode($modify->productCode);
            foreach($codeList as $code)
            {
                $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($code->assignProduct)->fetch();
                $res .= $product->code.',';

            }
            $res = rtrim($res,',');
            $this->dao->update(TABLE_MODIFY)->set('productCodeInfo')->eq($res)->where('id')->eq($modify->id)->exec();
       }

        echo "结束";
    }
    /**
     * 将单条关联编号写进modify表
     * @author liugaoyang
     */
    public function modifyLinkedProductCodeUpdateSingle($id)
    {

        //modify表历史数据处理
        $modify = $this->loadModel('modify')->getById($id);
        $codeList = json_decode($modify->productCode);
        foreach($codeList as $code)
        {
            $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($code->assignProduct)->fetch();
            $this->dao->update(TABLE_MODIFY)->set('productCodeInfo')->eq($product->code)->where('id')->eq($modify->id)->exec();
        }


        echo "结束";
    }
    /**
     * 将关联编号写进release表
     * @author liugaoyang
     */
    public function releaseLinkedProductCodeUpdate()
    {

        //release表历史数据处理
        $products = $this->dao->select('id ,code')->from(TABLE_PRODUCT)->fetchAll('id');

        foreach ( $products as $product) {
            $this->dao->update(TABLE_RELEASE)->set('productCodeInfo')->eq($product->code)->where('product')->eq($product->id)->exec();
        }

        echo "结束";
    }
    /**
     * 将单条关联编号写进release表
     * @author liugaoyang
     */
    public function releaseLinkedProductCodeUpdateSingle($id)
    {

        //release表历史数据处理
        $product = $this->loadModel('product')->getById($id);

        $this->dao->update(TABLE_RELEASE)->set('productCodeInfo')->eq($product->code)->where('product')->eq($product->id)->exec();

        echo "结束";
    }

    /**
     *
     * 处理已有的产品编号的启用日期=产品的新建日期，备注=“新建产品”
     * @author liugaoyang
     * @param $id
     */
    public function productCodeUpdate()
    {

        //产品表历史数据处理
        $products = $this->dao->select('id,createdDate,code')->from(TABLE_PRODUCT)->fetchAll('id');
        $codes = $this->dao->select("product")->from(TABLE_PRODUCTCODEINFO)->where("deleted")->eq(0)->fetchall();
        $codeProduct = [];
        foreach ($codes as $k=>$v) {
            $codeProduct[] = $v->product;
        }
        foreach ( $products as $product) {
            if (in_array($product->id,$codeProduct)){
                $updateParams = new stdClass();
                $updateParams->product = $product->id;
                $updateParams->code = $product->code;
                $updateParams->enableTime = $product->createdDate;
                $updateParams->createTime = $product->createdDate;
                $updateParams->desc ="新建产品";
                $this->dao->update(TABLE_PRODUCTCODEINFO)->data($updateParams)->autoCheck()->where('product')->eq($product->id)->exec();
            }

        }
        foreach ($products as $k2=>$v2){
            if (!in_array($v2->id,$codeProduct)){
                $updateParams = new stdClass();
                $updateParams->product = $v2->id;
                $updateParams->code = $v2->code;
                $updateParams->enableTime = $v2->createdDate;
                $updateParams->createTime = $v2->createdDate;
                $updateParams->desc ="新建产品";
                $this->dao->insert(TABLE_PRODUCTCODEINFO)->data($updateParams)->exec();
            }
        }
        echo "结束";
    }


    /**
     *
     * 处理已有的产品编号的启用日期=产品的新建日期，备注=“新建产品”
     * @author liugaoyang
     * @param $id
     */
    public function productCodeUpdateSingle($id)
    {

        //modify表历史数据处理
        $product = $this->loadModel('product')->getById($id);
        $updateParams = new stdClass();
        $updateParams->product = $id;
        $updateParams->code = $product->code;
        $updateParams->enableTime = $product->createdDate;
        $updateParams->createTime = $product->createdDate;
        $updateParams->desc ="新建产品";

        $this->dao->insert(TABLE_PRODUCTCODEINFO)->data($updateParams)->autoCheck()->exec();
        $this->dao->printSQL();
        echo "结束";
    }

    /**
     * @author wangshusen
     * 将projectplan表的code更新到project表
     */
    public function projectPlanUpdateProject()
    {

        $projectPlan = $this->dao->select('id,project,mark')
            ->from(TABLE_PROJECTPLAN)
            ->where('status')->eq('projected')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();
        $count = 0;
        foreach ($projectPlan as $value)
        {
            $this->dao->update(TABLE_PROJECT)->set('code')->eq($value->mark)->where('id')->eq($value->project)->exec();
            $count++;
        }
        echo '共处理' . $count . '条数据！'."<br>"."执行结束！";
    }

    // 按照计划和任务联动重新计算任务所属阶段的进度和状态
         public function updateTaskProgress($begin = 0, $end = 30)
         {
             echo '开始处理'."<br>";
             $projects = $this->dao->query("select
                    id
                 from
                     zt_project zp
                 where
                     zp.deleted = '0'
                     and zp.type = 'project'
                     and code not in (
                         	'紫荆', '福费廷', '花猫', '西瓜', '西汉', '青笋', '白桦', '南向通', '云杉',
                         	'瓦力','夏花','华山','钱塘江','新野','百合','花木兰','清秋','零零发','莫吉托',
                         	'纵横','智信','鹰眼','木棉','万泉河','博望','黑豹','启航','蓝天','RCPMIS',
                         	'一阶段','九河','擎天','星图','扬帆','蓬莱','鲸吞','木星','九华山','通宝','交子',
                         	'青海湖','金钱松','赤壁','水仙','蔷薇','千里眼','德芙','兰陵王','A','鲁班',
                         	'山竹','逍遥津','仙鹤','秦俑','秦风','图灵','蓝海','金星','先锋','款冬',
                         	'金盾','石头','天问','通达'
                         )
                 order by
                     id desc lImiT $begin,$end")->fetchAll();
             if(empty($projects))
             {
                 echo '所有项目处理完毕，无数据可执行！';
                 die();
             }
             $i = 0;
             foreach($projects as $project) {
                 $projectID = $project->id;
                 $this->dao->update(TABLE_TASK)->set('progress')->eq(0)->where('project')->eq($projectID)->andWhere('status')->eq('wait')->exec();
                 $this->dao->update(TABLE_TASK)->set('progress')->eq(100)->where('project')->eq($projectID)->andWhere('status')->in('done,closed')->exec();

                 $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('project')->eq($projectID)->andWhere('deleted')->eq(0)->fetchAll('id');
                 foreach($tasks as $task)
                 {
                     if($task->parent) $tasks[$task->parent]->hasChildren = true;
                 }
                 $this->loadModel('task');
                 foreach($tasks as $task)
                 {
                     if(!isset($task->hasChildren))
                     {
                         $plan = helper::diffDate($task->deadline, $task->estStarted) + 1;
                         $end  = substr($task->finishedDate, 0, 10);
                         $real = $end != '0000-00-00' ? helper::diffDate($end, $task->realStarted) + 1 : 0;
                         $this->dao->update(TABLE_TASK)->set('planDuration')->eq($plan)->set('realDuration')->eq($real)->where('id')->eq($task->id)->exec();
                         $this->task->computeConsumed($task->id);
                     }
                 }
                 $i++;
                 echo '已处理第'.$i."条"."<br>";
             }

             echo '处理了' . count($projects) . '条数据; 5秒后刷新数据,请等待执行完毕';
             $location = $this->createLink('history', 'updateTaskProgress', array('begin' => $begin + 30));
             header("refresh:5;url=$location");
         }

    /* 查询那些产品没有所属应用系统，需要处理为有应用系统。 */
    public function processAppByProduct()
    {
        $builds = $this->dao->query("SELECT * FROM `zt_product` where app = 0 and deleted = '0'")->fetchAll();

        echo '<table>';
        $i = 1;
        foreach($builds as $build)
        {
            if($i == 1)
            {
                echo '<tr>';
                foreach($build as $field => $value)
                {
                    echo '<th>';
                    echo $field;
                    echo '</th>';
                }
                echo '</tr>';
            }
            $i ++;

            echo '<tr>';
            foreach($build as $field => $value)
            {
                echo '<td>';
                echo $value;
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        if(empty($builds))
        {
            echo '所有产品均有所属应用系统，无须处理！';
            die();
        }
    }

    /* Bug根据所属产品关联的所属系统进行更新。 */
    public function processAppByBug($begin = 0, $end = 100)
    {
        $bugs = $this->dao->query("SELECT * FROM `zt_bug` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($bugs))
        {
            echo '所有Bug处理完毕，无数据可执行！';
            die();
        }

        foreach($bugs as $bug)
        {
            $applicationID = $this->dao->select('app')->from(TABLE_PRODUCT)->where('id')->eq($bug->product)->fetch('app');
            $applicationID = (int)$applicationID;

            // 对象如果有所属应用系统，但所属产品未查到所属应用系统，可能是历史数据执行过。
            if(!empty($bug->applicationID) and empty($applicationID)) continue;

            $this->dao->update(TABLE_BUG)->set('applicationID')->eq($applicationID)->where('id')->eq($bug->id)->exec();
        }

        echo '处理了' . count($bugs) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processAppByBug', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    /* 测试用例根据所属产品关联的所属系统进行更新。 */
    public function processAppByTestcase($begin = 0, $end = 100)
    {
        $cases = $this->dao->query("SELECT * FROM `zt_case` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            echo '所有测试用例处理完毕，无数据可执行！';
            die();
        }

        foreach($cases as $object)
        {
            $applicationID = $this->dao->select('app')->from(TABLE_PRODUCT)->where('id')->eq($object->product)->fetch('app');
            $applicationID = (int)$applicationID;

            // 对象如果有所属应用系统，但所属产品未查到所属应用系统，可能是历史数据执行过。
            if(!empty($object->applicationID) and empty($applicationID)) continue;

            $this->dao->update(TABLE_CASE)->set('applicationID')->eq($applicationID)->where('id')->eq($object->id)->exec();
        }

        echo '处理了' . count($cases) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processAppByTestcase', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    /* 套件根据所属产品关联的所属系统进行更新。 */
    public function processAppByTestsuite($begin = 0, $end = 100)
    {
        $cases = $this->dao->query("SELECT * FROM `zt_testsuite` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            echo '所有套件处理完毕，无数据可执行！';
            die();
        }

        foreach($cases as $object)
        {
            $applicationID = $this->dao->select('app')->from(TABLE_PRODUCT)->where('id')->eq($object->product)->fetch('app');
            $applicationID = (int)$applicationID;

            // 对象如果有所属应用系统，但所属产品未查到所属应用系统，可能是历史数据执行过。
            if(!empty($object->applicationID) and empty($applicationID)) continue;

            $this->dao->update(TABLE_TESTSUITE)->set('applicationID')->eq($applicationID)->where('id')->eq($object->id)->exec();
        }

        echo '处理了' . count($cases) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processAppByTestsuite', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    /* 测试单根据所属产品关联的所属系统进行更新。 */
    public function processAppByTesttask($begin = 0, $end = 100)
    {
        $cases = $this->dao->query("SELECT * FROM `zt_testtask` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            echo '测试报告处理完毕，无数据可执行！';
            die();
        }

        foreach($cases as $object)
        {
            $applicationID = $this->dao->select('app')->from(TABLE_PRODUCT)->where('id')->eq($object->product)->fetch('app');
            $applicationID = (int)$applicationID;

            // 对象如果有所属应用系统，但所属产品未查到所属应用系统，可能是历史数据执行过。
            if(!empty($object->applicationID) and empty($applicationID)) continue;

            $this->dao->update(TABLE_TESTTASK)->set('applicationID')->eq($applicationID)->where('id')->eq($object->id)->exec();
        }

        echo '处理了' . count($cases) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processAppByTesttask', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    /* 测试报告根据所属产品关联的所属系统进行更新。 */
    public function processAppByTestReport($begin = 0, $end = 100)
    {
        $cases = $this->dao->query("SELECT * FROM `zt_testreport` oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            echo '测试报告处理完毕，无数据可执行！';
            die();
        }

        foreach($cases as $object)
        {
            $applicationID = $this->dao->select('app')->from(TABLE_PRODUCT)->where('id')->eq($object->product)->fetch('app');
            $applicationID = (int)$applicationID;

            // 对象如果有所属应用系统，但所属产品未查到所属应用系统，可能是历史数据执行过。
            if(!empty($object->applicationID) and empty($applicationID)) continue;

            $this->dao->update(TABLE_TESTREPORT)->set('applicationID')->eq($applicationID)->where('id')->eq($object->id)->exec();
        }

        echo '处理了' . count($cases) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processAppByTestReport', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    /* 零用例需求更换了模块，需要更新权限。 */
    public function processZeroStory()
    {
        $groupIdList = $this->dao->query("SELECT `group` FROM `zt_grouppriv` where module = 'story' and method = 'zerocase' group By `group`")->fetchAll();
        foreach($groupIdList as $group)
        {
            $this->dao->delete()->from(TABLE_GROUPPRIV)->where('module')->eq('story')->andWhere('method')->eq('zerocase')->exec();
            $data = new stdClass();
            $data->group  = $group->group;
            $data->module = 'testcase';
            $data->method = 'zerocase';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();
        }

        echo '零用例需求权限处理完毕，无数据可执行！';
        die();
    }

    /**
     * 创建一级节点
     */
    public function createOneStage(){
        $projectIds = array('193','113','112','111','110','109','108','107','106','105','104'); //年度计划
        $this->app->loadLang('task');
        foreach ($projectIds as $projectId) {
           // if($projectId == '104') {
                //查询项目id
                $id = $this->dao->select('t1.id')->from(TABLE_PROJECT)->alias('t1')->leftJoin(TABLE_PROJECTPLAN)->alias('t2')->on('t1.id = t2.project')->where('t2.id')->eq($projectId)->fetch();
                if ($id) {
                    $this->stage($id->id, $this->lang->task->stageList['sendyf']); //二线研发
                    $this->stage($id->id, $this->lang->task->stageList['sendgd']); //二线工单
                }
           // }
        }
        echo '处理了' . count($projectIds) . '条项目,一级阶段创建成功;';
    }
    public function  stage($id,$name){
        //创建阶段
        $execution = new stdClass();
        $execution->project      = $id;
        $execution->parent       = '0';
        $execution->name         = $name;
        $execution->type         = 'stage';
        $execution->resource     = '';
        $execution->begin        = $this->lang->task->begintime ;
        $execution->end          = $this->lang->task->endtime ;
        $execution->planDuration = helper::diffDate3($execution->end ,$execution->begin);
        $execution->grade        = '1';
        $execution->openedBy     = 'admin';
        $execution->openedDate   = helper::today();
        $execution->status       = 'wait';
        $execution->milestone    = 0;
        $execution->version      = 1;
        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
        $executionID = $this->dao->lastInsertID();

        //记录到版本库
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->version      = 1;
        $spec->name         = $execution->name;
        $spec->milestone    = $execution->milestone;
        $spec->begin        = $execution->begin;
        $spec->end          = $execution->end;
        $spec->planDuration = $execution->planDuration;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

        $path =  $id ;
        $order = $executionID * 5;
        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();
    }

     /**
         * 重置问题池状态
         */
        public function problemUpdateStatus()
        {
            //待开发状态 退回待分析
            $problem = $this->dao->select('id,status,acceptUser')
                ->from(TABLE_PROBLEM)
                ->where('status')->eq('feedbacked')
                ->fetchAll();
            $count = 0;
            foreach ($problem as $value)
            {

                $this->dao->update(TABLE_PROBLEM)->set('status')->eq('assigned')->where('id')->eq($value->id)->exec();
                $this->loadModel('action')->create('problem', $value->id, 'deal', '由于产品升级,制版申请挪到【项目管理】-【制版】,需要重新分析');
                echo 'id :'.$value->id."<br>";

                $count++;
            }
            echo '共处理' . $count . '条问题池状态是待开发数据！'."<br>"."执行结束！";
        }

        /**
         * 重置需求池状态
         */
        public function demandUpdateStatus()
        {
            //待开发状态 退回待分析
            $demand = $this->dao->select('id,status,acceptUser')
                ->from(TABLE_DEMAND)
                ->where('status')->eq('feedbacked')
                ->fetchAll();
            $count = 0;
            foreach ($demand as $value)
            {

                $this->dao->update(TABLE_DEMAND)->set('status')->eq('assigned')->where('id')->eq($value->id)->exec();
                $this->loadModel('action')->create('demand', $value->id, 'deal', '由于产品升级,制版申请挪到【项目管理】-【制版】,需要重新分析');
                echo 'id :'.$value->id."<br>";

                $count++;
            }
            echo '共处理' . $count . '条需求池状态是待开发数据！'."<br>"."执行结束！";
        }
 /**
     * 根据问题池需求池生成制版
     */
    public function createProblemBuild($id = 0)
    {
      //1、通过单号ID查询阶段
        $problemres = $this->dao->select('id,execution,acceptDept,status')
            ->from(TABLE_PROBLEM)
            ->beginIF($id == 0)->where("(status in ( 'solved','build','testsuccess' ) or (status = 'waitverify' and  systemverify = '1'))" )->fi()
            ->beginIF($id != 0)->where('id')->eq($id)->fi()
            ->fetchAll();

        $count = 0;
        foreach ($problemres as $problem) {
            //2、通过单号、类型、阶段查询任务ID
            $taskid = $this->dao->select('taskid')
                ->from(TABLE_TASK_DEMAND_PROBLEM)
                ->where('typeid')->eq($problem->id)
                ->andWhere('type')->eq('problem')
                ->andWhere('execution')->eq($problem->execution)
                ->fetch();
            //3、根据任务ID查询任务名称
            $task = $this->dao->select('name')
                ->from(TABLE_TASK)
                ->where('id')->eq($taskid->taskid)
                ->fetch();
            $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t2.parent')->from(TABLE_EXECUTION)->alias('t1')
                ->leftJoin(TABLE_EXECUTION)->alias('t2')
                ->on('t1.id = t2.parent')
                ->where('t1.deleted')->eq('0')
                ->andWhere('t2.id')->in($problem->execution)
                ->fetch();
            //4、根据受理部门查询CM
            $cm = $this->dao->select('cm')
                ->from(TABLE_DEPT)
                ->where('id')->eq($problem->acceptDept)
                ->fetch();
            $cmname = current(array_filter(explode(',',$cm->cm)));
            $arraystatus = array(
                'solved'    => 'build',//待制版
                'build'     => 'waittest', //待测试
                'waitverify'  => 'waitverify',// 待验版
                'testsuccess'  => 'testsuccess', //待验证
            );
            $status = $arraystatus[$problem->status];
            $taskname = $executions->name .'/'.$task->name;
            //5、查询问题池表，将相关数据插入制版表
            $sql = "INSERT INTO  zt_build(project,`createdDate`,`createdBy`,`scmPath`,svnPath,filePath,status,dealuser,execution,taskid,taskName,product,`version`,purpose,rounds,cm,systemverify,verifyUser,testUser,problemid,`name`,builder)
        SELECT projectPlan,SYSDATE(),acceptUser,plateMakAp,plateMakAp,plateMakInfo,'$status',dealUser,execution,'$taskid->taskid','$taskname',product,productPlan,'1','1','$cmname',systemverify,verifyperson,laboratorytest,code,CONCAT_WS('',code,'制版申请'),'$cmname'  FROM zt_problem  WHERE id = '$problem->id'";

            $this->dao->query($sql);
            a($this->dao->printSQL()."<br>");
            $lastid = $this->dao->lastInsertId();
            if(!dao::isError()){
                echo '问题池id: ' . $id . '已创建制版,制版id :'.$lastid."<br>";
            }else{
                echo dao::getError();
            }
            $count ++;
        }
        echo '总共: ' . $count . '个问题池单子已创建制版'."<br>";

    }
    /**
     * 根据需求池需求池生成制版
     */
    public function createDemandBuild($id = 0)
    {
        //1、通过单号ID查询阶段
        $demandres = $this->dao->select('id,execution,acceptDept,status')
            ->from(TABLE_DEMAND)
            ->beginIF($id == 0)->where("(status in ( 'solved','build','testsuccess' ) or (status = 'waitverify' and  systemverify = '1'))" )->fi()
            ->beginIF($id != 0)->where('id')->eq($id)->fi()
            ->fetchAll();
        $count = 0;
        foreach ($demandres as $demand) {
        //2、通过单号、类型、阶段查询任务ID
        $taskid = $this->dao->select('taskid')
            ->from(TABLE_TASK_DEMAND_PROBLEM)
            ->where('typeid')->eq($demand->id)
            ->andWhere('type')->eq('demand')
            ->andWhere('execution')->eq($demand->execution)
            ->fetch();
        //3、根据任务ID查询任务名称
        $task = $this->dao->select('name')
            ->from(TABLE_TASK)
            ->where('id')->eq($taskid->taskid)
            ->fetch();
        $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t2.parent')->from(TABLE_EXECUTION)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')
            ->on('t1.id = t2.parent')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.id')->in($demand->execution)
            ->fetch();
        //4、根据受理部门查询CM
        $cm = $this->dao->select('cm')
            ->from(TABLE_DEPT)
            ->where('id')->eq($demand->acceptDept)
            ->fetch();
        $cmname = current(array_filter(explode(',',$cm->cm)));
        $arraystatus = array(

            'solved'    => 'build',//待制版
            'build'     => 'waittest', //待测试
            'waitverify'  => 'waitverify',// 待验版
            'testsuccess'  => 'testsuccess', //待验证
        );
        $status = $arraystatus[$demand->status];
        $taskname = $executions->name .'/'.$task->name;
        //5、查询问题池表，将相关数据插入制版表
        $sql = "INSERT INTO  zt_build(project,`createdDate`,`createdBy`,`scmPath`,svnPath,filePath,status,dealuser,execution,taskid,taskName,product,`version`,purpose,rounds,cm,systemverify,verifyUser,testUser,demandid,`name`,builder)
        SELECT project,SYSDATE(),acceptUser,plateMakAp,plateMakAp,plateMakInfo,'$status',dealUser,execution,'$taskid->taskid','$taskname',product,productPlan,'1','1','$cmname',systemverify,verifyperson,laboratorytest,code,CONCAT_WS('',code,'制版申请'),'$cmname'  FROM zt_demand  WHERE id = '$demand->id'";
        $this->dao->query($sql);
        a($this->dao->printSQL()."<br>");
        $lastid = $this->dao->lastInsertId();
        if(!dao::isError()){
            echo '需求池id: ' . $id . '已创建制版,制版id :'.$lastid."<br>";
        }else{
            echo dao::getError();
        }
            $count ++;
        }
        echo '总共: ' . $count . '个需求池单子已创建制版'."<br>";

    }

        /**
         * 待上线和已上线成功  待处理人置空
         */
        public function demandUpdatDealUser()
        {
            //待开发状态 退回待分析
            $demand = $this->dao->select('id,status,dealUser')
                ->from(TABLE_DEMAND)
                ->where('status')->in('delivery,onlinesuccess')
                ->fetchAll();
            $count = 0;
            foreach ($demand as $value)
            {

                 $this->dao->update(TABLE_DEMAND)->set('dealUser')->eq('')->where('id')->eq($value->id)->exec();
                 echo 'id :'.$value->id."<br>";

                $count++;
            }
            echo '共处理' . $count . '条需求池数据！'."<br>"."执行结束！";
        }

        /**
         * 待上线和已上线成功  待处理人置空
         */
        public function pronblemUpdatDealUser()
        {
            //待开发状态 退回待分析
            $demand = $this->dao->select('id,status,dealUser')
                ->from(TABLE_PROBLEM)
                ->where('status')->in('delivery,onlinesuccess')
                ->fetchAll();
            $count = 0;
            foreach ($demand as $value)
            {

                $this->dao->update(TABLE_PROBLEM)->set('dealUser')->eq('')->where('id')->eq($value->id)->exec();
                echo 'id :'.$value->id."<br>";

                $count++;
            }
            echo '共处理' . $count . '条问题池数据！'."<br>"."执行结束！";
        }

       /**
        * 回填制版应用系统
        */
       public function insertBuildApp()
       {
           //查询制版
           $buildres = $this->dao->select('id,name,app,demandid,problemid,taskname')
               ->from(TABLE_BUILD)
               ->where('app')->isNull()
               ->andWhere('deleted')->eq('0')
               ->andWhere('name')->like('%制版申请')
               ->fetchAll();
           $count = 0;
           foreach ($buildres as $buildre) {
               $demandid = '';
               $problemid = '';
               if(strpos($buildre->name,'-D') !== false){
                   $table = TABLE_DEMAND;
                   $code = str_replace('制版申请','',$buildre->name);
               }elseif (strpos($buildre->name,'-Q') !== false){
                   $table = TABLE_PROBLEM;
                   $code = str_replace('制版申请','',$buildre->name);
               }
               $taskname = trim( trim( strrchr($buildre->taskname,'['),']'),'[');//所属任务
               $taskname = explode(',',$taskname);
               foreach ($taskname as $item) {
                    if(strpos($item,'D') !== false){
                        $demandid .= $item .',';
                    }elseif (strpos($item,'Q') !== false){
                        $problemid .= $item .',';
                    }
                }

               $app = $this->dao->select('app')
                   ->from($table)
                   ->where('code')->eq($code)
                   ->andWhere('status')->ne('deleted')
                   ->fetch();
               $data = new stdClass();
               $data->app = $app->app;
               $data->demandid = $demandid;
               $data->problemid = $problemid;
               //更新
               $this->dao->update(TABLE_BUILD)->data($data)->where('id')->eq($buildre->id)->exec();
               echo '制版id: ' . $buildre->id . '应用系统已更新'."<br>";
               $count++;
           }
           echo '已更新: ' . $count . '个制版应用系统'."<br>";
       }

    /**
     * 更新需求池所属项目
     */
    public function updateDemandProject()
    {
        $demandres = $this->dao->select('id,project')
            ->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('project')->ne('')
            ->orderBy('id desc')
            ->fetchAll();
        $count = 0;
        foreach ($demandres as $demand) {
           if($demand->project) {
               $projectplan = $this->dao->select('id,project,name')
                   ->from(TABLE_PROJECTPLAN)
                   ->where('id')->eq($demand->project)
                   ->andWhere('deleted')->eq('0')
                   ->fetch();
               $project = $this->dao->select('id,project,name')
                   ->from(TABLE_PROJECTPLAN)
                   ->where('project')->eq($demand->project)
                   ->andWhere('deleted')->eq('0')
                   ->fetch();
               $plan    = $this->loadModel('project')->getPairs();
               $projectname = zget($plan,$demand->project,'');
               if($projectplan && $project){
                  /* a($projectplan->name.' : '.$projectname);
                   a($project->name.' : '.$projectname);*/
                   if(($projectplan->name == $projectname) || $project->name == $projectname ) continue;
                   //更新
                   $this->dao->update(TABLE_DEMAND)->set('project')->eq($project->project)->where('id')->eq($demand->id)->exec();
                  // a($this->dao->printSQL());
                   echo '需求id: ' . $demand->id . '所属项目已更新'."<br>";
                   $count++;
               }
           }
        }
        echo '已更新: ' . $count . '个需求所属项目'."<br>";
    }

    /**
     * 结算需求反馈日均值
     */
    public function demandCollectionAvgFeedBackDate(){
        $sql = "select id, feedbackDate, handoverDate from zt_demandcollection where 1 and feedbackDate is not null and handoverDate is not null";
        $data = $this->dao->query($sql)->fetchAll();
        if(!$data){
            echo '没有符合条件的数据';
            exit();
        }
        $count = count($data);
        $dayCount = 0;
        foreach ($data as $val){
            $feedbackDate = $val->feedbackDate;
            $handoverDate = $val->handoverDate;
            $feedbackDay = substr($feedbackDate, 0, 10);
            $handoverDay = substr($handoverDate, 0, 10);
            if($feedbackDay == $handoverDay){
                $diffTime = strtotime($feedbackDate) - strtotime($handoverDate);
                $diffWorkDay = bcdiv($diffTime, 86400, 2);
            }else{
                $diffWorkDay = helper::diffDate2($handoverDate, $feedbackDate) - 1;
            }
            $dayCount += $diffWorkDay;
        }
        $avgFeedBackDate = bcdiv($dayCount, $count, 2);
        echo '反馈日-移交日期总的工作日差：'.$dayCount;
        echo '<br/>';
        echo '需求搜集数：'.$count;
        echo '<br/>';
        echo '反馈日期均值：'.$avgFeedBackDate;
        exit();

    }

  /**
     * 更新需求 问题 二线实现且所属项目为空的
     */
    public function updateProjectPlan(){
        $type = array('problem','demand');
        $start = '2021-12-16';
        foreach ($type as $item) {
            $count = 0;
            $objectIDs = $this->dao->select('distinct(objectID) id')->from(TABLE_CONSUMED)
                ->where('objectType')->in($item)
                ->andWhere('deleted')->eq(0)
                ->andWhere('createdDate')->ge($start)
                ->andWhere('extra')->ne('problemFeedBack')
                ->orderBy('objectID asc')
                ->fetchAll();
            $ids = array_column($objectIDs, 'id');
            $table = $item == 'problem' ? TABLE_PROBLEM : TABLE_DEMAND;
            $status = $item == 'problem' ? 'confirmed,assigned,deleted' : 'wait,assigned,deleted';
            $project = $item == 'problem' ? 'projectPlan' : 'project';
            //查询问题池或需求池符合的状态
            $res = $this->dao->select("id,$project,dealUser,acceptDept")->from($table)
                ->where('id')->in($ids)
                ->andWhere('status')->notin($status)
                ->andWhere('fixType')->eq('second')
              //  ->andWhere('(acceptDept is null or acceptDept = "")')
                ->andWhere('acceptDept')->ne('')
                ->andWhere("($project = '0' or $project = '')")
                ->andWhere('execution')->isNull()
                ->fetchAll();
            foreach ($res as $re){
                $projectid = $this->dao->select("id,project,bearDept")->from(TABLE_PROJECTPLAN)
                    ->where('bearDept')->eq($re->acceptDept)
                    ->andWhere('name')->like("%二线管理%")
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                if($projectid){
                    $this->dao->update($table)->set($project)->eq($projectid->project)->where('id')->eq($re->id)->exec();
                    echo '已更新: id : ' .$re->id . ' 的'.$item.'所属项目'."<br>";
                    $count ++;
                }


            }
            echo '已更新: ' . $count . '个'.$item.'所属项目'."<br>";
        }
    }

    /**
     * 需求池 问题池 二线实现 工时同步并自动创建任务
     */
    public function consumedToProblemTask(){

        echo '开始处理'."<br>";
        $type = array('problem');
        $start = '2021-12-16';
        //查询工时表中符合 创建时间 >= 2021-12-16的
        foreach ($type as $item) {
            $count = 0;
            $objectIDs = $this->dao->select('distinct(objectID) id')->from(TABLE_CONSUMED)
                ->where('objectType')->in($item)
                ->andWhere('deleted')->eq(0)
                ->andWhere('createdDate')->ge($start)

                ->andWhere('extra')->ne('problemFeedBack')
                ->orderBy('objectID asc')
                ->fetchAll();

            $ids = array_column($objectIDs,'id');
            $table = $item == 'problem' ? TABLE_PROBLEM : TABLE_DEMAND;
            $status = $item == 'problem' ? 'confirmed,assigned,deleted' : 'wait,assigned,deleted';
            $project = $item == 'problem' ? 'projectPlan' : 'project';
            //查询问题池或需求池符合的状态
            $res = $this->dao->select("id,app,code,$project,execution,dealUser,status,product,productPlan")->from($table)
                ->where('id')->in($ids)
                ->andWhere('status')->notin($status)
                ->andWhere('fixType')->eq('second')
                ->andWhere($project)->ne(0)
               ->andWhere($project)->in('1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,12292,12293,12294')//正式
              // ->andWhere($project)->in('1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,10489,10490,10491')//测试
                // ->andWhere($project)->in('1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,87917,87918,87919')//开发
                ->andWhere('app')->ne('')
                ->andWhere('execution')->isNull()
                ->fetchAll();
            foreach ($res as $re) {
              if($item == 'demand' && $re->product == '8'){
                $re->product = 0; //产品
              }
              $task =   $this->toTaskProblemDemand($re,$re->id,$item);//插入中间表
              if($task){
                  $project = $item == 'problem' ? $re->projectPlan : $re->project;
                  $this->createAndCheckStage($project,$re->app,$re,$item,$task);//生成任务
                  $this-> autoCreateEffort($task,$re->id,$start,$item); //插入工时表
                  echo '已生成: id : ' .$re->id . ' 的'.$item.'任务工时'."<br>";
                  $count++;

              }
            }
            echo '<br>';
            echo '已同步: ' . $count . '个'.$item.'个任务工时'."<br>";

        }
    }

 /**
     * 需求池 问题池 二线实现 工时同步并自动创建任务
     */
    public function consumedToDemandTask(){

        echo '开始处理'."<br>";
        $type = array('demand');
        $start = '2021-12-16';
        //查询工时表中符合 创建时间 >= 2021-12-16的
        foreach ($type as $item) {
            $count = 0;
            $objectIDs = $this->dao->select('distinct(objectID) id')->from(TABLE_CONSUMED)
                ->where('objectType')->in($item)
                ->andWhere('deleted')->eq(0)
                ->andWhere('createdDate')->ge($start)
                ->andWhere('extra')->ne('problemFeedBack')
                ->orderBy('objectID asc')
                ->fetchAll();

            $ids = array_column($objectIDs,'id');
            $table = $item == 'problem' ? TABLE_PROBLEM : TABLE_DEMAND;
            $status = $item == 'problem' ? 'confirmed,assigned,deleted' : 'wait,assigned,deleted';
            $project = $item == 'problem' ? 'projectPlan' : 'project';
            //查询问题池或需求池符合的状态
            $res = $this->dao->select("id,app,code,$project,execution,dealUser,status,product,productPlan")->from($table)
                ->where('id')->in($ids)
                ->andWhere('status')->notin($status)
                ->andWhere('fixType')->eq('second')
                ->andWhere($project)->ne(0)
               ->andWhere($project)->in('1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,12292,12293,12294')//正式
              // ->andWhere($project)->in('1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,10489,10490,10491')//测试
                // ->andWhere($project)->in('1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,87917,87918,87919')//开发
                ->andWhere('app')->ne('')
                ->andWhere('execution')->isNull()
                ->fetchAll();
            foreach ($res as $re) {
              if($item == 'demand' && $re->product == '8'){
                $re->product = 0; //产品
              }
              $task =   $this->toTaskProblemDemand($re,$re->id,$item);//插入中间表
              if($task){
                  $project = $item == 'problem' ? $re->projectPlan : $re->project;
                  $this->createAndCheckStage($project,$re->app,$re,$item,$task);//生成任务
                  $this-> autoCreateEffort($task,$re->id,$start,$item); //插入工时表
                  echo '已生成: id : ' .$re->id . ' 的'.$item.'任务工时'."<br>";
                  $count++;

              }
            }
            echo '<br>';
            echo '已同步: ' . $count . '个'.$item.'个任务工时'."<br>";

        }
    }

    /**
     * 自动生成任务工时
     * @param $task
     * @param $id
     * @param $start
     */
    public function autoCreateEffort($taskid,$id,$start,$type){

        $taskdemandid = $this->dao->select('taskid')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->eq($taskid)->fetch();
        $consumeds = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($id)
            ->andWhere('objectType')->eq($type)
            ->andWhere('deleted')->eq(0)
            ->andWhere('extra')->ne('problemFeedBack')
            ->andWhere('createdDate')->ge($start)
            ->fetchAll();
        $consumed = $this->dao->select('SUM(consumed) c')->from(TABLE_EFFORT)->where('objectType')->eq('task')
            ->andWhere('objectID')->eq($taskdemandid->taskid)
            ->andWhere('deleted')->eq(0)
            ->fetch('c');
        $task     = $this->loadModel('task')->getById($taskdemandid->taskid);
        $depts    = $this->dao->select('account, dept')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();
        $left     = $task->estimate ;
        $leftcon = $task->left ;
        //$progress = $task->progress;
        $work  =  ($type == 'problem' ? "处理问题单" : "处理需求条目")."(历史工作量迁移)";
        foreach($consumeds as $effort)
        {
            $this->dao->insert(TABLE_EFFORT)->set('objectType')->eq('task')
                ->set('objectID')->eq($taskdemandid->taskid)
                ->set('product')->eq(',,')
                ->set('project')->eq($task->project)
                ->set('execution')->eq($task->execution)
                ->set('account')->eq( $effort->account )
                ->set('deptID')->eq($depts[$effort->account])
                ->set('source')->eq(1)
                ->set('buildID')->eq( 0)
                ->set('consumedID')->eq($effort->id )
                ->set('work')->eq($work)
                ->set('date')->eq( date('Y-m-d',strtotime($effort->createdDate)))
                ->set('realDate')->eq($effort->createdDate)
                ->set('left')->eq($leftcon - $effort->consumed)
                ->set('consumed')->eq($effort->consumed)
                ->exec();
            $consumed += $effort->consumed;
            $left     =$leftcon - $effort->consumed;
        }
        $this->dao->update(TABLE_TASK)->set('consumed')->eq($consumed)->set('left')->eq($left)->where('id')->eq($taskdemandid->taskid)->exec();
        $this->loadMOdel('task')->computeConsumed($taskdemandid->taskid);
    }


    /**
     * 相关信息保存任务问题关联表
     * @param $data
     * @param $id
     * @param $type
     * @return mixed
     */
    public function toTaskProblemDemand($data,$id,$type){
        //查询是否存在
        $res = $this->dao->select('id')->from(TABLE_TASK_DEMAND_PROBLEM)
            ->where('typeid')->eq($id)->andWhere('deleted')->eq(0)
            ->andWhere('type')->eq($type)
            ->fetchAll();
        //存在删除
        if($res){
            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)
                ->set('deleted')->eq(1)
                ->where('typeid')->eq($id)->andWhere('type')->eq($type)->exec();
        }
        //新增
        $task = new stdClass();
        $task->product = $data->product; //产品
        $task->project = $type == 'problem' ? $data->projectPlan : $data->project; //项目
        $task->application = trim($data->app,','); //应用系统
        $task->version = $data->productPlan; //产品版本
        $task->execution = $data->execution ? $data->execution : '0' ; //所属阶段
        $task->code = $data->code;//单号
        $task->typeid = $id;//id
        $task->assignTo = trim($data->dealUser,'');//指派给
        $task->type = $type;//类型（问题、需求、二线工单）
        $task->createdDate = date('Y-m-d H:i:s');//创建时间
        $this->dao->insert(TABLE_TASK_DEMAND_PROBLEM)->data($task)->autoCheck()->exec();
        $taskID = $this->dao->lastInsertId();
        return $taskID;

    }
    public function getapplicationNameCodePairs()
     {
         return $this->dao->select('id,concat(concat(code,"_"),name)')->from(TABLE_APPLICATION)
             ->orderBy('id_desc')
             ->fetchPairs();
     }
    /**
     * 创建阶段和任务（二线研发管理）
     * @param $projectID
     * @param $app
     * @param $data
     * @param $name
     */
    public function createAndCheckStage($projectID,$app,$data,$type,$taskdemndid)
    {
        $firsetname = '二线研发管理';
        //查询一级阶段
        $stageres = $this->dao->select('id,path,parent')->from(TABLE_EXECUTION)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(1)
            ->andWhere('name')->eq($firsetname)
            ->andWhere('type')->eq('stage')
            ->andWhere('deleted')->eq(0)
            ->fetch();

        if (!$stageres) {
            //一级阶段不存在 ，创建
            $this->autoCreateStage($projectID, $firsetname, 1, 0, $app,  $data,$type,$taskdemndid);
        }else{
            //一级阶段存在
            //查询二级阶段
           // $appcode =  $this->loadModel('application')->getapplicationNameCodePairs();
            $appcode = $this->getapplicationNameCodePairs();
            $apps = array_filter(explode(',',$app));
          //  asort($apps);
            foreach ($apps as $ap) {
                $nowapp = zget($appcode,$ap,'');
                $stagetwo = $this->dao->select('id,path')->from(TABLE_EXECUTION)->where('project')->eq($projectID)
                    ->andWhere('grade')->eq(2)
                    ->andWhere('name')->eq($nowapp)
                    ->andWhere('type')->eq('stage')
                    ->andWhere('path')->like("$stageres->path%")
                    ->andWhere('deleted')->eq(0)
                    ->fetch();

                if($stagetwo){
                    $appid = $this->dao->select('application')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->eq($taskdemndid)->fetch();
                    if($appid->application != $ap){
                        $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('application')->eq($ap)->where('id')->eq($taskdemndid)->exec();
                    }
                    break;
                }
            }

            if($stagetwo){
                //查询三级任务

                $threename = $type == 'problem' ? '历史问题' : '历史需求条目';
                $taskthree = $this->dao->select('id,path,execution')->from(TABLE_TASK)->where('project')->eq($projectID)
                    ->andWhere('grade')->eq(1)
                    ->andWhere('name')->eq($threename)
                    ->andWhere('execution')->eq($stagetwo->id)
                    ->andWhere('type')->eq('devel')
                    ->andWhere('parent')->eq(0)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                    $code = $data->code;
                    if(count($apps) > 1){
                        $applicatid = $this->dao->select('application')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->eq($taskdemndid)->fetch();
                        $appsid = $applicatid->application;
                    }else{
                        $appsid = current($apps);
                    }
                    if($taskthree){
                        //查询四级
                        $this->updateFourTask($code,$projectID,$data,$taskthree,$taskthree->execution,$appsid);//更新四级任务
                    }else{
                        //创建三级
                        $nextName = $code;//四级任务
                        $executionid = $stagetwo->id;//所属阶段
                        $this->newTaskThreeObject($threename,$nextName,$executionid,$projectID,$data,$appsid);
                    }
            }else{
                //二级阶段不存在 ，创建
                $nowapp = zget($appcode,current($apps),'');
                $appid = $this->dao->select('application')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->eq($taskdemndid)->fetch();
                if($appid->application != current($apps)){
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('application')->eq(current($apps))->where('id')->eq($taskdemndid)->exec();
                }
                $this->autoCreateStage($projectID,$nowapp,2,$stageres,$data->app,$data,$type,$taskdemndid);
            }
        }
    }
    /**
     * 创建一二级阶段（二线研发管理）
     * @param $projectID
     * @param $name
     * @param $grade
     * @param $stageres
     */
    public function autoCreateStage($projectID,$name,$grade,$stageres,$app,$data,$type,$taskdemndid){
        $execution = new stdClass();
        $execution->project      = $projectID;
        $execution->parent       = $stageres != '0' ? $stageres->id : $stageres;
        $execution->name         = $name;
        $execution->type         = 'stage';
        $execution->resource     = '';
        $execution->begin        = '0000-00-00';
        $execution->end          = '0000-00-00';;
        $execution->planDuration = helper::diffDate3($execution->end , $execution->begin);
        $execution->grade        = $grade;
        $execution->openedBy     = 'admin';
        $execution->openedDate   = helper::today();
        $execution->status       = 'wait';
        $execution->milestone    = 0;
        $execution->version      = 1;

        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
        $executionID = $this->dao->lastInsertID();

        //记录到版本库
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->version      = 1;
        $spec->name         = $execution->name;
        $spec->milestone    = $execution->milestone;
        $spec->begin        = $execution->begin;
        $spec->end          = $execution->end;
        $spec->planDuration = $execution->planDuration;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

        $path = ($grade == 1 ? ',' . $projectID . ',' : ($stageres != '0' ? $stageres->path : '')) . $executionID . ',';
        $order = $executionID * 5;
        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();
        $this->createAndCheckStage($projectID,$app,$data,$type,$taskdemndid);

    }

    /**
     * 新增三级任务
     * @param $taskname
     * @param $stagename
     * @param $projectID
     * @param $stage
     * @param $data
     */
    public function newTaskThreeObject($threeName,$nextName,$execution,$projectID,$data,$app){
        unset($_POST);
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type']      = 'devel';// 任务类型
        $_POST['module']    =  0;//所属模块
        $_POST['assignedTo']     = array('0'=>'');//指派给
        $_POST['mailto']    = array('0'=>'');//$data->mailto;
        $_POST['name']      =  $threeName;// 任务名称
        $_POST['estStarted'] = '0000-00-00';//预计开始
        $_POST['deadline']   = '0000-00-00'; //预计结束
        $_POST['openedBy']   = $this->app->user->account; //由谁创建
        $_POST['openedDate'] = date('Y-m-d'); //创建时间
        $_POST['pri']    = 1;//优先级
        $_POST['status'] = 'wait';//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['design'] = 0;
        $_POST['color'] = '';
        $_POST['dropType']  = '0';

        $taskID = $this->loadModel('task')->newTask($projectID,$execution,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0);
        $this->dao->update(TABLE_TASK)->set('path')->eq($taskID)->where('id')->eq($taskID)->exec();
        $three = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
        $this->updateFourTask($nextName,$projectID,$data,$three,$execution,$app);//更新四级任务
    }

    /**
     * 创建更新四级任务
     * @param $taskname
     * @param $projectID
     * @param $data
     * @param $taskthree
     * @param $stage
     * @param $type
     */
    public function updateFourTask($taskname,$projectID,$data,$taskthree,$stage,$app){
        //查询四级
        $taskfour = $this->dao->select('id,name,productVersion,status,version,lastEditedDate,dropType,estStarted,deadline')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(2)
            ->andWhere('name')->eq("[$data->code]")
            ->andWhere('execution')->eq($stage)
            ->andWhere('type')->eq('devel')
            ->andWhere('path')->like("$taskthree->path%")
            ->andWhere('deleted')->eq(0)
            ->andWhere('dropType')->eq(0)
            ->fetch();
        //查询任务-问题-需求 关联表

        $codes = $this->dao->select('group_concat(code  order by id asc) code,group_concat(assignTo  order by id asc) assignto,group_concat(id  order by id asc) id,group_concat(type  order by id asc) type')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
            ->andWhere('product')->eq($data->product)
            ->andWhere('application')->eq($app)
            ->andWhere('version')->eq($data->productPlan)
           /* ->andWhere('execution')->eq($stage)*/
            ->andWhere('code')->eq($data->code)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        if(!$taskfour){
            //创建四级;
            $fourname =  '['.$codes->code.']';// 任务名称
            $taskID = $this->newTaskFourObject($stage,$fourname,$data,$projectID,$taskthree,$stage,$codes);
        }
        //更新 任务-问题-需求 关联表的taskid
        $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('taskid')->eq($taskID)->where('id')->in($codes->id)->exec();
    }

    /**
     * 新建四级
     * @param $execution
     * @param $assignedto
     * @param $name
     * @param $data
     * @param $projectID
     * @param $stage
     * @param $flag
     */
    public function newTaskFourObject($execution,$name,$data,$projectID,$taskthree,$stage,$codes){
        unset($_POST);

        $_POST['assignedTo']   = array('0'=>'');//指派给
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type']      = 'devel';// 任务类型
        $_POST['module']    =  0;//所属模块
        $_POST['mailto']   =  '';
        $_POST['name']       = $name;// 任务名称
        $_POST['estStarted'] = '0000-00-00'; //$this->lang->task->begintime;//预计开始
        $_POST['deadline']   = '0000-00-00'; //$this->lang->task->endtime; //预计结束
        $_POST['openedBy']   = $this->app->user->account; //由谁创建
        $_POST['openedDate'] = isset($data->lastDealDate) ? $data->lastDealDate : helper::today(); //创建时间
        $_POST['pri']    = 1;//优先级
        $_POST['status'] = 'done';//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['productVersion'] = $data->productPlan ?? 0;
        $_POST['parent'] = $taskthree->id;
        $_POST['dropType']  = '0';
        $_POST['progress']  = '100';
        /*$status = array('verifysuccess','delivery','closed','onlinesuccess'); //已完成
        if(in_array($data->status,$status)){
            $_POST['status'] = 'done';//状态
            $_POST['progress'] = '100';//状态
        }else{
            $_POST['status'] = 'doing';//状态
        }*/

        $taskID = $this->loadModel('task')->newTask($projectID,$stage,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0);
        if($taskID){
            $id = array_filter(explode(',',$codes->id));
            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('`execution`')->eq($execution)->where('id')->in($id)->exec();
            $flag = array_filter(explode(',',$codes->type));
            if(current($flag) == 'problem'){
                $this->dao->update(TABLE_PROBLEM)->set('`execution`')->eq($execution)->where('id')->in($data->id)->exec();
            }
            if(current($flag) == 'demand'){
                $this->dao->update(TABLE_DEMAND)->set('`execution`')->eq($execution)->where('id')->in($data->id)->exec();
            }
        }
        return $taskID;
    }

    public function fixSecondLine()
    {
        /** @var secondlineModel $secondLine */
        $secondLine =  $this->loadModel('secondline');
        $list = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->gt(0)->andwhere('deleted')->eq(0)->fetchall('id');
        foreach ($list as $outwardDelivery){
            if($outwardDelivery->problemId){
                $problemIds = explode(',', trim($outwardDelivery->problemId,','));
                $somethingWrong = 0;
                foreach ($problemIds as $problemId)
                {
                    $relate = $this->dao->select('*')->from(TABLE_SECONDLINE)
                        ->where('objectType')->eq('problem')
                        ->andWhere('objectID')->eq($problemId)
                        ->andWhere('relationType')->eq('modifycncc')
                        ->andWhere('relationId')->eq($outwardDelivery->modifycnccId)
                        ->andWhere('deleted')->eq(0)
                        ->fetchAll();
                    if(empty($relate)) $somethingWrong = 1;
                    if(empty($relate)) echo 'problem:'.$problemId .'=='. $outwardDelivery->modifycnccId .'<br>';
                }
//                if($somethingWrong) print_r([$outwardDelivery->modifycnccId, 'modifycncc', $outwardDelivery->problemId, 'problem']);die();
                if($somethingWrong) $secondLine->saveRelationship($outwardDelivery->modifycnccId, 'modifycncc', $outwardDelivery->problemId, 'problem');
            }
            if($outwardDelivery->demandId){
                $demandIds = explode(',', trim($outwardDelivery->demandId,','));
                $somethingWrong = 0;
                foreach ($demandIds as $demandId)
                {
                    $relate = $this->dao->select('*')->from(TABLE_SECONDLINE)
                        ->where('objectType')->eq('demand')
                        ->andWhere('objectID')->eq($demandId)
                        ->andWhere('relationType')->eq('modifycncc')
                        ->andWhere('relationId')->eq($outwardDelivery->modifycnccId)
                        ->andWhere('deleted')->eq(0)
                        ->fetchAll();
                    if(empty($relate)) $somethingWrong = 1;
                    if(empty($relate)) echo 'demandid:'.$demandId .'=='. $outwardDelivery->modifycnccId .'<br>';
                }
//                if($somethingWrong) print_r([$outwardDelivery->modifycnccId, 'modifycncc', $outwardDelivery->demandId, 'demand']);die();
                if($somethingWrong) $secondLine->saveRelationship($outwardDelivery->modifycnccId, 'modifycncc', $outwardDelivery->demandId, 'demand');
            }
            if($outwardDelivery->requirementId) {
                $requirementIds = explode(',', trim($outwardDelivery->requirementId,','));
                $somethingWrong = 0;
                foreach ($requirementIds as $requirementId)
                {
                    $relate = $this->dao->select('*')->from(TABLE_SECONDLINE)
                        ->where('objectType')->eq('requirement')
                        ->andWhere('objectID')->eq($requirementId)
                        ->andWhere('relationType')->eq('modifycncc')
                        ->andWhere('relationId')->eq($outwardDelivery->modifycnccId)
                        ->andWhere('deleted')->eq(0)
                        ->fetchAll();
                    if(empty($relate)) $somethingWrong = 1;
                    if(empty($relate)) echo 'requirement'.$requirementId .'=='. $outwardDelivery->modifycnccId .'<br>';
                }
                if($somethingWrong) $secondLine->saveRelationship($outwardDelivery->modifycnccId, 'modifycncc', $outwardDelivery->requirementId, 'requirement');
            }
        }
    }


    /**
     * 迭代十九新增打基线节点
     */
    public function changeAddArchiveStage(){
        $k = 0;
        $sql = "select zr.*, zc.id  as changeId, zc.status as changeStatus, zc.createdBy  as changeCreatedBy
                from zt_reviewnode zr 
                left join zt_change zc on zr.objectID = zc.id and zc.version = zr.version 
                where 1 
                and zr.objectType = 'change' 
                and zr.stage = 8 
                and zc.version = zr.version 
                and zr.nodeCode = 'baseline'";
        $data = $this->dao->query($sql)->fetchAll();
//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $endStatusArray = ['success', 'deleted', 'closing', 'closed', 'gmsuccess'];
        $stage = 8;
        $nodeCode = 'archive';
        foreach ($data as $val){
            $nodeId = $val->id;
            $changeId = $val->changeId;
            $version  = $val->version;
            $changeStatus = $val->changeStatus;
            $status         = $val->status;
            $nodeCodeStatus = 'wait';
            $isShow         = 1;
            if(in_array($changeStatus, $endStatusArray) || $status == 'ignore'){ //添加忽略节点
                $nodeCodeStatus = 'ignore';
                $isShow = 2; //忽略不显示
            }
            //新增审核节点
            $reviewNodes = array(
                array(
                    'reviewers' => array($val->changeCreatedBy),
                    'stage'     => $stage,
                    'status'    => $nodeCodeStatus,
                    'nodeCode'  => $nodeCode,
                    'isShow'   =>  $isShow,
                )
            );
            $this->loadModel('review')->submitReview($changeId, 'change', $version, $reviewNodes);
            //修改本节点排序和是否显示
            $nodeUpdateParams = new stdClass();
            $nodeUpdateParams->stage = 9;
            if($status == 'ignore'){
                $nodeUpdateParams->isShow = 2;
            }
            $this->dao->update(TABLE_REVIEWNODE)->data($nodeUpdateParams)->where('id')->eq($nodeId)->exec();
            $k++;
        }
        echo '处理了'.$k.'条数据';
    }
    /**
     * 迭代十九 项目变更主表评审人格式化
     */
    public function changeFormatReviewStage(){
        $k = 0;
        $sql = "select zc.*
                from zt_change zc 
                left join zt_reviewnode zr on zr.objectType = 'change' and  zr.objectID = zc.id 
                where 1
                and zc.reviewer like '%;%'
                and zr.id is null ";
        $data = $this->dao->query($sql)->fetchAll();
//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $this->app->loadLang('change');
        $reviewNodeCodeList = $this->lang->change->reviewNodeCodeList;
        $tempNodeCodeList = array_keys($reviewNodeCodeList);
        foreach ($data as $val){
            $changeId = $val->id;
            $level    = $val->level; //项目变更的等级
            $levelReviewNodeList = zget($this->lang->change->reviewLevelNodeCodeList, $level);
            $reviewer = $val->reviewer;
            $skipReviewNode = $val->skipReviewNode; //跳过节点
            $reviewerArray = explode(';', $reviewer);
            $reviewerArray = array_filter($reviewerArray);
            //补全信息
            $reviewerArray[8] = $reviewerArray[7];
            $reviewerArray[7] = $val->createdBy;
            ksort($reviewerArray);

            $skipReviewNodeArray = explode(';', $skipReviewNode);
            $skipReviewNodeArray = array_filter($skipReviewNodeArray);

            $newReviewers = [];
            $newSkipReviewNode = [];
            foreach ($tempNodeCodeList as $nodeKey => $nodeCode){
                if(in_array($nodeCode, $levelReviewNodeList) && !in_array($nodeKey, $skipReviewNodeArray)){
                    $currentReviewer = $reviewerArray[$nodeKey];
                    $newReviewers[$nodeCode] =  explode(',', $currentReviewer);
                }

                if(!empty($skipReviewNodeArray)){
                    if(in_array($nodeKey, $skipReviewNodeArray)){
                        $newSkipReviewNode[] = $nodeCode;
                    }
                }
            }

            // 评审人信息处理
            $updateParams = new stdClass();
            $updateParams->reviewer       = json_encode($newReviewers);
            $updateParams->skipReviewNode = implode(',', $newSkipReviewNode);
            $this->dao->update(TABLE_CHANGE)->data($updateParams)->where('id')->eq($changeId)->exec();
            $k++;
        }
        echo '处理了'.$k.'条数据';
    }


    /**
     *迭代十九 项目变更主表忽略节点格式化
     *
     * @return bool
     */
    public function changeFormatSkipReviewNode(){
        $k = 0;
        $sql = "select * from zt_change where skipReviewNode != ''";
        $data = $this->dao->query($sql)->fetchAll();
//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $this->app->loadLang('change');
        $reviewNodeCodeList = $this->lang->change->reviewNodeCodeList;
        $tempNodeCodeList = array_keys($reviewNodeCodeList);
        foreach ($data as $val){
            $changeId = $val->id;
            $skipReviewNode = $val->skipReviewNode; //跳过节点
            $skipReviewNodeArray = explode(';', $skipReviewNode);
            $skipReviewNodeArray = array_filter($skipReviewNodeArray);
            if(!empty($skipReviewNodeArray)){
                $newSkipReviewNode = [];
                foreach ($skipReviewNodeArray as $nodeKey){
                    if(isset($tempNodeCodeList[$nodeKey])){
                        $newSkipReviewNode[] = $tempNodeCodeList[$nodeKey];
                    }
                }
                if(!empty($newSkipReviewNode)){
                    // 评审人忽略节点信息处理
                    $updateParams = new stdClass();
                    $updateParams->skipReviewNode = implode(',', $newSkipReviewNode);
                    $this->dao->update(TABLE_CHANGE)->data($updateParams)->where('id')->eq($changeId)->exec();
                    $k++;
                }

            }
        }
        echo '处理了'.$k.'条数据';
    }

    /**
     *修改项目评审会议工时
     *
     *
     * @return bool
     */
    public function updateReviewMeetingRecordConsumed(){
        $sql = "select zrm.id, zrm.meetingCode, zrm.status, 
                group_concat(zr.id) as review_ids, zc.consumed 
                from zt_review_meeting zrm
                left join zt_review zr on zrm.meetingCode = zr.meetingCode
                left join zt_consumed zc on zrm.id = zc.objectID  and zc.objectType = 'reviewmeeting'
                where 1
                and zrm.deleted  = '0'
                and zrm.status in ('waitMeetingOwnerReview', 'pass')
                and zr.id != 0
                and zc.objectType = 'reviewmeeting'
                and zc.`before` = 'waitMeetingReview'
                and zc.`after` = 'waitMeetingOwnerReview'
                group by zrm.id";
        $data = $this->dao->query($sql)->fetchAll();
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $reviewMeetingIds = [];
        $reviewIds = [];
        foreach ($data as $val){
            $reviewMeetingIds[] = $val->id;
            $review_ids = explode(',', $val->review_ids);
            $reviewIds = array_merge($reviewIds, $review_ids);

        }
        $sql1 = "select zr.id as reviewId, zc.id as consumedId , zc.consumed 
                from zt_review zr 
                left join zt_consumed zc on zr.id = zc.objectID  and zc.objectType = 'review'
                where 1 
                and zr.id in (".implode(',', $reviewIds).")
                and zc.objectType = 'review'
                and zc.`before` = 'waitMeetingReview'
                and zc.`after` = 'waitMeetingOwnerReview'
                and zc.consumed = 0";
        $reviewList = $this->dao->query($sql1)->fetchAll();
        if(empty($reviewList)){
            echo '处理历史数据成功';
            return true;
        }
        $reviewList = array_column($reviewList, null, 'reviewId');
        $k = 0;
        $this->loadModel('reviewmeeting');
        foreach ($data as $val){
            $meetingConsumed = $val->consumed;
            $review_ids = explode(',', $val->review_ids);
            $consumedIds = [];
            foreach ($review_ids as $reviewId){
                $reviewInfo = zget($reviewList, $reviewId);
                if(($reviewInfo)  &&  (isset($reviewInfo->consumedId))){
                    $consumedId = $reviewInfo->consumedId;
                    $consumedIds[] = $consumedId;
                }
            }
//            echo '<pre>';
//            print_r($consumedIds);
//            echo '</pre>';
            $reviewCount =  count($consumedIds);
            if($reviewCount > 0){
                $averageConsumed = $this->reviewmeeting->getReviewAverageConsumed($meetingConsumed, $reviewCount);
                //分摊工时
                $updateParams = new stdClass();
                $updateParams->consumed = $averageConsumed;
                $this->dao->update(TABLE_CONSUMED)->data($updateParams)->where('id')->in($consumedIds)->exec();
                $k++;
            }
        }
        echo '处理了'.$k.'条数据';
    }


    /**
     *修改项目评审评审主席会议时间
     *
     *
     * @return bool
     */
    public function updateReviewMeetingOwnerRecordConsumed(){
        $sql = "select zrm.id, zrm.meetingCode, zrm.status, 
                group_concat(zr.id) as review_ids, zc.consumed 
                from zt_review_meeting zrm
                left join zt_review zr on zrm.meetingCode = zr.meetingCode
                left join zt_consumed zc on zrm.id = zc.objectID  and zc.objectType = 'reviewmeeting'
                where 1
                and zrm.deleted  = '0'
                and zrm.status = 'pass'
                and zr.id != 0
                and zc.objectType = 'reviewmeeting'
                and zc.`before` = 'waitMeetingOwnerReview'
                group by zrm.id";
        $data = $this->dao->query($sql)->fetchAll();
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $reviewMeetingIds = [];
        $reviewIds = [];
        foreach ($data as $val){
            $reviewMeetingIds[] = $val->id;
            $review_ids = explode(',', $val->review_ids);
            $reviewIds = array_merge($reviewIds, $review_ids);

        }
        $sql1 = "select zr.id as reviewId, zc.id as consumedId , zc.consumed 
                from zt_review zr 
                left join zt_consumed zc on zr.id = zc.objectID  and zc.objectType = 'review'
                where 1 
                and zr.id in (".implode(',', $reviewIds).")
                and zc.objectType = 'review'
                and zc.`before` = 'waitMeetingOwnerReview'
                and zc.consumed = 0";
        $reviewList = $this->dao->query($sql1)->fetchAll();
        if(empty($reviewList)){
            echo '处理历史数据成功';
            return true;
        }
        $reviewList = array_column($reviewList, null, 'reviewId');
        $k = 0;
        $this->loadModel('reviewmeeting');
        foreach ($data as $val){
            $meetingConsumed = $val->consumed;
            $review_ids = explode(',', $val->review_ids);
            $consumedIds = [];
            foreach ($review_ids as $reviewId){
                $reviewInfo = zget($reviewList, $reviewId);
                if(($reviewInfo)  &&  (isset($reviewInfo->consumedId))){
                    $consumedId = $reviewInfo->consumedId;
                    $consumedIds[] = $consumedId;
                }
            }
//            echo '<pre>';
//            print_r($consumedIds);
//            echo '</pre>';
            $reviewCount =  count($consumedIds);
            if($reviewCount > 0){
                $averageConsumed = $this->reviewmeeting->getReviewAverageConsumed($meetingConsumed, $reviewCount);
                //分摊工时
                $updateParams = new stdClass();
                $updateParams->consumed = $averageConsumed;
                $this->dao->update(TABLE_CONSUMED)->data($updateParams)->where('id')->in($consumedIds)->exec();
                $k++;
            }
        }
        echo '处理了'.$k.'条数据';
    }

    /**
     *修改项目评审评审开会时间
     *
     *
     * @return bool
     */
    public function updateReviewMeetingConsumed(){
        $sql = "select zrmd.review_id, zrmd.meetingCode, zrmd.consumed, 
                zr.project, zp.code , zr.version, zc.createdDate  
                from zt_review_meeting_detail zrmd  
                left join zt_review zr on zrmd.review_id = zr.id
                left join zt_project zp on zr.project =  zp.id
                left join zt_consumed zc on zc.objectType = 'review' and zc.objectID = zr.id  and zc.`before` = 'waitMeetingReview' and zc.`after` = 'waitMeetingOwnerReview'
                left join zt_consumed zc1 on zc1.objectType = 'review' and zc1.objectID = zr.id  and zc1.`before` = 'waitMeetingReview' and zc1.`after` = 'waitMeetingReview'
                where 1 
                and zrmd.deleted = '0'
                and zrmd.consumed > 0
                and zc.objectType = 'review'
                and zc.`before` = 'waitMeetingReview' 
                and zc.`after` = 'waitMeetingOwnerReview'
                and zc1.id is null 
                order by zc.id desc";
        $data = $this->dao->query($sql)->fetchAll();
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $reviewIds = [];
        $status = 'waitMeetingReview';
        $k = 0;
        foreach ($data as $val){
            $reviewId = $val->review_id;
            if(in_array($reviewId, $reviewIds)){
                continue;
            }
            $consumed = $val->consumed;
            $code        = $val->code;
            $meetingCode = $val->meetingCode;
            $userAccount = $code.'_'.$meetingCode;
            $version     = $val->version;
            $createdDateTemp = $val->createdDate;
            $createdDate = date('Y-m-d H:i:s', (strtotime($createdDateTemp) - 1));
            $this->loadModel('consumed')->record('review', $reviewId, $consumed, $userAccount, $status, $status, [], '', $version, $createdDate);
            $reviewIds[] = $reviewId;
            $k++;
        }
        echo '处理了'.$k.'条数据';

    }

    /**
     * 设置评审工时的评审阶段
     */
    public function setReviewConsumedReviewStage(){
        phpinfo();
        $total = 0;
        while (1){
            $sql = "select id, objectType, `before` , `after`, reviewStage  from  zt_consumed zc where 1 and objectType = 'review' and reviewStage = '' limit 100";
            $data = $this->dao->query($sql)->fetchAll();
            if(!$data){
               break;
            }
            foreach ($data as $val){
                $id     = $val->id;
                $before = $val->before;
                $after  = $val->after;
                $reviewStage = $this->loadModel('review')->getReviewStage($before, $after);
                if($reviewStage){
                    $this->dao->update(TABLE_CONSUMED)->set('reviewStage')->eq($reviewStage)->where('id')->eq($id)->exec();
                    $total++;
                }
            }
        }
        echo $total;
        exit();
    }

    /**
     * 修复项目发布的创建人
     *
     */
    public function fixProjectReleaseCreatedBy(){
        $total = 0;
        while (1){
            $sql = "select zr.id, zr.createdBy , za.actor  
                        from zt_release zr 
                        left join zt_action za on za.objectType = 'release' and zr.id = za.objectID and za.`action` = 'opened'
                        where 1 
                        and zr.createdBy is null and za.id > 0 limit 100";
            $data = $this->dao->query($sql)->fetchAll();
            if(!$data){
                break;
            }
            foreach ($data as $val){
                $id     = $val->id;
                $actor = $val->actor;
                $this->dao->update(TABLE_RELEASE)->set('createdBy')->eq($actor)->where('id')->eq($id)->exec();
                $total++;
            }
        }
        echo $total;
        exit();

    }

    /**
     * 修改研发一部默认跳转清总交付
     * @return void
     */
    public function fixSecondRount()
    {
        $userList = $this->loadModel('user')->getUserListByDeptId(5);
        foreach ($userList as $user){
            $item = new stdclass();
            $item->owner   = $user->account;
            $item->module  = 'common';
            $item->key     = 'secondLink';
            $item->value   = 'outwarddelivery-browse';
            $this->dao->replace(TABLE_CONFIG)->data($item)->exec();
        }
        a('执行成功');
    }

    /**
     * 修改对外交付创建者跳转清总交付
     * @return void
     */
    public function fixSecondRountByOutwarddelivery()
    {
        $userList = $this->dao->select('distinct createdBy')->from(TABLE_OUTWARDDELIVERY)->fetchAll('createdBy');
        foreach ($userList as $user){
            $item = new stdclass();
            $item->owner   = $user->createdBy;
            $item->module  = 'common';
            $item->key     = 'secondLink';
            $item->value   = 'outwarddelivery-browse';
            $this->dao->replace(TABLE_CONFIG)->data($item)->exec();
        }
        a('执行成功');
    }

    /**
     * 修改已审批节点
     * @return void
     */
    public function fixApprovedNode(){
        $this->loadModel('outwarddelivery');
        $this->app->loadLang('outwarddelivery');
        $outwarddeliveryList = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->fetchAll('id');
        foreach ($outwarddeliveryList as $outwarddelivery){
            $needStage = array();
            if($outwarddelivery->status == 'reject'){
                $lastReview = $this->dao->select('`before`')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('outwarddelivery')
                    ->andwhere('objectID')->eq($outwarddelivery->id)
                    ->andwhere('after')->eq('reject')
                    ->orderBy('id_desc')
                    ->fetch('before');
                if(in_array($lastReview, $this->lang->outwarddelivery->reviewBeforeStatusList)){
                    $stage = array_search($lastReview,$this->lang->outwarddelivery->reviewBeforeStatusList);
                    if($stage>7){
                        $needStage = array(0,1,2,3,4,5,6,7);
                    }elseif($stage == 0){
                        $needStage = array();
                    }else{
                        for($i = 0; $i<$stage; $i++){
                            array_push($needStage,$i);
                        }
                    }
                }else{
                    $needStage = array(0,1,2,3,4,5,6,7);
                }
            }elseif($outwarddelivery->status == 'waitsubmitted'){
                if($outwarddelivery->version == 1){
                    $needStage = array();
                }else{
                    $lastReview = $this->dao->select('`before`')->from(TABLE_CONSUMED)
                        ->where('objectType')->eq('outwarddelivery')
                        ->andwhere('objectID')->eq($outwarddelivery->id)
                        ->andwhere('after')->eq('reject')
                        ->orderBy('id_desc')
                        ->fetch('before');
                    if(in_array($lastReview, $this->lang->outwarddelivery->reviewBeforeStatusList)){
                        $stage = array_search($lastReview,$this->lang->outwarddelivery->reviewBeforeStatusList);
                        if($stage>7){
                            $needStage = array(0,1,2,3,4,5,6,7);
                        }elseif($stage == 0){
                            $needStage = array();
                        }else{
                            for($i = 0; $i<$stage; $i++){
                                array_push($needStage,$i);
                            }
                        }
                    }else{
                        $needStage = array(0,1,2,3,4,5,6,7);
                    }
                }
            }elseif(in_array($outwarddelivery->status, $this->lang->outwarddelivery->reviewBeforeStatusList)){
                $stage = array_search($outwarddelivery->status,$this->lang->outwarddelivery->reviewBeforeStatusList);
                if($stage>7){
                    $needStage = array(0,1,2,3,4,5,6,7);
                }elseif($stage == 0){
                    $needStage = array();
                }else{
                    for($i = 0; $i<$stage; $i++){
                        array_push($needStage,$i);
                    }
                }
            }else{
                $needStage = array(0,1,2,3,4,5,6,7);
            }
            if(!empty($needStage)){
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('approvedNode')->eq(implode(',',$needStage))->where('id')->eq($outwarddelivery->id)->exec();
            }
        }
        a("执行完成");
    }
    //修改清总知识产权自定义配置所属系统 key值
    public function editSystemKey(){
        $this->app->loadLang("copyrightqz");
        $systemList = $this->dao->select("*")->from(TABLE_LANG)->where('section')->eq('systemList')->fetchAll();
        if(!empty($systemList)){
            foreach ($systemList as $v){
                if ($v->key != ''){
                    foreach ($this->lang->copyrightqz->systemListEdit as $lk=>$lv) {
                        if ($lv == $v->value){
                            $this->dao->update(TABLE_LANG)->set('key')->eq($lk)->where('id')->eq($v->id)->exec();
                        }
                    }
                }
            }
            a('执行修改完成');
        }else {
            $data = new stdClass();
            foreach ($this->lang->copyrightqz->systemListEdit as $k=>$v) {
                $data->lang = 'zh-cn';
                $data->module = 'copyrightqz';
                $data->section = 'systemList';
                $data->key = $k;
                $data->value = $v;
                $data->system = '1';
                $this->dao->insert(TABLE_LANG)->data($data)->exec();

            }
            a('执行添加完成');

        }
    }
    //清总知识产权表 所属系统 数据更新
    public function editSystemCopyrightQz(){
        $this->app->loadLang("copyrightqz");

        $list = $this->dao->select("*")->from(TABLE_COPYRIGHTQZ)->where('`system`')->gt(0)->fetchall();
        unset($this->lang->copyrightqz->systemListEdit['']);
        $systemList = $this->lang->copyrightqz->systemList;
        foreach ($list as $k=>$v) {
            foreach ($this->lang->copyrightqz->systemListEdit as $ik=>$item) {
                if (isset($systemList[$v->system]) && $systemList[$v->system] == $item){
                    $this->dao->update(TABLE_COPYRIGHTQZ)->set('`system`')->eq($ik)->where('id')->eq($v->id)->exec();
                }
            }
        }
        a('执行完成');
    }

    /**
     * 外部同步需求的需求接收时间：需求意向、需求任务、取创建时间。需求条目取需求意向的需求接收时间
     * 内部自建需求的需求接收时间：需求意向为录入的“需求接收时间”，需求任务、需求条目取需求意向的需求接收时间
     */
    public function acceptTimeDeal()
    {
        $opinion = $this->dao->select("id,createdDate,receiveDate,createdBy")->from('zt_opinion')->where('receiveDate')->isNull()->fetchAll();
        //需求意向 内部自建一定存在，为手动填写的，只需要处理外部
        $opinionId = [];
        $requirementId = [];
        $deamandId = [];
        if($opinion){
            foreach ($opinion as $valueOpi){
                if($valueOpi->createdBy == 'guestcn'){ //清总
                    $receiveDate = $this->buildReceiveDate('opinion',$valueOpi->id,'synccreated');
                    if($receiveDate){
                        $this->dao->update(TABLE_OPINION)->set('receiveDate')->eq($receiveDate)->where('id')->eq($valueOpi->id)->exec();
                        $opinionId[] = $valueOpi->id;
                    }
                }else{
                    if($valueOpi->receiveDate == '' || $valueOpi->receiveDate == '0000-00-00 00:00:00'){
                        $receiveDateOwn = date('Y-m-d 00:00:00',strtotime($valueOpi->createdDate));
                        $this->dao->update(TABLE_OPINION)->set('receiveDate')->eq($receiveDateOwn)->where('id')->eq($valueOpi->id)->exec();
                        $opinionId[] = $valueOpi->id;
                    }
                }
            }
        }

        //需求任务
        $requirement = $this->dao->select("id,opinion,createdDate,acceptTime,createdBy")->from('zt_requirement')->where('acceptTime')->isNull()->fetchAll();
        if($requirement){
            foreach ($requirement as $valueReq){
                if($valueReq->createdBy == 'guestcn'){ //清总
                    $this->dao->update(TABLE_REQUIREMENT)->set('acceptTime')->eq($valueReq->createdDate)->where('id')->eq($valueReq->id)->exec();
                    $requirementId[] = $valueReq->id;
                }else{ //内部自建
                    $opinionByOpinionId = $this->dao->select('id,receiveDate')->from(TABLE_OPINION)->where('id')->eq($valueReq->opinion)->fetch();
                    if($opinionByOpinionId){
                        $this->dao->update(TABLE_REQUIREMENT)->set('acceptTime')->eq($opinionByOpinionId->receiveDate)->where('id')->eq($valueReq->id)->exec();
                        $requirementId[] = $valueReq->id;
                    }
                }
            }
        }

        //需求条目
        $demand = $this->dao->select("id,opinionID,createdDate,rcvDate,createdBy")->from('zt_demand')->where('rcvDate')->isNull()->fetchAll();
        if($demand){
            foreach ($demand as $valueDemand){//清总
                if($valueDemand->opinionID != 0){
                    $getByOpinionId = $this->dao->select('id,receiveDate')->from(TABLE_OPINION)->where('id')->eq($valueDemand->opinionID)->fetch();
                    if($getByOpinionId){
                        $rcvDate = date('Y-m-d',strtotime($getByOpinionId->receiveDate));
                        $this->dao->update(TABLE_DEMAND)->set('rcvDate')->eq($rcvDate)->where('id')->eq($valueDemand->id)->exec();
                        $deamandId[] = $valueDemand->id;
                    }
                }
            }
        }

        //需求条目
        $demand = $this->dao->select("id,opinionID,createdDate,rcvDate,createdBy")->from('zt_demand')->where('rcvDate')->eq('0000-00-00')->fetchAll();
        if($demand){
            foreach ($demand as $valueDemand){//清总
                if($valueDemand->opinionID != 0){
                    $getByOpinionId = $this->dao->select('id,receiveDate')->from(TABLE_OPINION)->where('id')->eq($valueDemand->opinionID)->fetch();
                    if($getByOpinionId){
                        $rcvDate = date('Y-m-d',strtotime($getByOpinionId->receiveDate));
                        $this->dao->update(TABLE_DEMAND)->set('rcvDate')->eq($rcvDate)->where('id')->eq($valueDemand->id)->exec();
                        $deamandId[] = $valueDemand->id;
                    }
                }
            }
        }
        echo '执行需求意向数据：'.count($opinionId).'条。被执行数据id:'.implode(',',$opinionId).'<br/>';
        echo '执行需求任务数据：'.count($requirementId).'条。被执行数据id:'.implode(',',$requirementId).'<br/>';
        echo '执行需求条目数据：'.count($deamandId).'条。被执行数据id:'.implode(',',$deamandId).'<br/>';
    }

    /**
     * @Notes:获取action
     * @Date: 2023/3/23
     * @Time: 17:51
     * @Interface buildReceiveDate
     * @param $objectType
     * @param $objectID
     * @param $action
     * @return string
     */
    public function buildReceiveDate($objectType,$objectID,$action)
    {
        $date = '';
        $action = $this->dao->select('date')->from(TABLE_ACTION)->where('objectType')->eq($objectType)->andWhere('objectID')->eq($objectID)->andWhere('action')->eq($action)->fetch();
        if($action){
            $date = $action->date;
        }
        return $date;

    }

    /**
     * 处里需求池解决时间为空的情况
     */
    public function dealSolvedTime(){
//        $demand = $this->dao->select(' id,status,solvedTime,createdBy,closedDate')->from(TABLE_DEMAND)->where('solvedTime')->isNull()->andWhere('status')->ne('deleted')->orderBy('id_desc')->fetchAll();
        $demand = $this->dao->select(' id,status,solvedTime,createdBy,closedDate')->from(TABLE_DEMAND)->where('status')->ne('deleted')->orderBy('id_desc')->fetchAll();
//        $demand = $this->dao->select(' id,status,solvedTime,createdBy,closedDate')->from(TABLE_DEMAND)->where('id')->eq('1197')->orderBy('id_desc')->fetchAll();
        $closedIdNoSecondArr = [];
        $closedIdNoSecondArrEmpty = [];
        $secondAboutTime = [];
        foreach ($demand as $item){
            $secondLine = $this->dao->select('*')->from(TABLE_SECONDLINE)->where('objectType')->eq('demand')->andWhere('objectID')->eq($item->id)->andWhere('deleted')->eq(0)->fetchAll();
            if(!$secondLine){
                //已关闭，未关联二线单子更新解决时间
                if($item->status == 'closed'){
                    if(empty($item->closedDate)){
                        $closedIdNoSecondArrEmpty[] = $item->id;
                    }else{
                        $closedDate = '';
                        $action = $this->dao->select('date')->from(TABLE_ACTION)->where('objectType')->eq('demand')->andWhere('objectID')->eq($item->id)->andWhere('action')->in(['closed'])->orderBy('id_desc')->fetch();
                        if($action){
                            $closedDate = $action->date;
                            $res = $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq($closedDate)->where('id')->eq($item->id)->exec();
                            if($res){
                                $closedIdNoSecondArr[] = $item->id;
                            }
                        }

                    }

                }
            }else{ //关联了二线单子
                $time1 = [];
                $time2 = [];
                $time3 = [];
                $time4 = [];
                foreach ($secondLine as $second){
                    switch ($second->relationType){
                        case 'gain'://金信-数据获取
                            $info = $this->dao->select('*')->from(TABLE_INFO)->where('id')->eq($second->relationID)->fetch();
                            if($info && $info->reviewStage > 7){
                                $time1[] = $this->getSolvedTime(7,'info',$second->relationID,$info->version);
                            }
                            break;
                        case 'modify'://金信-生产变更
                            $modify = $this->dao->select('*')->from(TABLE_MODIFY)->where('id')->eq($second->relationID)->fetch();
                            if($modify && $modify->reviewStage > 7){
                                $time2[] = $this->getSolvedTime(7,'modify',$second->relationID,$modify->version);
                            }
                            break;
                        case 'outwardDelivery'://清总-生产变更
                            $outwardDelivery = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('id')->eq($second->relationID)->fetch();
                            if(!empty($outwardDelivery->demandId) && !empty($outwardDelivery->modifycnccId) && $outwardDelivery->reviewStage > 7){
                                $time3[] = $this->getSolvedTime(7,'outwardDelivery',$second->relationID,$outwardDelivery->version);
                            }
                            break;
                        case 'gainQz'://清总-数据获取
                            $infoqz = $this->dao->select("*")->from(TABLE_INFO_QZ)->where('id')->eq($second->relationID)->fetch();
                            if(!empty($infoqz->demand) && $infoqz->reviewStage > 6){
                                $time4[] = $this->getSolvedTime(6,'infoqz',$second->relationID,$infoqz->version);
                            }
                            break;
                    }

                }


                $finalTime = array_merge($time1,$time2,$time3,$time4);
                if(!empty($finalTime)){
                    $res = $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq(max($finalTime))->where('id')->eq($item->id)->exec();
                    if($res){
                        $secondAboutTime[] = $item->id;
                    }
                }
            }
        }
        echo '执行未关联二线单子的数据：'.count($closedIdNoSecondArr).'条。被执行数据id:'.implode(',',$closedIdNoSecondArr).'<br/>';
        echo '执行未关联二线单子并且无关闭时间数据：'.count($closedIdNoSecondArrEmpty).'条。被执行数据id:'.implode(',',$closedIdNoSecondArrEmpty).'<br/>';
        echo '执行关联二线单子数据：'.count($secondAboutTime).'条。被执行数据id:'.implode(',',$secondAboutTime).'<br/>';
    }


    /**
     * 处里问题池解决时间为空的情况
     */
    public function dealSolvedTimeAboutProblem(){
//        $problem = $this->dao->select('id,status,solvedTime,createdBy,closedDate')->from(TABLE_PROBLEM)->where('solvedTime')->isNull()->andWhere('status')->ne('deleted')->orderBy('id_desc')->fetchAll();
        $problem = $this->dao->select('id,status,solvedTime,createdBy,closedDate')->from(TABLE_PROBLEM)->where('status')->ne('deleted')->fetchAll();
//        $problem = $this->dao->select('id,status,solvedTime,createdBy,closedDate')->from(TABLE_PROBLEM)->where('id')->eq('41')->fetchAll();
        $closedIdNoSecondArr = [];
        $closedIdNoSecondArrEmpty = [];
        $secondAboutTime = [];
        foreach ($problem as $item){
            $secondLine = $this->dao->select('*')->from(TABLE_SECONDLINE)->where('objectType')->eq('problem')->andWhere('objectID')->eq($item->id)->andWhere('deleted')->eq(0)->fetchAll();
            if(!$secondLine){
                //已关闭，未关联二线单子更新解决时间
                if($item->status == 'closed'){
                    if(empty($item->closedDate)){
                        $closedIdNoSecondArrEmpty[] = $item->id;
                    }else{
                        $closedDate = '';
                        $action = $this->dao->select('date')->from(TABLE_ACTION)->where('objectType')->eq('problem')->andWhere('objectID')->eq($item->id)->andWhere('action')->in(['closed'])->orderBy('id_desc')->fetch();
                        if($action){
                            $closedDate = $action->date;
                            $res = $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq($closedDate)->where('id')->eq($item->id)->exec();
                            if($res){
                                $closedIdNoSecondArr[] = $item->id;
                            }
                        }
                    }

                }
            }else{ //关联了二线单子
                $time1 = [];
                $time2 = [];
                $time3 = [];
                $time4 = [];
                foreach ($secondLine as $second){
                    switch ($second->relationType){
                        case 'gain'://金信-数据获取
                            $info = $this->dao->select('*')->from(TABLE_INFO)->where('id')->eq($second->relationID)->fetch();
                            if($info && $info->reviewStage > 7){
                                $time1[] = $this->getSolvedTime(7,'info',$second->relationID,$info->version);
                            }
                            break;
                        case 'modify'://金信-生产变更
                            $modify = $this->dao->select('*')->from(TABLE_MODIFY)->where('id')->eq($second->relationID)->fetch();
                            if($modify && $modify->reviewStage > 7){
                                $time2[] = $this->getSolvedTime(7,'modify',$second->relationID,$modify->version);
                            }
                            break;
                        case 'outwardDelivery'://清总-生产变更
                            $outwardDelivery = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('id')->eq($second->relationID)->fetch();
                            if(!empty($outwardDelivery->problemId) && !empty($outwardDelivery->modifycnccId) && $outwardDelivery->reviewStage > 7){
                                $time3[] = $this->getSolvedTime(7,'outwardDelivery',$second->relationID,$outwardDelivery->version);
                            }
                            break;
                        case 'gainQz'://清总-数据获取
                            $infoqz = $this->dao->select("*")->from(TABLE_INFO_QZ)->where('id')->eq($second->relationID)->fetch();
                            if(!empty($infoqz->problem) && $infoqz->reviewStage > 6){
                                $time4[] = $this->getSolvedTime(6,'infoqz',$second->relationID,$infoqz->version);
                            }
                            break;
                    }

                }


                $finalTime = array_merge($time1,$time2,$time3,$time4);
                if(!empty($finalTime)){
                    $res = $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq(max($finalTime))->where('id')->eq($item->id)->exec();
                    if($res){
                        $secondAboutTime[] = $item->id;
                    }
                }
            }
        }
        echo '执行未关联二线单子的数据：'.count($closedIdNoSecondArr).'条。被执行数据id:'.implode(',',$closedIdNoSecondArr).'<br/>';
        echo '执行未关联二线单子并且无关闭时间数据：'.count($closedIdNoSecondArrEmpty).'条。被执行数据id:'.implode(',',$closedIdNoSecondArrEmpty).'<br/>';
        echo '执行关联二线单子数据：'.count($secondAboutTime).'条。被执行数据id:'.implode(',',$secondAboutTime).'<br/>';
    }
    public function dealSolvedTimeByProblem()
    {
        $problemModel = $this->loadModel('problem');
        $this->app->loadLang('problem');

        $problems = $this->dao->select('*')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('createdDate')->ge('2022-08-01')
//            ->andWhere('id')->in([338,399,433,481,589,618,711,735,751,783,802,815,837,863,890,909,913,930,1124,1191,1484,1503,1535,1551])
//            ->andWhere('id')->gt([752])
            ->andWhere('id')->notin([32,63,29,33,35,36,37,51,70,71,84,91,108,109,111,114,133,172,173,175,177,187,213,300,378,440,455,460,580,672,720,752,778,795,797,803,811,871,879,912,975,976,977,979,983,993,994,43,663,817,910,938])
            ->fetchAll();
        $secondPassIds = $toCloseIds = $closeIds = $emptyIds = [];
        $editSecondPassIds = $editToCloseIds = $editCloseIds = $editEmptyIds = [];
        foreach ($problems as $problem){
            //所有二线
            $allSecond =  $problemModel->getAllSecond($problem->id, 'problem');
            $allSolveTime = $hasTime = [];
            foreach ($allSecond as $key => $item){
                if($key == 'count' || empty($item)){
                    continue;
                }
                //获取每一个二线解决时间
                $allSolve       = $problemModel->getOneSecondWhertherPass($key,$item);
                $allSolveTime[] = $allSolve['solve'];//解决时间
                $hasTime[$key]  = $allSolve['hasTime'];//所有有解决时间的二线
            }
            $newArr = [];
            foreach ($allSolveTime as $alls) {
                foreach ($alls as $key =>$all) {
                    $newArr[$key] = $all;
                }
            }

            //所有二线全部审批通过
            $flag = false; //是否关闭
            if(($allSecond['count'] == count($newArr)) && count($newArr) != 0) {
                $solveTime = max($newArr);//获取多个二线前一个节点时间中最大的
                $maxTypeID = explode('_',array_search(max($newArr),$newArr));
                $maxType   = current($maxTypeID);
                $maxID     = end($maxTypeID);
                $secondPassIds[] = $problem->id;
                $editType = 'editSecondPassIds';
            } else {
                //未全部通过,查询单子是否关闭,关闭取关闭时间.反之,置空,问题单查看是否有待关闭时间，没有取关闭时间，置空
                $consumed = $this->dao
                    ->select('objectID,max(createdDate) as createdDate')
                    ->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($problem->id)
                    ->andWhere('after')->eq('toclose')
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('extra')->eq('')
                    ->groupBy('objectID')
                    ->fetch();

                $consumedClose = $this->dao->select('objectId,createdDate')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($problem->id)
                    ->andWhere('after')->eq('closed')
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('extra')->eq('')
                    ->orderBy('id_desc')
                    ->fetch();
                $consumedFeedbackClose = $this->dao->select('objectId,createdDate')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($problem->id)
                    ->andWhere('after')->eq('closed')
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('extra')->eq('problemFeedBack')
                    ->orderBy('id_desc')
                    ->fetch();

                if(isset($consumed) && !empty($consumed)){
                    $flag = $toClose = true;
                    $solveTime = strtotime($consumed->createdDate);
                    $toCloseIds[] = $problem->id;
                    $editType = 'editToCloseIds';
                }elseif(
                    ($consumedClose->createdDate && strpos($consumedClose->createdDate, '0000-00-00') === false) ||
                    ($consumedFeedbackClose->createdDate && strpos($consumedFeedbackClose->createdDate, '0000-00-00') === false)
                ) {
                    if(!empty($consumedClose->createdDate) && !empty($consumedFeedbackClose->createdDate)){
                        $solveTime = min(strtotime($consumedClose->createdDate),strtotime($consumedFeedbackClose->createdDate)); //关闭时间
                    }elseif(!empty($consumedClose->createdDate)){
                        $solveTime = strtotime($consumedClose->createdDate);
                    }else{
                        $solveTime = strtotime($consumedFeedbackClose->createdDate);
                    }
                    $flag = true;
                    $closeIds[] = $problem->id;
                    $editType = 'editCloseIds';
                }else{
                    $solveTime = '';//置空
                    $emptyIds[] = $problem->id;
                    $editType = 'editEmptyIds';
                }
            }

            //处理以下逻辑前检查表中的解决时间和本次是否一致，不一致再处理
            $oldSolveTime = $this->dao->select('solvedTime,secureStatusLinkage')->from(TABLE_PROBLEM)
                ->where('id')->in($problem->id)
                ->fetch();

            if($oldSolveTime->solvedTime == '0000-00-00 00:00:00' || strpos($oldSolveTime->solvedTime,'1970-01-01') !== false ){
                $oldSolveTime->solvedTime = '';
            }else{
                $oldSolveTime->solvedTime = strtotime($oldSolveTime->solvedTime);
            }
            if(
                $oldSolveTime->solvedTime != $solveTime && abs($oldSolveTime->solvedTime - $solveTime) > 5 &&
                !(in_array($problem->id, $closeIds) && $oldSolveTime->solvedTime > 0)
            ){
                //获取所有二线和有时间二线的差异,用于回填历史记录备注
                $diff = [];
                foreach($allSecond as $key => $all) {
                    if($key == 'count' || empty($all)){
                        continue;
                    }
                    $diff[$key] = array_diff($all,$hasTime[$key]);
                }
                $diff = array_filter($diff);

                //根据差异查询具体单号,并生成备注
                $codes = [];
                foreach ($diff as $key => $item) {
                    if(!$item){
                        continue;
                    }
                    $code = $this->dao->select('code')->from($this->lang->problem->secondTable[$key])
                        ->where('id')->in($item)
                        ->fetch();
                    $codes[] = $code->code ?? '';
                }
                //$comment = implode(',',array_filter($codes));//备注code
                if(isset($toClose)) {
                    $comment = sprintf($this->lang->problem->solveTimeToColseTip,$this->lang->problem->typeName['problem'],$this->lang->problem->timeDesc['problem']);
                } elseif($flag) {
                    //未全部通过,单子关闭
                    $comment = sprintf($this->lang->problem->solveTimeTip,$this->lang->problem->typeName['problem'],$this->lang->problem->timeDesc['problem']);
                } else {
                    if($codes || !isset($maxType)) {
                        //未全部通过,单子未关闭
                        $comment = sprintf($this->lang->problem->solveTimeNoColseTip,$this->lang->problem->typeName['problem'],$this->lang->problem->timeDesc['problem']);
                    } else {
                        $code = $this->dao->select('code')->from($this->lang->problem->secondTable[$maxType])
                            ->where('id')->in($maxID)
                            ->fetch();
                        $comment = sprintf($this->lang->problem->solveTimeColseTip,$code->code,$this->lang->problem->timeDesc['problem']);
                    }
                }

                //更新解决时间
                $solveTime = !empty($solveTime) && strpos($solveTime, '0000-00-00') === false ? date('Y-m-d H:i:s',$solveTime) : '';
                if($oldSolveTime->secureStatusLinkage == '0'){
                    ($$editType)[] = $problem->id;
                    $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq($solveTime)
                        ->where('id')->eq($problem->id)
                        ->exec();
                    $this->loadModel('action')->create('problem', $problem->id, 'updatesolvetime', $comment,'','guestjk');
                }
            }
        }

        echo '执行关联二线单子数据：'.count($editSecondPassIds).'条。被执行数据id:'.implode(',',$editSecondPassIds).'<br/>';
        echo '执行待关闭数据：'.count($editToCloseIds).'条。被执行数据id:'.implode(',',$editToCloseIds).'<br/>';
        echo '执行已关闭：'.count($editCloseIds).'条。被执行数据id:'.implode(',',$editCloseIds).'<br/>';
        echo '执行解决时间为空：'.count($editEmptyIds).'条。被执行数据id:'.implode(',',$editEmptyIds).'<br/>';
    }


    /**
     * @Notes:解决时间取二线专员审核通过节点的前一个节点的处理节点时间 返回
     * @Date: 2023/3/21
     * @Time: 17:59
     * @Interface getSolvedTime
     * @param $i
     * @param $objectType
     * @param $infoId
     * @param $version
     * @return array
     */
    public function getSolvedTime($i,$objectType,$infoId,$version){
        /**
         * @var reviewModel $reviewModel
         */
        $reviewModel = $this->loadModel('review');
        $reviewTime = '';
        $solveTime = '';
        for ($i; $i>0; $i--)
        {
            $reviewNodeStatus = $reviewModel->getReviewInfoByStage($objectType, $infoId,$version,$i,'id,status');
            if($reviewNodeStatus)
            {
                if($reviewNodeStatus->status == 'pass')
                {
                    $nodeId = $reviewNodeStatus ->id;
                    $reviewers = $this->dao->select('id,status,reviewTime,reviewer')->from(TABLE_REVIEWER)->where('node')->eq($nodeId)->fetchAll();
                    $reviewTime  = array_column($reviewers,'reviewTime');
                    $solveTime = max($reviewTime);
                    //查询action历史记录表
                    if($solveTime == '' || $solveTime == '0000-00-00 00:00:00')
                    {
                        $actionInfo = [];
                        foreach ($reviewers as $reviewer) {
                            $actionInfo[] = $this->dao->select('*')->from(TABLE_ACTION)
                                ->where('objectType')->eq($objectType)
                                ->andWhere('objectID')->eq($infoId)
                                ->andWhere('actor')->eq($reviewer->reviewer)
                                ->orderBy('id_desc')
                                ->fetch();
                        }
                        $reviewTime  = array_column($actionInfo,'date');
                        if($reviewTime){
                            $solveTime = max($reviewTime);
                        }
                    }
                    break;
                }
            }
        }
        return $solveTime;
    }


    /**
     * @Notes:问题池：若待分析节点待处理人为空，取待分析节点的待处理人，及待处理人的所在部门刷新受理人员、受理部门
     * @Date: 2023/3/28
     * @Time: 16:03
     * @Interface dealProblemAcceptUser
     */
    public function dealProblemAcceptUser()
    {
        $problem =  $this->dao->select('id,abstract,dealUser,status,acceptUser,acceptDept')
            ->from(TABLE_PROBLEM)
            ->where('status')->eq('assigned')
            ->andWhere('acceptUser')->isNull()
            ->orWhere('acceptUser')->eq('')
            ->fetchAll();
        $problemIDs = [];
        if($problem){
            foreach ($problem as $item) {
                if (!empty($item->dealUser)) {
                    $acceptDeptId = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($item->dealUser)->fetch('dept');
                    $dealProblem = new stdClass();
                    $dealProblem->acceptUser = $item->dealUser;
                    $dealProblem->acceptDept = $acceptDeptId ?? '';
                    $problemIDs[] = $item->id;
                    $this->dao->update(TABLE_PROBLEM)->data($dealProblem)->where('id')->eq($item->id)->exec();
                }
            }
        }
        echo "共执行数据".count($problemIDs)."条。被执行数据id:".implode(',',$problemIDs);
    }

    /**
     * 系统管理历史数据刷是否同步清总  系统列表在application/lang/histortDeal.php
     */
    public function applicationSyncQz($param = "1"){
        $this->app->loadLang('application');
        if ($param == 1){
            $data = str_replace("\r\n",',',$this->lang->application->histroyName);
        }else{
            $data = str_replace("\n",',',$this->lang->application->histroyName);
        }

        $res = $this->dao->update(TABLE_APPLICATION)->set('isSyncQz')->eq('yes')->where('name')->in($data)->exec();
        echo "执行完成";

    }
    /**
     * 清总生产变更单-产品发布状态联动数据处理
     */
    public function qzReleaseDeal(){
        //根据历史记录获取需要恢复的产品发布
        $historys = $this->dao->select('objectID')->from(TABLE_ACTION)->where('`action`')->eq('modifysyncreleasestatus')->fetchall();
        $objectID = array_unique(array_column($historys,'objectID'));
        $releases = $this->dao->select('id,dealUser,syncStateTimes,version')->from(TABLE_RELEASE)->where('id')->in($objectID)->andWhere('`status`')->eq('waitBaseline')->fetchall();

        //根据待处理人获取部门cm
        $accounts = array_unique(array_column($releases,'dealUser'));
        $userList = $this->dao->select('account,dept')->from(TABLE_USER)->where('account')->in($accounts)->fetchall();
        $deptID = implode(',',array_unique(array_column($userList,'dept')));
        $deptList = $this->loadModel('dept')->getByIDs($deptID);

        foreach ($userList as $user) {
            foreach ($deptList as $dept) {
                if ($user->dept == $dept->id){
                    $cm = explode(',',$dept->cm);
                    $user->cm = $cm[0];
                }
            }
        }
        $updateParams = new stdClass();
        foreach ($releases as $release) {
            foreach ($userList as $user) {
                if ($release->dealUser == $user->account){
                    $releaseID[] = $release->id;
                    $updateParams->syncStateTimes = $release->syncStateTimes +1;
                    $updateParams->dealUser       = $user->cm;
                    $release->cm                  = $user->cm;
                    $this->dao->update(TABLE_RELEASE)->data($updateParams)->where('id')->eq($release->id)->exec();
                }
            }
        }
        $sql = "SELECT * from zt_reviewnode  as a where objectType='release' and objectID in (".implode(',',$releaseID).") and version in 
                  (SELECT max(version) from zt_reviewnode where objectType='release' and objectID = a.objectID) 
                  and nodeCode='baseline'";
        $reviewNodes = $this->dao->query($sql)->fetchAll();
        $reviewerParams = new stdClass();
        foreach ($releases as $release) {
            foreach ($reviewNodes as $reviewNode) {
                if ($release->id == $reviewNode->objectID){
                    $reviewerParams->reviewer = $release->cm;
                    $this->dao->update(TABLE_REVIEWER)->data($reviewerParams)->where('node')->eq($reviewNode->id)->exec();
                }
            }
        }
        echo '执行完成';
    }

 /**
     * 更新问题单内部是否超时字段
     */
    public function dealProblemInside(){
        /** @var problemModel $problemModel*/
        $this->app->loadLang('problem');
        $this->loadModel('problem');
        $problems =  $this->dao->select('id, status, code, actualOnlineDate, dealUser,createdBy,dealAssigned,dealFeedbackPass,ifOverDateInside')
            ->from(TABLE_PROBLEM)
            ->where('status')->notIN("confirmed,deleted") // 待分配、已删除不做联动
            ->andWhere('createdBy')->in("guestjx,guestcn")
            //->andWhere('id')->eq(678)
            ->fetchAll('id');

        $count = 0;
        foreach ($problems as $problem) {
            $new = new stdClass();
            //查询状态流转时间
            $consumed = $this->dao->select('createdDate')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere("id = (select max(id) id from zt_consumed where objectType = 'problem'
	             and objectID = '$problem->id' and `before` = 'confirmed'
	             and `after` = 'assigned' and deleted = '0')")
                ->fetch();

            $new->dealAssigned = isset($consumed->createdDate) ? $consumed->createdDate : '';  //待分配到待分析的时间
            //查询反馈单部门审批通过的时间
            //select reviewTime  from zt_reviewer zr where node  in (select id from zt_reviewnode zr  where objectType ='problem'  and objectID ='1657' and stage = '1' and status = 'pass') and status ='pass'
            $reviewer = $this->dao->select('reviewTime,createdDate')->from(TABLE_REVIEWER)
                ->where("node in (select max(id) from zt_reviewnode zr  where objectType ='problem'  and objectID ='$problem->id' and stage = '1' and status = 'pass')")
                ->andWhere('status')->eq('pass')
                ->fetch();
            $new->dealFeedbackPass = isset($reviewer->reviewTime) ? $reviewer->reviewTime : (isset($reviewer->createdDate) ?  $reviewer->createdDate : '');//反馈单部门审核通过时间
            $this->dao->update(TABLE_PROBLEM)->data($new)->where('id')->in($problem->id)->exec();
            //$problem->feedbackExpireTime = $jx ? $problemModel->getDateAfter($this->lang->problem->expireDaysList['jxExpireDays']) : $problemModel->getDateAfter($this->lang->problem->expireDaysList['days'],true);
            //$now = helper::today();
            $count ++;
            echo "问题单id :".$problem->id.'已处理'."<br>";
        }
        echo '处理完成，共处理： '.$count.' 条数据';
    }
    /**
     * 二线已关闭且实际结束时间为空，则填充计划结束时间
     * @return array
     */
    public function addSecondLineOnlineTime()
    {

        $this->app->loadLang('problem');
        $problems = $this->dao->select('id, status, code, actualOnlineDate, dealUser')
            ->from(TABLE_PROBLEM)
            ->where('status')
            ->notIN($this->lang->problem->statusArr['problemNotIn'])
            ->fetchAll('id');

        $problemIds = array_keys($problems);
        foreach ($problemIds as $problemId) {
            //关联二线生产变更
            $relations = $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')->eq('problem')
                ->andwhere('objectID')->eq($problemId)
                ->andwhere('deleted')->eq(0)
                ->andwhere('relationType')->in('modify')
                ->orderBY("id_asc")
                ->fetchAll();

            foreach ($relations as $relation) {
                if (empty($relation)) continue;
                if ($relation->relationType == 'modify') { //金信生产变更
                    $info = $this->dao->select('id,code,status, actualEnd, realEndTime, dealUser,planEnd')
                        ->from(TABLE_MODIFY)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('status')->eq("closed")
                        ->andWhere('realEndTime is null')
                        ->fetch();
                    if (empty($info)) continue;
                    //更新
                    $this->dao->update(TABLE_MODIFY)->set('realEndTime')->eq($info->planEnd)->where('id')->in($relation->last_relation_id)->exec();
                    echo '已处理 金信生产变更 ：' . $relation->last_relation_id.'<br>';
                }
            }
        }
    }

    /**
     * 二线已关闭且实际结束时间为空，则填充计划结束时间
     * @return array
     */
    public function addSecondLineOnlineTimeAboutDemand()
    {
        $this->app->loadLang('problem');
        $demands = $this->dao->select('id, status, code, actualOnlineDate, dealUser')
            ->from(TABLE_DEMAND)
            ->where('status')
            ->notIN($this->lang->problem->statusArr['problemNotIn'])
            ->fetchAll('id');

        $demandIds = array_keys($demands);
        foreach ($demandIds as $demandId) {
            //关联二线生产变更
            $relations = $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')->eq('demand')
                ->andwhere('objectID')->eq($demandId)
                ->andwhere('deleted')->eq(0)
                ->andwhere('relationType')->in('modify')
                ->orderBY("id_asc")
                ->fetchAll();

            foreach ($relations as $relation) {
                if (empty($relation)) continue;
                if ($relation->relationType == 'modify') { //金信生产变更
                    $info = $this->dao->select('id,code,status, actualEnd, realEndTime, dealUser,planEnd')
                        ->from(TABLE_MODIFY)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('status')->eq("closed")
                        ->andWhere('realEndTime is null')
                        ->fetch();
                    if (empty($info)) continue;
                    //更新
                    $this->dao->update(TABLE_MODIFY)->set('realEndTime')->eq($info->planEnd)->where('id')->in($relation->last_relation_id)->exec();
                    echo '已处理 金信生产变更 ：' . $relation->last_relation_id.'<br>';
                }
            }
        }
    }

    /**
     * Recalculate the binding relationship between use cases and projects.
     * 重新计算用例和项目的绑定关系。
     *
     * @param  int  $begin
     * @param  int  $end
     * @access public
     * @return void
     */
    public function rebindCasesAndProjects($begin = 0, $end = 1000)
    {
        if(!$begin) $this->dao->exec("truncate table zt_projectcase;");

        $cases = $this->dao->query("SELECT id,project,product,version FROM `zt_case` wHeRe product > 0 and deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            echo '所有用例处理完毕，无数据可执行！';
            die();
        }

        foreach($cases as $case)
        {
            $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($case->project)->orderBy('order_desc')->limit(1)->fetch('order');

            $data = new stdclass();
            $data->project = $case->project;
            $data->product = $case->product;
            $data->case    = $case->id;
            $data->version = $case->version;
            $data->order   = ++ $lastOrder;
            $this->dao->insert(TABLE_PROJECTCASE)->data($data)->exec();
        }

        echo '处理了' . count($cases) . '条数据; 1秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'rebindCasesAndProjects', array('begin' => $begin + 1000));
        header("refresh:1;url=$location");
    }

    /**
     * Cancel the project belonging to the use case in the use case library.
     * 取消用例库用例的所属项目。
     *
     * @param  int  $begin
     * @param  int  $end
     * @access public
     * @return void
     */
    public function cancelProjectToCaseLib($begin = 0, $end = 1000)
    {
        $cases = $this->dao->query("SELECT id,lib FROM `zt_case` wHeRe product = 0 and lib > 0 and deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            echo '所有用例库用例处理完毕，无数据可执行！';
            die();
        }

        foreach($cases as $case)
        {
            $data = new stdclass();
            $data->project = 0;
            $this->dao->update(TABLE_CASE)->data($data)->where('id')->eq($case->id)->exec();
        }

        echo '处理了' . count($cases) . '条数据; 1秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'cancelProjectToCaseLib', array('begin' => $begin + 1000));
        header("refresh:1;url=$location");
    }

    /**
     * Find use cases that deal with unusual product-to-project bindings.
     * 查找处理产品与项目绑定关系不正常的用例。
     *
     * @param  int  $begin
     * @param  int  $end
     * @access public
     * @return void
     */
    public function findUnusualCases($begin = 0, $end = 500, $init = 1)
    {
        $cases = $this->dao->query("SELECT id,project,product,title FROM `zt_case` wHeRe product > 0 and deleted = '0' oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($cases))
        {
            //echo '所有用例处理完毕，无数据可执行！';
            $downloadFile = $this->app->getAppRoot() . 'module/history/excel/export/fixCase.xlsx';
            $this->history->exportexcel(array(), array(), $downloadFile);
            die();
        }

        $template = $this->app->getAppRoot() . 'module/history/excel/template.xlsx'; // 定义一个文件模板
        $savePath = $this->app->getAppRoot() . 'module/history/excel/export/'; // 存储文件新地址
        $fileName = 'fixCase.xlsx';

        $saveFile = $savePath . $fileName;
        if($init) copy($template, $saveFile);

        foreach($cases as $case)
        {
            if(!$case->project) continue; // 没有项目的用例直接过滤。

            /* 产品与项目绑定关系有误的用例，导入到Excel文件中。*/
            $hasBind = $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($case->project)->andWhere('product')->eq($case->product)->fetch();
            if(empty($hasBind))
            {
                // 处理问题用例。
                $data   = array(array($case->id, $case->product, $case->project, $case->title));
                $column = array('A', 'B', 'C', 'D');

                $this->history->readExcel($data, $saveFile, $column);
            }
        }

        echo '处理了' . count($cases) . '条数据; 1秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'findUnusualCases', array('begin' => $begin + 500, 500, 0));
        header("refresh:1;url=$location");
    }

    /**
     * 更新问题池 金信外部反馈时间
     */
    public function updateJxOutside(){
        $problems = $this->dao->select('id,ifOverDate,feedbackExpireTime')
            ->from(TABLE_PROBLEM)
            ->where('createdBy')->eq('guestjx')
            ->andWhere('createdDate')->le('2023-05-11')
            ->fetchAll('id');
        $now = date('Y-m-d H:i:s');
        foreach ($problems as $problem) {
            $new = new stdClass();
            $new->feedbackExpireTime = date('Y-m-d H:i:s',strtotime('-15 day',strtotime($problem->feedbackExpireTime)));
           if(strtotime($new->feedbackExpireTime) < strtotime($now)){
               $new->ifOverDate = 1;
           }
           $this->dao->update(TABLE_PROBLEM)->data($new)->where('id')->in($problem->id)->exec();
           echo '已更新 id：'.$problem->id."<br>";
        }
    }
    /**
     * 更新问题池 内部反馈时间
     */
    public function updateHistoryOverDate(){
        $problems = $this->dao->select('id,ifOverDateInside,createdBy,version')->from(TABLE_PROBLEM)
            ->where('createdBy')->in('guestcn,guestjx')
            ->andWhere('status')->notIN("confirmed,deleted") // 待分配、已删除不做联动
            ->fetchAll();
        $consumedModel = $this->loadModel('consumed');
        $reviewModel = $this->loadModel('review');
        foreach ($problems as $item) {
            $consumedInfo = $consumedModel->getCreatedDate('problem', $item->id, 'confirmed', 'assigned');
            if($consumedInfo) {
                $startTime = $consumedInfo->createdDate;
              //  $finalTime = date('Y-m-d H:i:s', time()); //若部门审核一致未通过，或未到达部门审核节点，则取当前系统时间对比
                //部门审核通过的时间
                $node = $reviewModel->getNodeInfoByParams('id', 'pass', $item->version, 'problem', $item->id, 1);
                if ($node) {
                   $reviewTime = $reviewModel->getReviewerInfoByParams('id,reviewTime,createdDate', 'pass', $node->id);
                   if ($reviewTime) {
                        $finalTime = $reviewTime->reviewTime ? $reviewTime->reviewTime : $reviewTime->createdDate;
                   }
                }
                $this->dao->update(TABLE_PROBLEM)->set('dealAssigned')->eq($startTime)->beginIF($finalTime)->set('dealFeedbackPass')->eq($finalTime)->fi()->where('id')->in($item->id)->exec();
                echo '已更新 ：'.$item->id.'<br>';
            }
        }
    }

    /**
     * 迁移对外移交数据
     */
    public function transferData(){
        $sectransferList = $this->dao->select('*')->from('zt_flow_protransfer')
            ->where('status')->eq('7')
            ->fetchAll('id');
        foreach ($sectransferList as $sectransfer){
            $sectransfer->status = 'alreadyEdliver';
            $this->dao->insert(TABLE_SECTRANSFER)->data($sectransfer)
                ->autoCheck()
                ->exec();
        }
        echo '迁移完成';
    }

    /**
     * @Notes: 处理清总同步需求任务 内外部反馈起止时间脚本
     * @Date: 2023/6/8
     * @Time: 17:30
     * @Interface updateRequiurementAboutFeedBackTime
     */
    public function updateRequirementAboutFeedBackTime()
    {
        /* @var consumedModel $consumedModel */
        $requirementInfo = $this->dao->select('id,ifOverDate,version,ifOverTimeOutSide,createdDate')->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->andWhere('`status`')->ne('deleted')
            ->fetchAll();
        $this->app->loadLang('demand');
        $consumedModel = $this->loadModel('consumed');
        $count = [];
        if($requirementInfo){
            foreach ($requirementInfo as $value) {
                $id = $value->id;
                $consumedInfo = $consumedModel->getCreatedDate('requirement', $id, 'published', 'published');
                $actions = $this->dao->select('id,date')->from(TABLE_ACTION)->where('objectType')->eq('requirement')->andWhere('objectID')->eq($id)->andWhere('action')->eq('edited')->andWhere('comment')->eq('总中心接口同步创建')->andWhere('actor')->eq('guestcn')->orderBy('id_asc')->fetch();
                $actionCreate = $actions->date;
                if($consumedInfo){
                    //反馈开始时间
                    $feekBackStartTime = $consumedInfo->createdDate;
                    $hms = substr($feekBackStartTime,10);
                    //内部截止时间
                    $days = $this->lang->demand->expireDaysList['insideDays'];
                    $feekBackEndTimeInside = helper::getTrueWorkDay($feekBackStartTime,$days,true).$hms; //结束时间
                    //外部
                    $days = $this->lang->demand->expireDaysList['outsideDays'];
                    $feekBackEndTimeOutSide = helper::getTrueWorkDay($actionCreate,$days,true).substr($actionCreate,10);//创建时间

                    $data = new stdClass();
                    $data->feekBackStartTime     = $feekBackStartTime;
                    $data->feekBackEndTimeInside = $feekBackEndTimeInside;
                    $data->feekBackEndTimeOutSide     = $feekBackEndTimeOutSide;
                    $data->createdDate     = $actionCreate;
                    $res = $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($id)->exec();
                    if($res){
                        $count[] = $id;
                    }
                }else{
                    $consumedInfo = $consumedModel->getCreatedDate('requirement', $id, 'topublish', 'published');
                    if($consumedInfo){
                        //反馈开始时间
                        $feekBackStartTime = $consumedInfo->createdDate;
                        $hms = substr($feekBackStartTime,10);
                        //内部截止时间
                        $days = $this->lang->demand->expireDaysList['insideDays'];
                        $feekBackEndTimeInside = helper::getTrueWorkDay($feekBackStartTime,$days,true).$hms; //结束时间
                        //外部
                        $days = $this->lang->demand->expireDaysList['outsideDays'];
                        $feekBackEndTimeOutSide = helper::getTrueWorkDay($actionCreate,$days,true).substr($actionCreate,10); //结束时间
                        $data = new stdClass();
                        $data->feekBackStartTime     = $feekBackStartTime;
                        $data->feekBackEndTimeInside = $feekBackEndTimeInside;
                        $data->feekBackEndTimeOutSide     = $feekBackEndTimeOutSide;
                        $data->createdDate     = $actionCreate;
                        $res = $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($id)->exec();
                        if($res){
                            $count[] = $id;
                        }
                    }else{
                        //2022年以前的数据如果取不到待发布->待发布 或 已发布->已发布 的时间，用创建时间作为开始时间
                        if($value->createdDate < '2023-01-01 00:00:00') {
                            //反馈开始时间
                            $feekBackStartTime = $actionCreate;
                            $hms = substr($feekBackStartTime,10);
                            //内部截止时间
                            $days = $this->lang->demand->expireDaysList['insideDays'];
                            $feekBackEndTimeInside = helper::getTrueWorkDay($feekBackStartTime,$days,true).$hms; //结束时间
                            //外部
                            $days = $this->lang->demand->expireDaysList['outsideDays'];
                            $feekBackEndTimeOutSide = helper::getTrueWorkDay($actionCreate,$days,true).substr($actionCreate,10); //结束时间
                            $data = new stdClass();
                            $data->feekBackStartTime     = $feekBackStartTime;
                            $data->feekBackEndTimeInside = $feekBackEndTimeInside;
                            $data->feekBackEndTimeOutSide     = $feekBackEndTimeOutSide;
                            $data->createdDate     = $actionCreate;
                            $res = $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($id)->exec();
                            if($res){
                                $count[] = $id;
                            }
                        }else{
                            //外部
                            $days = $this->lang->demand->expireDaysList['outsideDays'];
                            $feekBackEndTimeOutSide = helper::getTrueWorkDay($actionCreate,$days,true).substr($actionCreate,10); //结束时间
                            $data = new stdClass();
                            $data->feekBackEndTimeOutSide     = $feekBackEndTimeOutSide;
                            $data->createdDate     = $actionCreate;
                            $res = $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($id)->exec();
                            if($res){
                                $count[] = $id;
                            }
                        }
                    }
                }

            }
        }
        echo '执行数据：'.count($count).'条。被执行数据id:'.implode(',',$count);
    }

    /**
     * @Notes: 处理清总同步的需求任务，部门审核通过时间和同步清总成功时间
     * @Date: 2023/8/9
     * @Time: 14:13
     * @Interface updateDeptAndInnovationPassTime
     */
    public function updateDeptAndInnovationPassTime()
    {
        //需要处理数据
        $requirementInfo = $this->dao->select('id,reviewStage,deptPassTime,innovationPassTime,version')->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->fetchAll();
        $count = [];
        foreach ($requirementInfo as $requirement)
        {
            $id = $requirement->id;
            $action = $this->getActionsUpdate('requirement',$id,'edited','id_desc','总中心接口同步更新');
            $version = $requirement->version;
            //无更新
            if(!$action){
                for($i=1; $i<=$version;$i++)
                {
                    //部门审核通过 (内部)
                    $reviewTime = $this->getNodeInfo('*','pass',$i,'requirement',$id,1);
                    if($reviewTime)
                    {
                        $this->dao->update(TABLE_REQUIREMENT)->set('deptPassTime')->eq($reviewTime)->where('id')->eq($id)->exec();
                        $count[] = $id;
                        //同步清总成功 (外部)
                        $reviewTimeOutSide = $this->getNodeInfo('*','syncsuccess',$i,'requirement',$id,3);
                        if($reviewTimeOutSide)
                        {
                            $this->dao->update(TABLE_REQUIREMENT)->set('innovationPassTime')->eq($reviewTimeOutSide)->where('id')->eq($id)->exec();
                            $count[] = $id;
                            break;
                        }
                        break;
                    }
                }
            }else{
                //有更新
                $actionCreate = $action->date; //最新的更新时间
                $reviewTimeOutSide = '';
                for($i=1; $i<=$version;$i++)
                {
                    //部门审核通过 (内部)
                    $reviewTime = $this->getNodeInfo('*','pass',$i,'requirement',$id,1);
                    if($reviewTime && $reviewTime > $actionCreate)
                    {
                        $this->dao->update(TABLE_REQUIREMENT)->set('deptPassTime')->eq($reviewTime)->where('id')->eq($id)->exec();
                        $count[] = $id;
                        //同步清总成功 (外部)
                        $reviewTimeOutSide = $this->getNodeInfo('*','syncsuccess',$i,'requirement',$id,3);
                        if($reviewTimeOutSide && $reviewTimeOutSide > $actionCreate)
                        {
                            $this->dao->update(TABLE_REQUIREMENT)->set('innovationPassTime')->eq($reviewTimeOutSide)->where('id')->eq($id)->exec();
                            $count[] = $id;
                            break;
                        }
                        break;
                    }
                }
                $outAction = $this->getActionsUpdate('requirement',$id,'feedbacked','id_asc','已推送反馈单审核',$actionCreate);
                if($outAction)
                {
                    if($outAction->date > $reviewTimeOutSide)
                    {
                        $this->dao->update(TABLE_REQUIREMENT)->set('innovationPassTime')->eq($outAction->date)->where('id')->eq($id)->exec();
                        $count[] = $id;
                    }
                }
            }

        }
        $count = array_unique($count);
        echo '执行数据通过数据：'.count($count).'条。被执行数据id:'.implode(',',$count);
    }



    /**
     * @Notes: 迭代二十九需求任务外部是否超时更新
     * @Date: 2023/8/7
     * @Time: 15:46
     * @Interface dealFeedbackTimeToInnovationPassTime
     */
    public function dealFeedbackTimeToInnovationPassTime()
    {
        $count = [];
        $actions = $this->dao->select('*')->from(TABLE_ACTION)->where('objectType')->eq('requirement')->andWhere('action')->eq('feedbacked')->orderBy('id_asc')->fetchALL();
        $new = array();
        foreach ($actions as $value)
        {
            $new[$value->objectID][] = $value;
        }

        $finalArr = [];
        foreach ($new as $k=>$v){
            $finalArr[$k] = $v[0];
        }

        foreach ($finalArr as $action)
        {
            $requirement = $this->dao->select('id,status')->from(TABLE_REQUIREMENT)->where('id')->eq($action->objectID)->fetch();
            if($requirement->status != 'deleted'){
                $this->dao->update(TABLE_REQUIREMENT)->set('innovationPassTime')->eq($action->date)->where('id')->eq($action->objectID)->exec();
                $count[] = $action->objectID;
            }
        }

        echo '执行数据：'.count($count).'条。被执行数据id:'.implode(',',$count);
    }


    /**
     * 发送附件
     * @return void
     */
    public function sendFile($fileId)
    {
        $file = $this->loadModel('file')->getById($fileId);
        $fileList = array();
        array_push($fileList, $file);
        $result = $this->loadModel('common')->sendFileBySftp($fileList);
        if (dao::isError()) {
            echo print_r(dao::getError(),true);
        }else{
            echo '发送成功-'.print_r($result,true);
        }
    }

    /**
     * 修正任务工单状态流转状态错误问题
     * @return void
     */
    public function updateSecondOrderConsumed()
    {
        $list = $this->dao
            ->select('id,status')->from(TABLE_SECONDORDER)
            ->where('createdDate')->gt('2023-06-18')
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        $ids = [];
        foreach ($list as $item) {
            $info = $this->dao
                ->select('id,objectID,after')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('secondorder')
                ->andWhere('objectID')->eq($item->id)
                ->orderBy('id_desc')
                ->fetch();

            if(!empty($info) && $item->status != $info->after){
                $this->dao->update(TABLE_CONSUMED)->set('after')->eq($item->status)->where('id')->eq($info->id)->exec();
                $ids[] = $item->id;
            }
        }

        echo '修正任务工单状态流转状态,任务工单ID：' . implode(',', $ids);
    }

    /**
     * 迭代30需求条目状态刷数
     */
    public function demandLinkageStatus(){
        $demands = $this->dao->select('id,status')->from(TABLE_DEMAND)
            ->where('status')->in('build,released')
            ->andWhere('sourceDemand')->eq(1)
            ->fetchall();
        $demandID = array_column($demands,'id');
        $this->dao->update(TABLE_DEMAND)->set('status')->eq('feedbacked')->where('id')->in($demandID)->exec();
        $comment = date("Y年m月d日").'参考最新需求对状态流转进行批量刷新状态。';
        $buildArray    = [];
        $releaseArray  = [];
        foreach ($demands as $demand) {
//            $this->loadModel('action')->create('demand', $demand->id, 'deal', '');
            if ($demand->status == 'build') $buildArray[] = $demand->id;
            if ($demand->status == 'released') $releaseArray[] = $demand->id;
        }
        echo '处理的测试中需求条目ID：'.implode(',',$buildArray);
        echo '<br/>';
        echo '处理的已发布需求条目ID：'.implode(',',$releaseArray);
    }

    /**
     * 已录入→开发中【保留】
     * 开发中→测试中【刷为：开发中→开发中】（状态流转不显示了）
     * 测试中→已发布【刷为：开发中→开发中】（状态流转不显示了）
     * 已发布→已交付【刷为：开发中→已交付】
     */
    public function demandLinkageStatusConsumed(){
        $comment = date("Y年m月d日").'参考最新需求对状态流转进行批量刷新状态。';
        $demands = $this->dao->select('id,status')->from(TABLE_DEMAND)
            ->where('sourceDemand')->eq(1)
            ->fetchall();
        $demanID = array_column($demands,'id');

        $res1 = $this->dao->select("*")
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('demand')
            ->andWhere('objectID')->in($demanID)
            ->andWhere('`before`')->in(['build','released'])
            ->fetchall();

        $res2 = $this->dao->select("*")
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('demand')
            ->andWhere('objectID')->in($demanID)
            ->andWhere('`after`')->in(['build','released'])
            ->fetchall();
        $beforeIds = array_column($res1,'id');
        $afterIds  = array_column($res2,'id');
        $dealDemands  = array_values(array_unique(array_merge(array_column($res1,'objectID'),array_column($res2,'objectID'))));

        $this->dao->update(TABLE_CONSUMED)
            ->set('`before`')->eq('feedbacked')
            ->set('extra')->eq($comment)
            ->where('id')->in($beforeIds)
            ->exec();
        $this->dao->update(TABLE_CONSUMED)
            ->set('`after`')->eq('feedbacked')
            ->set('extra')->eq($comment)
            ->where('id')->in($afterIds)
            ->exec();

        $this->dao->update(TABLE_CONSUMED)
            ->set('deleted')->eq('1')
            ->set('extra')->eq($comment)
            ->where('objectType')->eq('demand')
            ->andWhere('objectID')->in($dealDemands)
            ->andWhere('`before`')->eq('feedbacked')
            ->andWhere('`after`')->eq('feedbacked')
            ->exec();
        foreach ($dealDemands as $v) {
            $this->loadModel('action')->create('demand', $v, 'deal', $comment);
        }
        echo '条目id：'.implode(',',$dealDemands);
    }

    /**
     * @Notes:迭代二十九修改 处理内、外部反馈开始和截止时间
     * ①【存在外部更新的】，按照变更后的最新发布时间~变更后的反馈单部门首次审核通过时间
     * ②开始时间取值：待发布到已发布的时间（最新转变为已发布的时间）其中部分单子存在已发布-已发布、已发布-已发布场景。取最后一次已发布-已发布的时间
     * ③若存在反馈单审核通过后，又进行指派导致出现的已发布→已发布，该场景数据过滤掉
     *
     * @Date: 2023/8/9
     * @Time: 9:51
     * @Interface dealStartAndEndTime
     */
    public function dealStartAndEndTime()
    {
        $requirementInfo = $this->dao->select('id,ifOverDate,version,ifOverTimeOutSide,createdDate')->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->andWhere('`status`')->ne('deleted')
            ->fetchAll();
        $this->app->loadLang('demand');
        $count = [];
        if($requirementInfo){
            foreach ($requirementInfo as $value)
            {
                $id = $value->id;
                /*【存在外部更新的】，按照变更后的最新发布时间~变更后的首次反馈时间*/
                $action = $this->getActionsUpdate('requirement',$id,'edited','id_desc','总中心接口同步更新');
                //不存在更新
                if(!$action){
                    //外部开始和截止时间 取action创建时间，带时分秒
                    $createAction = $this->getActionsUpdate('requirement',$id,'created','date_asc','');
                    $dataOut =  $this->dealOutsideData($createAction);
                    $resOut = $this->dao->update(TABLE_REQUIREMENT)->data($dataOut)->where('id')->eq($id)->exec();
                    if($resOut) $count[] = $id;
                    //若存在反馈后，又出现的已发布→已发布，该场景数据过滤掉 其中feedbacked为历史状态，需要兼容
                    $createdFeedBackTime = $this->loadModel('requirement')->checkFeedBack('requirement',$id,'createfeedbacked','id_asc','','');
                    $feedBackTime = $this->loadModel('requirement')->checkFeedBack('requirement',$id,'feedbacked','id_asc','','');
                    if(!$createdFeedBackTime && !$feedBackTime)
                    {
                        $publishedInfo = $this->getConsumedInfoByID('requirement',$id,'published','published');
                    }else if(!$createdFeedBackTime and $feedBackTime){
                        $publishedInfo = $this->getFeedbackByID('requirement',$id,'published','published',$feedBackTime->date,'');
                    }else{
                        $publishedInfo = $this->getFeedbackByID('requirement',$id,'published','published',$createdFeedBackTime->date,'');
                    }
                }else{ /*存在更新*/
                    //外部开始和截止时间 取接口更新时间
                    $dataOut =  $this->dealOutsideData($action);
                    $resOut = $this->dao->update(TABLE_REQUIREMENT)->data($dataOut)->where('id')->eq($id)->exec();
                    if($resOut) $count[] = $id;

                    $date = $action->date;//最新更新时间
                    //若存在反馈后，又出现的已发布→已发布，该场景数据过滤掉 其中feedbacked为历史状态，需要兼容
                    $createdFeedBackTime = $this->loadModel('requirement')->checkFeedBack('requirement',$id,'createfeedbacked','id_asc','',$date);
                    $feedBackTime = $this->loadModel('requirement')->checkFeedBack('requirement',$id,'feedbacked','id_asc','',$date);
                    if(!$createdFeedBackTime && !$feedBackTime)
                    {
                        $publishedInfo = $this->getConsumedInfoByID('requirement',$id,'published','published',$date);
                    }else if(!$createdFeedBackTime and $feedBackTime){
                        $publishedInfo = $this->getFeedbackByID('requirement',$id,'published','published',$feedBackTime->date,$date);
                    }else{
                        $publishedInfo = $this->getFeedbackByID('requirement',$id,'published','published',$createdFeedBackTime->date,$date);
                    }

                }

                /*内部开始和截止时间*/
                if(!empty($publishedInfo))
                {
                        $data = $this->dealInsideInfo($publishedInfo);
                        $res = $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($id)->exec();
                        if($res) $count[] = $id;
                }else{

                    //冗余方法，减少查询  待发布查询
                    if(!$action){

                        if(!$createdFeedBackTime && !$feedBackTime)
                        {
                            $topublishInfo = $this->getConsumedInfoByID('requirement',$id,'topublish','published');
                        }else if(!$createdFeedBackTime and $feedBackTime){
                            $topublishInfo = $this->getFeedbackByID('requirement',$id,'topublish','published',$feedBackTime->date,'');
                        }else{
                            $topublishInfo = $this->getFeedbackByID('requirement',$id,'topublish','published',$createdFeedBackTime->date,'');
                        }

                    }else{
                        //存在更新
                        $date = $action->date;//最新更新时间
                        if(!$createdFeedBackTime && !$feedBackTime)
                        {
                            $topublishInfo = $this->getConsumedInfoByID('requirement',$id,'topublish','published',$date);
                        }else if(!$createdFeedBackTime and $feedBackTime){
                            $topublishInfo = $this->getFeedbackByID('requirement',$id,'topublish','published',$feedBackTime->date,$date);
                        }else{
                            $topublishInfo = $this->getFeedbackByID('requirement',$id,'topublish','published',$createdFeedBackTime->date,$date);
                        }
                    }
                    //取待发布->已发布
                    if(!empty($topublishInfo))
                    {
                        $data = $this->dealInsideInfo($topublishInfo);
                        $res = $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($id)->exec();
                        if($res) $count[] = $id;
                    }
                }

            }
        }
        echo '执行数据：'.count($count).'条。被执行数据id:'.implode(',',array_unique($count));

    }

    /**
     * @Notes:需求任务是否外单位更新数据操作 用于导出
     * @Date: 2023/8/11
     * @Time: 17:32
     * @Interface ifOutUpdate
     */
    public function ifOutUpdate()
    {
        $actions = $this->dao->select('id,objectID')->from(TABLE_ACTION)
            ->where('objectType')->eq('requirement')
            ->andWhere('actor')->eq('guestcn')
            ->andWhere('action')->eq('edited')
            ->andWhere('comment')->eq('总中心接口同步更新')
            ->fetchALL();
        $requirementIDs = array_unique(array_column($actions,'objectID'));
        $this->dao->update(TABLE_REQUIREMENT)->set('ifOutUpdate')->eq(2)->where('id')->in($requirementIDs)->exec();
        echo '执行数据：'.count($requirementIDs).'条。被执行数据id:'.implode(',',$requirementIDs);
    }


    /**
     * @Notes: 构造内部超时的开始时间和截止时间入库数据
     * @Date: 2023/8/9
     * @Time: 14:53
     * @Interface dealInsideInfo
     * @param $info
     * @return stdClass
     */
    public function dealInsideInfo($info)
    {
        $this->app->loadLang('demand');
        //开始时间
        $feekBackStartTime = $info->createdDate;
        $hms = substr($feekBackStartTime,10);

        //截止时间
        $days = $this->lang->demand->expireDaysList['insideDays'];
        $feekBackEndTimeInside = helper::getTrueWorkDay($feekBackStartTime,$days,true).$hms; //结束时间
        $data = new stdClass();
        $data->feekBackStartTime     = $feekBackStartTime;
        $data->feekBackEndTimeInside = $feekBackEndTimeInside;

        return $data;
    }


    /**
     * @Notes: 构造外部超时的开始时间和截止时间入库数据
     * @Date: 2023/8/9
     * @Time: 14:53
     * @Interface dealOutsideData
     * @param $info
     * @return stdClass
     */
    public function dealOutsideData($info)
    {
        $this->app->loadLang('demand');
        //开始时间
        $feekBackStartTimeOutside = $info->date;
        $hms = substr($feekBackStartTimeOutside,10);
        //截止时间
        $days = $this->lang->demand->expireDaysList['outsideDays'];
        $feekBackEndTimeOutSide = helper::getTrueWorkDay($feekBackStartTimeOutside,$days,true).$hms; //结束时间

        $data = new stdClass();
        $data->feekBackStartTimeOutside     = $feekBackStartTimeOutside;
        $data->feekBackEndTimeOutSide = $feekBackEndTimeOutSide;

        return $data;
    }


    /**
     * @Notes:获取指定流转状态数据
     * @Date: 2023/8/9
     * @Time: 10:45
     * @Interface getConsumedInfoByID
     * @param $type
     * @param $id
     * @param $before
     * @param $after
     * @param $feedBackDate
     * @param $date
     * @return consumedModel
     */
    public function getFeedbackByID($type,$id,$before,$after,$feedBackDate='',$date='')
    {
        $info = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($type)
            ->andWhere('objectID')->eq($id)
            ->andWhere('`before`')->eq($before)
            ->andWhere('`after`')->eq($after)
            ->beginIF(!empty($feedBackDate))->andWhere('createdDate')->lt($feedBackDate)->fi()
            ->beginIF(!empty($date))->andWhere('createdDate')->gt($date)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetch();
        return $info;
    }

    public function getConsumedInfoByID($type,$id,$before,$after,$date='')
    {
        $info = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($type)
            ->andWhere('objectID')->eq($id)
            ->andWhere('`before`')->eq($before)
            ->andWhere('`after`')->eq($after)
            ->beginIF(!empty($date))->andWhere('createdDate')->gt($date)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetch();
        return $info;
    }

    /**
     * @Notes: 获取历史记录节点
     * @Date: 2023/8/9
     * @Time: 10:57
     * @Interface getActionsUpdate
     * @param $objectType
     * @param $objectID
     * @param $action
     * @param $order
     * @param $comment
     * @param $date
     * @return mixed
     */
    public function getActionsUpdate($objectType,$objectID,$action,$order,$comment,$date='')
    {
        $action = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('action')->eq($action)
            ->beginIF(!empty($comment))->andWhere('comment')->eq($comment)->fi()
            ->beginIF(!empty($date))->andWhere('`date`')->gt($date)->fi()
            ->orderBy($order)
            ->fetch();
        return $action;
    }


    /**
     * @Notes: 获取审批节点
     * @Date: 2023/8/9
     * @Time: 11:12
     * @Interface getNodeInfo
     * @param $field
     * @param $status
     * @param $version
     * @param $objectType
     * @param $id
     * @param $stage
     * @return string
     */
    public function getNodeInfo($field,$status,$version,$objectType,$id,$stage)
    {
        /* @var reviewModel $reviewModel */
        $reviewModel = $this->loadModel('review');
        $node = $reviewModel->getNodeInfoByParams($field,$status,$version,$objectType,$id,$stage);
        $passTime = '';
        if($node){
            $nodeInfo = $reviewModel->getReviewerInfoByParams('*',$status,$node->id);
            $passTime = $nodeInfo->reviewTime;
        }
        return $passTime;
    }

    /**
     * 生成项目空间默认阶段
     */
    public function createDefaultStage($projectid = null,$begin = 0, $end = 30 ){
       //查询所有未关闭的项目
       /* $projects = $this->dao->select('id,name')->from(TABLE_EXECUTION)
            ->where('deleted')->eq('0')
            ->andWhere('`type`')->eq('project')
            ->andWhere('status')->ne('closed')
            //->andWhere('id')->notIN('3166,1050,12540,22364,22374,22375,13790,1057,5,1055,22488,1058,1052,3664,7510,8990')
            ->beginIF($projectID)->andWhere('id')->eq($projectID)->fi()
            ->fetchAll();*/
        $where = $projectid ? "id = '$projectid'" : ' 1= 1';
        $projects = $this->dao->query("select id,name from zt_project
            where  deleted = '0' and  `type` ='project'  and status != 'closed' and $where  order by id asc lImiT $begin,$end")->fetchAll();
        if(empty($projects))
        {
            echo '所有单子处理完毕，无数据可执行！';
            die();
        }
        foreach ($projects as $key =>$project) {
            $projectID = $project->id;
            //查询当前项目是否已有默认阶段
            $stage = $this->dao->select('id,name')->from(TABLE_EXECUTION)
                ->where('deleted')->eq('0')
                ->andWhere('`type`')->eq('stage')
                ->andWhere('status')->ne('closed')
                ->andWhere('dataVersion')->eq('2')
                ->andWhere('project')->eq($projectID)
                ->fetchAll();
            if(count($stage) > 0){
                echo '项目空间id:  '.$projectID.' | '.$project->name. '| 已存在'.'<br>';
                continue;
            }
            try {
                 $this->loadModel('task')->approvalAutoCreateStageAndTask($projectID);
                 echo '项目空间id:  '.$projectID.' | '.$project->name. '| 生成成功'.'<br>';
            }catch(Exception $e){
                 echo '项目空间id:  '.$projectID.' | '.$project->name. '| 生成失败'.'异常： '.$e.'<br>';
            }
        }
        if(empty($projectid)){
           echo '处理了' . count($projects) . '条数据; 2秒后刷新页面，请等待执行完毕';
           $location = $this->createLink('history', 'createDefaultStage', array('$projectID' =>'', 'begin' => $begin + 30));
           header("refresh:2;url=$location");
        }
    }

    /**
     * 生成任务
     * @param int $begin
     * @param int $end
     */
    public function createDefaultTask($type,$begin = 0, $end = 100 ,$project = null ,$id = null){

        $typeArr = array('problem' => TABLE_PROBLEM,'demand' => TABLE_DEMAND,'demandinside' => TABLE_DEMANDINSIDE,'secondorder' => TABLE_SECONDORDER,'deptorder' => TABLE_DEPTORDER);//任务类型
        $typeName = array('problem' => "问题单",'demand' => "外部需求单",'demandinside' => "内部需求单",'secondorder' => "任务工单",'deptorder' => "部门工单");//任务类型
        //查询所有未关闭的项目
        $projects = $this->dao->select('id,name')->from(TABLE_EXECUTION)
            ->where('deleted')->eq('0')
            ->andWhere('`type`')->eq('project')
            ->andWhere('status')->ne('closed')
            ->beginIF($project)->andWhere('id')->in($project)->fi()
            //->andWhere('id')->in('8990,7510')
            ->fetchAll();
        //任务中间表查出所有生成任务的单子
        $projeceAll = implode(',',array_column($projects,'id'));
        /*$allType = $this->dao->query("select id,typeid,type,assignTo,taskid,product,version,application,project,execution from zt_task_demand_problem
            where deleted = '0' and project in ($projeceAll) and product != '0' and version != '0'  oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();*/
        $where = $type == 'problem' || $type == 'demand' ? "product != '0' and version != '0' and type = '$type'" : ( "1 = 1  and type = '$type'");
        $deleted = $type == 'problem' || $type == 'demand' ? "status !='deleted' and product !='0' and productPlan != '0'" : "deleted !='1'";
        $id = $id ? "and id = '$id'" : "and 1 = 1";

        $taskDemandProblem = $this->dao->query("select * from $typeArr[$type] where id in(select distinct(typeid) from zt_task_demand_problem
            where  deleted = '0' and taskid != ''  and project in ($projeceAll) and $where ) and $deleted $id oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();
        
        if(empty($taskDemandProblem))
        {
            echo '所有单子处理完毕，无数据可执行！';
            die();
        }
        foreach ($taskDemandProblem as $item) {
            //查询单子主表详细信息
            $data = $this->dao->select("*")->from($typeArr[$type])
                ->where('id')->eq($item->id)
                ->fetch();

            //把中间表此单删除
            if($type != 'problem'){
                $task = $this->dao->select("*")->from(TABLE_TASK_DEMAND_PROBLEM)
                    ->where('code')->eq($item->code)
                    ->andWhere('type')->eq($type)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();
                $oldTaskid = $task->taskid;
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq('1')->where('id')->eq($task->id)->exec();
                $data->dealUser = empty($data->dealUser) ? $task->assignTo : $data->dealUser;
                $data->app = strpos($data->app,',') !== false ? $task->application : $data->app;
            }
            if($type == 'secondorder'){
                $data->product = '99999';
                $data->productPlan = '1';
                $data->fixType = $data->implementationForm  ? $data->implementationForm : 'second';
                $data->projectPlan = empty($data->internalProject) ? $task->project : $data->internalProject;;
                $data->project = empty($data->internalProject) ? $task->project : $data->internalProject;
            }else if($type == 'demand' || $type == 'demandinside'){
                $data->projectPlan = $data->project;
            }else if($type == 'deptorder'){
                $projectId = $this->loadModel('deptorder')->getProjectPlanInfo($data->acceptDept);
                $data->product = 0;
                $data->execution = '';
                $data->productPlan = 0;
                $data->project = $projectId;
            }else{
                $data->product = $data->product;
                $data->productPlan = $data->productPlan;
                $data->project = $data->projectPlan;
            }
            if($type == 'demand'){
                $type = $item->sourceDemand == '1' ? 'demand' : 'demandinside';
                /*if($data->product == '0'){
                    continue;
                }*/
            }

            $problemArr = array();
            if($type == 'problem'){
                $products = explode(',',$data->product );
                $productPlans = explode(',',$data->productPlan );
                foreach ($products as  $k => $product1) {

                    $problemTask = $this->dao->select("*")->from(TABLE_TASK_DEMAND_PROBLEM)
                        ->where('code')->eq($item->code)
                        ->andWhere('project')->eq($data->project)
                        ->andWhere('product')->eq($product1)
                        ->andWhere('version')->eq($productPlans[$k])
                        ->beginIF(strpos($data->app,',') !== false)->andWhere('application')->in($data->app)->fi()
                        ->beginIF(strpos($data->app,',') == false)->andWhere('application')->eq($data->app)->fi()
                        ->andWhere('deleted')->eq('0')
                        ->fetch();
                    $oldTaskid = $problemTask->taskid;
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq('1')->where('id')->eq($problemTask->id)->exec();
                    $data->dealUser = empty($data->dealUser) ? $problemTask->assignTo : $data->dealUser;
                    $data->app = strpos($data->app,',') !== false ? $problemTask->application : $data->app;
                    $problemArr[$k] = $data;
                    $problemArr[$k]->product = $product1;
                    $problemArr[$k]->productPlan = $productPlans[$k];
                    $taskID = $this->loadModel('problem')->toTaskProblemDemand($problemArr[$k],$item->id,$type); //新增关联表
                    if($taskID){
                        try{
                            $this->loadModel('task')->assignedAutoCreateStageTask($problemArr[$k]->project,$type ,$problemArr[$k]->app,$problemArr[$k]->code,$problemArr[$k]);
                            $taskDemandProblemID = $this->dao->select('taskid,execution')->from(TABLE_TASK_DEMAND_PROBLEM)
                                ->where('deleted')->eq('0')
                                ->andWhere('code')->eq($problemArr[$k]->code)
                                ->orderBy('id_desc')
                                ->fetch();
                            $oldTask =  $this->getTaskName($oldTaskid);
                            if(isset($taskDemandProblemID->taskid)){
                                $newTask =  $this->getTaskName($taskDemandProblemID->taskid);
                                $this->loadModel('action')->create($type, $problemArr[$k]->id, 'deal', '根据报工规则重新分析<br>原任务ID:' .$oldTaskid.' :'.$oldTask.'<br>'.' 新任务ID：' .$taskDemandProblemID->taskid.' :'.$newTask);
                                echo $typeName[$type] .' id:' .$problemArr[$k]->id . '  code:' .$problemArr[$k]->code . '| 生成任务成功'.'<br>';
                            }else{
                                echo $typeName[$type] .' id:' .$problemArr[$k]->id . '  code:' .$problemArr[$k]->code . '| 生成任务失败 '  .'<br>';
                            }

                        }catch(Exception $e){
                            echo $typeName[$type] .' id:' .$problemArr[$k]->id . '  code:' .$problemArr[$k]->code . '| 生成任务失败 ' .' 异常： '.$e .'<br>';
                        }
                    }
                }
            }
            else{
                $taskID = $this->loadModel('problem')->toTaskProblemDemand($data,$item->id,$type); //新增关联表
                if($taskID){
                    try{
                        $this->loadModel('task')->assignedAutoCreateStageTask($data->project,$type ,$data->app,$data->code,$data);
                        if($type == 'demandinside'){
                            $type =  'demand';
                        }
                        $taskDemandProblemID = $this->dao->select('taskid,execution')->from(TABLE_TASK_DEMAND_PROBLEM)
                            ->where('deleted')->eq('0')
                            ->andWhere('code')->eq($data->code)
                            ->orderBy('id_desc')
                            ->fetch();
                        $oldTask =  $this->getTaskName($oldTaskid);
                        if(isset($taskDemandProblemID->taskid)){
                            $newTask =  $this->getTaskName($taskDemandProblemID->taskid);
                            $this->loadModel('action')->create($type, $data->id, 'deal', '根据报工规则重新分析<br>原任务ID:' .$oldTaskid.' :'.$oldTask.'<br>'.' 新任务ID：' .$taskDemandProblemID->taskid.' :'.$newTask);
                            echo $typeName[$type] .' id:' .$data->id . '  code:' .$data->code . '| 生成任务成功'.'<br>';
                        }else{
                            echo $typeName[$type] .' id:' .$data->id . '  code:' .$data->code . '| 生成任务失败 ' .'<br>';
                        }

                    }catch(Exception $e){
                        echo $typeName[$type] .' id:' .$data->id . '  code:' .$data->code . '| 生成任务失败 ' .' 异常： '.$e .'<br>';
                    }
                }
            }
        }
        if(empty($project)){
            echo '处理了' . count($taskDemandProblem) . '条数据; 2秒后刷新页面，请等待执行完毕';
            $location = $this->createLink('history', 'createDefaultTask', array('type' =>$type, 'begin' => $begin + 100));
            header("refresh:2;url=$location");
        }

    }
    public function getTaskName($taskid){


            //$taskOne = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t1.execution ' )->from(TABLE_TASK)->alias('t1')
               /* ->leftJoin(TABLE_TASK)->alias('t2')
                ->on('t1.id = t2.parent')*/
        $taskOne = $this->dao->select("(case when t1.parent  !='0' then concat((select name from zt_task where id = t1.parent), '/',t1.name) 
	          else t1.name end)  name,t1.id,t1.execution  " )->from(TABLE_TASK)->alias('t1')
                ->where('t1.deleted')->eq('0')
                ->andWhere('t1.id')->in($taskid)
                ->fetch();
           
            //if($taskOne->execution){
               /* $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t2.parent')->from(TABLE_EXECUTION)->alias('t1')
                    ->leftJoin(TABLE_EXECUTION)->alias('t2')
                    ->on('t1.id = t2.parent')*/
          $executions = $this->dao->select("(case when t1.parent  !='0' then concat((select name from zt_project where id = t1.parent), '/',t1.name) 
	          else t1.name end)  name,t1.id,t1.parent")->from(TABLE_EXECUTION)->alias('t1')
                    ->where('t1.deleted')->eq('0')
                    ->andWhere('t1.id')->in($taskOne->execution)
                    ->fetch();
      
           // }

            $executionList =  isset($executions->name) ? $executions->name.'/'.$taskOne->name : $taskOne->name;
            $executionList = empty($executionList) ||  $executionList == '/' ? '' : $executionList;
        return $executionList;
   }

    /**
     * 内外部反馈首次通过时间
     * @return void
     */
    public function saveFeedbackTime()
    {
        $reviewModel = $this->loadModel('review');
        $problems    = $this->dao->select('id,dealAssigned,createdBy')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('createdBy')->in(['guestjx','guestcn'])
            ->andWhere('createdDate')->ge('2022-08-01')
            ->fetchAll();

        $ids = [];
        foreach ($problems as $problem){
            $actionInfo = $this->dao->select('objectID,max(date) as date')->from(TABLE_ACTION)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere('action')->eq('update')
                ->groupBy('objectID')
                ->fetch();

            $data = ['feedbackStartTimeInside' => $problem->dealAssigned];
            if(!empty($actionInfo)){
                $data['isChange'] = 1;
                $data['feedbackStartTimeOutside'] = $actionInfo->date;
            }else{
                $createdInfo = $this->dao
                    ->select('objectID, max(date) as createdDate')
                    ->from(TABLE_ACTION)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($problem->id)
                    ->andWhere('action')->eq('created')
                    ->groupBy('objectID')
                    ->fetch();
                $data['feedbackStartTimeOutside'] = $createdInfo->createdDate;
            }
            $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('`status`')->eq('pass')
                ->andWhere('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->beginIF(!empty($actionInfo) && substr($actionInfo->date, 0, 10) != '0000-00-00')
                ->andWhere('createdDate')->ge(substr($actionInfo->date, 0, 10))
                ->fi()
                ->andWhere('stage')->eq(1)->fetch();
            if($node){
                $nodeInfo = $reviewModel->getReviewerInfoByParams('id,reviewTime','pass',$node->id);
                $data['deptPassTime'] = $nodeInfo->reviewTime;
            }
            $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('`status`')->in(['syncsuccess','jxsyncsuccess'])
                ->andWhere('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->beginIF(!empty($actionInfo) && substr($actionInfo->date, 0, 10) != '0000-00-00')
                ->andWhere('createdDate')->ge(substr($actionInfo->date, 0, 10))
                ->fi()
                ->andWhere('stage')->eq(3)->fetch();
            if ($node) {
                if($problem->createdBy == 'guestcn'){
                    $nodeOutInfo = $reviewModel->getReviewerInfoByParams('id,reviewTime', 'syncsuccess', $node->id);
                }else{
                    $nodeOutInfo = $reviewModel->getReviewerInfoByParams('id,reviewTime', 'jxsyncsuccess', $node->id);
                }
                $data['innovationPassTime'] = $nodeOutInfo->reviewTime;
            }

            if(!empty($data)){
                $data['feedbackEndTimeInside'] = $data['feedbackEndTimeOutside'] = '';
                $res = $this->dao->update(TABLE_PROBLEM)->data($data)->where('id')->eq($problem->id)->exec();
                if($res){
                    $ids[] = $problem->id;
                }
            }
        }
        echo '获取问题池反馈单部门首次通过时间和产创首次通过时间、外单位是否更新,问题单数量：' . count($ids) . '<br/>';
        echo '获取问题池反馈单部门首次通过时间和产创首次通过时间、外单位是否更新,问题单ID：' . implode(',', $ids);
    }

    /**
     * 所有用户新增我要报工区块
     * @param null $account
     */
    public function addBlock($account = null){
        //查询所有用户
        $allAccount = $this->dao->select('id,account')->from(TABLE_USER)
            ->where('deleted')->eq('0')
            ->beginIF($account)->andWhere('account')->eq($account)->fi()
            ->fetchPairs('id','account');
        foreach ($allAccount as $key => $account){
            //查询当前用户区块
            $accountBlock = $this->dao->select('*')->from(TABLE_BLOCK)
                ->where('account')->eq($account)->andWhere('module')->eq('my')->orderBy("order asc")->fetchAll();
            try{
                $var = 12;
                foreach($accountBlock as $block){
                    $var +=1;
                    $this->dao->update(TABLE_BLOCK)->set('order')->eq($var)->where('id')->eq($block->id)->exec();
                }
                $sql = "INSERT INTO zt_block(id, account, module, `type`, title, source, block, params, `order`, grid, height, hidden) VALUES(NULL,'$account' , 'my', '', '我要报工', 'workreport', 'list', '{:}', 0, 4, 0, 0)";
                $this->dao->query($sql);
                echo '用户: '.$account.'新增我要报工成功！'.'<br>';
            }catch(Exception $e  ){
                echo '用户: '.$account.'新增我要报工失败！，异常'.$e.'<br>';
            }
        }
    }
    public function workreportToEmail($flag){
      $this->loadModel('workreport')->sendmail($flag);
      echo $flag == '1' ? '周提醒成功' : '月提醒成功';
    }
   /**
     * 已生成的阶段新增
     * @param $projectID
     * @param $stageID
     */
    public function addStage($projectID = null){

        $this->app->loadLang('task');
        $projectManger = $this->lang->task->stageList['projectManger'];//项目管理活动
        //$projectPlan = $this->lang->task->stageSecondList['projectPlan'];//计划
        $projectTechnology = $this->lang->task->stageSecondList['projectTechnology'];//工程实施
        $projectTechnologyTask = $this->lang->task->threeTaskList['projectTechnologyTask']; //工程实施任务
        //查询所有 有项目管理活动阶段的项目
        $allProject = $this->dao->select('id,project,begin,end,path')->from(TABLE_EXECUTION)
            ->where('name')->eq($projectManger)
            ->andWhere('grade')->eq('1')
            ->andWhere('`type`')->eq('stage')
            ->andWhere('dataVersion')->eq('2')
            ->beginIF($projectID)->andWhere('project')->eq("$projectID")->fi()
            ->fetchAll();
        if(!$allProject){
           echo '该项目不存在或该项目下没有项目管理活动一级阶段！'.'<br>';
        }
        foreach ($allProject as $item) {
            $second = $this->dao->select('id,project,begin,end,path')->from(TABLE_EXECUTION)
                ->where('name')->eq($projectTechnology)
                ->andWhere('grade')->eq('2')
                ->andWhere('`type`')->eq('stage')
                ->andWhere('project')->eq("$item->project")
                ->andWhere('dataVersion')->eq('2')
                ->fetch();
            if($second) {
                echo '项目id :'.$item->project .'二及阶段及三级任务已存在！'.'<br>';
                continue;
            }
            //生成二级阶段
           $stageID =  $this->autoStage($item->project,$projectTechnology,2,$item,$item->begin,$item->end);
           $this->createTaskThreeObject($projectTechnologyTask,$stageID,$item->project,$item->begin,$item->end);
           echo '项目id :'.$item->project  .'二及阶段及三级任务新增成功！'.'<br>';
        }
    }
    /*
     * 自动创建阶段
     */
    public function autoStage($projectID,$name,$grade,$parent,$begin,$end){
        $execution = new stdClass();
        $execution->project      = $projectID;
        $execution->parent       = $parent != '0' ? $parent->id : $parent;
        $execution->name         = $name;
        $execution->type         = 'stage';
        $execution->resource     = '';
        $execution->begin        = $begin;
        $execution->end          = $end;
        $execution->planDuration = helper::diffDate3($execution->end , $execution->begin);
        $execution->grade        = $grade;
        $execution->openedBy     = 'admin';
        $execution->openedDate   = helper::today();
        $execution->status       = 'wait';
        $execution->milestone    = 0;
        $execution->version      = 1;
        $execution->dataVersion  = 2;

        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
        $executionID = $this->dao->lastInsertID();

        //记录到版本库
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->version      = 1;
        $spec->name         = $execution->name;
        $spec->milestone    = $execution->milestone;
        $spec->begin        = $execution->begin;
        $spec->end          = $execution->end;
        $spec->planDuration = $execution->planDuration;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

        $path = ($grade == 1 ? ',' . $projectID . ',' : ($parent != '0' ? $parent->path : '')) . $executionID . ',';
        $order = $executionID * 5;
        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();
        return $executionID;
    }
    /**
     * 新增三级任务
     * @param $taskname
     * @param $stagename
     * @param $projectID
     * @param $stage
     * @param $data
     */
    public function createTaskThreeObject($threeName,$execution,$projectID,$begin,$end){
       unset($_POST);
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type']      = 'devel';// 任务类型
        $_POST['module']    =  0;//所属模块
        $_POST['assignedTo']     = array('0'=>'');//指派给
        $_POST['mailto']    = array('0'=>'');//$data->mailto;
        $_POST['name']      =  $threeName;// 任务名称
        $_POST['estStarted'] = $begin;//预计开始
        $_POST['deadline']   = $end; //预计结束
        $_POST['openedBy']   = $this->app->user->account; //由谁创建
        $_POST['openedDate'] = date('Y-m-d'); //创建时间
        $_POST['pri']    = 1;//优先级
        $_POST['status'] = 'wait';//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['design'] = 0;
        $_POST['color'] = '';
        $_POST['dropType']  = '0';
        $_POST['dataVersion']  = '2';

        $taskID = $this->loadModel('task')->newTask($projectID,$execution,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0);
        $this->dao->update(TABLE_TASK)->set('path')->eq($taskID)->where('id')->eq($taskID)->exec();
    }

    public function problemUpdateDealAssigned()
    {
        $data = $this->dao->select('*')->from(TABLE_PROBLEM)->where('status')->ne('deleted')->fetchAll();

        $ids = [];
        $date = '';
        foreach ($data as $item){
            $consumedInfo = $this->dao->select('*')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('deleted')->eq('0')
                ->andWhere('`before`')->eq('confirmed')
                ->andWhere('`after`')->eq('assigned')
                ->orderBy('id desc')
                ->fetch();
            $date = $consumedInfo->createdDate ?? '';
            if(!isset($consumedInfo->createdDate)){
                $consumedInfo = $this->dao->select('*')
                    ->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($item->id)
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('`before`')->eq('confirmed')
                    ->andWhere('`after`')->eq('closed')
                    ->orderBy('id desc')
                    ->fetch();
                if(isset($consumedInfo->createdDate)){
                    $actionInfo = $this->dao->select('*')
                        ->from(TABLE_ACTION)
                        ->where('objectType')->eq('problem')
                        ->andWhere('objectID')->eq($item->id)
                        ->andWhere('action')->eq('created')
                        ->fetch();
                    $date = $actionInfo->date;
                }
            }

            if(empty($date) || $date == $item->dealAssigned){
                continue;
            }
            $this->dao->update(TABLE_PROBLEM)->set('dealAssigned')->eq($date)->where('id')->eq($item->id)->exec();
            $ids[] = [$item->id, $item->code];
        }

        echo '待分配到待分析时间更新了' . count($ids) . '条';
        a($ids);
    }

    public function problemFeedbackTime()
    {
        $data = $this->dao->select('*')->from(TABLE_PROBLEM)->where('status')->ne('deleted')->fetchAll();

        $ids = [];
        foreach ($data as $item){
            $arr = [];
            $item = $this->loadModel('problem')->getIfOverDate($item);
            if(!empty($item->feedbackStartTimeInside) && (empty($item->feedbackEndTimeInside) || strpos($item->feedbackEndTimeInside, '0000') !== false) && isset($item->ifOverDateInside)){
                $arr['feedbackEndTimeInside'] = $item->ifOverDateInside['end'] ?? '';
            }
            if(!empty($item->feedbackStartTimeOutside) && (empty($item->feedbackEndTimeOutside) || strpos($item->feedbackEndTimeOutside, '0000') !== false)){
                $arr['feedbackEndTimeOutside'] = $item->ifOverDate['end'] ?? '';
            }

            if(!empty($arr)){
                $this->dao->update(TABLE_PROBLEM)->data($arr)->where('id')->eq($item->id)->exec();
                $ids[] = [$item->id, $item->code];
            }
        }

        echo '内外部反馈时间更新了' . count($ids) . '条';
        a($ids);
    }


    /**
     * Process use case data from older versions of the system.
     *
     * @param  int    $begin
     * @param  int    $end
     * @access public
     * @return string
     */
    public function processCase($begin = 0, $end = 100)
    {
        $cases = $this->dao->select("id,bugs,results,caseFails,stepNumber")->from(TABLE_CASE)->orderBy('id')->limit("$begin,$end")->fetchAll('id');
        if(empty($cases))
        {
            echo '所有Case处理完毕，无数据可执行！';
            die();
        }

        $caseIdList = array_keys($cases);
        $cases      = $this->appendCaseData($cases, $caseIdList);
        foreach($cases as $case)
        {
            $this->dao->update(TABLE_CASE)
                ->set('bugs')->eq($case->bugs)
                ->set('results')->eq($case->executions)
                ->set('caseFails')->eq($case->fails)
                ->set('stepNumber')->eq($case->steps)
                ->where('id')->eq($case->id)
                ->exec();
        }

        /* Update the use case associated with the test task. */
        $taskcaseList  = $this->dao->select('id,taskBugs,taskResults,taskCaseFails,taskStepNumber')->from(TABLE_TESTRUN)->where('`case`')->in($caseIdList)->fetchAll('id');
        $testRunIdList = array_keys($taskcaseList);
        $taskcaseList  = $this->appendTaskCaseData($taskcaseList, $testRunIdList);
        foreach($taskcaseList as $testcase)
        {
            $this->dao->update(TABLE_TESTRUN)
                ->set('taskBugs')->eq($testcase->taskBugs)
                ->set('taskResults')->eq($testcase->taskExecutions)
                ->set('taskCaseFails')->eq($testcase->taskFails)
                ->set('taskStepNumber')->eq($testcase->taskSteps)
                ->where('id')->eq($testcase->id)
                ->exec();
        }

        echo '处理了' . count($cases) . '条数据; 1秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processCase', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    /**
     * Append bugs, executions, fails and steps fields.
     *
     * @param  int    $cases
     * @param  array  $caseIdList
     * @access public
     * @return object
     */
    public function appendCaseData($cases, $caseIdList = array())
    {
        $bugs       = $this->dao->select('count(*) as count, `case`')->from(TABLE_BUG)->where('`case`')->in($caseIdList)->andWhere('deleted')->eq(0)->groupBy('`case`')->fetchPairs('case', 'count');
        $executions = $this->dao->select('count(*) as count, `case`')->from(TABLE_TESTRESULT)->where('`case`')->in($caseIdList)->groupBy('`case`')->fetchPairs('case', 'count');

        $fails = $this->dao->select('count(*) as count, `case`')->from(TABLE_TESTRESULT)
            ->where('caseResult')->eq('fail')
            ->andwhere('`case`')->in($caseIdList)
            ->groupBy('`case`')
            ->fetchPairs('case','count');

        $steps = $this->dao->select('count(distinct t1.id) as count, t1.`case`')->from(TABLE_CASESTEP)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.`case`=t2.`id`')
            ->where('t1.`case`')->in($caseIdList)
            ->andWhere('t1.type')->ne('group')
            ->andWhere('t1.version=t2.version')
            ->groupBy('t1.`case`')
            ->fetchPairs('case', 'count');

        foreach($cases as $key => $case)
        {
            $caseID = $case->id;

            $case->bugs       = isset($bugs[$caseID])       ? $bugs[$caseID]       : 0;
            $case->executions = isset($executions[$caseID]) ? $executions[$caseID] : 0;
            $case->fails      = isset($fails[$caseID])      ? $fails[$caseID]      : 0;
            $case->steps      = isset($steps[$caseID])      ? $steps[$caseID]      : 0;
        }

        return $cases;
    }

    /**
     * Append taskBugs, taskExecutions, taskFails and taskSteps fields.
     *
     * @param  int    $cases
     * @param  array  $caseIdList
     * @access public
     * @return object
     */
    public function appendTaskCaseData($cases, $caseIdList = array())
    {
        $bugs       = $this->dao->select('count(*) as count, `result`')->from(TABLE_BUG)->where('`result`')->in($caseIdList)->andWhere('deleted')->eq(0)->groupBy('`result`')->fetchPairs('result', 'count');
        $executions = $this->dao->select('count(*) as count, `run`')->from(TABLE_TESTRESULT)->where('`run`')->in($caseIdList)->groupBy('`run`')->fetchPairs('run', 'count');

        $fails = $this->dao->select('count(*) as count, `run`')->from(TABLE_TESTRESULT)
            ->where('caseResult')->eq('fail')
            ->andwhere('`run`')->in($caseIdList)
            ->groupBy('`run`')
            ->fetchPairs('run','count');

        $steps = $this->dao->select('count(distinct t1.id) as count, t2.`id` as run')->from(TABLE_CASESTEP)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.`case`=t2.`case`')
            ->where('t2.`id`')->in($caseIdList)
            ->andWhere('t1.type')->ne('group')
            ->andWhere('t1.version=t2.version')
            ->groupBy('t1.`case`')
            ->fetchPairs('run', 'count');

        foreach($cases as $key => $case)
        {
            $caseID = $case->id;
            $case->taskBugs       = isset($bugs[$caseID])       ? $bugs[$caseID]       : 0;
            $case->taskExecutions = isset($executions[$caseID]) ? $executions[$caseID] : 0;
            $case->taskFails      = isset($fails[$caseID])      ? $fails[$caseID]      : 0;
            $case->taskSteps      = isset($steps[$caseID])      ? $steps[$caseID]      : 0;
        }

        return $cases;
    }

    /* 将旧的testtask关联字段数据迁移到新字段linkTesttask上 */
    public function processLinkTesttask($begin = 0, $end = 100)
    {
        $this->dao->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $bugFields = $this->dao->query("DESC zt_bug")->fetchAll();

        $isTesttaskExist = false;

        foreach ($bugFields as $field) {
            if($field->field == 'testtask')
            {
                $isTesttaskExist = true;
                break;
            }
        }

        if(!$isTesttaskExist)
        {
            echo 'testtask字段不存在，无须处理！';
            die();
        }

        $bugs = $this->dao->query("SELECT * FROM `zt_bug` WHERE linkTesttask = '' AND testtask != 0 oRdEr bY `id` asc lImiT $begin,$end")->fetchAll();

        if(empty($bugs))
        {
            echo '所有Bug处理完毕，无数据可执行！';
            /*$this->dao->exec("ALTER TABLE zt_bug DROP COLUMN testtask;");
            echo '<br>删除废弃字段完成！';*/
            die();
        }

        foreach($bugs as $bug)
        {
            $this->dao->update(TABLE_BUG)->set('linkTesttask')->eq($bug->testtask)->where('id')->eq($bug->id)->exec();
        }

        echo '处理了' . count($bugs) . '条数据; 3秒后刷新页面，请等待执行完毕';
        $location = $this->createLink('history', 'processLinkTesttask', array('begin' => $begin + 100));
        header("refresh:1;url=$location");
    }

    //展示 要进行状态联动的数据
    public function demandLinkAges(){
        $this->app->loadLang('demand');
        $fileds = 'id,demandId,createdDate,code,status';
        $modifyLinkStatus     = $this->lang->demand->linkage['modify'];
        $modifylinkStatusAll  = array_merge($modifyLinkStatus['delivery'],$modifyLinkStatus['changeabnormal'],$modifyLinkStatus['chanereturn'],$modifyLinkStatus['onlinesuccess']);

        $outwardDeliveryLinkStatus     = $this->lang->demand->linkage['outwarddelivery'];
        $outwardDeliverylinkStatusAll  = array_merge($outwardDeliveryLinkStatus['delivery'],$outwardDeliveryLinkStatus['changeabnormal'],$outwardDeliveryLinkStatus['chanereturn'],$outwardDeliveryLinkStatus['onlinesuccess']);
        $modifys = $this->dao->select($fileds.',realEndTime')
            ->from(TABLE_MODIFY)
            ->where('abnormalCode')->eq('')
            ->andWhere('demandId')->ne('')
            ->andWhere('status')->in($modifylinkStatusAll)
            ->fetchAll();
        $outwardDeliverys = $this->dao->select($fileds)
            ->from(TABLE_OUTWARDDELIVERY)
            ->where('abnormalCode')->eq('')
            ->andWhere('demandId')->ne('')
            ->andWhere('deleted')->eq(0)
            ->andWhere('isNewModifycncc')->eq('1')
            ->andWhere('status')->in($outwardDeliverylinkStatusAll)
            ->fetchAll();
        $outwardDemandId  = trim(implode(',',array_column($outwardDeliverys,'demandId')));
        $modifyDemand     = trim(implode(',',array_column($modifys,'demandId')),',');
        $demandId         = $outwardDemandId.','.$modifyDemand;
        $demandId         = array_unique(array_filter(explode(',',$demandId)));
        $demands = $this->dao->select('id,status,code,title')->from(TABLE_DEMAND)
            ->where('id')->in($demandId)
            ->andwhere('status')->notIN("wait,closed,suspend,deleted")
            ->andWhere('secureStatusLinkage')->eq('0')
            ->andWhere('sourceDemand')->eq(1)
            ->fetchall('id');
        $onlineTimeList = [];//上线时间
        $onlineIdList = [];
        $statusKeys = array_keys($modifyLinkStatus);
        $newDemand = [];
        $modifyAbnormalCode = [];
        $outwardDeliveryAbnormalCode = [];
        $unsetDemand = [];
        foreach ($demands as $demand) {
            foreach ($modifys as $modify) {
                $modifyDemand = explode(',',trim($modify->demandId,','));
                if (in_array($demand->id,$modifyDemand)){
                    foreach ($modifyLinkStatus as $modifyLinkStatusKey => $modifyLinkStatusVal) {
                        if (in_array($modify->status,$modifyLinkStatusVal)){
                            $modifystatusKey = array_search($modifyLinkStatusKey,$statusKeys);
                            $newDemand[$demand->id][] = $modifystatusKey;
                            //变更成功 联动为上线成功
                            if (!isset($onlineIdList[$demand->id]['status']) || $modifystatusKey < $onlineIdList[$demand->id]['status']){
                                $onlineIdList[$demand->id]['codeStr'] = $modify->code."($modify->id)";
                                $onlineIdList[$demand->id]['status']  = $modifystatusKey;
                            }
                            //变更异常
                            if (in_array($modify->status,$modifyLinkStatus['changeabnormal'])){
                                $modifyAbnormalCode[] = $modify->code;
                            }
                            if (in_array($modify->status,$modifyLinkStatus['onlinesuccess'])){
                                if(empty($onlineTimeList[$demand->id]) || $modify->realEndTime  > $onlineTimeList[$demand->id]){
                                    $onlineTimeList[$demand->id] = $modify->realEndTime;
                                }
                            }
                        }
                        if (!in_array($modify->status,$modifylinkStatusAll)){
                            $unsetDemand[$demand->id] = $demand->id;
                        }
                    }
                }
            }

            foreach ($outwardDeliverys as $outwardDelivery) {
                $outwardDeliveryDemand = explode(',',trim($outwardDelivery->demandId,','));
                if (in_array($demand->id,$outwardDeliveryDemand)){
                    foreach ($outwardDeliveryLinkStatus as $outwardDeliveryLinkStatuskey => $outwardDeliveryLinkStatusVal) {
                        if (in_array($outwardDelivery->status,$outwardDeliveryLinkStatusVal)){
                            $outwardDeliverystatuskey = array_search($outwardDeliveryLinkStatuskey,$statusKeys);
                            $newDemand[$demand->id][] = $outwardDeliverystatuskey;

                            if (!isset($onlineIdList[$demand->id]['status']) || $outwardDeliverystatuskey < $onlineIdList[$demand->id]['status']){
                                $onlineIdList[$demand->id]['codeStr'] = $outwardDelivery->code."($outwardDelivery->id)";
                                $onlineIdList[$demand->id]['status']  = $outwardDeliverystatuskey;
                            }
                            //变更异常
                            if (in_array($outwardDelivery->status,$outwardDeliveryLinkStatus['changeabnormal'])){
                                $outwardDeliveryAbnormalCode[] = $outwardDelivery->code;
                            }
                            if (in_array($outwardDelivery->status,$outwardDeliveryLinkStatus['onlinesuccess'])){
                                $lastDealDate = $this->dao->select('actualEnd')->from(TABLE_MODIFYCNCC)->where('id')->eq($outwardDelivery->modifycnccId)->fetch('actualEnd');

                                if(empty($onlineTimeList[$demand->id]) || $lastDealDate > $onlineTimeList[$demand->id]){
                                    $onlineTimeList[$demand->id] = $lastDealDate;
                                }
                            }
                        }
                        if (!in_array($outwardDelivery->status,$outwardDeliverylinkStatusAll)){
                            $unsetDemand[$demand->id] = $demand->id;
                        }
                    }
                }
            }
        }
        $this->app->loadLang('modify');
        $this->app->loadLang('outwarddelivery');
        //业务确认不需要再处理的需求条目
        $noDealDeamdId = [7,11,12,15,23,27,40,44,53,220,313];
        $dealDemand = [];
        foreach ($newDemand as $k=>$v) {
            //取最小状态值
            $status = $statusKeys[min($v)];
            if ($status != $demands[$k]->status && !in_array($k,$noDealDeamdId)){
                $params = new stdClass();
                $params->status              = $status;
                $params->actualOnlineDate    = null;
                if ($status == 'onlinesuccess'){
                    $params->actualOnlineDate    = $onlineTimeList[$k];
                }
                $this->dao->update(TABLE_DEMAND)->data($params)->where('id')->eq($k)->exec();
                $this->loadModel('consumed')->recordAuto('demand', $k, 0, $demands[$k]->status, $status);
                $code = $onlineIdList[$k]['codeStr'];
                $comment = sprintf($this->lang->action->actionNotesDesc,$code.' : '.$this->lang->demand->statusList[$demands[$k]->status], $this->lang->demand->statusList[$status]);
                $this->loadModel('action')->create('demand', $k, 'linkagestatus', $comment,'','guestjk');
                $dealDemand[] = $k;
            }
        }
        //异常联动过一次后就不再参与状态联动
        if (!empty($modifyAbnormalCode)){
            $this->dao->update(TABLE_MODIFY)->set('demandLinked')->eq('1')->where('`code`')->in(array_unique($modifyAbnormalCode))->exec();
        }
        if (!empty($outwardDeliveryAbnormalCode)){
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('demandLinked')->eq('1')->where('`code`')->in(array_unique($outwardDeliveryAbnormalCode))->exec();
        }
        echo '已处理的需求条目ID：'.implode(',',$dealDemand);
    }

    /**
     * @Notes:需求任务实际实现方式脚本
     * @Date: 2023/10/26
     * @Time: 14:40
     * @Interface actualFixtype
     */
    public function actualFixtype()
    {
        $requirement  = $this->dao->select('id,method,actualMethod')->from(TABLE_REQUIREMENT)
            ->where('sourceRequirement')->eq(1)
            ->andWhere('status')->ne('deleted')
            ->fetchAll();
        $ids = [];
        foreach ($requirement as $v)
        {
            //有需求条目
            $method = []; //需求任务反馈单选择的实现方式  project:项目实现 patch:二线实现
            $requirementID = $v->id;
            $insertMethod = $v->method;
            $actualMethod = $v->actualMethod;
            if($insertMethod == 'patch') {
                $method[] = 'second';
            }else{
                $method[] = $insertMethod;
            }
            $getDemandByRequirement = $this->dao->select('id,fixType')->from(TABLE_DEMAND)->where('requirementID')->eq($requirementID)->andWhere('sourceDemand')->eq(1)->andWhere('`status`')->ne('deleted')->fetchAll();
            //有需求条目
            if(!empty($getDemandByRequirement))
            {
                //需求任务导出新增的【实际实现方式】取值逻辑调整：任务反馈单所选的实现方式 需求条目所选的实现方式，去重取并集。逗号间隔（例如：项目实现，二线实现）
                $demandFixType = array_column($getDemandByRequirement,'fixType');
                $str = implode(',',array_filter(array_unique(array_merge($demandFixType,$method))));
                if($str != $actualMethod)
                {
                    $res = $this->dao->update(TABLE_REQUIREMENT)->set('actualMethod')->eq($str)->where('id')->eq($requirementID)->exec();
                    if($res)
                    {
                        $ids[] = $requirementID;

                    }
                }
            }else{ //无需求条目
                if(!empty($method) && $insertMethod != $actualMethod)
                {
                    if($insertMethod == 'patch')
                    {
                        $insertMethod = 'second';
                    }
                    $res = $this->dao->update(TABLE_REQUIREMENT)->set('actualMethod')->eq($insertMethod)->where('id')->eq($requirementID)->exec();
                    if($res)
                    {
                        $ids[] = $v->id;

                    }
                }
            }

        }
        echo '更新了' . count($ids) . '条';
        a($ids);
    }

    /**
     * @Notes:需求任务最新发布时间
     * @Date: 2023/11/3
     * @Time: 15:46
     * @Interface newPublishedTime
     */
    public function newPublishedTime($begin = 0)
    {

        $requirements = $this->dao->select('id')->from(TABLE_REQUIREMENT)
            ->where('`status`')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(1)
            ->andWhere('id')->between($begin,$begin+400)
            ->orderby('id asc')
            ->fetchAll();
        if(empty($requirements))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }

        foreach($requirements as $requirement)
        {

            $consumedInfo = $this->loadModel('consumed')->getCreatedDate('requirement',$requirement->id,'','published');

            if($consumedInfo)
            {
                 $this->dao->update(TABLE_REQUIREMENT)->set('newPublishedTime')->eq($consumedInfo->createdDate)->where('id')->eq($requirement->id)->exec();
            }

        }


        echo '处理了' . count($requirements) . '个任务。1秒后自动跳转，请等待处理完毕。';
        $location = $this->createLink('history', 'newPublishedTime', array('begin' => $begin + 400));
        header("refresh:1;url=$location");
    }


    /**
     * @Notes:更新需求条目交付时间
     * @Date: 2023/11/17
     * @Time: 9:50
     * @Interface solvedTimeAboutDemand
     * @param int $begin
     */
    public function solvedTimeAboutDemand($begin = 0)
    {
        /** @var problemModel $problemModel */
        $problemModel = $this->loadModel('problem');
        $demands = $this->dao->select('*')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('sourceDemand')->eq(1)
            ->andWhere('id')->between($begin,$begin+300)
            ->fetchALL();
        if(empty($demands))
        {
            echo '所有任务处理完毕，无数据可执行！';
            die();
        }
        foreach ($demands as $demand)
        {
            $problemModel->getAllSecondSolveTime($demand->id,'demand');
            if(in_array($demand->status,['wait','feedbacked']))
            {
                $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq(null)->where('id')->eq($demand->id)->exec();
            }
            if($demand->status != 'onlinesuccess'){
                $this->dao->update(TABLE_DEMAND)->set('actualOnlineDate')->eq(null)->where('id')->eq($demand->id)->exec();
            }
        }

        echo '处理了' . count($demands) . '条数据。1秒后自动跳转，请等待处理完毕。';
        $location = $this->createLink('history', 'solvedTimeAboutDemand', array('begin' => $begin + 300));
        header("refresh:1;url=$location");

    }

    /**
     * @Notes:待反馈状态反馈单待处理人数据处理
     * @Date: 2023/12/7
     * @Time: 14:21
     * @Interface dealFeedbackDealUser
     */
    public function dealFeedbackDealUser()
    {
        $ids = [];
        $requirementInfo = $this->dao->select('id,`status`,feedbackStatus,dealUser,feedbackDealUser,createdDate')->from(TABLE_REQUIREMENT)
            ->where('feedbackStatus')->eq('tofeedback')
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll();
        foreach ($requirementInfo as $key => $value)
        {
            if(empty($value->feedbackDealUser))
            {
                $dealUser = array_merge(array_filter(explode(',',$value->dealUser)));
                $res = $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq($dealUser[0])->where('id')->eq($value->id)->exec();
                if($res)
                {
                    $ids[] = $value->id;
                }
            }
        }
        echo '处理了' . count($ids) . '条数据。';
        a($ids);
    }


    /**
     * @Notes:处理需求任务开始时间
     * 清总同步：取最新待发布->已发布时间
     * 内部自建：取创建时间
     * 内部自建：若存在变更，则取最新变更通过时间 经查询，无变更单，无需关注
     * @Date: 2023/12/5
     * @Time: 15:38
     * @Interface dealRequirementStartTime
     */
    public function dealRequirementStartTime()
    {
        $ids = [];
        $requirementInfo = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where('`status`')->ne('deleted')->andWhere('sourceRequirement')->eq(1)->fetchAll();
        foreach ($requirementInfo as $requirement)
        {
            //清总同步
            if($requirement->createdBy == 'guestcn')
            {
                $consumedInfo = $this->dao->select('id,createdDate')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('requirement')
                    ->andWhere('objectID')->eq($requirement->id)
                    ->andWhere('`before`')->eq('topublish')
                    ->andWhere('`after`')->eq('published')
                    ->andWhere('`deleted`')->eq(0)
                    ->orderBy('id_desc')
                    ->fetch();
                if($consumedInfo)
                {
                    $res = $this->dao->update(TABLE_REQUIREMENT)->set('startTime')->eq($consumedInfo->createdDate)->where('id')->eq($requirement->id)->exec();
                    if($res)
                    {
                        $ids[] = $requirement->id;
                    }
                }
            }else{
                $res = $this->dao->update(TABLE_REQUIREMENT)->set('startTime')->eq($requirement->createdDate)->where('id')->eq($requirement->id)->exec();
                if($res)
                {
                    $ids[] = $requirement->id;
                }
            }

        }
        echo "共执行： ".count($ids).' 条数据';
    }

   /**
     * 迁移coas 和mojituo数据至cjdp
     * @param $type 类型 coas 或 mojito
     */
    public function insertActionAndHistory($type){
        if(!$type) echo '类型参数不能为空';
        $table = 'zt_flow_'.$type;
        $coas = $this->dao->select('*')->from("$table")->where('deleted')->eq(0)->fetchAll();

        foreach ($coas as $cos){

            $cjdp               = new stdclass();
            $cjdp->parent = $cos->parent;
            $cjdp->assignedTo = $cos->assignedTo;
            $cjdp->status = $cos->status;
            $cjdp->createdBy = $cos->createdBy;
            $cjdp->createdDate = $cos->createdDate;
            $cjdp->editedBy = $cos->editedBy;
            $cjdp->editedDate = $cos->editedDate;
            $cjdp->assignedBy = $cos->assignedBy;
            $cjdp->assignedDate = $cos->assignedDate;
            $cjdp->mailto = $cos->mailto;
            $cjdp->deleted = $cos->deleted;
            $cjdp->name = $cos->name;
            $cjdp->content = $cos->content;
            $cjdp->dept = $cos->dept;
            $cjdp->type = $cos->type;
            $cjdp->approver = $cos->approver;
            $cjdp->feedback = $cos->feedback;
            $cjdp->tips = $cos->tips;
            $cjdp->manager = $cos->manager;
            $cjdp->submitter = $cos->submitter;
            $cjdp->pri = $cos->pri;
            $cjdp->fxdate = $cos->fxdate;
            $cjdp->dev = $cos->dev;
            $cjdp->closeBy = $cos->closeBy;
            $cjdp->closeDate = $cos->closeDate;
            $cjdp->acfix = $cos->acfix;
            $cjdp->cost = $cos->cost;
            $cjdp->solve = $cos->solve;
            $cjdp->publicComponentName = $type == 'coas' ? 69 : 1;
            $cjdp->project = $cos->project;
            $this->dao->insert("zt_flow_cjdpf")->data($cjdp)->exec();
            $cjdpid = $this->dao->lastInsertID();
            $action = $this->dao->select('*')->from(TABLE_ACTION)->where('objectType')->eq("$type")->andWhere('objectID')->eq($cos->id)->fetchAll();

            foreach ($action as $act){
                $ac = new stdClass();
                $ac->objectType = 'cjdpf';
                $ac->objectID = $cjdpid;
                $ac->product  = $act->product;
                $ac->project  = $act->project;
                $ac->execution = $act->execution;
                $ac->actor = $act->actor;
                $ac->action = $act->action;
                $ac->date = $act->date;
                $ac->comment = $act->comment;
                $ac->extra = $act->extra;
                $ac->read = $act->read;
                $ac->efforted = $act->efforted;
                $ac->ip = $act->ip;
                $this->dao->insert(TABLE_ACTION)->data($ac)->exec();
                $actionid = $this->dao->lastInsertID();
                $historys = $this->dao->select('*')->from(TABLE_HISTORY)->where('action')->eq($act->id)->fetchAll();

                foreach ($historys as $history){
                    $his = new stdClass();
                   $his->action = $actionid;
                   $his->field = $history->field;
                   $his->old = $history->old;
                   $his->new = $history->new;
                   $his->diff = $history->diff;
                   $this->dao->insert(TABLE_HISTORY)->data($his)->exec();
                }
            }
            echo $type.' id :'.$cos->id .'迁移成功'.'迁移后id:' .$cjdpid.'<br>';
        }
    }

    /**
     * @Notes:二线实现的需求条目已挂起均刷为已关闭
     * @Date: 2024/1/26
     * @Time: 14:45
     * @Interface dealDemandStatusAboutSuspend
     */
    public function dealDemandStatusAboutSuspend()
    {
        $demands = $this->dao->select('id,status')->from(TABLE_DEMAND)->where('sourceDemand')->eq(1)->andWhere('status')->eq('suspend')->andWhere('fixType')->eq('second')->fetchAll();
        foreach ($demands as $value)
        {
            $data = new stdClass();
            $action = $this->dao->select('id,objectID,actor,date,action,comment')->from(TABLE_ACTION)->where('objectType')->eq('demand')->andWhere('objectID')->eq($value->id)->andWhere('action')->eq('suspended')->orderBy('id desc')->limit(1)->fetch();
            if($action)
            {
                $data->closedBy   = $action->actor;
                $data->closedDate = substr($action->date, 0, 10);
            }
            $data->status = 'closed';
            $this->dao->update(TABLE_DEMAND)->data($data)->where('id')->eq($value->id)->exec();
            $this->loadModel('action')->create('demand', $value->id, 'edited', '二线实现需求条目在调整关闭状态后，处理历史数据由已挂起改为已关闭。');
            $this->loadModel('consumed')->record('demand', $value->id, 0, $this->app->user->account,'suspend', 'closed', '');
        }
        $ids = array_column($demands,'id');
        echo '共执行：'.count($ids).'条数据。';
        a(implode(',',$ids));
    }
    /**
     * @Notes:处理内部需求池测试中状态的数据
     * @Date: 2024/3/4
     * @Time: 16:51
     * @Interface dealBuildDemand
     */
    public function dealBuildDemand()
    {
        $demandInfo =  $this->dao->select('id,status')->from(TABLE_DEMAND)->where('sourceDemand')->eq(2)->andWhere('`status`')->eq('build')->fetchAll();
        $ids = [];
        if($demandInfo)
        {
            $ids = array_column($demandInfo,'id');
            foreach ($demandInfo as $demand)
            {
                $this->dao->update(TABLE_DEMAND)->set('status')->eq('feedbacked')->where('id')->eq($demand->id)->exec();
                $this->loadModel('consumed')->recordAuto('demand', $demand->id, 0, 'build', 'feedbacked');
                $this->loadModel('action')->create('demand', $demand->id, 'edited', '批量处理内部需求条目数据，将测试中更新为开发中，并增加流转状态。','','guestjk');
            }
        }
        echo '处理了' . count($ids) . '条数据';
        a($ids);
    }
    //
   public function pushMessageToCenter($id=''){
        if ($id == ''){
            echo 'id参数不能为空';
            exit;
        }
        $idArray =explode(',',$id);
        $this->loadModel('action')->pushMessages($idArray);
        echo '执行完毕';
   }

    /**
     * 更新需要系统部处理，但状态未到系统部制版的字段
     * @param null $id
     */
   public function updateBuild($id = null ){

       $all = $this->dao->select("*")->from(TABLE_BUILD)
           ->where('systemverify ')->eq('1')
           ->andWhere('status')->notin('released,testfailed,versionfailed,verifyfailed,testsuccess,waitverifyapprove,verifyrejectsubmit')
           ->beginIF($id)->andWhere('id')->eq($id)->fi()
           ->andWhere('deleted')->eq('0')
           ->fetchAll();
       foreach ($all as $item) {
           if(!$item) continue;
           $this->dao->update(TABLE_BUILD)->set('systemverify')->eq('0')->where('id')->eq($item->id)->exec();
           $this->loadModel('action')->create('build', $item->id, 'updatesystem', '因部门职责发生变更，制版过程系统部不再支持验证工作，故将原“需要系统部验证”修改为“不需要系统部验证。”需求收集id 3735','','');
           echo '已处理制版id：'.$item->id .'<br>';
       }
       echo '执行完毕';
   }

    /**
     * @Notes:处理内部自建需求任务计划完成时间 按照需求意向期望完成时间刷数据
     * @Date: 2024/4/8
     * @Time: 14:54
     * @Interface dealEndDate
     */
   public function dealEndDate()
   {
       $requirements = $this->dao->select('id,end,opinion,deadLine')->from(TABLE_REQUIREMENT)
           ->where('`status`')->ne('deleted')
           ->andWhere('createdBy')->ne('guestcn')
           ->orderBy('id_desc')
           ->fetchAll();
       $ids = [];
       foreach ($requirements as $requirement)
       {
            if($requirement->end == '0000-00-00' || empty($requirement->end))
            {
                if(!empty($requirement->deadLine) && $requirement->deadLine != '0000-00-00')
                {
                    $updateEnd = date('Y-m-d',strtotime($requirement->deadLine));
                    $this->dao->update(TABLE_REQUIREMENT)->set('`end`')->eq($updateEnd)->where('id')->eq($requirement->id)->exec();
                    $ids[] = $requirement->id;
                }
            }
       }
       echo '处理了' . count($ids) . '条数据';
       a($ids);

   }

    /**
     * @Notes:处理需求条目交付是否超期
     * @Date: 2024/4/11
     * @Time: 14:41
     * @Interface demandDeliveryOver
     */
   public function demandDeliveryOver()
   {
        /* @var problemModel $problemModel*/
        $problemModel = $this->loadModel('problem');
        $demandInfo = $this->dao->select('`id`,`solvedTime`,`status`,`delayStatus`, `requirementID`')
            ->from(TABLE_DEMAND)
            ->where('sourceDemand')->eq('1')
            ->andWhere('status')->notIN('deleted,deleteout')
            ->andWhere('fixType')->eq('second')
            ->fetchAll();

        $outTime   = $this->lang->demand->demandOutTime['demandOutTime']  ?? 2; //交付超期月份
        $ids_yes = [];
        $ids_no  = [];
        foreach ($demandInfo as $demand)
        {
            if($demand->requirementID == 0) continue;
            $solvedTime = $demand->solvedTime != '0000-00-00 00:00:00' ? $demand->solvedTime : '';
            $requirement = $this->dao->select('id, createdBy, newPublishedTime, feedbackStatus, feekBackStartTime')
                ->from(TABLE_REQUIREMENT)
                ->where('id')->eq($demand->requirementID)
                ->andWhere('status')->notIN('deleted,deleteout')
                ->fetch();

            if($requirement)
            {
                /*
                 * ①清总同步按照内部反馈开始时间计算
                 * ②内部自建按照交付周期计算起始时间计算
                 */
                if('guestcn' == $requirement->createdBy){
                    $newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
                }else{
                    $newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
                }

                $his = substr($newPublishedTime, 10);
                $end   = $problemModel->getOverDate($newPublishedTime, $outTime); //超期时间
                $end   = date('Y-m-d', strtotime($end)).$his;

                //无交付时间用当前时间作对比
                $demandId = $demand->id;
                if(empty($solvedTime))
                {
                    if(helper::now() > $end){
                        $deliveryOver = 2;//是
                        $ids_yes[] = $demandId;
                    }else{
                        $deliveryOver = 1;//否
                        $ids_no[] = $demandId;
                    }
                }else{
                    if($end > $solvedTime){
                        $deliveryOver = 2;//是
                        $ids_yes[] = $demandId;
                    }else{
                        $deliveryOver = 1;//否
                        $ids_no[] = $demandId;
                    }
                }
            }

        }
        if(!empty($ids_yes))  $this->dao->update(TABLE_DEMAND)->set('deliveryOver')->eq(2)->where('id')->in($ids_yes)->exec();
        if(!empty($ids_no))   $this->dao->update(TABLE_DEMAND)->set('deliveryOver')->eq(1)->where('id')->in($ids_no)->exec();

        echo "处理已超期数据" . count($ids_yes) . "条。"."<br />". "未超期数据".count($ids_no)."条。"."<br />";
        echo '已超期数据id集合：';
        a($ids_yes);
        echo '未超期数据id集合：';
        a($ids_no);
   }

    /**
     *  手动推送发布单
     * @param $ids
     */
   public function afreshPushSafeAsset($ids = null){
       if(empty($ids)){
           echo '需要处理的发布单id不能为空！';
           exit;
       }
       $releaseAll = $this->dao->select('*')->from(TABLE_RELEASE)->where('id')->in(explode(',',$ids))->fetchAll();
       if(!$releaseAll){
           echo '未查询到指定的发布单数据！';
           exit;
       }
       $res = array();
       //同步发布单
       foreach ($releaseAll as $item) {
           if(!empty($item->app)) {
               $this->loadModel('projectrelease')->pushSafeAsset($item);
               $requestlogSql = "select status,response  from zt_requestlog zr where objectType ='projectrelease' and purpose ='pushSafeAsset' and json_extract(params,'$.dpmpReleaseId') = '$item->id' order by id desc limit 1";
               $response = $this->dao->query($requestlogSql)->fetch();
               $res[$item->id] = $response;
           }
       }
       echo '同步完成，结果如下：'."<br />";
       a($res);
   }


    /**
     * 更新项目中涉及到的测试、评审、变更、发布等单子的项目id
     * @param $oldProjectID
     * @param $newProjectID
     */
    public function updateProjectId($oldProjectID = null ,$newProjectID = null ,$table = null,$column = null){

        if( empty($oldProjectID) || empty($newProjectID) ){
            echo '原项目ID或新项目ID不能为空'."<br />";
            die();
        }
        if($oldProjectID == $newProjectID ){
            echo '原项目ID不能等于新项目ID'."<br />";
            die();
        }
        $oldProject = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('project')->in($oldProjectID)->fetch();
        if(!$oldProject){
            echo '原项目ID未查到相关信息'."<br />";
            die();
        }
        if(isset($oldProject->secondLine) && $oldProject->secondLine != 1){
            echo '原项目ID查询到的项目非二线，不在迁移范围'."<br />";
            die();
        }
        $newProject = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('project')->in($newProjectID)->fetch();
        if(!$newProject){
            echo '新项目ID未查到相关信息'."<br />";
            die();
        }
        if(isset($newProject->secondLine) && $newProject->secondLine != 1){
            echo '新项目ID查询到的项目非二线，不在迁移范围'."<br />";
            die();
        }
        $res = array();
        if(($table && !$column) || (!$table && $column)){
            echo '指定迁移表或迁移表的字段不能为空！'."<br />";
            die();
        }
        if($table && $column){
            $custom = $this->dao->select('id')->from($table)->where("$column")->eq($oldProjectID)->fetchAll();
            if($custom){
                $ids = array_column($custom,'id');
                //更新项目ID
                $this->dao->update($table)->set("$column")->eq($newProjectID)->where('id')->in($ids)->exec();
                $res[$table] = implode(',',$ids);
            }
        }else{
            //评审
            $review = $this->dao->select('id')->from(TABLE_REVIEW)->where('project')->eq($oldProjectID)->andWhere('deleted')->eq('0')->fetchAll();
            if($review){
                $reviewIds = array_column($review,'id');
                //更新评审项目ID
                $this->dao->update(TABLE_REVIEW)->set('project')->eq($newProjectID)->where('id')->in($reviewIds)->exec();
                $res['review'] = implode(',',$reviewIds);
            }
            //测试
            $bug = $this->dao->select('id')->from(TABLE_BUG)->where('project')->eq($oldProjectID)->andWhere('deleted')->eq('0')->fetchAll();
            if($bug){
                $bugIds = array_column($bug,'id');
                //更新测试项目ID
                $this->dao->update(TABLE_BUG)->set('project')->eq($newProjectID)->where('id')->in($bugIds)->exec();
                $res['bug'] = implode(',',$bugIds);
            }
            //测试
            $bug = $this->dao->select('id')->from(TABLE_BUG)->where('project')->eq($oldProjectID)->andWhere('deleted')->eq('0')->fetchAll();
            if($bug){
                $bugIds = array_column($bug,'id');
                //更新测试项目ID
                $this->dao->update(TABLE_BUG)->set('project')->eq($newProjectID)->where('id')->in($bugIds)->exec();
                $res['bug'] = implode(',',$bugIds);
            }
            //基线
            $baseline = $this->dao->select('id')->from(TABLE_BASELINE)->where('project')->eq($oldProjectID)->andWhere('deleted')->eq('0')->fetchAll();
            if($baseline){
                $bugIds = array_column($baseline,'id');
                //更新基线项目ID
                $this->dao->update(TABLE_BASELINE)->set('project')->eq($newProjectID)->where('id')->in($bugIds)->exec();
                $res['baseline'] = implode(',',$bugIds);
            }

            //周报
            $weeklyreport = $this->dao->select('id')->from(TABLE_WEEKLYREPORT)->where('project')->eq($oldProjectID)->fetchAll();
            if($weeklyreport){
                $weeklyreportIds = array_column($weeklyreport,'id');
                //更新周报项目ID
                $this->dao->update(TABLE_WEEKLYREPORT)->set('project')->eq($newProjectID)->where('id')->in($weeklyreportIds)->exec();
                $res['weeklyreport'] = implode(',',$weeklyreportIds);
            }
            //关联产品
            $product = $this->dao->select('id,product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($oldProjectID)->fetchAll();
            if($product) {
                $newHaveProduct = $this->dao->select('id,product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($newProjectID)->fetchAll();
                $newProduct = array_column($newHaveProduct,'product');
                foreach ($product as $key => $item) {
                    if(in_array($item->product,$newProduct)){
                        unset($product[$key]);
                    }
                }
                $productIds = array_column($product,'id');
                if($productIds){
                    //更新关联产品项目ID
                    $this->dao->update(TABLE_PROJECTPRODUCT)->set('project')->eq($newProjectID)->where('id')->in($productIds)->exec();
                    $res['projectproduct'] = implode(',', $productIds);
                }
            }

            //团队
            $user = $this->dao->select('account')->from(TABLE_USER)->where('dept')->in('26,27,28,29')->andWhere('deleted')->eq('0')->fetchAll();
            $userArr = array_column($user,'account');
            $teamUser = $this->dao->select('account')->from(TABLE_TEAM)->where('root')->eq($newProjectID)->andWhere('`type`')->eq('project')->andWhere('account')->in($userArr)->fetchAll();

            if(!$teamUser){
                foreach ($userArr as $item) {
                    $team = new stdClass();
                    $team->root = $newProjectID;
                    $team->type = 'project';
                    $team->account = $item;
                    $team->role    = ',6';
                    $team->join    = helper::today();
                    $team->hours   = '7';
                    $this->dao->insert(TABLE_TEAM)->data($team)->exec();
                }
                $res['team'] = implode(',',$userArr);
            }else{
                $teamUserArr = array_column($teamUser,'account');
                foreach ($userArr as $key => $arr) {
                    if(array_search($arr,$teamUserArr) !== false){
                        unset($userArr[$key]);
                        continue;
                    }
                    $team = new stdClass();
                    $team->root = $newProjectID;
                    $team->type = 'project';
                    $team->account = $arr;
                    $team->role    = ',6';
                    $team->join    = helper::today();
                    $team->hours   = '7';
                    $this->dao->insert(TABLE_TEAM)->data($team)->exec();
                    $res['team'] = implode(',',$userArr);
                }
            }
            //白名单
            $acl = $this->dao->select('id')->from(TABLE_ACL)->where('objectID')->eq($oldProjectID)->andWhere('`type`')->eq('whitelist')->andWhere('objectType')->eq('project')->fetchAll();
            if($acl){
                $aclIds = array_column($acl,'id');
                //更新白名单项目ID
                $this->dao->update(TABLE_ACL)->set('objectID')->eq($newProjectID)->where('id')->in($aclIds)->exec();
                $res['acl'] = implode(',',$aclIds);
            }
        }
        echo '同步完成，结果如下：'."<br />";
        a($res);
    }

    /**
     *  废弃
     * 更新问题单、需求单、二线工单项目
     * @param $project
     */
    public function updateOrderProject($project){

        $res = array();
        if(!$project){
            echo '更新的项目id不能为空'."<br />";
            die();
        }
        $problem = $this->dao->select('id')->from(TABLE_PROBLEM)->where('projectPlan')->in('12292,12293')->andWhere('status')->ne('deleted')->fetchAll();
        if($problem){
            $problemIds = array_column($problem,'id');
            $this->dao->update(TABLE_PROBLEM)->set('projectPlan')->eq($project)->where('id')->in($problemIds)->exec();
            $res['problem'] = $problemIds;

            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('project')->eq($project)->where('typeid')->in($problemIds)->andWhere('`type`')->eq('problem')->andWhere('deleted')->eq('0')->exec();
            $res['task_problem'] = $problemIds;
        }
        $demand = $this->dao->select('id')->from(TABLE_DEMAND)->where('project')->in('12292,12293')->andWhere('status')->ne('deleted')->fetchAll();
        if($demand){
            $demandIds = array_column($demand,'id');
            $this->dao->update(TABLE_DEMAND)->set('project')->eq($project)->where('id')->in($demandIds)->exec();
            $res['demand'] = $demandIds;

            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('project')->eq($project)->where('typeid')->in($demandIds)->andWhere('`type`')->in('demand,demandinside')->andWhere('deleted')->eq('0')->exec();
            $res['task_demand'] = $demandIds;
        }
        $second = $this->dao->select('id')->from(TABLE_SECONDORDER)->where('internalProject')->in('12292,12293')->andWhere('deleted')->eq('0')->fetchAll();
        if($second){
            $secondIds = array_column($second,'id');
            $this->dao->update(TABLE_SECONDORDER)->set('internalProject')->eq($project)->where('id')->in($secondIds)->exec();
            $res['second'] = $secondIds;

            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('project')->eq($project)->where('typeid')->in($secondIds)->andWhere('`type`')->eq('secondorder')->andWhere('deleted')->eq('0')->exec();
            $res['task_second'] = $secondIds;
        }
        $credit = $this->dao->select('id')->from(TABLE_CREDIT)->where('projectPlanId')->in('12292,12293')->andWhere('deleted')->eq('0')->fetchAll();
        if($credit){
            $creditIds = array_column($credit,'id');
            $this->dao->update(TABLE_CREDIT)->set('projectPlanId')->eq($project)->where('id')->in($creditIds)->exec();
            $res['credit'] = $creditIds;
        }
        echo '更新完成，结果如下：'."<br />";
        a($res);
    }

    /**
     * 全量复制，废弃
     * 将现有的TJ1、TJ2二线和部门项目下的计划，在新的二线和部门下迁移过去
     */
    public function updatePlanProjectAllDrop($oldProject,$newProject,$executionId = null){

        $res = array();
        $oldName = $this->dao->select("name")->from(TABLE_EXECUTION)
            ->where('id')->eq($oldProject)->fetch();
        //阶段
        $execution = $this->dao->select("*")->from(TABLE_EXECUTION)
            ->where('`type`')->eq('stage')
            ->andWhere('project')->eq($oldProject)
            ->beginIF($executionId)->andWhere('id')->eq($executionId)->fi()
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        if(!$execution){
            echo '该项目未查询到阶段'."<br />";
            die();
        }
        foreach ($execution as $key => $item) {
            if(!$item) continue;
            //更新阶段信息
            $exec = new stdClass();
            $exec->project = $newProject;
            $exec->parent  = $item->parent;
            $exec->path    = str_replace($oldProject,$newProject,$item->path);
            $exec->name    = '历史'.strstr($oldName->name,'_',true)."_".$item->name;

            $this->dao->update(TABLE_EXECUTION)->data($exec)->where('id')->eq($item->id)->exec();
            if($item->parent == '0' && $executionId){
                $sonStage =   $this->dao->select('id,parent,path,name')->from(TABLE_EXECUTION)
                    ->where('`type`')->eq('stage')
                    ->andWhere('project')->eq($oldProject)
                    ->andWhere('parent')->eq($executionId)
                    ->andWhere('deleted')->eq('0')
                    ->fetchAll();
                if($sonStage){
                    foreach ($sonStage as $stage) {
                        $son = new stdClass();
                        $son->project = $newProject;
                        $son->parent  = $stage->parent;
                        $son->path    = str_replace($oldProject,$newProject,$stage->path);
                        $son->name    = '历史'.strstr($oldName->name,'_',true)."_".$stage->name;
                        $this->dao->update(TABLE_EXECUTION)->data($son)->where('id')->eq($stage->id)->exec();
                        $res['execution'][] = $stage->id;
                    }
                }
            }
            $res['execution'][] = $item->id;

            //任务
            $tasks = $this->dao->select("*")->from(TABLE_TASK)
                ->where('project')->eq($oldProject)
                ->andWhere('deleted')->eq('0')
                ->andWhere('execution')->in($item->id)
                ->fetchAll();
            if($tasks){
                //更新任务信息
                $taskIds = array_column($tasks,'id');
                $this->dao->update(TABLE_TASK)->set('project')->eq($newProject)->where('id')->in($taskIds)->exec();
                $res['task'] = implode(',',$taskIds);
            }
            //报工
            $works = $this->dao->select("*")->from(TABLE_WORKREPORT)
                ->where('project')->eq($oldProject)
                ->andWhere('deleted')->eq('0')
                ->andWhere('apps')->in($item->id)
                ->fetchAll();
            if($works){
                //更新报工信息
                $workIds = array_column($works,'id');
                $this->dao->update(TABLE_WORKREPORT)->set('project')->eq($newProject)->where('id')->in($workIds)->exec();
                $res['work'] = implode(',',$workIds);
            }
            //工时
            $effort = $this->dao->select("*")->from(TABLE_EFFORT)
                ->where('project')->eq($oldProject)
                ->andWhere('deleted')->eq('0')
                ->andWhere('objectType')->eq('task')
                ->andWhere('execution')->in($item->id)
                ->fetchAll();
            if($effort){
                //更新报工信息
                $effortIds = array_column($effort,'id');
                $this->dao->update(TABLE_EFFORT)->set('project')->eq($newProject)->where('id')->in($effortIds)->exec();
                $res['effort'] = implode(',',$effortIds);
            }
        }
        echo "项目id由{$oldProject}更新为{$newProject}完成，涉及数据结果如下："."<br />";
        a($res);

    }

    /**
     * 只复制生成任务的问题单、工单、需求单
     * 将现有的TJ1、TJ2二线和部门项目下的计划，在新的二线和部门下迁移过去
     */
    public function updatePlanProject($oldProject,$newProject,$type = null,$typeid = null){
        $res = array();
        $table = array(
            'problem' => TABLE_PROBLEM,
            'demand'  => TABLE_DEMAND,
            'demandinside' => TABLE_DEMAND,
            'secondorder' => TABLE_SECONDORDER,
        );
        $tableProject = array(
            'problem' => 'projectPlan',
            'demand'  => 'project',
            'demandinside' => 'project',
            'secondorder' => 'internalProject',
        );
        $credit = $this->dao->select('id')->from(TABLE_CREDIT)->where('projectPlanId')->in($oldProject)->andWhere('deleted')->eq('0')->fetchAll();
        if($credit){
            $creditIds = array_column($credit,'id');
            $this->dao->update(TABLE_CREDIT)->set('projectPlanId')->eq($newProject)->where('id')->in($creditIds)->exec();
            $res['credit'] = $creditIds;
        }

        //查询本项目所有生成任务的单子
        $allOrder = $this->dao->select('*')->from(TABLE_TASK_DEMAND_PROBLEM)
            ->where('project')->in($oldProject)
            ->andWhere('deleted')->eq('0')
            ->beginIF($type)->andWhere('`type`')->eq($type)->fi()
            ->beginIF($typeid)->andWhere('typeid')->eq($typeid)->fi()
            ->fetchAll();
        if(!$allOrder){
            echo '该项目未查询到有任务的工单'."<br />";
            die();
        }
        foreach ($allOrder as $item) {
            $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($item->taskid)->andWhere('deleted')->eq('0')->fetch();
            if($task) {

                //任务阶段
                $execution = $this->dao->select("*")->from(TABLE_EXECUTION)
                    ->where('`type`')->eq('stage')
                    ->andWhere('id')->eq($task->execution)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();

                $executionParent = $this->dao->select("*")->from(TABLE_EXECUTION)
                    ->where('`type`')->eq('stage')
                    ->andWhere('id')->eq($execution->parent)
                    ->andWhere('parent')->eq('0')
                    ->andWhere('deleted')->eq('0')
                    ->fetch();

                //查询父阶段在新项目是否存在
                $newExecutionParent =   $this->dao->select("*")->from(TABLE_EXECUTION)
                    ->where('`type`')->eq('stage')
                    ->andWhere('project')->eq($newProject)
                    ->andWhere('name')->eq($executionParent->name)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();

                //父节点在新项目中不存在
                if(!$newExecutionParent){
                    $exec = new stdClass();
                    $exec->project = $newProject;
                    $exec->parent  = $executionParent->parent;
                    $exec->path    = str_replace($oldProject,$newProject,$executionParent->path);

                    $this->dao->update(TABLE_EXECUTION)->data($exec)->where('id')->eq($executionParent->id)->exec();
                    $res['execution'][] = implode(',',array($executionParent->id));

                    $newExecutionParent = $executionParent;
                }

                $problemStage = $this->dao->select('*')->from(TABLE_EXECUTION)->where('name')->eq('二线实现_问题池(通过问题池_二线实现生成任务条)')->andWhere('project')->eq($newProject)->fetch();

                if($problemStage){
                    $secondZx = $this->dao->select('*')->from(TABLE_EXECUTION)->where('name')->eq('CIMMS_征信系统')->andWhere('project')->eq($newProject)->andWhere('parent')->eq($problemStage->id)->fetch();
                    if(!$secondZx){
                        $this->autoStage($newProject,'CIMMS_征信系统',2,$problemStage,$problemStage->begin,$problemStage->end);
                    }
                }

                $secStage = $this->dao->select('*')->from(TABLE_EXECUTION)->where('name')->eq('二线实现_工单池(通过工单池_二线实现生成任务条)')->andWhere('project')->eq($newProject)->fetch();

                if($secStage){
                    $seZx = $this->dao->select('*')->from(TABLE_EXECUTION)->where('name')->eq('CIMMS_征信系统')->andWhere('project')->eq($newProject)->andWhere('parent')->eq($secStage->id)->fetch();
                    if(!$seZx){
                        $this->autoStage($newProject,'CIMMS_征信系统',2,$secStage,$secStage->begin,$secStage->end);
                    }
                }
                // 一级阶段在新项目存在
                //查二级阶段是否存在
                $newExecution =   $this->dao->select("*")->from(TABLE_EXECUTION)
                    ->where('`type`')->eq('stage')
                    ->andWhere('project')->eq($newProject)
                    ->andWhere('name')->eq($execution->name)
                    ->andWhere('parent')->eq($newExecutionParent->id)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();

                //二级阶段在新项目不存在
                if(!$newExecution) {

                    $execSon = new stdClass();
                    $execSon->project = $newProject;
                    $execSon->parent = $newExecutionParent->id;
                    $execSon->path = str_replace($oldProject, $newProject, $execution->path);

                    $this->dao->update(TABLE_EXECUTION)->data($execSon)->where('id')->eq($execution->id)->exec();
                    $res['execution_second'][] = implode(',', array($execution->id));
                    $newExecution = $execution;
                }

                $oldTasksName = $this->dao->select("*")->from(TABLE_TASK)
                    ->where('id')->eq($task->parent)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();

                $newTasks = $this->dao->select("*")->from(TABLE_TASK)
                    ->where('name')->eq($oldTasksName->name)
                    ->andWhere('project')->eq($newProject)
                    ->andWhere('execution')->eq($newExecution->id)
                    ->andWhere('parent')->eq('0')
                    ->andWhere('deleted')->eq('0')
                    ->fetch();

                //三级任务在新项目不存在
                if(!$newTasks) {
                    //更新任务信息
                    $ts = new stdClass();
                    $ts->execution = $newExecution->id;
                    $ts->project = $newProject;
                    $this->dao->update(TABLE_TASK)->data($ts)->where('id')->in($oldTasksName->id)->exec();
                    $res['task_parent'][] = implode(',', array($oldTasksName->id));

                    $this->dao->update(TABLE_TASK)->set('project')->eq($newProject)->set('execution')->eq($newExecution->id)->where('id')->in($task->id)->exec();
                    $res['task'][] = implode(',',array($task->id));
                }else{
                    $ts = new stdClass();
                    $ts->execution = $newExecution->id;
                    $ts->project = $newProject;
                    $ts->parent = $newTasks->id;
                    $ts->path = str_replace($oldTasksName->id, $newTasks->id, $task->path);
                    $this->dao->update(TABLE_TASK)->data($ts)->where('id')->in($task->id)->exec();
                    $res['task'][] = implode(',',array($task->id));
                }

                //工时
                $effort = $this->dao->select("*")->from(TABLE_EFFORT)
                    ->where('project')->eq($oldProject)
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('objectType')->eq('task')
                    ->andWhere('objectID')->in($task->id)
                    ->fetchAll();
                if($effort){
                    //更新工时信息
                    $effortIds = array_column($effort,'id');
                    $this->dao->update(TABLE_EFFORT)->set('project')->eq($newProject)->set('execution')->eq($newExecution->id)->where('id')->in($effortIds)->exec();
                    $res['effort'][] = implode(',',$effortIds);
                }

                //报工
                //更新报工信息
                $workIds = array_filter(array_column($effort,'workID'));

                if(!empty($workIds)){
                    $this->dao->update(TABLE_WORKREPORT)->set('project')->eq($newProject)->set('activity')->eq($newExecutionParent->id)->set('apps')->eq($newExecutionParent->id)->where('id')->in($workIds)->exec();
                    a($this->dao->printSQL());
                    $res['work'][] = implode(',',$workIds);
                }

                //更新单子主表
                $this->dao->update($table[$item->type])->set($tableProject[$item->type])->eq($newProject)->set('execution')->eq($newExecution->id)->where('id')->in($item->typeid)->exec();

                $res[$item->type][] = $item->typeid;

                //更新关联表信息
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('project')->eq($newProject)
                    ->set('execution')->eq($newExecution->id)
                    ->where('typeid')->in($item->typeid)
                    ->andWhere('`type`')->eq("$item->type")
                    ->andWhere('product')->eq($item->product)
                    ->andWhere('application')->eq($item->application)
                    ->andWhere('deleted')->eq('0')
                    ->exec();
                $res["task_".$item->type][] = implode(',',array($item->typeid));
            }
        }
        echo "项目id由{$oldProject}更新为{$newProject}完成，涉及数据结果如下："."<br />";
        a($res);
    }

    /**
     * 生成项目空间现场支持阶段
     */
    public function createLocaleSupportStage($projectid = null ){
        //查询所有未关闭的项目
        $where = $projectid ? "zp.id = '$projectid'" : ' 1= 1';
        $projects = $this->dao->query("select zp.id,zp.name,plan.begin,plan.end from zt_project zp left join zt_projectplan plan on zp.id = plan.project
            where  zp.deleted = '0' and  zp.`type` ='project'  and zp.status != 'closed' and plan.secondline = '1' and zp.name like '%二线管理' and $where  order by zp.id asc ")->fetchAll();

        if(empty($projects))
        {
            echo '没有符合条件的数据！';
            die();
        }
        $count = array();
        foreach ($projects as $key =>$project) {
            $projectID = $project->id;
            $order = $this->dao->select('`order`')->from(TABLE_EXECUTION)
                ->where('deleted')->eq('0')
                ->andWhere('`type`')->eq('stage')
                ->andWhere('status')->ne('closed')
                ->andWhere('dataVersion')->eq('2')
                ->andWhere('name')->eq('二线实现_工单池(通过工单池_二线实现生成任务条)')
                ->andWhere('project')->eq($projectID)
                ->fetch();

            //查询当前项目是否已有默认阶段
            $stage = $this->dao->select('id,name')->from(TABLE_EXECUTION)
                ->where('deleted')->eq('0')
                ->andWhere('`type`')->eq('stage')
                ->andWhere('status')->ne('closed')
                ->andWhere('dataVersion')->eq('2')
                ->andWhere('name')->eq('二线工作_现场支持(通过现场服务_现场支持生成任务条)')
                ->andWhere('project')->eq($projectID)
                ->fetchAll();

            if(count($stage) > 0){
                echo '项目空间id:  '.$projectID.' | '.$project->name. '| 已存在'.'<br>';
                continue;
            }
            $execution = new stdClass();
            $execution->project      = $projectID;
            $execution->parent       = 0 ;
            $execution->name         = '二线工作_现场支持(通过现场服务_现场支持生成任务条)';
            $execution->type         = 'stage';
            $execution->resource     = '';
            $execution->begin        = $project->begin ;
            $execution->end          = $project->end ;
            $execution->planDuration = helper::diffDate3($execution->end , $execution->begin);
            $execution->grade        = 1;
            $execution->openedBy     = 'admin';
            $execution->openedDate   = helper::today();
            $execution->status       = 'wait';
            $execution->milestone    = 0;
            $execution->version      = 1;
            $execution->source       = 1;
            $execution->dataVersion  = 2;//为了将历史数据隔离
            $execution->order       = isset($order->order) ? $order->order+ 2 : 5000;
            $execution->isLocaleSupport = 2;
            $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
            $executionID = $this->dao->lastInsertID();
            $path =      ','.$projectID .','. $executionID . ',';
            $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->where('id')->eq($executionID)->exec();
            $count[] = $executionID;
            echo '项目空间id:  '.$projectID.' | '.$project->name. '| 生成成功'.'<br>';
        }
        echo '共处理了'.count($count).'个项目生成成功';
    }

    /**
     * 前提条件：年度计划和立项书都已导入；上海项目导入年度计划，并生成项目
     * @param $date
     * @param $user
     */
    public function createProjectSH($beginId,$user){
        if(!$beginId || !$user){
            echo '年度计划起始id或导入年度计划创建人不能为空！'."<br />";
            die();
        }
        $projectPlan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('id')->ge($beginId)->andWhere('createdBy')->eq($user)->fetchAll();
        if(!$projectPlan){
            echo '未查到符合条件的年度计划信息'."<br />";
            die();
        }
        //$projectPlanIds = array_column($projectPlan,'id');
        $noName = array('CIPS用户数据服务APP项目',
            '大额支付系统行内系统等保改造项目',
            '商密统一前置项目',
            '城银清算渗透服务采购项目',
            '智能信息发布服务系统国密改造项目',
            'CFMQ内研一期项目',
            '大额支付系统行内子系统维保服务项目',
            '大额支付系统维保服务项目',
            '交易中心SMRS系统优化项目',
            'CIPS信息传输项目',
            '上海票据交易所数据中台咨询服务项目',
            '上清所应急支付平台项目',
            '统一接口平台一期',
            '账管系统前置机一期',
            'CIPS数据服务APP应用安全加固服务项目',
            '上海黄金交易所2023年官网系统开发服务项目',
            'SMRS系统拆分及配合项目',
            '自贸区电子围栏项目',
            '交易中心接入境内外币支付',
            '交易中心态势感知平台重保防护项目',
            '城银清算信创数据库改造项目',
            'CIPS交易中心接入系统报文升级改造项目',
            '外汇交易中心金融市场监测管理系统信创改造项目',
            'CIPS系统净额清算功能完善项目',
            'CIPS跨境理财通统计查询系统功能优化项目'); //不需要生成项目的年度计划名称
        $res = array();
        foreach ($projectPlan as $projectPlanId) {
            //更新项目的涉及成都工作量为0
            $this->dao->update(TABLE_PROJECTPLAN)->set('workloadChengdu')->eq('0')->set('nextYearWorkloadChengdu')->eq('0')->set('status')->eq('projected')->where('id')->in($projectPlanId->id)->exec();

            //计划工作量特殊处理，刷为0
            $load = array('666','666.00');
            if(in_array($projectPlanId->workload ,$load) ){
                $this->dao->update(TABLE_PROJECTPLAN)->set('workload')->eq('NA')->where('id')->in($projectPlanId->id)->exec();
            }
            //本年度计划工作量（不含成都分公司）特殊处理，刷为0
            if(in_array($projectPlanId->workloadBase,$load)){
                $this->dao->update(TABLE_PROJECTPLAN)->set('workloadBase')->eq('0')->where('id')->in($projectPlanId->id)->exec();
            }
            //下年度计划工作量（不含成都分公司）特殊处理，刷为0
            if(in_array($projectPlanId->nextYearWorkloadBase,$load)){
                $this->dao->update(TABLE_PROJECTPLAN)->set('nextYearWorkloadBase')->eq('0')->where('id')->in($projectPlanId->id)->exec();
            }

            //更新年度立项书表关联的年度计划id
            $projectCreation = $this->dao->select('id,code,mark')->from(TABLE_PROJECTCREATION)->where('name')->eq($projectPlanId->name)->andWhere('deleted')->eq('0')->fetch();
            if($projectCreation){
                $this->dao->update(TABLE_PROJECTCREATION)->set('plan')->eq($projectPlanId->id)->where('id')->in($projectCreation->id)->exec();
                $this->dao->update(TABLE_PROJECTPLAN)->set('code')->eq($projectCreation->code)->set('mark')->eq($projectCreation->mark)->where('id')->in($projectPlanId->id)->exec();
            }

            if(in_array($projectPlanId->name,$noName)){
                echo '年度计划id:  '.$projectPlanId->id.' | '.$projectPlanId->name. '| 已结项，不需要生成项目空间'.'<br>';
                continue;
            }

            //查询年度计划，新增项目
            $sql = "INSERT INTO `zt_project`(project,model,TYPE,product,`name`,`code`,`begin`,`end`,`status`,statge,pri,acl,deleted,lifetime,output,auth,
                    PATH,grade,realBegan,realEnd,progress,days,storyConcept,`resource`,`desc`,`version`,parentVersion,planDuration,realDuration,planHour,realHour,openedDate,
                    openedVersion,lastEditedDate,closedDate,canceledDate,team,whitelist,`order`,QA,PO,QD,RD,PM,startDate,changeDate,finishDate,splitDate) 
                    SELECT '0','waterfall','project','single',`name`,`code`,`begin`,`end`,'doing','1','1','private','0','','','',
                    '','0',`begin`,`end`,'0','0','0','','','0','0',`workloadBase`,'0','0','0',`begin`,
                    '',`begin` ,`end`,`end`,'',',','0', 'admin','','','',owner,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00' 
                    FROM zt_projectplan WHERE id= $projectPlanId->id";
            $this->dao->query($sql);
            $lastid = $this->dao->lastInsertId();
            //更新年度计划的项目
            $this->dao->query( "update zt_projectplan set project= $lastid  where id= $projectPlanId->id");
            $res[$projectPlanId->name] = $lastid;


            $team = new stdClass();
            $team->root = $lastid;
            $team->type = 'project';
            $team->account = $projectPlanId->owner;
            $team->role    = ',2';
            $team->join    = helper::today();
            $team->hours   = '7';
            $this->dao->insert(TABLE_TEAM)->data($team)->exec();

            $projectID = $lastid;
            //查询当前项目是否已有默认阶段
            $stage = $this->dao->select('id,name')->from(TABLE_EXECUTION)
                ->where('deleted')->eq('0')
                ->andWhere('`type`')->eq('stage')
                ->andWhere('status')->ne('closed')
                ->andWhere('dataVersion')->eq('2')
                ->andWhere('project')->eq($projectID)
                ->fetchAll();
            if(count($stage) > 0){
                echo '项目空间id:  '.$projectID.' | '.$projectPlanId->name. '| 已存在'.'<br>';
                continue;
            }
            try {
                $this->loadModel('task')->approvalAutoCreateStageAndTask($projectID);
                echo '项目空间id:  '.$projectID.' | '.$projectPlanId->name. '| 生成成功'.'<br>';
            }catch(Exception $e){
                echo '项目空间id:  '.$projectID.' | '.$projectPlanId->name. '| 生成失败'.'异常： '.$e.'<br>';
            }
        }
        echo '项目生成成功'."<br />";
        a($res);
    }

    /**
     *  根据问题延期表信息，填充问题单本表信息
     */
    public function updateProblemPlannedTime($problemID = null ){
        //select p.id,p.code,p.status,p.PlannedTimeOfChange,d.objectId,d.delayResolutionDate  from zt_problem p left join zt_delay d on p.id = d.objectId  where d.delayStatus = 'success'
        $delays = $this->dao->select('p.id,p.code,p.PlannedTimeOfChange,d.objectId,d.delayResolutionDate')->from(TABLE_PROBLEM)->alias('p')
            ->leftJoin(TABLE_DELAY)->alias('d')
            ->on('p.id = d.objectId')
            ->where('d.delayStatus')->eq('success')
            ->andWhere('p.status')->ne('deleted')
            ->beginIF($problemID)->andWhere('d.objectId')->eq($problemID)->fi()
            ->fetchAll();
        $count = 0;
        if(!$delays){
            echo '未查到相关数据！';
            die;
        }
        foreach ($delays as $delay){
            if(strtotime($delay->PlannedTimeOfChange) < strtotime( $delay->delayResolutionDate)){
                a($delay);
               // $this->dao->query( "update zt_problem set PlannedTimeOfChange= $delay->delayResolutionDate  where id= $delay->id");
                $this->loadModel('action')->create('problem', $delay->id, 'update', ".计划解决(变更)时间,由：$delay->PlannedTimeOfChange.'改为'.$delay->delayResolutionDate",'','');
                echo '单号：'.$delay->code .'更新计划解决(变更)时间,由：'.$delay->PlannedTimeOfChange.'改为'.$delay->delayResolutionDate .'<br>';
                $count ++;
            }
        }
        echo '更新完成,共处理'.$count.'条数据';
    }


    /**
     * 通过手动调用问题单是否按计划完成和考核结果字段的更新
     * @param null $type
     * @param null $problem
     */
    public function changeProblemCompletePlanAndResult($type = null,$problemID = null){

        $res = array();
        if($type && $type == 'plan'){
            $res[] = $this->loadModel('problem')->getCompletedPlan($problemID); //是否按计划完成
        }
        if($type && $type == 'result'){
            $res[] = $this->loadModel('problem')->getExaminationResult($problemID);//审核结果
        }

        if(!$type){
            $res['updateCompletedPlanProblemId']= $this->loadModel('problem')->getCompletedPlan($problemID); //是否按计划完成
            $res['updateexaminationResultProblemId'] = $this->loadModel('problem')->getExaminationResult($problemID);//审核结果
        }
        echo '执行完成，结果如下'.'<br>';
        a($res);
    }

    /**
     * 填充用户员工编号
     * @return string
     */
    public function getLDAPUserCode()
    {
        $type = 'all';
        $users = array();
        $ldapConfig = $this->loadModel('user')->getLDAPConfig();
        if(empty($ldapConfig)) {
            echo 'ldap配置未启用';
            exit();
        };
        if(empty($ldapConfig->turnon)) return 'off';
        $ldapConn = $this->loadModel('ldap')->ldapConnect($ldapConfig);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, isset($ldapConfig->version) ? $ldapConfig->version : 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        $ldapBind = true;
        if(!isset($ldapConfig->anonymous))
        {
            $ldapPassword = html_entity_decode(helper::decryptPassword($ldapConfig->password));
            $ldapBind = @ldap_bind($ldapConn, $ldapConfig->admin, $ldapPassword);
        }
        if(!$ldapBind)
        {
            ldap_unbind($ldapConn);
            return $users;
        }

        $condition = "({$ldapConfig->account}=*)";
        if($type == 'bysearch') $condition = $this->session->ldapQuery;

        $ldapList  = ldap_search($ldapConn, "$ldapConfig->baseDN", $condition, array('*'), $attrsonly = 0, $sizelimit = 0);
        $infos     = ldap_get_entries($ldapConn, $ldapList);

        $allUsers  = $this->dao->select('account,employeeNumber,id')->from(TABLE_USER)->where('deleted')->eq(0)->fetchAll('account');

        if($this->config->debug) file_put_contents($this->app->getTmpRoot() . 'log/ldap.log.php', "<?php\n die(); \n?" . ">\n" . var_export($infos, true));
        $i = 0;
        foreach($infos as $key => $info)
        {
            if(!isset($info[$ldapConfig->account])) continue;

            $account = $info[$ldapConfig->account][0];
            if(empty($account)) continue;
            if(isset($allUsers[$account]) ) {
                if(!empty($allUsers[$account]->employeeNumber)) continue;
                $employeeNumber   = (empty($ldapConfig->employeeNumber) or empty($info[$ldapConfig->employeeNumber])) ? '' : $info[$ldapConfig->employeeNumber];
                if(is_array($employeeNumber))   $employeeNumber  = $employeeNumber[0];
                $id = $allUsers[$account]->id;
                $this->dao->query( "update zt_user set employeeNumber = '$employeeNumber' where id = '$id' limit 1");
                echo '用户 ：'.$account ." : " .$employeeNumber .'员工编号补充成功！'."<br>";
                $i++;
            }
        }
        ldap_free_result($ldapList);
        ldap_unbind($ldapConn);
        echo '执行完成，共处理'.$i.'个用户' ."<br>";
    }

    /**
     * 更新年度计划 项目计划工期（原来计算工作日，现在计算自然日）
     */
    public function updateProjectPlanDuration($id = null){

        $projectplan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('1 = 1 ')->beginIF($id)->andWhere('id')->eq($id)->fi()->fetchAll();
        foreach ($projectplan as $item) {
            if(!$item->duration){
               continue;
            }
            $duration = helper::diffDate3($item->end,$item->begin);
            $this->dao->update(TABLE_PROJECTPLAN)->set("duration")->eq($duration)->where('id')->eq($item->id)->exec();
            echo '年度计划ID:'.$item->id.' 项目计划工期，原 '.$item->duration .' 更新后 '.$duration.'<br>';
        }
        echo '更新完成';
    }
}
