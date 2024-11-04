<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::import($jsRoot . 'misc/base64.js');?>
<?php
js::set('abnormalTips', $this->lang->modify->abnormalTips);
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
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->modify->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' novalidate>
            <!-- 交付类型  -->
            <table class="table table-form hidden">
                <tbody>
                    <tr>
                        <th class='w-140px'><?php echo $lang->modify->deliveryType;?></th>
                        <td><?php echo html::checkbox('isNewTestingRequest',["1" => $lang->modify->testingrequest],'',"class='testingrequestCheckbox' onclick='testingrequestChange(this.checked)'");?></td>
                        <td><?php echo html::checkbox('isNewProductEnroll', ["1" => $lang->modify->productenroll],'', "class='productenrollCheckbox' onclick='productenrollChange(this.checked)'");?></td>
                        <td><?php echo html::checkbox('isNewModifycncc', ["1" => $lang->modify->modifycncc],'',"class='modifycnccCheckbox' onclick='modifycnccChange(this.checked)'");?></td>
                    </tr>
                </tbody>
            </table>
            <!-- 基础信息  -->
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->baseinfo;?>
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
                            <!-- <tr>
                                <th class='w-110px'><?php echo $lang->modify->outwardDeliveryDesc;?></th>
                                <td colspan='3' class='required'><?php echo html::input('outwardDeliveryDesc', '', "class='form-control' maxlength='200' ");?></td>
                            </tr> -->
                            <tr>
                                <!--关联申请单 -->
                                <!-- <th class='w-100px'><?php echo $lang->modify->relatedTestingRequest;?></th>
                                <td ><?php echo html::select('testingRequestId', $testingrequestList, '', "class='form-control chosen outwarddelivertRelatedTestingRequest' onchange='selectTestingRequestChange(this.value)'");?></td> -->
                                <!--关联产品登记 -->
                                <!-- <th class='w-100px'><?php echo $lang->modify->relatedProductEnroll;?></th>
                                <td><?php echo html::select('productEnrollId', $productenrollList, '', "class='form-control chosen outwarddeliverRelatedProductenroll' onchange='selectProductenrollChange(this.value)'");?></td> -->
                            </tr>
                            <tr>
                                <!--产品线 -->
                                <!-- <th class='w-100px'><?php echo $lang->modify->productLine;?></th>
                                <td class='required'><?php echo html::select('productLine', $productlineList, '', "class='form-control chosen'");?></td> -->
                                <!--所属系统 -->
                                <th class='w-100px'><?php echo $lang->modify->app;?></th>
                                <td class='required' colspan='3'>
                                    <?php
                                        echo html::select('app[]', $apps, '', "id='app' class='form-control chosen outwarddeliveryApp' onchange='getProductName()' multiple");
                                    ?>
                                </td>
                            </tr>
                            <!-- tcbs系统新增字段 -->
                            <tr class="tcbstr hidden">
                                <!--材料是否评审 -->
                                <th><?php echo $lang->modify->materialIsReview;?></th>
                                <td class='required'><?php echo html::select('materialIsReview', $lang->modify->materialIsReviewList, '', "class='form-control chosen' onchange='selectIsReview2(this.value)'");?></td>
                                <!--评审人 -->
                                <th class='w-100px'><?php echo $lang->modify->materialReviewUser;?></th>
                                <td class='required'><?php echo html::select('materialReviewUser', $users, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr class="tcbstr hidden">
                                <th class='w-100px'><?php echo $lang->modify->materialReviewResult;?></i></th>
                                <td class='required' colspan='3'><?php echo html::input('materialReviewResult', '', "class='form-control'");?></td>
                            </tr>
                            <tr>
                                <!--产品名称 -->
                                <th class='w-100px'><?php echo $lang->modify->productName;?><i title="<?php echo $lang->modify->productNameHelp;?>" class="icon icon-help"></i></th>
                                <td class="productTd1" colspan='3'><?php echo html::select('productId[]', [], '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");?></td>
                                <td class="productTd2 hidden " colspan='3'><?php echo html::select('productId[]', [], '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");?></td>
                            </tr>
                            <tr>
                                <!--产品编号 -->
                                <!-- <th class='w-100px'><?php echo $lang->modify->productInfoCode;?></th>
                                <td class='' colspan='3'><?php echo html::input('productInfoCode', '', "class='form-control'");?></td> -->
                            </tr>
                            <tr>
                                <!--实现方式 -->
                                <th><?php echo $lang->modify->implementationForm;?></th>
                                <td class='required'><?php echo html::select('implementationForm', $lang->modify->implementationFormList, '', "class='form-control chosen' onchange='selectFixType(this.value)'");?></td>
                                <!--所属项目 -->
                                <th class='w-100px'><?php echo $lang->modify->projectPlanId;?><i title="<?php echo $lang->modify->projectHelp;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('projectPlanId', $projectList, '', "class='form-control chosen' onchange='getDefectList()'");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->secondorderId;?></th>
                                <td><?php echo html::select('secondorderId[]', $secondorderList, '', "class='form-control chosen' multiple");?></td>
                                <th class='w-100px'><?php echo $lang->modify->associaitonOrder;?><i title="<?php echo $lang->modify->abnormalHelp;?>" class="icon icon-help"></i></th>
                                <td><?php echo html::select('abnormalCode', $abnormalList, [], "class='form-control chosen '  onchange='selectabnormalCode()' ");?></td>
                            </tr>
                            <tr>
                                <!--关联问题 -->
                                <th class='w-100px'><?php echo $lang->modify->problemId;?></th>
                                <td><?php echo html::select('problemId[]', $problemList, '', "class='form-control chosen problemIdClass' multiple");?></td>
                                <!--关联需求 -->
                                <th class='w-100px'><?php echo $lang->modify->demandId;?></th>
                                <td><?php echo html::select('demandId[]', $demandList, '', "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple ");?></td>
                            </tr>
                            <tr class='hidden'>
                                <!--关联需求任务 -->
                                <th><?php echo $lang->modify->requirementId;?></th>
                                <td><?php echo html::select('requirementId[]', $requirementList, '', "class='form-control chosen requirementClass' multiple");?></td>
                                <!--所属CBP项目 -->
                                <th><?php echo $lang->modify->CBPprojectId;?></th>
                                <td><?php echo html::select('CBPprojectId', $cbpprojectList,'', "class='form-control chosen CBPprojectIdClass'");?></td>
                            </tr>
                            <tr>
                                <!--变更来源 -->
                                <th><?php echo $lang->modify->changeSource;?></th>
                                <td class="required"><?php echo html::select('changeSource',$lang->modify->changeSourceList, '', "class='form-control chosen' onchange='selectChangeSource(this.value)'");?></td>
                                <!--所属(外部)项目/任务 -->
                                <th class="outsidePlanIdTd hidden"><?php echo $lang->modify->outsidePlanId;?></th>
                                <td class="required outsidePlanIdTd hidden"><?php echo html::select('outsidePlanId', $outsideProjectList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--是否后补流程 -->
                                <th class='w-100px'><?php echo $lang->modify->isMakeAmends;?></th>
                                <td class="required"><?php echo html::select('isMakeAmends', $lang->modify->isMakeAmendsList, '', "class='form-control chosen' onchange='selectIsMakeAmends()'");?></td>
                                <!--实际交付时间 -->
                                <th class='w-100px'><?php echo $lang->modify->actualDeliveryTime;?></th>
                                <td><?php echo html::input('actualDeliveryTime', '', "class='form-control form-datetime'")?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!--生产变更  -->
            <div class="outwarddeliveryModifycncc hidden">
                <!-- 变更参数  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->params;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--变更级别 -->
                                <th class='w-100px'><?php echo $lang->modify->level ;?><i title="<?php echo $lang->modify->levelTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('level', $lang->modify->levelList, '', "class='form-control chosen' onchange='selectLevel(this.value)'");?></td>
                                <!--变更节点 -->
                                <th class='w-100px'><?php echo $lang->modify->node;?></th>
                                <td class='required'><?php echo html::select('node[]', $lang->modify->nodeList, '', "class='form-control chosen' multiple onchange='selectNode(this.value)'");?></td>
                            </tr>
                            <tr style="display: none">
                                <th class='w-100px'><?php echo $lang->modify->operationType;?></th>
                                <td><?php echo html::select('operationType',$lang->modify->operationTypeList, '1', "class='form-control chosen'");?></td>
                            </tr>
                            <tr class="app-partition hidden">
                                <th class='w-100px'><?php echo $lang->modify->app;?></th>
                                <td colspan="3" class="required">
                                    <div class="table-row app-partitions">
                                        <div class="table-col w-500px">
                                            <div class="input-group">
                                                <!--系统名称 -->
                                                <span class="input-group-addon"><?php echo $lang->modify->applicationName;?></span>
                                                <?php echo html::select('appmodify[0]', $apps, ' ', "id='appmodify0' data-index='0' class='form-control chosen' onchange='selectApp(this.value,this.id)'");?>
                                            </div>
                                        </div>
                                        <div class="table-col">
                                            <div class="input-group">
                                                <!--分区 -->
                                                <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modify->partitionName;?></span>
                                                <?php echo html::select('partition[0][]', [], ' ', "id='partition0' class='form-control chosen' multiple");?>
                                                <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- <tr class="app-only"> -->
                                <!--涉及业务系统 -->
                                <!-- <th><?php echo $lang->modify->app;?></th>
                                <td colspan="3" class="required">
                                    <?php echo html::select('app[]', $apps, '', "id='app' class='form-control chosen' multiple");?>
                                </td> -->
                            <!-- </tr> -->
                            <tr>
                                <!--变更类型 -->
                                <th class='w-100px'><?php echo $lang->modify->mode;?></th>
                                <td class="required"><?php echo html::select('mode', $lang->modify->modeList, '', "class='form-control chosen'");?></td>
                                <!--变更类型 -->
                                <th class='w-100px'><?php echo $lang->modify->classify;?></th>
                                <td class="required"><?php echo html::select('classify', $lang->modify->classifyList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--变更阶段 -->
                                <th><?php echo $lang->modify->changeStage;?></th>
                                <td class="required"><?php echo html::select('changeStage', $lang->modify->changeStageList, '3', "class='form-control chosen'");?></td>
                                <!--是否涉及数据库表结构变化 -->
                                <th class='w-100px'><?php echo $lang->modify->involveDatabase;?></th>
                                <td class="required"><?php echo html::select('involveDatabase', $lang->modify->materialIsReviewList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--实施方式 -->
                                <th><?php echo $lang->modify->implementModality;?></th>
                                <td class="required"><?php echo html::select('implementModality',$lang->modify->implementModalityList, '1', "class='form-control chosen'");?></td>
                                <!--变更紧急程度 -->
                                <th class='w-100px'><?php echo $lang->modify->type;?><i title="<?php echo $lang->modify->typeTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::select('type', $lang->modify->typeList, '2', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--是否需要业务配合 -->
                                <th><?php echo $lang->modify->isBusinessCooperate;?></th>
                                <td class="required"><?php echo html::select('isBusinessCooperate',$lang->modify->isBusinessCooperateList, '1', "class='form-control chosen' onchange='selectIsBusinessCooperate(this.value)'");?></td>
                                <!--是否需要业务验证 -->
                                <th><?php echo $lang->modify->isBusinessJudge;?></th>
                                <td class="required"><?php echo html::select('isBusinessJudge',$lang->modify->isBusinessJudgeList, '1', "class='form-control chosen' onchange='selectIsBusinessJudge(this.value)'");?></td>
                            </tr>
                            <tr>
                                <!--实施期间是否有业务影响 -->
                                <th><?php echo $lang->modify->isBusinessAffect;?></th>
                                <td class="required"><?php echo html::select('isBusinessAffect',$lang->modify->isBusinessAffectList, '1', "class='form-control chosen' onchange='selectIsBusinessAffect(this.value)'");?></td>
                                <!--是否临时变更 -->
                                <th class='w-100px'><?php echo $lang->modify->property;?></th>
                                <td class="required"><?php echo html::select('property', $lang->modify->propertyList, '2', "class='form-control chosen' onchange='selectProperty(this.value)'");?></td>
                            </tr>
                            <!-- 是否评审方案 -->
                            <tr class="isReviewTr hidden">
                                <th><?php echo $lang->modify->isReview;?></th>
                                <td class="required">
                                    <?php echo html::select('isReview',$lang->modify->isReviewList, '', "class='form-control chosen' onchange='selectIsReview()'");?>
                                </td>
                                
                                <th class="isReview-required hidden"><?php echo $lang->modify->reviewReport;?></th>
                                <td class="isReview-required hidden required">
                                    <?php echo html::select('reviewReport[]',array(), '', "class='form-control chosen' multiple");?>
                                    <!-- <?php common::printIcon('review', 'view', 'reviewId=', '', 'list', 'link', '', 'iframe reviewClass hidden', true,"data-width='90%' style='margin-left:10px'");?> -->
                                </td>
                            </tr>
                            <!-- <tr class="isReview-required hidden"> -->
                                <!-- <th><?php echo $lang->modify->isReviewPass;?></th>
                                <td class="required">
                                    <?php echo html::select('isReviewPass',$lang->modify->isReviewPassList, '', "class='form-control chosen'");?>
                                </td> -->
                            <!-- </tr> -->
                            <tr class="backspaceExpectedTime hidden">
                                <!--预计回退开始时间 -->
                                <th><?php echo $lang->modify->backspaceExpectedStartTime;?></th>
                                <td class="required"><?php echo html::input('backspaceExpectedStartTime', '', "class='form-control form-datetime'");?></td>
                                <!--预计回退结束时间 -->
                                <th><?php echo $lang->modify->backspaceExpectedEndTime;?></th>
                                <td class="required"><?php echo html::input('backspaceExpectedEndTime', '', "class='form-control form-datetime'");?></td>
                            </tr>
                            <tr>
                                <!--预计开始时间 -->
                                <th><?php echo $lang->modify->planBegin;?><i title="<?php echo $lang->modify->planBeginTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::input('planBegin', '', "class='form-control form-datetime'");?></td>
                                <!--预计结束时间 -->
                                <th><?php echo $lang->modify->planEnd;?></th>
                                <td class="required"><?php echo html::input('planEnd', '', "class='form-control form-datetime'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更内容  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->content;?>
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
                                <!--变更摘要 -->
                                <th class='w-100px'><?php echo $lang->modify->desc;?></th>
                                <td colspan="3" class="required"><?php echo html::input('desc', '', "class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--变更目标 -->
                                <th><?php echo $lang->modify->target;?></th>
                                <td class="required"><?php echo html::textarea('target', '', "rows='5' class='form-control' maxlength='2000'");?></td>
                                <!--变更原因 -->
                                <th><?php echo $lang->modify->reason;?></th>
                                <td class="required"><?php echo html::textarea('reason', '', "rows='5' class='form-control' maxlength='1000'");?></td>
                            </tr>
                            <tr>
                                <!--变更执行步骤 -->
                                <th><?php echo $lang->modify->step;?></th>
                                <td class="required"><?php echo html::textarea('step', '', "rows='5' class='form-control'");?></td>
                                <!--技术验证 -->
                                <th><?php echo $lang->modify->techniqueCheck;?></th>
                                <td class="required"><?php echo html::textarea('techniqueCheck', '', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            <tr>
                                <!--变更测试结果 -->
                                <!-- <th><?php echo $lang->modify->test;?></th>
                                <td class="required"><?php echo html::textarea('test', '', "rows='5' class='form-control'");?></td> -->
                            </tr>
                            <tr>
                                <!--变更内容和方法 -->
                                <th><?php echo $lang->modify->changeContentAndMethod;?></th>
                                <td class="required"><?php echo html::textarea('changeContentAndMethod', '', "rows='5' class='form-control'");?></td>
                                <!--上线材料清单 -->
                                <th><?php echo $lang->modify->checkList;?></th>
                                <td class="required"><?php echo html::textarea('checkList', '', "rows='5' class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 关联变更  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->relationChange;?>
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
                                <!--前置变更 -->
                                <th class='w-100px'><?php echo $lang->modify->preChange;?><i title="<?php echo $lang->modify->relateTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::textarea('preChange', '', "placeholder='无此类关联变更' rows='5' class='form-control' maxlength='2000'");?></td>
                                <!--后置变更 -->
                                <th class='w-100px'><?php echo $lang->modify->postChange;?><i title="<?php echo $lang->modify->relateTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::textarea('postChange', '', "placeholder='无此类关联变更' rows='5' class='form-control' maxlength='2000'");?></td>
                            </tr>
                            <tr>
                                <!--同步实施 -->
                                <th class='w-100px'><?php echo $lang->modify->synImplement;?><i title="<?php echo $lang->modify->relateTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::textarea('synImplement', '', "placeholder='无此类关联变更' rows='5' class='form-control' maxlength='2000'");?></td>
                                <!--试点变更 -->
                                <th class='w-100px'><?php echo $lang->modify->pilotChange;?><i title="<?php echo $lang->modify->relateTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::textarea('pilotChange', '', "placeholder='无此类关联变更' rows='5' class='form-control' maxlength='2000'");?></td>
                            </tr>
                            <tr>
                                <!--推广变更 -->
                                <th class='w-100px'><?php echo $lang->modify->promotionChange;?><i title="<?php echo $lang->modify->relateTip;?>" class="icon icon-help"></i></th>
                                <td colspan="3" class="required"><?php echo html::textarea('promotionChange', '', "placeholder='无此类关联变更'  rows='5' class='form-control' maxlength='2000'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 业务配合  -->
                <div class="panel outwarddeliveryBusinessCooperate hidden">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->BusinessCooperate;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--配合业务部门 -->
                                <th class="w-100px"><?php echo $lang->modify->cooperateDepNameList;?></th>
                                <td class="required"><?php echo html::select('cooperateDepNameList',$lang->modify->cooperateDepNameListList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--需要业务配合内容 -->
                                <th class="w-100px"><?php echo $lang->modify->businessCooperateContent;?></th>
                                <td class="required"><?php echo html::textarea('businessCooperateContent', '', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 业务验证  -->
                <div class="panel outwarddeliveryBusinessJudge hidden">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->BusinessJudge;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--验证部门 -->
                                <th class="w-100px"><?php echo $lang->modify->judgeDep;?></th>
                                <td class="required"><?php echo html::select('judgeDep',$lang->modify->judgeDepList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--验证方案 -->
                                <th><?php echo $lang->modify->judgePlan;?></th>
                                <td class="required"><?php echo html::textarea('judgePlan', '', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 项目控制表  -->
                <div class="panel outwarddeliveryControltable hidden">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->Controltable;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--所属项目控制表 -->
                                <th class="w-100px"><?php echo $lang->modify->controlTableFile;?></th>
                                <td><?php echo html::input('controlTableFile', '', "placeholder='请填写正常的名称， 否则推送金信失败，比如“2021年数据管理CBP项目上线控制表”' class='form-control' maxlength='100'");?></td>
                            </tr>
                            <tr>
                                <!--所属项目控制表步骤 -->
                                <th class="w-100px"><?php echo $lang->modify->controlTableSteps;?></th>
                                <td><?php echo html::textarea('controlTableSteps', '', "rows='5' placeholder='多个之间以英文逗号分隔，不支持中文，否则推送金信失败，比如“BJ101,BJ102,BJ103”' class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更关联  -->
                <!-- <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->Project;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->modify->changeRelation;?></th>
                                <td colspan="3">
                                    <div class="table-row">
                                        <div class="table-col w-400px">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?php echo $lang->modify->tableTitle->relate;?></span>
                                                <?php echo html::select('relate[]', $lang->modify->relateTypeList, '', "id='relate0' data-index='0' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class="table-col">
                                            <div class="input-group">
                                                <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modify->tableTitle->relateNum ;?></span>
                                                <?php echo html::select('relateNum[]', $modifycnccList, '', "id='relateNum0' class='form-control'");?>
                                                <a class="input-group-btn" href="javascript:void(0)" onclick="addRelate(this)" data-id='0' id='addRelateItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a class="input-group-btn" href="javascript:void(0)" onclick="delRelate(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div> -->
                <!-- 变更可行性分析  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->FeasibilityAnalysis;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--变更可行性分析 -->
                                <th class="w-100px"><?php echo $lang->modify->feasibilityAnalysis;?></th>
                                <td class="required"><?php echo html::select('feasibilityAnalysis[]',$lang->modify->feasibilityAnalysisList, '2', "class='form-control chosen' multiple");?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->modify->relatedTestingRequest;?></th>
                                <td ><?php echo html::select('testingRequestId', $testingrequestList, '', "class='form-control chosen outwarddelivertRelatedTestingRequest'");?></td>
                            </tr>
                            <tr>
                                <!--分析情况说明 -->
                                <th><?php echo $lang->modify->risk;?></th>
                                <td class="required"><?php echo html::textarea('risk', '', "rows='5' class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 风险分析与应急处置  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->RiskAnalysis;?>
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
                            <tr>
                                <td>1</td>
                                <td><?php echo html::textarea('riskAnalysis[]', '', "placeholder='没有则填无' class='form-control'");?></td>
                                <td><?php echo html::textarea('emergencyBackWay[]', '', "placeholder='没有则填无' class='form-control'");?></td>
                                <td>
                                    <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更影响  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->Effect;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--给生产系统带来的影响变化 -->
                                <th class="w-100px"><?php echo $lang->modify->effect;?></th>
                                <td class="required"><?php echo html::textarea('effect', '', "rows='5' class='form-control' maxlength='200'");?></td>
                                <!--给业务功能带来的影响 -->
                                <th><?php echo $lang->modify->businessFunctionAffect;?></th>
                                <td class="required"><?php echo html::textarea('businessFunctionAffect', '', "rows='5' class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--主备数据中心变更同步情况说明 -->
                                <th><?php echo $lang->modify->backupDataCenterChangeSyncDesc;?></th>
                                <td class="required"><?php echo html::textarea('backupDataCenterChangeSyncDesc', '', "rows='5' class='form-control' maxlength='200'");?></td>
                                <!--对应急处置策略的影响（对故障处置策略自动化切换等的影响） -->
                                <th><?php echo $lang->modify->emergencyManageAffect;?></th>
                                <td class="required"><?php echo html::textarea('emergencyManageAffect', '', "rows='5' class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr class="businessAffect hidden">
                                <!--实施期间业务影响 -->
                                <th><?php echo $lang->modify->businessAffect;?></th>
                                <td colspan="3" class="required"><?php echo html::textarea('businessAffect', '', "rows='5' class='form-control' maxlength='500'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 基准验证  -->
                <!-- <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->modify->subTitle->benchmarkVerification;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th class="w-100px"><?php echo $lang->modify->benchmarkVerificationType;?></th>
                                <td class="required"><?php echo html::select('benchmarkVerificationType',$lang->modify->benchmarkVerificationTypeList, '', "class='form-control chosen'");?></td>
          
                                <th class="w-100px"><?php echo $lang->modify->verificationResults;?></th>
                                <td><?php echo html::input('verificationResults', '', "class='form-control'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div> -->
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
                    <th class='w-160px'>
                        <!--评审人员 -->
                        <?php echo $lang->modify->reviewNodes;?>
                        <i title="<?php echo $lang->modify->reviewNodesTip;?>" class="icon icon-help"></i>
                    </th>
                    <td colspan="2">
                        <?php
                        foreach($lang->modify->reviewerList as $key => $nodeName):
                            if (! in_array($key, $lang->modify->skipNodes)):
                                $currentAccounts = '';
                                if(in_array($key, $defChosenReviewNodes) && isset($reviewerAccounts[$key])):
                                    $currentAccounts = implode(',', $reviewerAccounts[$key]);
                                endif;
                        ?>
                            <div class='input-group node-item node<?php echo $key;?>' style='width:80%'>
                                <span class='input-group-addon'><?php echo $nodeName;?></span>
                                <?php  
                                if($key!=1){
                                    echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");
                                }else{
                                    echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' multiple");
                                }?>
                            </div>
                        <?php endif;?>
                        <?php endforeach;?>
                    </td>
                </tr>
                <tr>
                    <!--申请人联系方式 -->
                    <th class='w-120px'><?php echo $lang->modify->applyUsercontact;?></th>
                    <td class="required"><?php echo html::input('applyUsercontact', '', "class='form-control' maxlength='20'");?></td>
                    <!--工作量(小时) -->
                   <!-- <th class="w-100px"><?php /*echo $lang->modify->consumed;*/?></th>
                    <td class="required"><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>-->
                </tr>
                <tr>
                    <input type="hidden" name="issubmit" value="save">
                    <td class='form-actions text-center' colspan='4'><?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') . html::commonButton($lang->modify->submit, '', 'btn btn-wide btn-primary submitBtn') . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<input type="hidden" id="responseid">
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
        <td><?php echo html::textarea('mediaName[]', '', "placeholder='没有则填无' class='form-control'");?></td>
        <td><?php echo html::textarea('mediaBytes[]', '', "placeholder='请输入正整数' class='form-control' onkeyup='this.value=this.value.replace(/[^\d]/g, \"\")' onblur='this.value=this.value.replace(/[^\d]/g, \"\")'");?></td>
        <td>
            <a href="javascript:void(0)" onclick="addMediaLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="deleteMediaLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>
<script>
    //保存不需要校验数据
    $(".saveBtn").click(function () {
        $(this).attr('disabled', true);
        $("[name='issubmit']").val("save");
        submitData();
    })
    //提交需要校验数据
    $(".submitBtn").click(function () {
        $("[name='issubmit']").val("submit");
        if($('#type').val() == 2){
            var msg = "按照金信要求，非紧急变更，一级变更提前二十个工作日，二级变更提前五个工作日，三级变更提前三个工作日，请确认【预计开始时间:"+$("#planBegin").val()+"】是否符合要求，点击“确定”则保存生产变更单，点击取消，则不保存继续修改";
            if(confirm(msg) == true){
                submitData();
            }else{
                return false;
            }
        }else{
            submitData();
        }
    });
    //初始化按钮js
    $(function() {
      modifycnccChange(true);
        selectIsMakeAmends()
    //     $('#submit').click(function() {
    //         if($('#type').val() == 2){
    //             var msg = "按照金信要求，非紧急变更，一级（重大）变更提前二十个工作日，二级（较大）变更提前五个工作日，三级（一般）变更提前三个工作日，请确认【预计开始时间:"+$("#planBegin").val()+"】是否符合要求，点击“确定”则保存生产变更单，点击取消，则不保存继续修改";
    //             if(confirm(msg) == true){
    //                 return true;
    //             }else{
    //                 return false;
    //             }
    //         }
    //     });
    });
    function getDefectList(){
        var project = $('#projectPlanId').val();
        $.get(createLink('modify', 'ajaxGetReview', 'project=' + project), function(data)
        {
            $('#reviewReport_chosen').remove();
            $('#reviewReport').replaceWith(data);
            $('#reviewReport').chosen();
        });
        // selectReviewReport();
    };

    function selectReviewReport(reportId){
        // if(!isEmpty(reportId)){
        //     $('.reviewClass').removeClass('hidden');
        //     var href = $('.reviewClass').attr('href');
        //     var hrefList = href.split('-');
        //     hrefList[2] = reportId;
        //     str = hrefList.join('-')
        //     str += '.html?onlybody=yes'
        //     $('.reviewClass').attr('href', str);
        // }else{
        //     $('.reviewClass').addClass('hidden');
        // }
    }

    function selectIsReview(){
        var isReview = $('#isReview').val();
        console.log(isReview)
        if(isReview==2){
            $('.isReview-required').removeClass('hidden')
        }else{
            $('.isReview-required').addClass('hidden')
        }

    }

    //需求条目选择触发需求任务
    function selectDemand(){
        var demandIds = $(".demandIdClass").val();
        var requirementIds = $(".requirementClass").val();
        demandIds?$('#requirementId').parent().addClass('required'):$('#requirementId').parent().removeClass('required')
        $.get(createLink('outwarddelivery', 'ajaxGetOpinionByDemand', "demandIds=" + (demandIds?demandIds:'')), function(data){
                $('#requirementId').nextAll().remove();
                $('#requirementId').replaceWith(data)
                $('#requirementId').chosen()
                $('.requirementClass').val(requirementIds).trigger("chosen:updated");
        });
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

    //生产变更模块展示方法
    function modifycnccModuleShow(isShow){
        if(isShow){
            $('.outwarddeliveryModifycncc').removeClass('hidden');
        }else{
            $('.outwarddeliveryModifycncc').addClass('hidden');
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
    }

    //产品名称下拉框是否多选
    function productMultiple(isMultiple){
        console.log(isMultiple);
        if(isMultiple){
            $('.productTd1').addClass('hidden');
            $('.outwarddeliveryProduct1').val('').trigger("chosen:updated");
            $('.productTd2').removeClass('hidden');
        }else{
            $('.productTd2').addClass('hidden');
            $('.outwarddeliveryProduct2').val('').trigger("chosen:updated");
            $('.productTd1').removeClass('hidden');
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
        }else{
            testingrequestModuleShow(false);
            relatedTestingrequestUsable(true);
            if(!$('.productenrollCheckbox').prop('checked')){
                relatedProductenrollUsable(true);
            }
            productMultiple(false);
            appMultiple(false);
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
        }else{
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
    }

    //复选框-生产变更单事件
    function modifycnccChange(isModifycncc){
        if(isModifycncc == true){
            modifycnccModuleShow(true);
            productMultiple(true);
            appMultiple(false);
            var level = $('#level').val();
            reviwerNodeShow(level);
        }else{
            modifycnccModuleShow(false);
            if(!$('.productenrollCheckbox').prop('checked') && !$('.modifycnccCheckbox').prop('checked') && $('.testingrequestCheckbox').prop('checked')){
                productMultiple(false);
                appMultiple(true);
                reviwerNodeShow(98);
            }else if($('.productenrollCheckbox').prop('checked')){
                reviwerNodeShow(99);
            }
        }
    }

    //选择产品变更单下拉框
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
            problemList = obj.problemId.split(",");
            $('.problemIdClass').val(problemList).trigger("chosen:updated");
            var requirementList = [];
            requirementList = obj.requirementId.split(",");
            $('.requirementClass').val(requirementList).trigger("chosen:updated");
            $('.demandIdClass').val(obj.demandId.split(",")).trigger("chosen:updated");
            $('.CBPprojectIdClass').val(obj.CBPprojectId.split(",")).trigger("chosen:updated");
        });
    }

    //申请测试单下拉框选择事件
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

    //关联产品下拉框选择事件
    function selectProductId(productId){
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
    }

    var lastProductId = '';
    //关联产品多选下拉框选择事件
    function selectProductMultId(){
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
    }

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

    //增加媒体介质
    function addMediaLine(obj)
    {
        $(obj).parent().parent().after($('#lineMediaDemo').children(':first-child').clone())
        sortMedialine()
    }

    //删除媒体介质
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

    var partitionIndex = 0

    function addPartition(obj)
    {
        var originIndex = $(obj).attr('data-id');
        partitionIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionIndex, 'id':'addItem' + partitionIndex});

        $currentRow.find('#appmodify' + originIndex + '_chosen').remove();
        $currentRow.find('#appmodify' + originIndex).attr({'id':'appmodify' + partitionIndex,'name':'appmodify['+partitionIndex+']'});

        $currentRow.find('#partition' + originIndex + '_chosen').remove();
        $currentRow.find('#partition' + originIndex).attr({'id':'partition' + partitionIndex,'name':'partition['+partitionIndex+'][]'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#appmodify' + partitionIndex).attr('class','form-control chosen');
        $('#appmodify' + partitionIndex).chosen();

        $('#partition' + partitionIndex).attr('class','form-control chosen');
        $('#partition' + partitionIndex).chosen();
        $('#appmodify'+partitionIndex).change();
    }

    function delPartition(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($(".app-partitions").length > 1)
        {
            $currentRow.remove();
        }

    }

    var relateIndex = 0

    function addRelate(obj)
    {
        var originIndex = $(obj).attr('data-id');
        relateIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addRelateItem' + originIndex).attr({'data-id': relateIndex, 'id':'addRelateItem' + relateIndex});

        $currentRow.find('#relate' + originIndex + '_chosen').remove();
        $currentRow.find('#relate' + originIndex).attr({'id':'relate' + relateIndex});

        $currentRow.find('#relateNum' + originIndex + '_chosen').remove();
        $currentRow.find('#relateNum' + originIndex).attr({'id':'relateNum' + relateIndex});
        // $currentRow.find('.picker').css('display','none');


        $(obj).parent().parent().parent().after($currentRow);

        $('#relate' + relateIndex).attr('class','form-control chosen');
        $('#relate' + relateIndex).chosen();

        $('#relateNum' + relateIndex).attr('class','form-control');

        $('#relate' + relateIndex).change();
    }

    function delRelate(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($("select[name*='relate[]']").length > 1)
        {
            $currentRow.remove();
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
            $('.outsidePlanIdTd').removeClass('hidden');
        }else
        {
            $('.outwarddeliveryControltable').addClass('hidden');
            $('.outsidePlanIdTd').addClass('hidden');
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
            if (isReview == 2){
                $('.isReview-required').removeClass('hidden');
            }
        }
        else if(level == 2)
        {
            $('.node6').addClass('hidden');
            $('.isReviewTr').addClass('hidden');
            $('.isReview-required').addClass('hidden');
        }
        else if(level == 3)
        {
            $('.node5').addClass('hidden');
            $('.node6').addClass('hidden');
            $('.isReviewTr').addClass('hidden');
            $('.isReview-required').addClass('hidden');
        }
        $('.node3').addClass('hidden');
    }

    function selectNode(val){
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
        if(hasNPC){
            $('.app-only').addClass('hidden')
            $('.app-partition').removeClass('hidden')
            //$('#appmodify0').change();
        }else{
            $('.app-partition').addClass('hidden')
            $('.app-only').removeClass('hidden')
        }
    }

    function selectApp(app,id)
    {
        if(app){
            $.get(createLink('modifycncc', 'ajaxGetPartitionByCode', 'applicationcode=' + app), function(data)
            {
                var current = $(data)
                current.attr({'id':'partition' + index,'name':'partition['+index+'][]'})
                $('#partition' + index + '_chosen').remove();
                $('#partition' + index).replaceWith(current);
                $('#partition' + index).chosen();
            })
        }
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

    $(function() {
        $('#app0').change();
        // window.editor['effect'].edit.afterChange(function (){
        //     var limitNum = 200;  //设定限制字数
        //     window.editor['effect'].sync();
        //     var strValue = $("#effect").val();
        //     strValue = strValue.replace(/<[^>]+>/g,"");
        //     if(strValue.length > limitNum) {
        //         var value = window.editor['effect'].text();
        //         value = value.substring(0,limitNum);
        //         window.editor['effect'].text(value);
        //         window.editor['effect'].focus();
        //         window.editor['effect'].appendHtml('');
        //     }
        // });

        // window.editor['businessFunctionAffect'].edit.afterChange(function (){
        //     var limitNum = 200;  //设定限制字数
        //     window.editor['businessFunctionAffect'].sync();
        //     var strValue = $("#businessFunctionAffect").val();
        //     strValue = strValue.replace(/<[^>]+>/g,"");
        //     if(strValue.length > limitNum) {
        //         var value = window.editor['businessFunctionAffect'].text();
        //         value = value.substring(0,limitNum);
        //         window.editor['businessFunctionAffect'].text(value);
        //         window.editor['businessFunctionAffect'].focus();
        //         window.editor['businessFunctionAffect'].appendHtml('');
        //     }
        // });

        // window.editor['backupDataCenterChangeSyncDesc'].edit.afterChange(function (){
        //     var limitNum = 200;  //设定限制字数
        //     window.editor['backupDataCenterChangeSyncDesc'].sync();
        //     var strValue = $("#backupDataCenterChangeSyncDesc").val();
        //     strValue = strValue.replace(/<[^>]+>/g,"");
        //     if(strValue.length > limitNum) {
        //         var value = window.editor['backupDataCenterChangeSyncDesc'].text();
        //         value = value.substring(0,limitNum);
        //         window.editor['backupDataCenterChangeSyncDesc'].text(value);
        //         window.editor['backupDataCenterChangeSyncDesc'].focus();
        //         window.editor['backupDataCenterChangeSyncDesc'].appendHtml('');
        //     }
        // });

        // window.editor['emergencyManageAffect'].edit.afterChange(function (){
        //     var limitNum = 200;  //设定限制字数
        //     window.editor['emergencyManageAffect'].sync();
        //     var strValue = $("#emergencyManageAffect").val();
        //     strValue = strValue.replace(/<[^>]+>/g,"");
        //     if(strValue.length > limitNum) {
        //         var value = window.editor['emergencyManageAffect'].text();
        //         value = value.substring(0,limitNum);
        //         window.editor['emergencyManageAffect'].text(value);
        //         window.editor['emergencyManageAffect'].focus();
        //         window.editor['emergencyManageAffect'].appendHtml('');
        //     }
        // });
    });
</script>
<?php include '../../common/view/footer.html.php';?>
