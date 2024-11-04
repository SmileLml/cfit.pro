<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
js::set('isNewTestingRequest', $outwarddelivery->isNewTestingRequest);
js::set('isNewProductEnroll', $outwarddelivery->isNewProductEnroll);
js::set('isNewModifycncc', $outwarddelivery->isNewModifycncc);
js::set('productIdOldTemp', trim($outwarddelivery->productId, ','));
js::set('productIdOld', explode(',',$outwarddelivery->productId));
js::set('appOld', explode(',',$outwarddelivery->app));
js::set('testingrequestDisable',  empty($testingrequest)? false: $testingrequest->disable);
js::set('testingrequestIsOnly',  empty($testingrequest)? false: $testingrequest->isOnly);
js::set('productenrollDisable', empty($productenroll)? false: $productenroll->disable);
js::set('productenrollIsOnly',  empty($productenroll)? false: $productenroll->isOnly);
js::set('modifycnccDisable', empty($modifycncc)? false: $modifycncc->disable);
js::set('implementationForm', $outwarddelivery->implementationForm);
js::set('projectPlanIdOld', $outwarddelivery->projectPlanId);
js::set('propertyOld', empty($modifycncc)? '': $modifycncc->property);
js::set('nodeOld', empty($modifycncc)? '': $modifycncc->node);
js::set('partitionIndex', empty($modifycncc)? 0 : count($modifycncc->appWithPartition)-1);
js::set('changeSourceOld', empty($modifycncc)? '': $modifycncc->changeSource);
js::set('isBusinessCooperateOld', empty($modifycncc)? '': $modifycncc->isBusinessCooperate);
js::set('isBusinessJudgeOld', empty($modifycncc)? '': $modifycncc->isBusinessJudge);
js::set('isBusinessAffectOld', empty($modifycncc)? '': $modifycncc->isBusinessAffect);
js::set('propertyOld', empty($modifycncc)? '': $modifycncc->property);
js::set('isFirst', true);
js::set('lastProductId', explode(',',$outwarddelivery->productId));
js::set('isMul', '');
js::set('relateIndex',empty($modifycncc)||empty($modifycncc->relation)? 0 : count($modifycncc->relation)-1);
js::set('confirmMsg', $lang->outwarddelivery->choiceProjectMsg);
js::set('outwarddeliveryId', $outwarddelivery->id);
js::set('abnormalTips', $this->lang->outwarddelivery->abnormalTips);
js::set('isDisable', '');
?>
<style>
    .input-group-addon{min-width: 150px;} .input-group{margin-bottom: 6px;}
    .top-table{border-top: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .middle-table{border-left: 0px solid; border-right: 0px solid;}
    .tail-table{border-bottom: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
    .input-group-btn{padding: 4px;}
    .chosen-auto-max-width{width: 100% !important;}
    .partitionContainer{float:left;width:90%;display: flex !important;flex-wrap: wrap;justify-content: start;height:auto !important;min-height: 32px;padding: 5px; -webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
    .partitionContainer>div{margin: 0 15px 5px 0}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->outwarddelivery->reissueOrder;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <!-- 交付类型  -->
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-140px'><?php echo $lang->outwarddelivery->deliveryType;?></th>
                    <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)
                        ||(!empty($modifycncc) && $modifycncc->disable)):?>
                        <td><?php echo html::checkbox('isNewTestingRequest',["1" => $lang->outwarddelivery->testingrequest],$outwarddelivery->isNewTestingRequest,"class='testingrequestCheckbox' disabled onclick='testingrequestChange(this.checked)'");?></td>
                        <td class='hidden'><?php echo html::checkbox('isNewTestingRequest',["1" => $lang->outwarddelivery->testingrequest],$outwarddelivery->isNewTestingRequest,"class='testingrequestCheckbox' onclick='testingrequestChange(this.checked)'");?></td>
                    <?php else:?>
                        <td><?php echo html::checkbox('isNewTestingRequest',["1" => $lang->outwarddelivery->testingrequest],$outwarddelivery->isNewTestingRequest,"class='testingrequestCheckbox' onclick='testingrequestChange(this.checked)'");?></td>
                    <?php endif;?>
                    <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)
                        ||(!empty($modifycncc) && $modifycncc->disable)):?>
                        <td><?php echo html::checkbox('isNewProductEnroll', ["1" => $lang->outwarddelivery->productenroll],$outwarddelivery->isNewProductEnroll, "class='productenrollCheckbox' disabled onclick='productenrollChange(this.checked)'");?></td>
                        <td class='hidden'><?php echo html::checkbox('isNewProductEnroll', ["1" => $lang->outwarddelivery->productenroll],$outwarddelivery->isNewProductEnroll, "class='productenrollCheckbox' onclick='productenrollChange(this.checked)'");?></td>
                    <?php else:?>
                        <td><?php echo html::checkbox('isNewProductEnroll', ["1" => $lang->outwarddelivery->productenroll],$outwarddelivery->isNewProductEnroll, "class='productenrollCheckbox' onclick='productenrollChange(this.checked)'");?></td>
                    <?php endif;?>
                    <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)
                        ||(!empty($modifycncc) && $modifycncc->disable)):?>
                        <td><?php echo html::checkbox('isNewModifycncc', ["1" => $lang->outwarddelivery->modifycncc],$outwarddelivery->isNewModifycncc,"class='modifycnccCheckbox' disabled onclick='modifycnccChange(this.checked)'");?></td>
                        <td class='hidden'><?php echo html::checkbox('isNewModifycncc', ["1" => $lang->outwarddelivery->modifycncc],$outwarddelivery->isNewModifycncc,"class='modifycnccCheckbox' disabled onclick='modifycnccChange(this.checked)'");?></td>
                    <?php else:?>
                        <td>
                            <?php echo html::checkbox('isNewModifycncc', ["1" => $lang->outwarddelivery->modifycncc,],$outwarddelivery->isNewModifycncc,"class='modifycnccCheckbox' disabled onclick='modifycnccChange(this.checked)'");?>
                            <!-- 重新发起页面生产变更单不可选 设置隐藏域传值  -->
                            <div class="hidden">
                                <?php echo html::checkbox('isNewModifycncc', ["1" => $lang->outwarddelivery->modifycncc],$outwarddelivery->isNewModifycncc,"class='modifycnccCheckbox'  onclick='modifycnccChange(this.checked)'");?>
                            </div>
                        </td>
                    <?php endif;?>
                </tr>
                </tbody>
            </table>
            <!-- 基础信息  -->
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->baseinfo;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!--                        调整表格格式，勿删-->
                            <tr>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>
                            <!--交付摘要 -->
                            <tr>
                                <th class='w-100px'><?php echo $lang->outwarddelivery->outwardDeliveryDesc;?></th>
                                <td colspan='3' class='required'><?php echo html::input('outwardDeliveryDesc', $outwarddelivery->outwardDeliveryDesc, "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--关联申请单 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->relatedTestingRequest;?></th>
                                <td ><?php echo html::select('testingRequestId', $testingrequestList, $outwarddelivery->testingRequestId, "class='form-control chosen outwarddelivertRelatedTestingRequest' onchange='selectTestingRequestChange(this.value)'");?></td>
                                <!--关联产品登记 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->relatedProductEnroll;?></th>
                                <td><?php echo html::select('productEnrollId', $productenrollList, $outwarddelivery->productEnrollId, "class='form-control chosen outwarddeliverRelatedProductenroll' onchange='selectProductenrollChange(this.value)'");?></td>
                            </tr>
                            <tr>
                                <!--产品线 -->
                                <!-- <th class='w-100px'><?php /*echo $lang->outwarddelivery->productLine;*/?></th>
                                <?php /*if(!empty($productenroll) && $productenroll->disable):*/?>
                                    <td class='required'><?php /*echo html::select('productLine', $productlineList, $outwarddelivery->productLine, "class='form-control chosen' disabled");*/?></td>
                                    <td class='hidden'><?php /*echo html::select('productLine', $productlineList, $outwarddelivery->productLine, "class='form-control chosen'");*/?></td>
                                <?php /*else:*/?>
                                    <td class='required'><?php /*echo html::select('productLine', $productlineList, $outwarddelivery->productLine, "class='form-control chosen'");*/?></td>
                                --><?php /*endif;*/?>
                                <!--所属系统 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->app;?></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td class='required'><?php echo html::select('app[]', $appList, '', "class='form-control chosen outwarddeliveryApp' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('app[]', $appList, '', "class='form-control chosen outwarddeliveryApp'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('app[]', $appList, '', "class='form-control chosen outwarddeliveryApp' onchange='getProductName(this.value)'");?></td>
                                <?php endif;?>
                                <!--产品名称 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->productName;?><i title="<?php echo $lang->outwarddelivery->productNameHelp;?>" class="icon icon-help"></i></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td class="productTd1 required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' disabled onchange='selectProductId(this.value)'");?></td>
                                    <td class="productTd2 hidden required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' disabled multiple onchange='selectProductMultId()'");?></td>
                                    <td class="hidden required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");?></td>
                                    <td class="hidden required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");?></td>
                                <?php else:?>
                                    <td class="productTd1 required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");?></td>
                                    <td class="productTd2 hidden required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <!--产品名称 -->
                                <!--<th class='w-100px'><?php /*echo $lang->outwarddelivery->productName;*/?></th>
                                <?php /*if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):*/?>
                                    <td class="productTd1 required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' disabled onchange='selectProductId(this.value)'");*/?></td>
                                    <td class="productTd2 hidden required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' disabled multiple onchange='selectProductMultId()'");*/?></td>
                                    <td class="hidden required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");*/?></td>
                                    <td class="hidden required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");*/?></td>
                                <?php /*else:*/?>
                                    <td class="productTd1 required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");*/?></td>
                                    <td class="productTd2 hidden required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");*/?></td>
                                --><?php /*endif;*/?>
                                <!--产品编号 -->
                                <!--<th class='w-100px'><?php /*echo $lang->outwarddelivery->productInfoCode;*/?></th>
                                <td class='code1 required'><?php /*echo html::input('productInfoCode', $outwarddelivery->productInfoCode, "class='form-control'");*/?></td>-->
                            </tr>
                            <tr>
                                <!--实现方式 -->
                                <th><?php echo $lang->outwarddelivery->implementationForm;?></th>
                                <td class='required'><?php echo html::select('implementationForm', $lang->outwarddelivery->implementationFormList, $outwarddelivery->implementationForm, "class='form-control chosen' onchange='selectFixType(this.value)'");?></td>
                                <!--所属项目 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->projectPlanId;?><i title="<?php echo $lang->outwarddelivery->projectHelp;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('projectPlanId', $projectList, $outwarddelivery->projectPlanId, "class='form-control chosen' onchange='getDefectList()'");?></td>
                            </tr>
                            <tr>
                                <!--二线工单 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->secondorderId;?></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td><?php echo html::select('secondorderId[]', $secondorderList, $outwarddelivery->secondorderId, "class='form-control chosen secondorderIdClass' disabled multiple");?></td>
                                    <td class='hidden'><?php echo html::select('demandId[]', $secondorderList, $outwarddelivery->secondorderId, "class='form-control chosen secondorderIdClass' multiple");?></td>
                                <?php else:?>
                                    <td><?php echo html::select('secondorderId[]', $secondorderList, $outwarddelivery->secondorderId, "class='form-control chosen secondorderIdClass' disabled multiple");?></td>
                                <?php endif;?>
                                <th class='w-100px abnormalCodeTr'><?php echo $lang->outwarddelivery->associaitonOrder;?><i title="<?php echo $lang->outwarddelivery->abnormalHelp;?>" class="icon icon-help"></i></th>
                                <td class="abnormalCodeTr">
                                    <?php echo html::select('abnormalCode', $abnormalList, $outwarddelivery->modifycnccId, "class='form-control chosen ' disabled  onchange='selectabnormalCode()' ");?>
                                    <div class="abnormalTips" style="margin-top:3px;color:red"><?php echo $this->lang->outwarddelivery->abnormalTips?></div>
                                </td>
                            </tr>
                            <tr>
                                <!--关联问题 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->problemId;?></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td><?php echo html::select('problemId[]', $problemList, $outwarddelivery->problemId, "class='form-control chosen problemIdClass' disabled multiple");?></td>
                                    <td class='hidden'><?php echo html::select('problemId[]', $problemList, $outwarddelivery->problemId, "class='form-control chosen problemIdClass' multiple");?></td>
                                <?php else:?>
<!--                                    <td>--><?php //echo html::select('problemId[]', $problemList, $outwarddelivery->problemId, "class='form-control chosen problemIdClass' disabled multiple");?><!--</td>-->
                                    <td><?php echo html::select('problemId[]', [], [], "class='form-control chosen problemIdClass' disabled multiple");?></td>
                                <?php endif;?>
                                <!--关联需求 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->demandId;?></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td><?php echo html::select('demandId[]', $demandList, $outwarddelivery->demandId, "class='form-control chosen demandIdClass' onchange='selectDemand()' disabled multiple");?></td>
                                    <td class='hidden'><?php echo html::select('demandId[]', $demandList, $outwarddelivery->demandId, "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple");?></td>
                                <?php else:?>
<!--                                    <td>--><?php //echo html::select('demandId[]', $demandList, $outwarddelivery->demandId, "class='form-control chosen demandIdClass' onchange='selectDemand()' disabled multiple");?><!--</td>-->
                                    <td><?php echo html::select('demandId[]', [], [], "class='form-control chosen demandIdClass' onchange='selectDemand()' disabled multiple");?></td>
                                <?php endif;?>
                            </tr>

                            <tr>
                                <!--关联需求任务 -->
                                <th><?php echo $lang->outwarddelivery->requirementId;?></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td><?php echo html::select('requirementId[]', $requirementList, $outwarddelivery->requirementId, "class='form-control chosen requirementClass' disabled multiple");?></td>
                                    <td class='hidden'><?php echo html::select('requirementId[]', $requirementList, $outwarddelivery->requirementId, "class='form-control chosen requirementClass' multiple");?></td>
                                <?php else:?>
                                    <td><?php echo html::select('requirementId[]', $requirementList, $outwarddelivery->requirementId, "class='form-control chosen requirementClass' multiple");?></td>
                                <?php endif;?>
                                <!--所属CBP项目 -->
                                <th><?php echo $lang->outwarddelivery->CBPprojectId;?></th>
                                <?php if((!empty($testingrequest) && $testingrequest->disable) || (!empty($productenroll) && $productenroll->disable)):?>
                                    <td><?php echo html::select('CBPprojectId[]', $cbpprojectList,$outwarddelivery->CBPprojectId, "class='form-control chosen CBPprojectIdClass' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('CBPprojectId[]', $cbpprojectList,$outwarddelivery->CBPprojectId, "class='form-control chosen CBPprojectIdClass'");?></td>
                                <?php else:?>
                                    <td><?php echo html::select('CBPprojectId[]', $cbpprojectList,$outwarddelivery->CBPprojectId, "class='form-control chosen CBPprojectIdClass'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr id="defect">
                                <!--遗留缺陷 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->leaveDefect;?></th>
                                <td class='leaveDefect'><?php echo html::select('leaveDefect[]', $leaveDefectList, $outwarddelivery->leaveDefect, "class='form-control chosen'multiple"); ?></td>
                                <!--修复缺陷 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->fixDefect;?></th>
                                <td class='fixDefect'><?php echo html::select('fixDefect[]', $fixDefectList, $outwarddelivery->fixDefect, "class='form-control chosen'multiple"); ?></td>
                            </tr>
                            <tr class="isMakeAmendsTr hidden">
                                <!--是否后补流程 -->
                                <th class='w-100px'><?php echo $lang->modify->isMakeAmends;?></th>
                                <td class="required"><?php echo html::select('isMakeAmends', $lang->modify->isMakeAmendsList, $modifycncc->isMakeAmends, "class='form-control chosen' onchange='selectIsMakeAmends()'");?></td>
                                <!--实际交付时间 -->
                                <th class='w-100px'><?php echo $lang->modify->actualDeliveryTime;?></th>
                                <td class="<?php if($modifycncc->isMakeAmends == 'yes'){echo 'required';}?>"><?php echo html::input('actualDeliveryTime', $modifycncc->actualDeliveryTime, "class='form-control form-datetime'")?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 测试申请  -->
            <div class="outwarddeliveryTestingrequest hidden">
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->testingrequest;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!--                        调整表格格式，勿删-->
                            <tr>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>
                            <tr>
                                <!-- 测试摘要 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->testSummary;?></th>
                                <td colspan='3' class='required'><?php echo html::input('testSummary', !empty($testingrequest)?$testingrequest->testSummary:'', "class='form-control' maxlength='50' ");?></td>
                            </tr>
                            <tr>
                                <!--测试目标 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->testTarget;?></th>
                                <td colspan='3' class='required'><?php echo html::input('testTarget', !empty($testingrequest)?$testingrequest->testTarget:'', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--是否为集中测试 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->isCentralizedTest;?></th>
                                <td colspan='3' class='required'><?php echo html::select('isCentralizedTest', $lang->testingrequest->isCentralizedTestList, !empty($testingrequest)?$testingrequest->isCentralizedTest:'', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--验收测试类型 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->acceptanceTestType;?></th>
                                <td class='required'><?php echo html::select('acceptanceTestType', $lang->testingrequest->acceptanceTestTypeList, !empty($testingrequest)?$testingrequest->acceptanceTestType:'', "class='form-control chosen' maxlength='200' ");?></td>
                                <!--目前阶段 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->currentStage;?></th>
                                <td class='required'><?php echo html::input('currentStage', !empty($testingrequest)?$testingrequest->currentStage:'', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--操作系统 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->os;?></th>
                                <td class='required'><?php echo html::input('os', !empty($testingrequest)?$testingrequest->os:'', "class='form-control' maxlength='200' ");?></td>
                                <!--数据库类型 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->db;?></th>
                                <td class='required'><?php echo html::input('db', !empty($testingrequest)?$testingrequest->db:'', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--测试内容 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->content;?></th>
                                <td class='required'><?php echo html::textarea('content', !empty($testingrequest)?$testingrequest->content:'', "class='form-control' rows='5'"); ?></td>
                                <!--环境综述 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->env;?></th>
                                <td class='required'><?php echo html::textarea('env', !empty($testingrequest)?$testingrequest->env:'', "class='form-control' rows='5'"); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 产品登记  -->
            <div class="outwarddeliveryProductenroll hidden">
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->productenroll;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!--                        调整表格格式，勿删-->
                            <tr>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>
                            <tr>
                                <!--登记摘要 -->
                                <th class='w-100px'><?php echo $lang->productenroll->productenrollDesc;?></th>
                                <td colspan='3' class='required'><?php echo html::input('productenrollDesc', !empty($productenroll)?$productenroll->productenrollDesc : '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--理由 -->
                                <th class='w-100px'><?php echo $lang->productenroll->reasonFromJinke;?></th>
                                <td colspan='3' class='required'><?php echo html::input('reasonFromJinke', !empty($productenroll)?$productenroll->reasonFromJinke:'', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--是否计划内 -->
                                <th class='w-100px'><?php echo $lang->productenroll->isPlan;?> <i title="<?php echo $lang->outwarddelivery->isPlanTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('isPlan', $lang->productenroll->isPlanList, !empty($productenroll)?$productenroll->isPlan:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('isPlan', $lang->productenroll->isPlanList, !empty($productenroll)?$productenroll->isPlan:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('isPlan', $lang->productenroll->isPlanList, !empty($productenroll)?$productenroll->isPlan:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <!--计划产品名称 -->
                                <th class='w-100px'><?php echo $lang->productenroll->planProductName;?></th>
                                <td><?php echo html::input('planProductName', !empty($productenroll)?$productenroll->planProductName:'', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--软件名称英文 -->
                                <th class='w-100px'><?php echo $lang->productenroll->dynacommEn;?><i title="<?php echo $lang->outwarddelivery->dynacommEnTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::input('dynacommEn', !empty($productenroll)?$productenroll->dynacommEn:'', "class='form-control' maxlength='200' ");?></td>
                                <!--软件名称中文 -->
                                <th class='w-100px'><?php echo $lang->productenroll->dynacommCn;?><i title="<?php echo $lang->outwarddelivery->dynacommCnTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::input('dynacommCn', !empty($productenroll)?$productenroll->dynacommCn:'', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--版本号 -->
                                <th class='w-100px'><?php echo $lang->productenroll->versionNum;?></th>
                                <td class='required'><?php echo html::input('versionNum', !empty($productenroll)?$productenroll->versionNum:'', "placeholder='例如 V1.0.0.1' class='form-control' maxlength='200' ");?></td>
                                <!--上一版本号 -->
                                <th class='w-100px'><?php echo $lang->productenroll->lastVersionNum;?></th>
                                <td class='required'><?php echo html::input('lastVersionNum', !empty($productenroll)?$productenroll->lastVersionNum:'', "placeholder='例如V1.0.0.0，如没有则填写无' class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--检测单位 -->
                                <th class='w-100px'><?php echo $lang->productenroll->checkDepartment;?><i title="<?php echo $lang->outwarddelivery->checkDepartmentTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('checkDepartment', $lang->productenroll->checkDepartmentList, !empty($productenroll)?$productenroll->checkDepartment:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('checkDepartment', $lang->productenroll->checkDepartmentList, !empty($productenroll)?$productenroll->checkDepartment:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('checkDepartment', $lang->productenroll->checkDepartmentList, !empty($productenroll)?$productenroll->checkDepartment:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <!--测试结论 -->
                                <th class='w-100px'><?php echo $lang->productenroll->result;?></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('result', $lang->productenroll->resultList, !empty($productenroll)?$productenroll->result:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('result', $lang->productenroll->resultList, !empty($productenroll)?$productenroll->result:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('result', $lang->productenroll->resultList, !empty($productenroll)?$productenroll->result:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <!--安装节点 -->
                                <th class='w-100px'><?php echo $lang->productenroll->installationNode;?></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('installationNode', $lang->productenroll->installNodeList, !empty($productenroll)?$productenroll->installationNode:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('installationNode', $lang->productenroll->installNodeList, !empty($productenroll)?$productenroll->installationNode:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('installationNode', $lang->productenroll->installNodeList, !empty($productenroll)?$productenroll->installationNode:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <!--软件产品补丁 -->
                                <th class='w-100px'><?php echo $lang->productenroll->softwareProductPatch;?><i title="<?php echo $lang->outwarddelivery->softwareProductPatchTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('softwareProductPatch', $lang->productenroll->softwareProductPatchList, !empty($productenroll)?$productenroll->softwareProductPatch:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('softwareProductPatch', $lang->productenroll->softwareProductPatchList, !empty($productenroll)?$productenroll->softwareProductPatch:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('softwareProductPatch', $lang->productenroll->softwareProductPatchList, !empty($productenroll)?$productenroll->softwareProductPatch:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <!--申请计算机软件著作权登记 -->
                                <th class='w-100px'><?php echo $lang->productenroll->softwareCopyrightRegistration;?><i title="<?php echo $lang->outwarddelivery->softwareCopyrightRegistrationTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('softwareCopyrightRegistration', $lang->productenroll->softwareCopyrightRegistrationList, !empty($productenroll)?$productenroll->softwareCopyrightRegistration:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('softwareCopyrightRegistration', $lang->productenroll->softwareCopyrightRegistrationList, !empty($productenroll)?$productenroll->softwareCopyrightRegistration:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('softwareCopyrightRegistration', $lang->productenroll->softwareCopyrightRegistrationList, !empty($productenroll)?$productenroll->softwareCopyrightRegistration:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <!--所属平台 -->
                                <th class='w-100px'><?php echo $lang->productenroll->platform;?></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::select('platform', $lang->productenroll->appList, !empty($productenroll)?$productenroll->platform:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('platform', $lang->productenroll->appList, !empty($productenroll)?$productenroll->platform:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('platform', $lang->productenroll->appList, !empty($productenroll)?$productenroll->platform:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <!--计划发布时间 -->
                                <th class='w-100px'><?php echo $lang->productenroll->planDistributionTime;?></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::input('planDistributionTime', !empty($productenroll)?$productenroll->planDistributionTime:'', "class='form-control form-datetime' disabled"); ?></td>
                                    <td class='hidden'><?php echo html::input('planDistributionTime', !empty($productenroll)?$productenroll->planDistributionTime:'', "class='form-control form-datetime'"); ?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::input('planDistributionTime', !empty($productenroll)?$productenroll->planDistributionTime:'', "class='form-control form-datetime'"); ?></td>
                                <?php endif;?>
                                <!--计划上线时间 -->
                                <th class='w-100px'><?php echo $lang->productenroll->planUpTime;?></th>
                                <?php if(!empty($productenroll) && $productenroll->disable):?>
                                    <td class='required'><?php echo html::input('planUpTime', !empty($productenroll)?$productenroll->planUpTime:'', "class='form-control form-datetime' disabled"); ?></td>
                                    <td class='hidden'><?php echo html::input('planUpTime', !empty($productenroll)?$productenroll->planUpTime:'', "class='form-control form-datetime'"); ?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::input('planUpTime', !empty($productenroll)?$productenroll->planUpTime:'', "class='form-control form-datetime'"); ?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <!--主要功能及用途简介 -->
                                <th class='w-100px'><?php echo $lang->productenroll->introductionToFunctionsAndUses;?><i title="<?php echo $lang->outwarddelivery->introductionToFunctionsAndUsesTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::textarea('introductionToFunctionsAndUses', !empty($productenroll)?$productenroll->introductionToFunctionsAndUses:'', "class='form-control' rows='5'"); ?></td>
                                <!--备注 -->
                                <th class='w-100px'><?php echo $lang->productenroll->remark;?></th>
                                <td><?php echo html::textarea('remark', !empty($productenroll)?$productenroll->remark:'', "class='form-control' rows='5'"); ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <!--产品介质及字节数 -->
                        <div class="panel">
                            <div class="panel-heading">
                                <?php echo $lang->productenroll->mediaInfo;?>
                            </div>
                            <div class="panel-body">
                                <table class="table table-form table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="w-40px">NO</th>
                                        <th><?php echo $lang->productenroll->media;?></th>
                                        <th><?php echo $lang->productenroll->mediaBytes;?></th>
                                        <th class="w-120px">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="aidMedia">
                                    <?php if(!empty($productenroll)):?>
                                        <?php if($productenroll->disable == true || (!empty($modifycncc) && $modifycncc->disable)):?>
                                            <?php foreach($productenroll->mediaInfoList as $key => $line): ?>
                                                <tr>
                                                    <td><?php echo $key+1 ?></td>
                                                    <td><?php echo html::textarea('mediaName[]', $line->name, "placeholder='没有则填无' readonly class='form-control'");?></td>
                                                    <td><?php echo html::textarea('mediaBytes[]', $line->bytes, "placeholder='请输入正整数' readonly  class='form-control' onkeyup='this.value=this.value.replace(/[^\d]/g, \"\")' onblur='this.value=this.value.replace(/[^\d]/g, \"\")'");?></td>
                                                    <td>
                                                        <a href="javascript:void(0)" onclick="addMediaLine(this)" disabled="true" class="btn btn-link"><i class="icon-plus"></i></a>
                                                        <a href="javascript:void(0)" onclick="deleteMediaLine(this)" disabled="true" class="btn btn-link"><i class="icon-close"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach;?>
                                        <?php else:?>
                                            <?php foreach($productenroll->mediaInfoList as $key => $line): ?>
                                                <tr>
                                                    <td><?php echo $key+1 ?></td>
                                                    <td><?php echo html::textarea('mediaName[]', $line->name, "placeholder='没有则填无'  class='form-control'");?></td>
                                                    <td><?php echo html::textarea('mediaBytes[]', $line->bytes, "placeholder='请输入正整数' class='form-control' onkeyup='this.value=this.value.replace(/[^\d]/g, \"\")' onblur='this.value=this.value.replace(/[^\d]/g, \"\")'");?></td>
                                                    <td>
                                                        <a href="javascript:void(0)" onclick="addMediaLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                                        <a href="javascript:void(0)" onclick="deleteMediaLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    <?php else:?>
                                        <tr>
                                            <td>1</td>
                                            <td><?php echo html::textarea('mediaName[]', '', "placeholder='没有则填无' class='form-control '");?></td>
                                            <td><?php echo html::textarea('mediaBytes[]', '', "placeholder='请输入正整数' class='form-control' onkeyup='this.value=this.value.replace(/[^\d]/g, \"\")' onblur='this.value=this.value.replace(/[^\d]/g, \"\")'");?></td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="addMediaLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a href="javascript:void(0)" onclick="deleteMediaLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                            </td>
                                        </tr>
                                    <?php endif;?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--生产变更  -->
            <div class="outwarddeliveryModifycncc hidden">
                <!-- 变更参数  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->params;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--变更级别 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->level ;?><i title="<?php echo $lang->outwarddelivery->levelTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('level', $lang->modifycncc->levelList, !empty($modifycncc)?$modifycncc->level:'', "class='form-control chosen' onchange='selectLevel(this.value)' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('level', $lang->modifycncc->levelList, !empty($modifycncc)?$modifycncc->level:'', "class='form-control chosen' onchange='selectLevel(this.value)'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('level', $lang->modifycncc->levelList, !empty($modifycncc)?$modifycncc->level:'', "class='form-control chosen' onchange='selectLevel(this.value)'");?></td>
                                <?php endif;?>
                                <!--变更节点 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->node;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('node[]', $lang->modifycncc->nodeList, !empty($modifycncc)?$modifycncc->node:'', "class='form-control chosen' multiple disabled onchange='selectNode(this.value)'");?></td>
                                    <td class='hidden'><?php echo html::select('node[]', $lang->modifycncc->nodeList, !empty($modifycncc)?$modifycncc->node:'', "class='form-control chosen' multiple onchange='selectNode(this.value)'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('node[]', $lang->modifycncc->nodeList, !empty($modifycncc)?$modifycncc->node:'', "class='form-control chosen' multiple onchange='selectNode(this.value)'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr style="display: none">
                                <th class='w-100px'><?php echo $lang->modifycncc->operationType;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td><?php echo html::select('operationType',$lang->modifycncc->operationTypeList, !empty($modifycncc)?$modifycncc->operationType:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('operationType',$lang->modifycncc->operationTypeList, !empty($modifycncc)?$modifycncc->operationType:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td><?php echo html::select('operationType',$lang->modifycncc->operationTypeList, !empty($modifycncc)?$modifycncc->operationType:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr class="app-partition">
                                <th class='w-100px'><?php echo $lang->modifycncc->app;?></th>
                                <td colspan="3" class="required app-partitions-content">
                                    <?php if(!empty($modifycncc->appsInfo)):?>
                                        <?php foreach($modifycncc->appsInfo as $line): ?>
                                            <div class="app-partitions">
                                                <div style="width: 45%;float:left">
                                                    <div class="input-group ">
                                                        <span class="input-group-addon"><?php echo $lang->modifycncc->applicationName;?></span>
                                                        <?php echo html::select("appmodify[$line->index]", $apps, $line->code, "id='appmodify$line->index' data-index='$line->index' class='form-control chosen effectApp' onchange='selectApp(this.value,this.id)'");?>
                                                    </div>
                                                </div>
                                                <div style="width: 55%;float:left">
                                                    <div>
                                                        <a style="margin-left: 5px" href="javascript:void(0)" onclick="addPartition(this)" data-id='<?php echo $line->index ?>' id='addItem<?php echo $line->index ?>' class="btn btn-link"><i class="icon-plus"></i></a>
                                                        <a style="margin-left: 5px" class="" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                    </div>
                                                    <div class="input-group" style="display: none;">
                                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->partitionName;?></span>
                                                        <!--                                                        --><?php //echo html::select("partition[$line->index][]", $line->partitionList,  $line->partition, "id='partition$line->index' class='form-control chosen' multiple");?>
                                                        <div class="form-control">

                                                        </div>
                                                        <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='<?php echo $line->index ?>' id='addItem<?php echo $line->index ?>' class="btn btn-link"><i class="icon-plus"></i></a>
                                                        <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                    </div>
                                                </div>
                                                <div style="clear:both">
                                                    <div style="float: left;padding: 5px"><?php echo $lang->modifycncc->partitionName;?>：</div>
                                                    <div class="partitionContainer">
                                                        <?php echo html::checkbox("partition[$line->index]",$line->partitionList,$line->partition);?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach;?>
                                    <?php else:?>
                                        <div class="app-partitions">
                                            <div style="width: 45%;float:left">
                                                <div class="input-group ">
                                                    <span class="input-group-addon"><?php echo $lang->modifycncc->applicationName;?></span>
                                                    <?php echo html::select('appmodify[0]', $apps, '', "id='appmodify0' data-index='0' class='form-control chosen' onchange='selectApp(this.value,this.id)'");?>
                                                </div>
                                            </div>
                                            <div style="width: 55%;float:left">
                                                <div>
                                                    <a style="margin-left: 5px" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                    <a style="margin-left: 5px" class="" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                </div>
                                                <div class="input-group" style="display:none">
                                                    <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->partitionName;?></span>
                                                    <!--                                                        --><?php //echo html::select('partition[0][]', [], ' ', "id='partition0' class='form-control chosen' multiple");?>
                                                    <div class="form-control">

                                                    </div>
                                                    <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                    <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                </div>
                                            </div>
                                            <div style="clear:both">
                                                <div style="float: left;padding: 5px"><?php echo $lang->modifycncc->partitionName;?>：</div>
                                                <div class="partitionContainer">

                                                </div>
                                            </div>
                                        </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr class="app-only hidden">
                                <th><?php echo $lang->modifycncc->app;?></th>
                                <td colspan="3" class="required">
                                    <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                        <?php echo html::select('appOnly[]', $apps, !empty($modifycncc)?$modifycncc->appOnly:'', "id='appOnly' class='form-control chosen' multiple disabled");?>
                                        <div class="hidden"><?php echo html::select('appOnly[]', $apps, !empty($modifycncc)?$modifycncc->appOnly:'', "id='appOnly' class='form-control chosen ' multiple");?></div>
                                    <?php else:?>
                                        <?php echo html::select('appOnly[]', $apps, !empty($modifycncc)?$modifycncc->appOnly:'', "id='appOnly' class='form-control chosen' multiple");?>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->modifycncc->mode;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('mode', $lang->modifycncc->modeList, !empty($modifycncc)?$modifycncc->mode:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('mode', $lang->modifycncc->modeList, !empty($modifycncc)?$modifycncc->mode:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('mode', $lang->modifycncc->modeList, !empty($modifycncc)?$modifycncc->mode:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <th class='w-100px'><?php echo $lang->modifycncc->classify;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('classify', $lang->modifycncc->classifyList, !empty($modifycncc)?$modifycncc->classify:'', "class='form-control chosen' disabled");?></td>
                                    <td class="hidden"><?php echo html::select('classify', $lang->modifycncc->classifyList, !empty($modifycncc)?$modifycncc->classify:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('classify', $lang->modifycncc->classifyList, !empty($modifycncc)?$modifycncc->classify:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->changeSource;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('changeSource',$lang->modifycncc->changeSourceList, !empty($modifycncc)?$modifycncc->changeSource:'', "class='form-control chosen' disabled onchange='selectChangeSource(this.value)'");?></td>
                                    <td class='hidden'><?php echo html::select('changeSource',$lang->modifycncc->changeSourceList, !empty($modifycncc)?$modifycncc->changeSource:'', "class='form-control chosen' onchange='selectChangeSource(this.value)'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('changeSource',$lang->modifycncc->changeSourceList, !empty($modifycncc)?$modifycncc->changeSource:'', "class='form-control chosen' onchange='selectChangeSource(this.value)'");?></td>
                                <?php endif;?>
                                <th><?php echo $lang->modifycncc->changeStage;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('changeStage', $lang->modifycncc->changeStageList, !empty($modifycncc)?$modifycncc->changeStage:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('changeStage', $lang->modifycncc->changeStageList, !empty($modifycncc)?$modifycncc->changeStage:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('changeStage', $lang->modifycncc->changeStageList, !empty($modifycncc)?$modifycncc->changeStage:'', "class='form-control chosen' onchange='selectChangeStage(this.value)'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->implementModality;?><i title="<?php echo $lang->modifycncc->implementModalityTips;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('implementModality',$lang->modifycncc->implementModalityNewList, !empty($modifycncc)?$modifycncc->implementModality:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('implementModality',$lang->modifycncc->implementModalityNewList, !empty($modifycncc)?$modifycncc->implementModality:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('implementModality',$lang->modifycncc->implementModalityNewList, !empty($modifycncc)?$modifycncc->implementModality:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <th class='w-100px'><?php echo $lang->modifycncc->type;?><i title="<?php echo $lang->outwarddelivery->typeTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('type', $lang->modifycncc->typeList, !empty($modifycncc)?$modifycncc->type:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('type', $lang->modifycncc->typeList, !empty($modifycncc)?$modifycncc->type:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('type', $lang->modifycncc->typeList, !empty($modifycncc)?$modifycncc->type:'', "class='form-control chosen' onchange='selectChange()'");?></td>
                                <?php endif;?>
                            </tr>
                            <?php
                            $toolsClass = '';
                            if (!in_array($modifycncc->implementModality,[4,5])){
                                $toolsClass = 'hidden';
                            }
                            ?>
                            <tr>
                                <!--变更形式 -->
                                <th><?php echo $lang->modifycncc->changeForm;?><i title="<?php echo $lang->modifycncc->changeFormTips;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('changeForm',$lang->modifycncc->changeFormList, !empty($modifycncc)?$modifycncc->changeForm:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('changeForm',$lang->modifycncc->changeFormList, !empty($modifycncc)?$modifycncc->changeForm:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('changeForm',$lang->modifycncc->changeFormList, !empty($modifycncc)?$modifycncc->changeForm:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <!--自动化工具 -->
                                <th class="<?php echo $toolsClass;?>"><?php echo $lang->modifycncc->automationTools;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('automationTools',$lang->modifycncc->automationToolsList, !empty($modifycncc)?$modifycncc->automationTools:'', "class='form-control chosen' disabled");?></td>
                                    <td class='hidden'><?php echo html::select('automationTools',$lang->modifycncc->automationToolsList, !empty($modifycncc)?$modifycncc->automationTools:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required <?php echo $toolsClass;?>"><?php echo html::select('automationTools',$lang->modifycncc->automationToolsList, !empty($modifycncc)?$modifycncc->automationTools:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr class="hidden urgent">
                                <!--紧急来源 -->
                                <th><?php echo $lang->outwarddelivery->urgentSource;?></th>
                                <td class="required"><?php echo html::select('urgentSource',$lang->modifycncc->urgentSourceList, !empty($modifycncc)?$modifycncc->urgentSource:'', "class='form-control chosen'");?></td>
                                <!--紧急原因 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->urgentReason;?></th>
                                <td class="required"><?php echo html::input('urgentReason', !empty($modifycncc)?$modifycncc->urgentReason:'', "class='form-control'");?></td>
                            </tr>
                            <?php
                            $aadsReasonClass = 'hidden';
                            if (in_array($modifycncc->implementModality,[1,3,6])){
                                $aadsReasonClass = '';
                            }
                            ?>
                            <tr class="aadsReasonTr <?php echo $aadsReasonClass?>">
                                <th class='w-100px'><?php echo $lang->outwarddelivery->aadsReason;?></th>
                                <td class='required' colspan="3"><?php echo html::input('aadsReason', $modifycncc->aadsReason, "class='form-control'"); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->isBusinessCooperate;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('isBusinessCooperate',$lang->modifycncc->isBusinessCooperateList, !empty($modifycncc)?$modifycncc->isBusinessCooperate:'', "class='form-control chosen' disabled onchange='selectIsBusinessCooperate(this.value)'");?></td>
                                    <td class="hidden"><?php echo html::select('isBusinessCooperate',$lang->modifycncc->isBusinessCooperateList, !empty($modifycncc)?$modifycncc->isBusinessCooperate:'', "class='form-control chosen' onchange='selectIsBusinessCooperate(this.value)'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('isBusinessCooperate',$lang->modifycncc->isBusinessCooperateList, !empty($modifycncc)?$modifycncc->isBusinessCooperate:'', "class='form-control chosen' onchange='selectIsBusinessCooperate(this.value)'");?></td>
                                <?php endif;?>
                                <th><?php echo $lang->modifycncc->isBusinessJudge;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('isBusinessJudge',$lang->modifycncc->isBusinessJudgeList, !empty($modifycncc)?$modifycncc->isBusinessJudge:'', "class='form-control chosen' disabled onchange='selectIsBusinessJudge(this.value)'");?></td>
                                    <td class='hidden'><?php echo html::select('isBusinessJudge',$lang->modifycncc->isBusinessJudgeList, !empty($modifycncc)?$modifycncc->isBusinessJudge:'', "class='form-control chosen' onchange='selectIsBusinessJudge(this.value)'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('isBusinessJudge',$lang->modifycncc->isBusinessJudgeList, !empty($modifycncc)?$modifycncc->isBusinessJudge:'', "class='form-control chosen' onchange='selectIsBusinessJudge(this.value)'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->modifycncc->isBusinessAffect;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('isBusinessAffect',$lang->modifycncc->isBusinessAffectList, !empty($modifycncc)?$modifycncc->isBusinessAffect:'', "class='form-control chosen' disabled onchange='selectIsBusinessAffect(this.value)'");?></td>
                                    <td class='hidden'><?php echo html::select('isBusinessAffect',$lang->modifycncc->isBusinessAffectList, !empty($modifycncc)?$modifycncc->isBusinessAffect:'', "class='form-control chosen' onchange='selectIsBusinessAffect(this.value)'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('isBusinessAffect',$lang->modifycncc->isBusinessAffectList, !empty($modifycncc)?$modifycncc->isBusinessAffect:'', "class='form-control chosen' onchange='selectIsBusinessAffect(this.value)'");?></td>
                                <?php endif;?>
                                <th class='w-100px'><?php echo $lang->modifycncc->property;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::select('property', $lang->modifycncc->propertyList, !empty($modifycncc)?$modifycncc->property:'', "class='form-control chosen' disabled onchange='selectProperty(this.value)'");?></td>
                                    <td class='hidden'><?php echo html::select('property', $lang->modifycncc->propertyList, !empty($modifycncc)?$modifycncc->property:'', "class='form-control chosen' onchange='selectProperty(this.value)'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::select('property', $lang->modifycncc->propertyList, !empty($modifycncc)?$modifycncc->property:'', "class='form-control chosen' onchange='selectProperty(this.value)'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr class="backspaceExpectedTime hidden">
                                <th><?php echo $lang->modifycncc->backspaceExpectedStartTime;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::input('backspaceExpectedStartTime', !empty($modifycncc)?$modifycncc->backspaceExpectedStartTime:'', "class='form-control form-datetime' disabled");?></td>
                                    <td class="hidden"><?php echo html::input('backspaceExpectedStartTime', !empty($modifycncc)?$modifycncc->backspaceExpectedStartTime:'', "class='form-control form-datetime'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::input('backspaceExpectedStartTime', !empty($modifycncc)?$modifycncc->backspaceExpectedStartTime:'', "class='form-control form-datetime'");?></td>
                                <?php endif;?>
                                <th><?php echo $lang->modifycncc->backspaceExpectedEndTime;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::input('backspaceExpectedEndTime', !empty($modifycncc)?$modifycncc->backspaceExpectedEndTime:'', "class='form-control form-datetime' disabled");?></td>
                                    <td class="hidden"><?php echo html::input('backspaceExpectedEndTime', !empty($modifycncc)?$modifycncc->backspaceExpectedEndTime:'', "class='form-control form-datetime'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::input('backspaceExpectedEndTime', !empty($modifycncc)?$modifycncc->backspaceExpectedEndTime:'', "class='form-control form-datetime'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr class="isReviewTr">
                                <!--是否评审方案 -->
                                <th><?php echo $lang->modifycncc->isReview;?></th>
                                <td class="required"><?php echo html::select('isReview',$lang->modifycncc->isReviewList, !empty($modifycncc)?$modifycncc->isReview:'', "class='form-control chosen' onchange='selectReview()'");?></td>
                                <th class="reviewReportClass hidden"><?php echo $lang->modifycncc->reviewReport;?></th>
                                <td class="required reviewReportClass hidden"><?php echo html::select('reviewReport[]', $reviewReportList, !empty($modifycncc)?$modifycncc->reviewReport:'', "class='form-control chosen' multiple");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更内容  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->content;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!--                        调整表格格式，勿删-->
                            <tr>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-100px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->modifycncc->desc;?></th>
                                <td colspan="3" class='required'><?php echo html::input('desc', !empty($modifycncc)?$modifycncc->desc:'', "class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->planBegin;?><i title="<?php echo $lang->outwarddelivery->planBeginTip;?>" class="icon icon-help"></i></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::input('planBegin', !empty($modifycncc)?$modifycncc->planBegin:'', "class='form-control form-datetime' disabled");?></td>
                                    <td class='hidden'><?php echo html::input('planBegin', !empty($modifycncc)?$modifycncc->planBegin:'', "class='form-control form-datetime'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::input('planBegin', !empty($modifycncc)?$modifycncc->planBegin:'', "class='form-control form-datetime'");?></td>
                                <?php endif;?>
                                <th><?php echo $lang->modifycncc->planEnd;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class='required'><?php echo html::input('planEnd', !empty($modifycncc)?$modifycncc->planEnd:'', "class='form-control form-datetime' disabled");?></td>
                                    <td class='hidden'><?php echo html::input('planEnd', !empty($modifycncc)?$modifycncc->planEnd:'', "class='form-control form-datetime'");?></td>
                                <?php else:?>
                                    <td class='required'><?php echo html::input('planEnd', !empty($modifycncc)?$modifycncc->planEnd:'', "class='form-control form-datetime'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->target;?></th>
                                <td class='required'><?php echo html::textarea('target', !empty($modifycncc)?$modifycncc->target:'', "rows='5' class='form-control' maxlength='2000'");?></td>
                                <th><?php echo $lang->modifycncc->reason;?></th>
                                <td class='required'><?php echo html::textarea('reason', !empty($modifycncc)?$modifycncc->reason:'', "rows='5' class='form-control' maxlength='1000'");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->changeContentAndMethod;?></th>
                                <td class='required'><?php echo html::textarea('changeContentAndMethod', !empty($modifycncc)?$modifycncc->changeContentAndMethod:'', "rows='5' class='form-control'");?></td>
                                <th><?php echo $lang->modifycncc->step;?></th>
                                <td class='required'><?php echo html::textarea('step', !empty($modifycncc)?$modifycncc->step:'', "rows='5' class='form-control'");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->techniqueCheck;?></th>
                                <td class='required'><?php echo html::textarea('techniqueCheck', !empty($modifycncc)?$modifycncc->techniqueCheck:'', "rows='5' class='form-control' maxlength='500'");?></td>
                                <th><?php echo $lang->modifycncc->test;?></th>
                                <td class='required'><?php echo html::textarea('test', !empty($modifycncc)?$modifycncc->test:'', "rows='5' class='form-control'");?></td>
                            </tr
                            <tr>
                                <th><?php echo $lang->modifycncc->checkList;?></th>
                                <td colspan="3" class='required'>
                                    <?php echo html::textarea('checkList', !empty($modifycncc)?$modifycncc->checkList:'', "rows='5' class='form-control'");?>
                                    <div style="margin-top:3px;color:red"><?php echo $lang->outwarddelivery->checkListTip?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 业务配合  -->
                <div class="panel outwarddeliveryBusinessCooperate hidden">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->BusinessCooperate;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->cooperateDepNameList;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('cooperateDepNameList',$lang->modifycncc->cooperateDepNameListList, !empty($modifycncc)?$modifycncc->cooperateDepNameList:'', "class='form-control chosen' disabled");?></td>
                                    <td class="hidden"><?php echo html::select('cooperateDepNameList',$lang->modifycncc->cooperateDepNameListList, !empty($modifycncc)?$modifycncc->cooperateDepNameList:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('cooperateDepNameList',$lang->modifycncc->cooperateDepNameListList, !empty($modifycncc)?$modifycncc->cooperateDepNameList:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->businessCooperateContent;?></th>
                                <td class="required"><?php echo html::textarea('businessCooperateContent', !empty($modifycncc)?$modifycncc->businessCooperateContent:'', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 业务验证  -->
                <div class="panel outwarddeliveryBusinessJudge hidden">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->BusinessJudge;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->judgeDep;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('judgeDep',$lang->modifycncc->judgeDepList, !empty($modifycncc)?$modifycncc->judgeDep:'', "class='form-control chosen' disabled");?></td>
                                    <td class="hidden"><?php echo html::select('judgeDep',$lang->modifycncc->judgeDepList, !empty($modifycncc)?$modifycncc->judgeDep:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('judgeDep',$lang->modifycncc->judgeDepList, !empty($modifycncc)?$modifycncc->judgeDep:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->judgePlan;?></th>
                                <td class="required"><?php echo html::textarea('judgePlan', !empty($modifycncc)?$modifycncc->judgePlan:'', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 项目控制表  -->
                <div class="panel outwarddeliveryControltable hidden">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->Controltable;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->controlTableFile;?></th>
                                <td class="required"><?php echo html::input('controlTableFile', !empty($modifycncc)?$modifycncc->controlTableFile:'', "placeholder='请填写正确的名称，比如“2021年数据管理CBP项目上线控制表”' class='form-control' maxlength='100'");?></td>
                            </tr>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->controlTableSteps;?></th>
                                <td class="required"><?php echo html::input('controlTableSteps', !empty($modifycncc)?$modifycncc->controlTableSteps:'', "rows='5' placeholder='多个之间以英文逗号分隔，比如“BJ101,BJ102,BJ102”' class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更关联  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->Project;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th><?php echo $lang->modifycncc->changeRelation;?></th>
                                <td colspan="3">
                                    <?php if(empty($modifycncc) || count($modifycncc->relation)==0):?>
                                        <div class="table-row changeRequired">
                                            <div class="table-col w-400px">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><?php echo $lang->modifycncc->tableTitle->relate;?></span>
                                                    <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                                        <?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, '', "id='relate0' data-index='0' class='form-control chosen' disabled");?>
                                                        <div class="hidden"><?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, '', "id='relate0' data-index='0' class='form-control chosen'");?></div>
                                                    <?php else:?>
                                                        <?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, '', "id='relate0' data-index='0' class='form-control chosen'");?>
                                                    <?php endif;?>

                                                </div>
                                            </div>
                                            <div class="table-col">
                                                <div class="input-group">
                                                    <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->tableTitle->relateNum ;?></span>
                                                    <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                                        <?php echo html::select('relateNum[]', $modifycnccList, '', "id='relateNum0' class='form-control chosen' disabled");?>
                                                        <div class="hidden"><?php echo html::select('relateNum[]', $modifycnccList, '', "id='relateNum0' class='form-control hidden chosen'");?></div>
                                                    <?php else:?>
                                                        <?php echo html::select('relateNum[]', $modifycnccList, '', "id='relateNum0' class='form-control chosen'");?>
                                                    <?php endif;?>
                                                    <a class="input-group-btn" href="javascript:void(0)" onclick="addRelate(this)" data-id='0' id='addRelateItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                    <a class="input-group-btn" href="javascript:void(0)" onclick="delRelate(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else:?>
                                        <?php foreach($modifycncc->relation as $key => $line):?>
                                            <div class="table-row changeRequired">
                                                <div class="table-col w-400px">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><?php echo $lang->modifycncc->tableTitle->relate;?></span>
                                                        <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                                            <?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, $line[0], "id='relate$key' data-index='$key'  class='form-control chosen' disabled");?>
                                                            <div class="hidden"><?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, $line[0], "id='relate$key' data-index='$key'  class='form-control chosen hidden'");?></div>
                                                        <?php else:?>
                                                            <?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, $line[0], "id='relate$key' data-index='$key'  class='form-control chosen'");?>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="table-col">
                                                    <div class="input-group">
                                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->tableTitle->relateNum;?></span>
                                                        <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                                            <?php echo html::select('relateNum[]', $modifycnccList, $line[1], "id='relateNum$key' class='form-control relateNum chosen' disabled");?>
                                                            <div class="hidden"><?php echo html::select('relateNum[]', $modifycnccList, $line[1], "id='relateNum$key' class='form-control hidden chosen'");?></div>
                                                        <?php else:?>
                                                            <?php echo html::select('relateNum[]', $modifycnccList, $line[1], "id='relateNum$key' class='form-control chosen'");?>
                                                        <?php endif;?>
                                                        <a class="input-group-btn" href="javascript:void(0)" onclick="addRelate(this)" data-id='<?php echo $key ?>' id='addRelateItem<?php echo $key ?>' class="btn btn-link"><i class="icon-plus"></i></a>
                                                        <a class="input-group-btn" href="javascript:void(0)" onclick="delRelate(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach?>
                                    <?php endif;?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更可行性分析  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->FeasibilityAnalysis;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->feasibilityAnalysis;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('feasibilityAnalysis[]',$lang->modifycncc->feasibilityAnalysisList, !empty($modifycncc)?$modifycncc->feasibilityAnalysis:'', "class='form-control chosen' multiple disabled");?></td>
                                    <td class="hidden"><?php echo html::select('feasibilityAnalysis[]',$lang->modifycncc->feasibilityAnalysisList, !empty($modifycncc)?$modifycncc->feasibilityAnalysis:'', "class='form-control chosen' multiple");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('feasibilityAnalysis[]',$lang->modifycncc->feasibilityAnalysisList, !empty($modifycncc)?$modifycncc->feasibilityAnalysis:'', "class='form-control chosen' multiple");?></td>
                                <?php endif;?>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->risk;?></th>
                                <td class="required"><?php echo html::textarea('risk', !empty($modifycncc)?$modifycncc->risk:'', "rows='5' class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 风险分析与应急处置  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->RiskAnalysis;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form table-bordered">
                            <thead>
                            <tr>
                                <th class="w-40px">NO</th>
                                <th class="required">风险分析</th>
                                <th class="required">应急回退方式</th>
                                <th class="w-120px">操作</th>
                            </tr>
                            </thead>
                            <tbody id="aid">
                            <?php if(!empty($modifycncc)):?>
                                <?php foreach($modifycncc->riskAnalysisEmergencyHandle as $key => $line): ?>
                                    <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                        <tr>
                                            <td><?php echo $key+1 ?></td>
                                            <td><?php echo html::textarea('riskAnalysis[]', $line->riskAnalysis, "placeholder='没有则填无' class='form-control' readonly");?></td>
                                            <td><?php echo html::textarea('emergencyBackWay[]', $line->emergencyBackWay, "placeholder='没有则填无' class='form-control' readonly");?></td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="addLine(this)" disabled="true" class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a href="javascript:void(0)" onclick="deleteLine(this)" disabled="true" class="btn btn-link"><i class="icon-close"></i></a>
                                            </td>
                                        </tr>
                                    <?php else:?>
                                        <tr>
                                            <td><?php echo $key+1 ?></td>
                                            <td><?php echo html::textarea('riskAnalysis[]', $line->riskAnalysis, "placeholder='没有则填无' class='form-control' ");?></td>
                                            <td><?php echo html::textarea('emergencyBackWay[]', $line->emergencyBackWay, "placeholder='没有则填无' class='form-control' ");?></td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="addLine(this)"  class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a href="javascript:void(0)" onclick="deleteLine(this)"  class="btn btn-link"><i class="icon-close"></i></a>
                                            </td>
                                        </tr>
                                    <?php endif?>
                                <?php endforeach;?>
                            <?php else:?>
                                <tr>
                                    <td>1</td>
                                    <td><?php echo html::textarea('riskAnalysis[]', '', "placeholder='没有则填无' class='form-control'");?></td>
                                    <td><?php echo html::textarea('emergencyBackWay[]', '', "placeholder='没有则填无' class='form-control'");?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                        <a href="javascript:void(0)" onclick="deleteLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                    </td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更影响  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->Effect;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->effect;?></th>
                                <td class="required"><?php echo html::textarea('effect', !empty($modifycncc)?$modifycncc->effect:'', "rows='5' class='form-control'  maxlength='200'");?></td>
                                <th><?php echo $lang->modifycncc->businessFunctionAffect;?></th>
                                <td class="required"><?php echo html::textarea('businessFunctionAffect', !empty($modifycncc)?$modifycncc->businessFunctionAffect:'', "rows='5' class='form-control'  maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->backupDataCenterChangeSyncDesc;?></th>
                                <td class="required"><?php echo html::textarea('backupDataCenterChangeSyncDesc', !empty($modifycncc)?$modifycncc->backupDataCenterChangeSyncDesc:'', "rows='5' class='form-control'  maxlength='200'");?></td>
                                <th><?php echo $lang->modifycncc->emergencyManageAffect;?></th>
                                <td class="required"><?php echo html::textarea('emergencyManageAffect', !empty($modifycncc)?$modifycncc->emergencyManageAffect:'', "rows='5' class='form-control'  maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--变更关联影响分析 -->
                                <th><?php echo $lang->modifycncc->changeImpactAnalysis;?><i title="<?php echo $lang->modifycncc->changeImpactAnalysisTips;?>" class="icon icon-help"></i></th>
                                <td colspan="3" class="required">
                                    <?php echo html::textarea('changeImpactAnalysis', !empty($modifycncc)?$modifycncc->changeImpactAnalysis:'', "rows='5' placeholder='".$lang->modifycncc->changeImpactAnalysisTips."' class='form-control' maxlength='2000'");?>
                                    <div style="margin-top:3px;color:red"><?php echo $lang->modifycncc->changeImpactAnalysisTips?></div>
                                </td>
                            </tr>
                            <tr class="businessAffect hidden">
                                <th><?php echo $lang->modifycncc->businessAffect;?></th>
                                <td colspan="3" class="required"><?php echo html::textarea('businessAffect', !empty($modifycncc)?$modifycncc->businessAffect:'', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 基准验证  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->outwarddelivery->subTitle->benchmarkVerification;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modifycncc->benchmarkVerificationType;?></th>
                                <?php if(!empty($modifycncc) && $modifycncc->disable):?>
                                    <td class="required"><?php echo html::select('benchmarkVerificationType',$lang->modifycncc->benchmarkVerificationTypeList, !empty($modifycncc)?$modifycncc->benchmarkVerificationType:'', "class='form-control chosen' disabled");?></td>
                                    <td class="hidden"><?php echo html::select('benchmarkVerificationType',$lang->modifycncc->benchmarkVerificationTypeList, !empty($modifycncc)?$modifycncc->benchmarkVerificationType:'', "class='form-control chosen'");?></td>
                                <?php else:?>
                                    <td class="required"><?php echo html::select('benchmarkVerificationType',$lang->modifycncc->benchmarkVerificationTypeList, !empty($modifycncc)?$modifycncc->benchmarkVerificationType:'', "class='form-control chosen'");?></td>
                                <?php endif;?>
                                <th class="w-100px"><?php echo $lang->modifycncc->verificationResults;?></th>
                                <td><?php echo html::input('verificationResults', !empty($modifycncc)?$modifycncc->verificationResults:'', "class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <table class="table table-form">
                <tbody>
                <!--                        调整表格格式，勿删-->
                <tr>
                    <th class='w-120px' style="height: 0;padding:0"></th>
                    <td style="height: 0;padding:0"></td>
                    <th class='w-80px' style="height: 0;padding:0"></th>
                    <td style="height: 0;padding:0"></td>
                </tr>
                <tr class="nodes hidden">
                    <th class='w-100px'>
                        <?php echo $lang->modifycncc->reviewNodes;?>
                        <i title="<?php echo $lang->modifycncc->reviewNodesTip;?>" class="icon icon-help"></i>
                    </th>
                    <td colspan="2">
                        <?php
                        foreach($lang->modifycncc->reviewerList as $key => $nodeName):
                            if ( !in_array($key, $lang->outwarddelivery->skipNodes)):
                                $currentAccounts = 3 == $key ? implode(',', array_keys($reviewers[$key])) : '';
                                if(in_array($key, $defChosenReviewNodes) && isset($reviewerAccounts[$key])):
                                    $currentAccounts = implode(',', $reviewerAccounts[$key]);
                                endif;
                                ?>
                                <div class='input-group node-item node<?php echo $key;?>' style='width:80%'>
                                    <span class='input-group-addon'><?php echo $nodeName;?></span>
                                    <!--组长审批是非必填 -->
                                    <?php if($key == '1'): ?>
                                        <?php echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' multiple");?>
                                    <?php else: ?>
                                        <?php echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");?>
                                    <?php endif; ?>
                                </div>
                            <?php endif;?>
                        <?php endforeach;?>
                    </td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->outwarddelivery->applyUsercontact;?></th>
                    <td class="required"><?php echo html::input('applyUsercontact', $outwarddelivery->contactTel, "placeholder='请填写手机号' class='form-control' maxlength='20'");?></td>
                    <!-- <th class="w-100px"><?php /*echo $lang->outwarddelivery->consumed;*/?></th>
                    <td class="required"><?php /*echo html::input('consumed', $outwarddelivery->consumed, "class='form-control'");*/?></td>-->
                </tr>
                <tr class="hidden manufacturerTr">
                    <!--产商支持人员 -->
                    <th class='w-100px'><?php echo $lang->outwarddelivery->manufacturer;?></th>
                    <td><?php echo html::input('manufacturer', $outwarddelivery->manufacturer, "class='form-control'");?></td>
                    <!--产商支持人员联系方式 -->
                    <th class="w-100px"><?php echo $lang->outwarddelivery->manufacturerConnect;?></th>
                    <td><?php echo html::input('manufacturerConnect', $outwarddelivery->manufacturerConnect, "class='form-control'");?></td>
                </tr>
                <tr>
                    <input type="hidden" name="issubmit" value="<?php echo $outwarddelivery->issubmit;?>">
                    <td class='form-actions text-center' colspan='4'><?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') . html::commonButton($lang->outwarddelivery->submit, '', 'btn btn-wide btn-primary submitBtn') . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
        <input type="hidden" value="<?php echo $outwarddelivery->status?>" id="status">
        <input type="hidden" value="<?php echo $outwarddelivery->id?>" id="id">
        <input type="hidden" id="responseid">
    </div>
</div>
<table class="hidden">
    <tbody id="lineDemo">
    <tr>
        <td></td>
        <td><?php echo html::textarea('riskAnalysis[]', '', "placeholder='没有则填无' class='form-control'");?></td>
        <td><?php echo html::textarea('emergencyBackWay[]', '', "placeholder='没有则填无' class='form-control'");?></td>
        <td>
            <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="deleteLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>
<table class="hidden">
    <tbody id="lineMediaDemo">
    <tr>
        <td></td>
        <td><?php echo html::textarea('mediaName[]', '', "placeholder='没有则填无' class='form-control '");?></td>
        <td><?php echo html::textarea('mediaBytes[]', '', "placeholder='请输入正整数' class='form-control ' onkeyup='this.value=this.value.replace(/[^\d]/g, \"\")' onblur='this.value=this.value.replace(/[^\d]/g, \"\")'");?></td>
        <td>
            <a href="javascript:void(0)" onclick="addMediaLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="deleteMediaLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>
<script>
    $(function () {
        selectChangeStage(<?php echo $modifycncc->changeStage;?>)
        selectabnormalCode();
        selectChange()
        selectIsMakeAmends()
    })
    //保存不需要校验数据
    $(".saveBtn").click(function () {
        $(this).attr('disabled', true);
        $("[name='issubmit']").val("save");
        submitBtn();
    })

    $(function () {
        $('.submitBtn').click(function() {
            $("[name='issubmit']").val("submit");
            var type = $(this).parent().attr("data-type");
            // alert(type);
            if($('.modifycnccCheckbox').prop('checked') && $('#type').val() == 2){
                var msg = "按照清总要求，非紧急变更，一级变更提前十个工作日，二级变更提前五个工作日，三级变更提前三个工作日，请确认【预计开始时间:"+$("#planBegin").val()+"】是否符合要求，点击“确定”则保存对外交付单，点击取消，则不保存继续修改";
                if(confirm(msg) == true){
                    submitBtn();
                }else{
                    return false;
                }
            }else{
                submitBtn();
            }
        });
    })
    var _level = "<?php echo $modifycncc->level?>";
    if (_level != 1){
        $('.isReviewTr').addClass('hidden');
        $('.isReview-required').addClass('hidden');
    }
    function selectReviewReport(reportId){
        if(!isEmpty(reportId)){
            $('.reviewClass').removeClass('hidden');
            var href = $('.reviewClass').attr('href');
            var hrefList = href.split('-');
            hrefList.length = 3;
            hrefList[2] = reportId;
            str = hrefList.join('-')
            str += '.html?onlybody=yes'
            $('.reviewClass').attr('href', str);
        }else{
            $('.reviewClass').addClass('hidden');
        }
    }

    function selectReview(){
        var value = $('#isReview').val();
        if(value == 1){
            $('.reviewReportClass').removeClass('hidden');
        }else{
            $('.reviewReportClass').addClass('hidden');
        }
    }

    //需求条目选择触发需求任务
    function selectDemand(){
        var demandIds = $(".demandIdClass").val();
        var requirementIds = $(".requirementClass").val();
        if(demandIds == null || demandIds == '' || (demandIds.length == 1 && demandIds[0] == '')){
            $('#requirementId').parent().removeClass('required');
        }else{
            $('#requirementId').parent().addClass('required');
        }
        $.get(createLink('outwarddelivery', 'ajaxGetOpinionByDemand', "demandIds=" + demandIds), function(data){
            $('#requirementId').nextAll().remove();
            $('#requirementId').replaceWith(data)
            $('#requirementId').chosen()
            // $('.requirementClass').val(requirementIds).trigger("chosen:updated");
        });
    }
    //测试申请模块不能编辑
    function testingrequestModuleDisable(isDisable,isOnly){
        if(isDisable){
            $('#testTarget').attr('readonly','readonly');
            $('#currentStage').attr('readonly','readonly');
            $('#os').attr('readonly','readonly');
            $('#db').attr('readonly','readonly');
            window.editor['content'].readonly(true);
            window.editor['env'].readonly(true);
        }
        if(isOnly){
            $('#outwardDeliveryDesc').attr('readonly','readonly');
            $('#testingRequestId').prop('disabled', true).trigger("chosen:updated");
            $('#productEnrollId').prop('disabled', true).trigger("chosen:updated");
            $('#productLine').prop('disabled', true).trigger("chosen:updated");
            $('#app').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliveryProduct1').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliveryProduct2').prop('disabled', true).trigger("chosen:updated");
            $('#productInfoCode').attr('readonly','readonly');
            $('#implementationForm').prop('disabled', true).trigger("chosen:updated");
            $('#projectPlanId').prop('disabled', true).trigger("chosen:updated");
            $('.problemIdClass').prop('disabled', true).trigger("chosen:updated");
            $('.demandIdClass').prop('disabled', true).trigger("chosen:updated");
            $('.secondorderIdClass').prop('disabled', true).trigger("chosen:updated");
            $('#requirementId').prop('disabled', true).trigger("chosen:updated");
            $('#CBPprojectId').prop('disabled', true).trigger("chosen:updated");
            $('.nodesClass').prop('disabled', true).trigger("chosen:updated");
            $('#applyUsercontact').attr('readonly','readonly');
            $('#consumed').attr('readonly','readonly');
            $('#ROR').attr('readonly','readonly');
            $('.submitBtn').attr('disabled', 'disabled');
            $('.saveBtn').attr('disabled', 'disabled');
        }
    }
    //产品等级模块不能编辑
    function productenrollModuleDisable(isDisable, isOnly){
        if(isDisable){
            $('#productenrollDesc').attr('readonly','readonly');
            $('#planProductName').attr('readonly','readonly');
            $('#dynacommEn').attr('readonly','readonly');
            $('#dynacommCn').attr('readonly','readonly');
            $('#versionNum').attr('readonly','readonly');
            $('#lastVersionNum').attr('readonly','readonly');
            $('#reasonFromJinke').attr('readonly','readonly');
            window.editor['introductionToFunctionsAndUses'].readonly(true);
            window.editor['remark'].readonly(true);
        }
        if(isOnly){
            $('#outwardDeliveryDesc').attr('readonly','readonly');
            $('#testingRequestId').prop('disabled', true).trigger("chosen:updated");
            $('#productEnrollId').prop('disabled', true).trigger("chosen:updated");
            $('#productLine').prop('disabled', true).trigger("chosen:updated");
            $('#app').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliveryProduct1').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliveryProduct2').prop('disabled', true).trigger("chosen:updated");
            $('#productInfoCode').attr('readonly','readonly');
            $('#implementationForm').prop('disabled', true).trigger("chosen:updated");
            $('#projectPlanId').prop('disabled', true).trigger("chosen:updated");
            $('.problemIdClass').prop('disabled', true).trigger("chosen:updated");
            $('.demandIdClass').prop('disabled', true).trigger("chosen:updated");
            $('.secondorderIdClass').prop('disabled', true).trigger("chosen:updated");
            $('#requirementId').prop('disabled', true).trigger("chosen:updated");
            $('#CBPprojectId').prop('disabled', true).trigger("chosen:updated");
            $('.nodesClass').prop('disabled', true).trigger("chosen:updated");
            $('#applyUsercontact').attr('readonly','readonly');
            $('#consumed').attr('readonly','readonly');
            $('#ROR').attr('readonly','readonly');
            $('.submitBtn').attr('disabled', 'disabled');
            $('.saveBtn').attr('disabled', 'disabled');
        }

    }
    //生产变更模块不能编辑
    function modifycnccModuleDisable(isDisable){
        if(isDisable){
            $('#testTarget').attr('readonly','readonly');
            $('#currentStage').attr('readonly','readonly');
            $('#os').attr('readonly','readonly');
            $('#db').attr('readonly','readonly');
            window.editor['content'].readonly(true);
            window.editor['env'].readonly(true);

            $('#productenrollDesc').attr('readonly','readonly');
            $('#planProductName').attr('readonly','readonly');
            $('#dynacommEn').attr('readonly','readonly');
            $('#dynacommCn').attr('readonly','readonly');
            $('#versionNum').attr('readonly','readonly');
            $('#lastVersionNum').attr('readonly','readonly');
            $('#reasonFromJinke').attr('readonly','readonly');
            window.editor['introductionToFunctionsAndUses'].readonly(true);
            window.editor['remark'].readonly(true);

            $('#outwardDeliveryDesc').attr('readonly','readonly');
            $('#testingRequestId').prop('disabled', true).trigger("chosen:updated");
            $('#productEnrollId').prop('disabled', true).trigger("chosen:updated");
            $('#productLine').prop('disabled', true).trigger("chosen:updated");
            $('#app').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliveryProduct1').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliveryProduct2').prop('disabled', true).trigger("chosen:updated");
            $('#productInfoCode').attr('readonly','readonly');
            $('#implementationForm').prop('disabled', true).trigger("chosen:updated");
            $('#projectPlanId').prop('disabled', true).trigger("chosen:updated");
            $('.problemIdClass').prop('disabled', true).trigger("chosen:updated");
            $('.demandIdClass').prop('disabled', true).trigger("chosen:updated");
            $('.secondorderIdClass').prop('disabled', true).trigger("chosen:updated");
            $('#requirementId').prop('disabled', true).trigger("chosen:updated");
            $('#CBPprojectId').prop('disabled', true).trigger("chosen:updated");
            $('.nodesClass').prop('disabled', true).trigger("chosen:updated");
            $('#applyUsercontact').attr('readonly','readonly');
            $('#consumed').attr('readonly','readonly');
            $('#ROR').attr('readonly','readonly');
            $('.submitBtn').attr('disabled', 'disabled');
            $('.saveBtn').attr('disabled', 'disabled');

            $('#desc').attr('readonly','readonly');
            window.editor['target'].readonly(true);
            window.editor['reason'].readonly(true);
            window.editor['changeContentAndMethod'].readonly(true);
            window.editor['step'].readonly(true);
            window.editor['techniqueCheck'].readonly(true);
            window.editor['test'].readonly(true);
            window.editor['checkList'].readonly(true);
            window.editor['businessCooperateContent'].readonly(true);
            window.editor['judgePlan'].readonly(true);
            window.editor['risk'].readonly(true);
            $('#controlTableFile').attr('readonly','readonly');
            $('#controlTableSteps').attr('readonly','readonly');
            window.editor['effect'].readonly(true);
            window.editor['businessFunctionAffect'].readonly(true);
            window.editor['backupDataCenterChangeSyncDesc'].readonly(true);
            window.editor['emergencyManageAffect'].readonly(true);
            window.editor['businessAffect'].readonly(true);
            window.editor['businessAffect'].readonly(true);
            $('#verificationResults').attr('readonly','readonly');

            $('#isPlan').prop('disabled', true).trigger("chosen:updated");
            $('#checkDepartment').prop('disabled', true).trigger("chosen:updated");
            $('#result').prop('disabled', true).trigger("chosen:updated");
            $('#installationNode').prop('disabled', true).trigger("chosen:updated");
            $('#softwareProductPatch').prop('disabled', true).trigger("chosen:updated");
            $('#softwareCopyrightRegistration').prop('disabled', true).trigger("chosen:updated");
            $('#planDistributionTime').prop('disabled', true).trigger("chosen:updated");
            $('#planUpTime').prop('disabled', true).trigger("chosen:updated");
            $('#platform').prop('disabled', true).trigger("chosen:updated");
            $('#platform').prop('disabled', true).trigger("chosen:updated");
        }
    }
    //测试申请模块展示方法
    function testingrequestModuleShow(isShow){
        if(isShow){
            $('.outwarddeliveryTestingrequest').removeClass('hidden');
        }else{
            $('.outwarddeliveryTestingrequest').addClass('hidden');
        }
    }

    //产品等级模块展示方法
    function productenrollModuleShow(isShow){
        if(isShow){
            $('.outwarddeliveryProductenroll').removeClass('hidden');
        }else{
            $('.outwarddeliveryProductenroll').addClass('hidden');
        }
    }



    //关联测试单下拉框可点击方法
    function relatedTestingrequestUsable(isUsable){
        if(isUsable){
            $('.outwarddelivertRelatedTestingRequest').prop('disabled', false).trigger("chosen:updated");
            $('#testingRequestId_chosen').find('.chosen-single').removeAttr('style','background-color:#f5f5f5');
        }else{
            $('.outwarddelivertRelatedTestingRequest').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddelivertRelatedTestingRequest').val('').trigger("chosen:updated");
            $('#testingRequestId_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
        }
    }

    //关联产品登记单下拉框可点击方法
    function relatedProductenrollUsable(isUsable){
        if(isUsable){
            $('.outwarddeliverRelatedProductenroll').prop('disabled', false).trigger("chosen:updated");
            $('#productEnrollId_chosen').find('.chosen-single').removeAttr('style','background-color:#f5f5f5');
        }else{
            $('.outwarddeliverRelatedProductenroll').prop('disabled', true).trigger("chosen:updated");
            $('.outwarddeliverRelatedProductenroll').val('').trigger("chosen:updated");
            $('#productEnrollId_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
        }
        // $("[name='checkList']").val('')
    }

    //产品名称下拉框是否多选
    function productMultiple(isMultiple){
        isMul = isMultiple;
        if(isMultiple){
            $('.productTd1').addClass('hidden');
            $('.outwarddeliveryProduct1').val('').trigger("chosen:updated");
            $('.productTd2').removeClass('hidden');
            $('.outwarddeliveryProduct2').val(productIdOld).trigger("chosen:updated");
        }else{
            $('.productTd2').addClass('hidden');
            $('.outwarddeliveryProduct2').val('').trigger("chosen:updated");
            $('.productTd1').removeClass('hidden');
            $('.outwarddeliveryProduct1').val(productIdOld).trigger("chosen:updated");
        }
    }

    //所属系统下拉框是否多选
    function appMultiple(isMultiple){
        /*if(isMultiple){
            $('.outwarddeliveryApp').chosen('destroy').attr('multiple','multiple').chosen();
        }else{
            $('.outwarddeliveryApp').chosen('destroy').removeAttr('multiple').chosen();
        }*/
    }

    //评审人员动态展示
    function reviwerNodeShow(level){
        switch(level){
            case 1:
                $('.nodes').removeClass('hidden');
                $('.node-item').removeClass('hidden');
                break;
            case 2:
                $('.nodes').removeClass('hidden');
                $('.node-item').removeClass('hidden');
                $('.node6').addClass('hidden');
                break;
            case 3:
                $('.nodes').removeClass('hidden');
                $('.node-item').removeClass('hidden');
                $('.node5').addClass('hidden');
                $('.node6').addClass('hidden');
                break;
            //测试申请
            case 98:
                $('.nodes').removeClass('hidden');
                $('.node-item').removeClass('hidden');
                $('.node6').addClass('hidden');
                $('.node4').addClass('hidden');
                $('.node3').addClass('hidden');
                $('.node5').addClass('hidden');
                break;
            //产品登记
            case 99:
                $('.nodes').removeClass('hidden');
                $('.node-item').removeClass('hidden');
                $('.node6').addClass('hidden');
                $('.node5').addClass('hidden');
                $('.node3').addClass('hidden');
                break;
            default:
                $('.nodes').removeClass('hidden');
                $('.node-item').removeClass('hidden');
                break;
        }
        $('.node3').addClass('hidden');
    }

    //复选框-测试申请单事件
    function testingrequestChange(isTestingrequest){
        if(isTestingrequest == true){
            testingrequestModuleShow(true);
            relatedTestingrequestUsable(false);
            relatedProductenrollUsable(false);
            if(!$('.productenrollCheckbox').prop('checked') && !$('.modifycnccCheckbox').prop('checked')){
                productMultiple(true);
                appMultiple(true);
                reviwerNodeShow(98);
            }
            $('#defect').removeClass('hidden');
        }else{
            if($('.modifycnccCheckbox').prop('checked') && !$('.productenrollCheckbox').prop('checked'))
            {
                $('#defect').addClass('hidden');
            }
            testingrequestModuleShow(false);
            relatedTestingrequestUsable(true);
            if(!$('.productenrollCheckbox').prop('checked')){
                relatedProductenrollUsable(true);
            }
            productMultiple(false);
            appMultiple(false);
        }
        if(!$('.productenrollCheckbox').prop('checked') && $('.modifycnccCheckbox').prop('checked')&& !$('.testingrequestCheckbox').prop('checked')){
            $('.productTd1').removeClass('required');
            $('.code1').removeClass('required');
        }else{
            $('.productTd1').addClass('required');
            $('.code1').addClass('required');
        }
    }

    //复选框-产品等级单事件
    function productenrollChange(isProductenroll){
        if(isProductenroll == true){
            productenrollModuleShow(true);
            relatedProductenrollUsable(false);
            productMultiple(false);
            appMultiple(false);
            if(!$('.modifycnccCheckbox').prop('checked')){
                reviwerNodeShow(99);
            }
            $('#defect').removeClass('hidden');
        }else{
            if($('.modifycnccCheckbox').prop('checked') && !$('.testingrequestCheckbox').prop('checked'))
            {
                $('#defect').addClass('hidden');
            }
            productenrollModuleShow(false);
            if(!$('.testingrequestCheckbox').prop('checked')){
                relatedProductenrollUsable(true);
            }
            if(!$('.productenrollCheckbox').prop('checked') && !$('.modifycnccCheckbox').prop('checked') && $('.testingrequestCheckbox').prop('checked')){
                productMultiple(true);
                appMultiple(true);
                reviwerNodeShow(98);
            }
        }
        if(!$('.productenrollCheckbox').prop('checked') && $('.modifycnccCheckbox').prop('checked')&& !$('.testingrequestCheckbox').prop('checked')){
            $('.productTd1').removeClass('required');
            $('.code1').removeClass('required');
        }else{
            $('.productTd1').addClass('required');
            $('.code1').addClass('required');
        }
    }

    //复选框-生产变更单事件
    function modifycnccChange(isModifycncc){
        var modify = isModifycncc ? 1 : 0;
        $.get(createLink('outwarddelivery', 'ajaxDemandSelect', "isNewModifycncc=" + modify + '&outwarddeliveryId=' + outwarddeliveryId), function(data){
            $('#demandId').next().remove();
            $('#demandId').replaceWith(data);
            $('#demandId').chosen();
        });

        if(isModifycncc == true){
            isSelectAbnormalList(true)
            isSelectAbnormalList(true)
            modifycnccModuleShow(true);
            productMultiple(false);
            appMultiple(false);
            var level = $('#level').val();
            reviwerNodeShow(parseInt(level));
            if(!$('.productenrollCheckbox').prop('checked') && !$('.testingrequestCheckbox').prop('checked'))
            {
                $('#defect').addClass('hidden');
            }
            // $(".isMakeAmendsTr").removeClass('hidden');
        }else{
            isSelectAbnormalList(false)
            isSelectAbnormalList(false)
            modifycnccModuleShow(false);
            if(!$('.productenrollCheckbox').prop('checked') && !$('.modifycnccCheckbox').prop('checked') && $('.testingrequestCheckbox').prop('checked')){
                productMultiple(true);
                appMultiple(true);
                reviwerNodeShow(98);
            }else if($('.productenrollCheckbox').prop('checked')){
                reviwerNodeShow(99);
            }
            $('#defect').removeClass('hidden');
            // $(".isMakeAmendsTr").addClass('hidden');
        }
        if(!$('.productenrollCheckbox').prop('checked') && $('.modifycnccCheckbox').prop('checked')&& !$('.testingrequestCheckbox').prop('checked')){
            $('.productTd1').removeClass('required');
            $('.code1').removeClass('required');
        }else{
            $('.productTd1').addClass('required');
            $('.code1').addClass('required');
        }
    }

    function selectProductenrollChange(productenrollId){
        $.get(createLink('outwarddelivery', 'ajaxGetProductenroll', "productenrollId=" + productenrollId), function(data){
            var obj = JSON.parse(data);
            $('.outwarddeliveryApp').val(obj.app).trigger("chosen:updated");
            $('#productLine').val(obj.productLine).trigger("chosen:updated");
            if(!$('.productenrollCheckbox').prop('checked') && !$('.modifycnccCheckbox').prop('checked') && $('.testingrequestCheckbox').prop('checked')){
                $('.outwarddeliveryProduct2').val(obj.productId).trigger("chosen:updated");
            }else{
                $('.outwarddeliveryProduct1').val(obj.productId).trigger("chosen:updated");
            }
            var productStr = '';
            if(!isEmpty(obj.productCode)){
                productStr = productStr+obj.productCode;
            }else{
                productStr = productStr;
            }
            if(!isEmpty(obj.productVerson)){
                productStr = productStr+'-'+obj.productVerson+'-for';
            }else{
                productStr = productStr+'-'+'V'+'-for';
            }
            if(isEmpty(obj.productOs) && isEmpty(obj.productArch)){
                productStr = productStr+'-';
            }else{
                if(!isEmpty(obj.productOs)){
                    productStr = productStr+'-'+obj.productOs;
                }else{
                    productStr = productStr+'-';
                }
                if(!isEmpty(obj.productArch)){
                    productStr = productStr+'-'+obj.productArch;
                }else{
                    productStr = productStr+'-';
                }
            }
            $('#productInfoCode').val(productStr);
            $('#projectPlanId_chosen').remove();
            $('#projectPlanId').replaceWith(obj.projects);
            $('#projectPlanId').chosen();
            $('#implementationForm').val(obj.implementationForm).trigger("chosen:updated");
            $('#projectPlanId').val(obj.projectPlanId).trigger("chosen:updated");
            var problemList = [];
            if (obj.problemId != ''){
                problemList = obj.problemId.split(",");
            }
            $('.problemIdClass').val(problemList).trigger("chosen:updated");
            var secondorderList = [];
            if (obj.secondorderId != '' && obj.secondorderId != null){
                secondorderList = obj.secondorderId.split(",");
            }
            $('.secondorderIdClass').val(secondorderList).trigger("chosen:updated");
            var requirementList = [];
            if (obj.requirementId != ''){
                requirementList = obj.requirementId.split(",");
            }
            $('.requirementClass').val(requirementList).trigger("chosen:updated");
            var demandIdList = []
            if (obj.demandId != ''){
                demandIdList = obj.demandId.split(",")
            }
            $('.demandIdClass').val(demandIdList).trigger("chosen:updated");
            var CBPprojectIdList = [];
            if (obj.CBPprojectId != ''){
                CBPprojectIdList = obj.CBPprojectId.split(",")
            }
            $('.CBPprojectIdClass').val(CBPprojectIdList).trigger("chosen:updated");
            selectabnormalCode()
        });
    }

    function selectTestingRequestChange(testRequestId){
        $.get(createLink('outwarddelivery', 'ajaxGetTestRequest', "testRequestId=" + testRequestId), function(data){
            var obj = JSON.parse(data);
            $('#projectPlanId_chosen').remove();
            $('#projectPlanId').replaceWith(obj.projects);
            $('#projectPlanId').chosen();
            $('#implementationForm').val(obj.implementationForm).trigger("chosen:updated");
            $('#projectPlanId').val(obj.projectPlanId).trigger("chosen:updated");
            $('.CBPprojectIdClass').val(obj.CBPprojectId.split(",")).trigger("chosen:updated");
        });
    }

    function selectProductId(productId){
        /*if(!isFirst){
            $.get(createLink('outwarddelivery', 'ajaxGetProduct', "productId=" + productId), function(data){
                var obj = JSON.parse(data);
                if(!isEmpty(obj)){
                    $('#dynacommEn').val(obj.code);
                    $('#dynacommCn').val(obj.name);
                    var productStr = '';
                    if(!isEmpty(obj.code)){
                        productStr = productStr+obj.code;
                    }else{
                        productStr = productStr;
                    }
                    productStr = productStr+'-'+'V'+'-for';
                    if(isEmpty(obj.os) && isEmpty(obj.arch)){
                        productStr = productStr+'-';
                    }else{
                        if(!isEmpty(obj.os)){
                            productStr = productStr+'-'+obj.os;
                        }else{
                            productStr = productStr+'-';
                        }
                        if(!isEmpty(obj.arch)){
                            productStr = productStr+'-'+obj.arch;
                        }else{
                            productStr = productStr+'-';
                        }
                    }
                    $('#productInfoCode').val(productStr);
                }else{
                    $('#productInfoCode').val('');
                }
            });
        }*/
        /*getDefectList();*/
    }

    //关联产品多选下拉框选择事件
    function selectProductMultId(){
        /*if(!isFirst){
            var productIds = $(".outwarddeliveryProduct2").val();
            //重新编辑顺序
            if(lastProductId != ''){
                for(var i = 0; i<productIds.length; i++){
                    if(lastProductId.indexOf(productIds[i]) < 0){
                        lastProductId.push(productIds[i]);
                    }
                }
                for(var j = lastProductId.length-1; j>=0; j--){
                    if(productIds.indexOf(lastProductId[j]) < 0){
                        lastProductId.splice(j, 1);
                    }
                }
            }else{
                lastProductId = productIds;
            }
            $.get(createLink('outwarddelivery', 'ajaxGetProductInfoCode', "productIdList=" + lastProductId), function(data){
                $('#productInfoCode').val(data);
            });
        }*/
        /*getDefectList();*/
    }

    function getDefectList(){
        var product = new Array();
        if(isMul){
            var items = new Array();
            items = $(".outwarddeliveryProduct2 option:selected");
            for(var i = 0;i<items.length;i++){
                product.push(items[i].value);
            }
        }else{
            product.push($('.outwarddeliveryProduct1').val());
        }
        var project = $('#projectPlanId').val();
        // 2023-07-04 根据系统获取缺陷
        // $.get(createLink('outwarddelivery', 'ajaxGetLeaveDefects', 'product=' + product + '&project=' + project), function(data)
        // {
        //     $('#leaveDefect_chosen').remove();
        //     $('#leaveDefect').replaceWith(data);
        //     $('#leaveDefect').chosen();
        // });
        // $.get(createLink('outwarddelivery', 'ajaxGetfixDefects', 'product=' + product + '&project=' + project), function(data)
        // {
        //     $('#fixDefect_chosen').remove();
        //     $('#fixDefect').replaceWith(data);
        //     $('#fixDefect').chosen();
        // });
        if(!isFirst){
            $.get(createLink('outwarddelivery', 'ajaxGetReview', 'project=' + project), function(data)
            {
                $('#reviewReport_chosen').remove();
                $('#reviewReport').replaceWith(data);
                $('#reviewReport').chosen();
                $('.reviewClass').addClass('hidden');
            });
        }
    };

    function isEmpty(obj)
    {
        if(typeof obj == "undefined" || obj == null || obj == "" || obj == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function addMediaLine(obj)
    {
        $(obj).parent().parent().after($('#lineMediaDemo').children(':first-child').clone())
        sortMedialine()
    }

    function deleteMediaLine(obj)
    {
        if($(obj).parent().parent().parent().children().length>1){
            $(obj).parent().parent().remove()
            sortMedialine()
        }
    }

    function sortMedialine()
    {
        $('#aidMedia').children('tr').each(function (index){
            $(this).children(':first-child').text(index+1)
        })
    }

    var iscpcc = false
    function addPartition(obj)
    {
        if(iscpcc){
            return
        }
        var originIndex = $(obj).attr('data-id');
        partitionIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionIndex, 'id':'addItem' + partitionIndex});

        $currentRow.find('#appmodify' + originIndex + '_chosen').remove();
        $currentRow.find('#appmodify' + originIndex).attr({'id':'appmodify' + partitionIndex,'name':'appmodify['+partitionIndex+']'});

        // $currentRow.find('#partition' + originIndex + '_chosen').remove();
        // $currentRow.find('#partition' + originIndex).attr({'id':'partition' + partitionIndex,'name':'partition['+partitionIndex+'][]'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#appmodify' + partitionIndex).attr('class','form-control chosen');
        $('#appmodify' + partitionIndex).chosen();
        $(obj).parent().parent().parent().next().children().children(".partitionContainer").empty();

        // $('#partition' + partitionIndex).attr('class','form-control chosen');
        // $('#partition' + partitionIndex).chosen();
        // $('#appmodify'+partitionIndex).change();
    }

    function delPartition(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($(".app-partitions").length > 1)
        {
            $currentRow.remove();
        }

    }

    function addRelate(obj)
    {
        var originIndex = $(obj).attr('data-id');
        relateIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addRelateItem' + originIndex).attr({'data-id': relateIndex, 'id':'addRelateItem' + relateIndex});

        $currentRow.find('.picker-has-value').remove();
        $currentRow.find('#relate' + originIndex + '_chosen').remove();
        $currentRow.find('#relate' + originIndex).attr({'id':'relate' + relateIndex});

        // $currentRow.find('#relateNum' + originIndex + '_chosen').remove();
        if (originIndex < 1){
            $currentRow.find('.picker').remove();
        }else{
            $currentRow.find('#relateNum' + originIndex + '_chosen').remove();
        }
        $currentRow.find('#relateNum' + originIndex).attr({'id':'relateNum' + relateIndex});
        // $currentRow.find('.picker').css('display','none');


        $(obj).parent().parent().parent().after($currentRow);

        $('#relate' + relateIndex).attr('class','form-control chosen');
        $('#relate' + relateIndex).chosen();

        $('#relateNum' + relateIndex).attr('class','form-control chosen');
        $('#relateNum' + relateIndex).chosen();
    }

    function delRelate(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($("select[name*='relate[]']").length > 1)
        {
            $currentRow.remove();
        }else if($("select[name*='relate[]']").length == 1){
            $("select[name*='relate[]']").val('').trigger("chosen:updated");
            $("select[name*='relateNum[]']").val('');
        }

    }
    function addLine(obj)
    {
        $(obj).parent().parent().after($('#lineDemo').children(':first-child').clone())
        sortline()
    }

    function deleteLine(obj)
    {
        if($(obj).parent().parent().parent().children().length>1){
            $(obj).parent().parent().remove()
            sortline()
        }
    }

    function sortline()
    {
        $('#aid').children('tr').each(function (index){
            $(this).children(':first-child').text(index+1)
        })
    }

    function selectFixType(obj){
        $.get(createLink('outwarddelivery', 'ajaxGetSecondLine', "fixType=" + obj), function(data){
            $('#projectPlanId_chosen').remove();
            $('#projectPlanId').replaceWith(data);
            $('#projectPlanId').chosen();
        });
    }

    function selectChangeSource(changeSource)
    {
        if(changeSource == 1)
        {
            $('.outwarddeliveryControltable').removeClass('hidden');
        }else
        {
            $('.outwarddeliveryControltable').addClass('hidden');
        }

    }

    function selectLevel(level)
    {
        $('.nodes').removeClass('hidden');
        $('.node-item').removeClass('hidden');
        if(level == 1)
        {
            $('.node-item').removeClass('hidden');
            $('.isReviewTr').removeClass('hidden');
            var isReview = $("#isReview option:selected").val();
            if (isReview == 1){
                $('.reviewReportClass').removeClass('hidden');
            }
        }
        else if(level == 2)
        {
            $('.node6').addClass('hidden');
        }
        else if(level == 3)
        {
            $('.node5').addClass('hidden');
            $('.node6').addClass('hidden');
        }
        if (level != 1){
            $('.isReviewTr').addClass('hidden');
            $('.reviewReportClass').addClass('hidden');
        }
        $('.node3').addClass('hidden');
    }

    var ccpcAppList = ''
    var appList = ''
    function getAppNode(iscpcc){
        $.get(createLink('modifycncc','ajaxGetApp', 'isCPCC=2'), function(data){
            var current = $(data)
            ccpcAppList = current
        })
        $.get(createLink('modifycncc','ajaxGetApp', ''), function(data){
            var current = $(data)
            appList = current
        })
    }

    getAppNode()

    function selectNode(val){
        return true
        var val = $('#node').val()
        var hasNPC = false
        $('#node option').each(function () {
            var title = $(this).attr('title')
            var value = $(this).attr('value')
            if(val&&val.some(function(item){
                    return item == value
                })&&title.indexOf('NPC')!==-1){
                hasNPC = true
            }
        })
        var id = $('.app-partitions-content .app-partitions:first-child select')[0].id
        // var id = $('.app-partitions-content .app-partitions:first-child select')[0].id
        if(!hasNPC && val){
            if(!iscpcc){
                $('.app-partitions-content .app-partitions select:even').each(function(){
                    var index = this.id.split('appmodify')[1]
                    var current = ccpcAppList.clone().attr({'id':'appmodify'+index,'name':'appmodify['+index+']'})
                    $('#appmodify'+index + '_chosen').remove();
                    $('#appmodify'+index).replaceWith(current);
                    $('#appmodify'+index).chosen();
                    selectApp('','appmodify'+index)
                })
                // $('#'+id).val('PMTSCCPC').trigger("chosen:updated");
                // $('#'+id).prop('disabled',true).trigger("chosen:updated");
                // $('#dataform').append(`<input type="text" id="hiddenApp" name="appmodify[${id.split('appmodify')[1]}]" value="PMTSCCPC" class="form-control hidden" autocomplete="off">`)
                // selectApp('PMTSCCPC',id)
                // $('.app-partitions-content .app-partitions:not(:first-child)').remove()
                iscpcc = true
            }
        }else{
            if(iscpcc){
                $('.app-partitions-content .app-partitions select:even').each(function(){
                    var index = this.id.split('appmodify')[1]
                    var current = appList.clone().attr({'id':'appmodify'+index,'name':'appmodify['+index+']'})
                    $('#appmodify'+index + '_chosen').remove();
                    $('#appmodify'+index).replaceWith(current);
                    $('#appmodify'+index).chosen();
                    selectApp('','appmodify'+index)
                })
                iscpcc = false
            }
            // $('#'+id).prop('disabled',false).trigger("chosen:updated");
            // $('#hiddenApp').remove()
        }
    }

    function selectApp(app,id)
    {
        var index = id.split('appmodify')[1]
        // $.get(createLink('modifycncc', 'ajaxGetPartitionByCodeNew', 'applicationcode=' + app.split('-').join('^')), function(data)
        $.post(createLink('modifycncc', 'ajaxGetPartitionByCodeNew'),{'applicationcode' : app.split('-').join('^')}, function(data)
        {
            var current = $("#"+id).parent().parent().siblings().children(".partitionContainer");
            $(current).empty().append(data)
            $(current).children(".checkbox-primary").each(function(){
                $(this).children("[name='partition[]']").attr({'name':'partition['+index+'][]'})
            })
            // var current = $(data)
            // current.attr({'id':'partition' + index,'name':'partition['+index+'][]'})
            // $('#partition' + index + '_chosen').remove();
            // $('#partition' + index).replaceWith(current)
            // $('#partition' + index).chosen()
        })
    }

    function selectIsBusinessCooperate(isBusinessCooperate)
    {
        if(isBusinessCooperate == 2)
        {
            $('.outwarddeliveryBusinessCooperate').removeClass('hidden');
        }else
        {
            $('.outwarddeliveryBusinessCooperate').addClass('hidden');
        }

    }

    function selectIsBusinessJudge(isBusinessJudge)
    {
        if(isBusinessJudge == 2)
        {
            $('.outwarddeliveryBusinessJudge').removeClass('hidden');
        }else
        {
            $('.outwarddeliveryBusinessJudge').addClass('hidden');
        }
    }

    function selectIsBusinessAffect(isBusinessAffect)
    {
        if(isBusinessAffect == 2)
        {
            $('.businessAffect').removeClass('hidden');
        }else
        {
            $('.businessAffect').addClass('hidden');
        }
    }

    function selectProperty(property)
    {
        if(property == '1')
        {
            $('.backspaceExpectedTime').removeClass('hidden');
        }else
        {
            $('.backspaceExpectedTime').addClass('hidden');
        }
    }

    function selectFirstNode(val){
        var val = $('#node').val()
        var hasNPC = false
        $('#node option').each(function () {
            var title = $(this).attr('title')
            var value = $(this).attr('value')
            if(val&&val.some(function(item){
                    return item == value
                })&&title.indexOf('NPC')!==-1){
                hasNPC = true
            }
        })
        if(!hasNPC && $('#appmodify0').val()=='PMTSCCPC'){
            //考虑到历史数据
            // $('#appmodify0').prop('disabled',true).trigger("chosen:updated");
            // $('#dataform').append(`<input type="text" id="hiddenApp" name="appmodify[0]" value="PMTSCCPC" class="form-control hidden" autocomplete="off">`)
        }
    }

    $(function() {
        $('#app0').change();
        if(isNewTestingRequest == 1){
            testingrequestChange(true);
        }
        if(isNewProductEnroll == 1){
            productenrollChange(true);
        }
        if(isNewModifycncc == 1){
            modifycnccChange(true);
            selectProperty(propertyOld);
            selectFirstNode(nodeOld);
            selectChangeSource(changeSourceOld);
            selectIsBusinessCooperate(isBusinessCooperateOld);
            selectIsBusinessJudge(isBusinessJudgeOld);
            selectIsBusinessAffect(isBusinessAffectOld);
            selectProperty(propertyOld);
            selectReview();
        }else{
            selectReviewReport('');
        }
        $('.outwarddeliveryApp').val(appOld).trigger("chosen:updated");
        testingrequestModuleDisable(testingrequestDisable,testingrequestIsOnly);
        productenrollModuleDisable(productenrollDisable, productenrollIsOnly);
        modifycnccModuleDisable(modifycnccDisable);



        $.get(createLink('outwarddelivery', 'ajaxGetSecondLine', "fixType=" + implementationForm), function(data){
            $('#projectPlanId_chosen').remove();
            $('#projectPlanId').replaceWith(data);
            $('#projectPlanId').chosen();
            $('#projectPlanId').val(projectPlanIdOld).trigger("chosen:updated");
        });

        // $('.submitBtn').click(function() {
        //     if($('.modifycnccCheckbox').prop('checked') && $('#type').val() == 2){
        //         var msg = "按照清总要求，非紧急变更，一级（重大）变更提前十个工作日，二级（较大）变更提前五个工作日，三级（一般）变更提前三个工作日，请确认【预计开始时间:"+$("#planBegin").val()+"】是否符合要求，点击“确定”则保存对外交付单，点击取消，则不保存继续修改";
        //         if(confirm(msg) == true){
        //             submitBtn();
        //         }else{
        //             return false;
        //         }
        //     }else{
        //         submitBtn();
        //     }
        // });

        isFirst = false;
        //selectProductId(productIdOldTemp);

        window.editor['effect'].edit.afterChange(function (){
            var limitNum = 200;  //设定限制字数
            window.editor['effect'].sync();
            var strValue = $("#effect").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['effect'].text();
                value = value.substring(0,limitNum);
                window.editor['effect'].text(value);
                window.editor['effect'].focus();
                window.editor['effect'].appendHtml('');
            }
        });

        window.editor['businessFunctionAffect'].edit.afterChange(function (){
            var limitNum = 200;  //设定限制字数
            window.editor['businessFunctionAffect'].sync();
            var strValue = $("#businessFunctionAffect").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['businessFunctionAffect'].text();
                value = value.substring(0,limitNum);
                window.editor['businessFunctionAffect'].text(value);
                window.editor['businessFunctionAffect'].focus();
                window.editor['businessFunctionAffect'].appendHtml('');
            }
        });

        window.editor['backupDataCenterChangeSyncDesc'].edit.afterChange(function (){
            var limitNum = 200;  //设定限制字数
            window.editor['backupDataCenterChangeSyncDesc'].sync();
            var strValue = $("#backupDataCenterChangeSyncDesc").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['backupDataCenterChangeSyncDesc'].text();
                value = value.substring(0,limitNum);
                window.editor['backupDataCenterChangeSyncDesc'].text(value);
                window.editor['backupDataCenterChangeSyncDesc'].focus();
                window.editor['backupDataCenterChangeSyncDesc'].appendHtml('');
            }
        });

        window.editor['emergencyManageAffect'].edit.afterChange(function (){
            var limitNum = 200;  //设定限制字数
            window.editor['emergencyManageAffect'].sync();
            var strValue = $("#emergencyManageAffect").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['emergencyManageAffect'].text();
                value = value.substring(0,limitNum);
                window.editor['emergencyManageAffect'].text(value);
                window.editor['emergencyManageAffect'].focus();
                window.editor['emergencyManageAffect'].appendHtml('');
            }
        });
    });
</script>
<?php include '../../common/view/footer.html.php';?>
