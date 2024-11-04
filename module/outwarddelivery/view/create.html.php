<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('isMul', ''); ?>
<?php js::set('confirmMsg', $lang->outwarddelivery->choiceProjectMsg); ?>
<?php
js::set('abnormalTips', $this->lang->outwarddelivery->abnormalTips);
js::set('outwarddeliveryId', '');
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
            <h2><?php echo $lang->outwarddelivery->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <!-- 交付类型  -->
            <table class="table table-form">
                <tbody>
                    <tr>
                        <th class='w-140px'><?php echo $lang->outwarddelivery->deliveryType;?></th>
                        <td><?php echo html::checkbox('isNewTestingRequest',["1" => $lang->outwarddelivery->testingrequest],'',"class='testingrequestCheckbox' onclick='testingrequestChange(this.checked)'");?></td>
                        <td><?php echo html::checkbox('isNewProductEnroll', ["1" => $lang->outwarddelivery->productenroll],'', "class='productenrollCheckbox' onclick='productenrollChange(this.checked)'");?></td>
                        <td><?php echo html::checkbox('isNewModifycncc', ["1" => $lang->outwarddelivery->modifycncc],'',"class='modifycnccCheckbox' onclick='modifycnccChange(this.checked)'");?></td>
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
                                <th class='w-110px'><?php echo $lang->outwarddelivery->outwardDeliveryDesc;?></th>
                                <td colspan='3' class='required'><?php echo html::input('outwardDeliveryDesc', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--关联申请单 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->relatedTestingRequest;?></th>
                                <td ><?php echo html::select('testingRequestId', $testingrequestList, '', "class='form-control chosen outwarddelivertRelatedTestingRequest' onchange='selectTestingRequestChange(this.value)'");?></td>
                                <!--关联产品登记 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->relatedProductEnroll;?></th>
                                <td><?php echo html::select('productEnrollId', $productenrollList, '', "class='form-control chosen outwarddeliverRelatedProductenroll' onchange='selectProductenrollChange(this.value)'");?></td>
                            </tr>
                            <tr>
                                <!--产品线 -->
                                <!--<th class='w-100px'><?php /*echo $lang->outwarddelivery->productLine;*/?></th>
                                <td class='required'><?php /*echo html::select('productLine', $productlineList, '', "class='form-control chosen'");*/?></td>-->
                                <!--所属系统 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->app;?></th>
                                <td class='required'><?php echo html::select('app[]', $appList, '', "class='form-control chosen outwarddeliveryApp' onchange='getProductName(this.value)'");?></td>
                                <!--产品名称 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->productName;?><i title="<?php echo $lang->outwarddelivery->productNameHelp;?>" class="icon icon-help"></i></th>
                                <td class="productTd1 required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");?></td>
                                <td class="productTd2 hidden required"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");?></td>
                            </tr>
                            <tr>
                                <!--产品名称 -->
                                <!--<th class='w-100px'><?php /*echo $lang->outwarddelivery->productName;*/?></th>
                                <td class="productTd1 required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");*/?></td>
                                <td class="productTd2 hidden required"><?php /*echo html::select('productId[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");*/?></td>-->
                                <!--产品编号 -->
                                <!--<th class='w-100px'><?php /*echo $lang->outwarddelivery->productInfoCode;*/?></th>
                                <td class='code1 required'><?php /*echo html::input('productInfoCode', '', "class='form-control'");*/?></td>-->
                            </tr>
                            <tr>
                                <!--实现方式 -->
                                <th><?php echo $lang->outwarddelivery->implementationForm;?></th>
                                <td class='required'><?php echo html::select('implementationForm', $lang->outwarddelivery->implementationFormList, '', "class='form-control chosen' onchange='selectFixType(this.value)'");?></td>
                                <!--所属项目 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->projectPlanId;?><i title="<?php echo $lang->outwarddelivery->projectHelp;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('projectPlanId', $projectList, '', "class='form-control chosen' onchange='getDefectList()'");?></td>
                            </tr>

                            <tr>
                                <!--二线工单 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->secondorderId;?></th>
                                <td><?php echo html::select('secondorderId[]', $secondorderList, '', "class='form-control chosen secondorderIdClass' multiple");?></td>
                                <th class='w-100px abnormalCodeTr hidden'><?php echo $lang->outwarddelivery->associaitonOrder;?><i title="<?php echo $lang->outwarddelivery->abnormalHelp;?>" class="icon icon-help"></i></th>
                                <td class="abnormalCodeTr hidden"><?php echo html::select('abnormalCode', $abnormalList, [], "class='form-control chosen '  onchange='selectabnormalCode()' ");?></td>
                            </tr>
                            <tr>
                                <!--关联问题 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->problemId;?></th>
                                <td><?php echo html::select('problemId[]', $problemList, '', "class='form-control chosen problemIdClass' multiple");?></td>
                                <!--关联需求 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->demandId;?></th>
                                <td><?php echo html::select('demandId[]', $demandList, '', "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple");?></td>
                            </tr>
                            <tr>
                                <!--关联需求任务 -->
                                <th><?php echo $lang->outwarddelivery->requirementId;?></th>
                                <td><?php echo html::select('requirementId[]', $requirementList, '', "class='form-control chosen requirementClass' multiple");?></td>
                                <!--所属CBP项目 -->
                                <th><?php echo $lang->outwarddelivery->CBPprojectId;?></th>
                                <td><?php echo html::select('CBPprojectId', $cbpprojectList,'', "class='form-control chosen CBPprojectIdClass'");?></td>
                            </tr>
                            <tr id="defect">
                                <!--遗留缺陷 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->leaveDefect;?></th>
                                <td class='leaveDefect'><?php echo html::select('leaveDefect[]', $leaveDefectList, '', "class='form-control chosen'multiple"); ?></td>
                                <!--修复缺陷 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->fixDefect;?></th>
                                <td class='fixDefect'><?php echo html::select('fixDefect[]', $fixDefectList, '', "class='form-control chosen'multiple"); ?></td>
                            </tr>
                            <tr class="isMakeAmendsTr hidden">
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
                                <td colspan='3' class='required'><?php echo html::input('testSummary', '', "class='form-control' maxlength='50' ");?></td>
                            </tr>
                            <tr>
                                <!--测试目标 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->testTarget;?></th>
                                <td colspan='3' class='required'><?php echo html::input('testTarget', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--是否为集中测试 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->isCentralizedTest;?></th>
                                <td colspan='3' class='required'><?php echo html::select('isCentralizedTest', $lang->testingrequest->isCentralizedTestList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--验收测试类型 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->acceptanceTestType;?></th>
                                <td class='required'><?php echo html::select('acceptanceTestType', $lang->testingrequest->acceptanceTestTypeList, '', "class='form-control chosen' maxlength='200' ");?></td>
                                <!--目前阶段 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->currentStage;?></th>
                                <td class='required'><?php echo html::input('currentStage', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--操作系统 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->os;?></th>
                                <td class='required'><?php echo html::input('os', '', "class='form-control' maxlength='200' ");?></td>
                                <!--数据库类型 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->db;?></th>
                                <td class='required'><?php echo html::input('db', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--测试内容 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->content;?></th>
                                <td class='required'><?php echo html::textarea('content', '', "class='form-control' rows='5'"); ?></td>
                                <!--环境综述 -->
                                <th class='w-100px'><?php echo $lang->testingrequest->env;?></th>
                                <td class='required'><?php echo html::textarea('env', '', "class='form-control' rows='5'"); ?></td>
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
                                <td colspan='3' class='required'><?php echo html::input('productenrollDesc', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--理由 -->
                                <th class='w-100px'><?php echo $lang->productenroll->reasonFromJinke;?></th>
                                <td colspan='3' class='required' ><?php echo html::input('reasonFromJinke', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--是否计划内 -->
                                <th class='w-100px'><?php echo $lang->productenroll->isPlan;?> <i title="<?php echo $lang->outwarddelivery->isPlanTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('isPlan', $lang->productenroll->isPlanList, 1, "class='form-control chosen'");?></td>
                                <!--计划产品名称 -->
                                <th class='w-100px'><?php echo $lang->productenroll->planProductName;?></th>
                                <td><?php echo html::input('planProductName', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--软件名称英文 -->
                                <th class='w-100px'><?php echo $lang->productenroll->dynacommEn;?><i title="<?php echo $lang->outwarddelivery->dynacommEnTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::input('dynacommEn', '', "class='form-control' maxlength='200' ");?></td>
                                <!--软件名称中文 -->
                                <th class='w-100px'><?php echo $lang->productenroll->dynacommCn;?><i title="<?php echo $lang->outwarddelivery->dynacommCnTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::input('dynacommCn', '', "class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--版本号 -->
                                <th class='w-100px'><?php echo $lang->productenroll->versionNum;?></th>
                                <td class='required'><?php echo html::input('versionNum', '', "placeholder='例如 V1.0.0.1' class='form-control' maxlength='200' ");?></td>
                                <!--上一版本号 -->
                                <th class='w-100px'><?php echo $lang->productenroll->lastVersionNum;?></th>
                                <td class='required'><?php echo html::input('lastVersionNum', '', "placeholder='例如V1.0.0.0，如没有则填写无' class='form-control' maxlength='200' ");?></td>
                            </tr>
                            <tr>
                                <!--检测单位 -->
                                <th class='w-100px'><?php echo $lang->productenroll->checkDepartment;?><i title="<?php echo $lang->outwarddelivery->checkDepartmentTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('checkDepartment', $lang->productenroll->checkDepartmentList, '', "class='form-control chosen'");?></td>
                                <!--测试结论 -->
                                <th class='w-100px'><?php echo $lang->productenroll->result;?></th>
                                <td class='required'><?php echo html::select('result', $lang->productenroll->resultList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--安装节点 -->
                                <th class='w-100px'><?php echo $lang->productenroll->installationNode;?></th>
                                <td class='required'><?php echo html::select('installationNode', $lang->productenroll->installNodeList, '', "class='form-control chosen'");?></td>
                                <!--软件产品补丁 -->
                                <th class='w-100px'><?php echo $lang->productenroll->softwareProductPatch;?><i title="<?php echo $lang->outwarddelivery->softwareProductPatchTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('softwareProductPatch', $lang->productenroll->softwareProductPatchList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--申请计算机软件著作权登记 -->
                                <th class='w-100px'><?php echo $lang->productenroll->softwareCopyrightRegistration;?><i title="<?php echo $lang->outwarddelivery->softwareCopyrightRegistrationTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::select('softwareCopyrightRegistration', $lang->productenroll->softwareCopyrightRegistrationList, '', "class='form-control chosen'");?></td>
                                <!--所属平台 -->
                                <th class='w-100px'><?php echo $lang->productenroll->platform;?></th>
                                <td class='required'><?php echo html::select('platform', $lang->productenroll->appList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--计划发布时间 -->
                                <th class='w-100px'><?php echo $lang->productenroll->planDistributionTime;?></th>
                                <td class='required'><?php echo html::input('planDistributionTime', '', "class='form-control form-datetime'"); ?></td>
                                <!--计划上线时间 -->
                                <th class='w-100px'><?php echo $lang->productenroll->planUpTime;?></th>
                                <td class='required'><?php echo html::input('planUpTime', '', "class='form-control form-datetime'"); ?></td>
                            </tr>
                            <tr>
                                <!--主要功能及用途简介 -->
                                <th class='w-100px'><?php echo $lang->productenroll->introductionToFunctionsAndUses;?><i title="<?php echo $lang->outwarddelivery->introductionToFunctionsAndUsesTip;?>" class="icon icon-help"></i></th>
                                <td class='required'><?php echo html::textarea('introductionToFunctionsAndUses', '', "class='form-control' rows='5'"); ?></td>
                                <!--备注 -->
                                <th class='w-100px'><?php echo $lang->productenroll->remark;?></th>
                                <td><?php echo html::textarea('remark', '', "class='form-control' rows='5'"); ?></td>
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
                                    <tr>
                                        <td>1</td>
                                        <td><?php echo html::textarea('mediaName[]', '', "placeholder='没有则填无' class='form-control'");?></td>
                                        <td><?php echo html::textarea('mediaBytes[]', '', "placeholder='请输入正整数' class='form-control' onkeyup='this.value=this.value.replace(/[^\d]/g, \"\")' onblur='this.value=this.value.replace(/[^\d]/g, \"\")'");?></td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="addMediaLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                            <a href="javascript:void(0)" onclick="deleteMediaLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                        </td>
                                    </tr>
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
                                <td class='required'><?php echo html::select('level', $lang->modifycncc->levelList, '', "class='form-control chosen' onchange='selectLevel(this.value)'");?></td>
                                <!--变更节点 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->node;?></th>
                                <td class='required'><?php echo html::select('node[]', $lang->modifycncc->nodeList, '', "class='form-control chosen' multiple onchange='selectNode(this.value)'");?></td>
                            </tr>
                            <tr style="display: none">
                                <th class='w-100px'><?php echo $lang->modifycncc->operationType;?></th>
                                <td><?php echo html::select('operationType',$lang->modifycncc->operationTypeList, '1', "class='form-control chosen'");?></td>
                            </tr>
                            <tr class="app-partition">
                                <th class='w-100px'><?php echo $lang->modifycncc->app;?></th>
                                <td colspan="3" class="required app-partitions-content">
<!--                                    table-row -->
                                    <div class="app-partitions">
                                        <div style="width: 45%;float:left">
                                            <div class="input-group">
                                                <!--系统名称 -->
                                                <span class="input-group-addon"><?php echo $lang->modifycncc->applicationName;?></span>
                                                <?php echo html::select('appmodify[0]', $apps, ' ', "id='appmodify0' data-index='0' class='form-control chosen' onchange='selectApp(this.value,this.id)'");?>
                                            </div>
                                        </div>
                                        <div style="width:55%;float:left">
                                            <div>
                                                <a style="margin-left: 5px" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a style="margin-left: 5px" class="" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                            </div>

                                            <div class="input-group" style="display: none">
                                                <!--分区 -->
                                                <span class="input-group-addon fix-border fix-padding">二级系统</span>
<!--                                                --><?php //echo html::select('partition[0][]', [], ' ', "id='partition0' class='form-control chosen' multiple");?>

                                                <div class="form-control partitionContainer">

                                                </div>
                                                <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                                <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                            </div>
                                        </div>
                                        <div style="clear:both">
                                            <div style="float: left;padding: 5px"><?php echo $lang->modifycncc->partitionName;?>：</div>
                                            <div class="partitionContainer"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="app-only hidden">
                                <!--涉及业务系统 -->
                                <th><?php echo $lang->modifycncc->app;?></th>
                                <td colspan="3" class="required">
                                    <?php echo html::select('appOnly[]', $apps, '', "id='appOnly' class='form-control chosen' multiple");?>
                                </td>
                            </tr>
                            <tr>
                                <!--变更类型 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->mode;?></th>
                                <td class="required"><?php echo html::select('mode', $lang->modifycncc->modeList, '', "class='form-control chosen'");?></td>
                                <!--变更类型 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->classify;?></th>
                                <td class="required"><?php echo html::select('classify', $lang->modifycncc->classifyList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--变更来源 -->
                                <th><?php echo $lang->modifycncc->changeSource;?></th>
                                <td class="required"><?php echo html::select('changeSource',$lang->modifycncc->changeSourceList, '', "class='form-control chosen' onchange='selectChangeSource(this.value)'");?></td>
                                <!--变更阶段 -->
                                <th><?php echo $lang->modifycncc->changeStage;?></th>
                                <td class="required"><?php echo html::select('changeStage', $lang->modifycncc->changeStageList, '', "class='form-control chosen' onchange='selectChangeStage(this.value)'");?></td>
                            </tr>
                            <tr>
                                <!--实施方式 -->
                                <th><?php echo $lang->modifycncc->implementModality;?><i title="<?php echo $lang->modifycncc->implementModalityTips;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::select('implementModality',$lang->modifycncc->implementModalityNewList, '', "class='form-control chosen'");?></td>
                                <!--变更紧急程度 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->type;?><i title="<?php echo $lang->outwarddelivery->typeTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::select('type', $lang->modifycncc->typeList, '2', "class='form-control chosen' onchange='selectChange()'");?></td>
                            </tr>
                            <tr>
                                <!--变更形式 -->
                                <th><?php echo $lang->modifycncc->changeForm;?><i title="<?php echo $lang->modifycncc->changeFormTips;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::select('changeForm',$lang->modifycncc->changeFormList, '', "class='form-control chosen'");?></td>
                                <!--自动化工具 -->
                                <th><?php echo $lang->modifycncc->automationTools;?></th>
                                <td class="required"><?php echo html::select('automationTools', $lang->modifycncc->automationToolsList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr class="hidden urgent">
                                <!--紧急来源 -->
                                <th><?php echo $lang->outwarddelivery->urgentSource;?></th>
                                <td class="required"><?php echo html::select('urgentSource',$lang->modifycncc->urgentSourceList, '', "class='form-control chosen'");?></td>
                                <!--紧急原因 -->
                                <th class='w-100px'><?php echo $lang->outwarddelivery->urgentReason;?><i title="<?php echo $lang->outwarddelivery->typeTip;?>" class="icon"></i></th>
                                <td class="required"><?php echo html::input('urgentReason', '', "class='form-control'");?></td>
                            </tr>
                            <tr class="aadsReasonTr hidden">
                                <th class='w-100px'><?php echo $lang->outwarddelivery->aadsReason;?></th>
                                <td class='required' colspan="3"><?php echo html::input('aadsReason', '', "class='form-control'"); ?></td>
                            </tr>
                            <tr>
                                <!--是否需要业务配合 -->
                                <th><?php echo $lang->modifycncc->isBusinessCooperate;?></th>
                                <td class="required"><?php echo html::select('isBusinessCooperate',$lang->modifycncc->isBusinessCooperateList, '1', "class='form-control chosen' onchange='selectIsBusinessCooperate(this.value)'");?></td>
                                <!--是否需要业务验证 -->
                                <th><?php echo $lang->modifycncc->isBusinessJudge;?></th>
                                <td class="required"><?php echo html::select('isBusinessJudge',$lang->modifycncc->isBusinessJudgeList, '1', "class='form-control chosen' onchange='selectIsBusinessJudge(this.value)'");?></td>
                            </tr>
                            <tr>
                                <!--实施期间是否有业务影响 -->
                                <th><?php echo $lang->modifycncc->isBusinessAffect;?></th>
                                <td class="required"><?php echo html::select('isBusinessAffect',$lang->modifycncc->isBusinessAffectList, '1', "class='form-control chosen' onchange='selectIsBusinessAffect(this.value)'");?></td>
                                <!--是否临时变更 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->property;?></th>
                                <td class="required"><?php echo html::select('property', $lang->modifycncc->propertyList, '2', "class='form-control chosen' onchange='selectProperty(this.value)'");?></td>
                            </tr>
                            <tr class="backspaceExpectedTime hidden">
                                <!--预计回退开始时间 -->
                                <th><?php echo $lang->modifycncc->backspaceExpectedStartTime;?></th>
                                <td class="required"><?php echo html::input('backspaceExpectedStartTime', '', "class='form-control form-datetime'");?></td>
                                <!--预计回退结束时间 -->
                                <th><?php echo $lang->modifycncc->backspaceExpectedEndTime;?></th>
                                <td class="required"><?php echo html::input('backspaceExpectedEndTime', '', "class='form-control form-datetime'");?></td>
                            </tr>
                            <tr  class="isReviewTr hidden">
                                <!--是否评审方案 -->
                                <th><?php echo $lang->modifycncc->isReview;?></th>
                                <td class="required"><?php echo html::select('isReview',$lang->modifycncc->isReviewList, '2', "class='form-control chosen' onchange='selectReview()' ");?></td>
                                <!--方案评审结果 -->
                                <!--<th class='w-100px reviewReportClass hidden'><?php /*echo $lang->modifycncc->isReviewPass;*/?></th>
                                <td class="required reviewReportClass hidden"><?php /*echo html::select('isReviewPass', $lang->modifycncc->isReviewPassList, '', "class='form-control chosen'");*/?></td>-->
                                <th class="reviewReportClass hidden"><?php echo $lang->modifycncc->reviewReport;?></th>
                                <td class="required reviewReportClass hidden"><?php echo html::select('reviewReport[]',array(), '', "class='form-control chosen' multiple");?></td>
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
                                <!--变更摘要 -->
                                <th class='w-100px'><?php echo $lang->modifycncc->desc;?></th>
                                <td colspan="3" class="required"><?php echo html::input('desc', '', "class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--预计开始时间 -->
                                <th><?php echo $lang->modifycncc->planBegin;?><i title="<?php echo $lang->outwarddelivery->planBeginTip;?>" class="icon icon-help"></i></th>
                                <td class="required"><?php echo html::input('planBegin', '', "class='form-control form-datetime'");?></td>
                                <!--预计结束时间 -->
                                <th><?php echo $lang->modifycncc->planEnd;?></th>
                                <td class="required"><?php echo html::input('planEnd', '', "class='form-control form-datetime'");?></td>
                            </tr>
                            <tr>
                                <!--变更目标 -->
                                <th><?php echo $lang->modifycncc->target;?></th>
                                <td class="required"><?php echo html::textarea('target', '', "rows='5' class='form-control' maxlength='2000'");?></td>
                                <!--变更原因 -->
                                <th><?php echo $lang->modifycncc->reason;?></th>
                                <td class="required"><?php echo html::textarea('reason', '', "rows='5' class='form-control' maxlength='1000'");?></td>
                            </tr>
                            <tr>
                                <!--变更内容和方法 -->
                                <th><?php echo $lang->modifycncc->changeContentAndMethod;?></th>
                                <td class="required"><?php echo html::textarea('changeContentAndMethod', '', "rows='5' class='form-control'");?></td>
                                <!--变更执行步骤 -->
                                <th><?php echo $lang->modifycncc->step;?></th>
                                <td class="required"><?php echo html::textarea('step', '', "rows='5' class='form-control'");?></td>
                            </tr>
                            <tr>
                                <!--技术验证 -->
                                <th><?php echo $lang->modifycncc->techniqueCheck;?></th>
                                <td class="required"><?php echo html::textarea('techniqueCheck', '', "rows='5' class='form-control' maxlength='500'");?></td>
                                <!--变更测试结果 -->
                                <th><?php echo $lang->modifycncc->test;?></th>
                                <td class="required"><?php echo html::textarea('test', '', "rows='5' class='form-control'");?></td>
                            </tr>
                            <tr>
                                <!--重要变更窗口外申请原因 -->
                                <th><?php echo $lang->modifycncc->applyReasonOutWindow;?></th>
                                <td><?php echo html::textarea('applyReasonOutWindow', '', "rows='5' class='form-control'");?></td>
                                <!--重保期变更必要性说明 -->
                                <th><?php echo $lang->modifycncc->keyGuaranteePeriodApplyReason;?></th>
                                <td><?php echo html::textarea('keyGuaranteePeriodApplyReason', '', "rows='5' class='form-control'");?></td>
                            </tr>
                            <tr>
                                <!--上线材料清单 -->
                                <th><?php echo $lang->modifycncc->checkList;?></th>
                                <td colspan="3" class="required">
                                    <?php echo html::textarea('checkList', '', "rows='5' class='form-control'");?>
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
                                <!--配合业务部门 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->cooperateDepNameList;?></th>
                                <td class="required"><?php echo html::select('cooperateDepNameList',$lang->modifycncc->cooperateDepNameListList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--需要业务配合内容 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->businessCooperateContent;?></th>
                                <td class="required"><?php echo html::textarea('businessCooperateContent', '', "rows='5' class='form-control' maxlength='500'");?></td>
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
                                <!--验证部门 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->judgeDep;?></th>
                                <td class="required"><?php echo html::select('judgeDep',$lang->modifycncc->judgeDepList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <!--验证方案 -->
                                <th><?php echo $lang->modifycncc->judgePlan;?></th>
                                <td class="required"><?php echo html::textarea('judgePlan', '', "rows='5' class='form-control' maxlength='500'");?></td>
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
                                <!--所属项目控制表 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->controlTableFile;?></th>
                                <td class="required"><?php echo html::input('controlTableFile', '', "placeholder='请填写正常的名称， 否则推送清总和金信失败，比如“2021年数据管理CBP项目上线控制表”' class='form-control' maxlength='100'");?></td>
                            </tr>
                            <tr>
                                <!--所属项目控制表步骤 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->controlTableSteps;?></th>
                                <td class="required"><?php echo html::textarea('controlTableSteps', '', "rows='5' placeholder='多个之间以英文逗号分隔，不支持中文，否则推送清总和金信失败，比如“BJ101,BJ102,BJ103”' class='form-control'");?></td>
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
                                <th class='w-100px'><?php echo $lang->modifycncc->changeRelation;?></th>
                                <td colspan="3">
                                    <div class="table-row changeRequired">
                                        <div class="table-col w-400px">
                                            <div class="input-group">
                                                <!--关系 -->
                                                <span class="input-group-addon"><?php echo $lang->modifycncc->tableTitle->relate;?></span>
                                                <?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, '', "id='relate0' data-index='0' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class="table-col">
                                            <div class="input-group">
                                                <!--关联单号 -->
                                                <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->tableTitle->relateNum ;?></span>
                                                <?php echo html::select('relateNum[]', $modifycnccList, '', "id='relateNum0' class='form-control chosen'");?>
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
                                <!--变更可行性分析 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->feasibilityAnalysis;?></th>
                                <td class="required"><?php echo html::select('feasibilityAnalysis[]',$lang->modifycncc->feasibilityAnalysisList, '', "class='form-control chosen' multiple");?></td>
                            </tr>
                            <tr>
                                <!--分析情况说明 -->
                                <th><?php echo $lang->modifycncc->risk;?></th>
                                <td class="required"><?php echo html::textarea('risk', '', "rows='5' class='form-control'");?></td>
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
                        <?php echo $lang->outwarddelivery->subTitle->Effect;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--给生产系统带来的影响变化 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->effect;?></th>
                                <td class="required"><?php echo html::textarea('effect', '', "rows='5' class='form-control' maxlength='200'");?></td>
                                <!--给业务功能带来的影响 -->
                                <th><?php echo $lang->modifycncc->businessFunctionAffect;?></th>
                                <td class="required"><?php echo html::textarea('businessFunctionAffect', '', "rows='5' class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--主备数据中心变更同步情况说明 -->
                                <th><?php echo $lang->modifycncc->backupDataCenterChangeSyncDesc;?></th>
                                <td class="required"><?php echo html::textarea('backupDataCenterChangeSyncDesc', '', "rows='5' class='form-control' maxlength='200'");?></td>
                                <!--对应急处置策略的影响（对故障处置策略自动化切换等的影响） -->
                                <th><?php echo $lang->modifycncc->emergencyManageAffect;?></th>
                                <td class="required"><?php echo html::textarea('emergencyManageAffect', '', "rows='5' class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--变更关联影响分析 -->
                                <th><?php echo $lang->modifycncc->changeImpactAnalysis;?><i title="<?php echo $lang->modifycncc->changeImpactAnalysisTips;?>" class="icon icon-help"></i></th>
                                <td colspan="3" class="required">
                                    <?php echo html::textarea('changeImpactAnalysis', '', "rows='5' placeholder='".$lang->modifycncc->changeImpactAnalysisTips."' class='form-control' maxlength='2000'");?>
                                    <div style="margin-top:3px;color:red"><?php echo $lang->modifycncc->changeImpactAnalysisTips?></div>
                                </td>
                            </tr>
                            <tr class="businessAffect hidden">
                                <!--实施期间业务影响 -->
                                <th><?php echo $lang->modifycncc->businessAffect;?></th>
                                <td colspan="3" class="required"><?php echo html::textarea('businessAffect', '', "rows='5' class='form-control' maxlength='500'");?></td>
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
                                <!--基准验证类型 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->benchmarkVerificationType;?></th>
                                <td class="required"><?php echo html::select('benchmarkVerificationType',$lang->modifycncc->benchmarkVerificationTypeList, '', "class='form-control chosen'");?></td>
                                <!--验证结果 -->
                                <th class="w-100px"><?php echo $lang->modifycncc->verificationResults;?></th>
                                <td><?php echo html::input('verificationResults', '', "class='form-control'");?></td>
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
                    <th class='w-160px'>
                        <!--评审人员 -->
                        <?php echo $lang->modifycncc->reviewNodes;?>
                        <i title="<?php echo $lang->modifycncc->reviewNodesTip;?>" class="icon icon-help"></i>
                    </th>
                    <td colspan="2">
                        <?php
                        foreach($lang->modifycncc->reviewerList as $key => $nodeName):
                            if (! in_array($key, $lang->outwarddelivery->skipNodes)):
                                $currentAccounts = '';
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
                    <!--申请人联系方式 -->
                    <th class='w-120px'><?php echo $lang->outwarddelivery->applyUsercontact;?></th>
                    <td class="required"><?php echo html::input('applyUsercontact', '', "placeholder='请填写手机号' class='form-control' maxlength='20'");?></td>
                    <!--工作量(小时) -->
                   <!-- <th class="w-100px"><?php /*echo $lang->outwarddelivery->consumed;*/?></th>
                    <td class="required"><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>-->
                </tr>
                <tr class="hidden manufacturerTr">
                    <!--产商支持人员 -->
                    <th class='w-120px'><?php echo $lang->outwarddelivery->manufacturer;?></th>
                    <td><?php echo html::input('manufacturer', '', "class='form-control'");?></td>
                    <!--产商支持人员联系方式 -->
                    <th class="w-100px"><?php echo $lang->outwarddelivery->manufacturerConnect;?></th>
                    <td><?php echo html::input('manufacturerConnect', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <input type="hidden" name="issubmit" value="save">
                    <td class='form-actions text-center' colspan='4'><?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') . html::commonButton($lang->outwarddelivery->submit, '', 'btn btn-wide btn-primary submitBtn') . html::backButton();?></td>
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
    $(function () {
        selectIsMakeAmends()
    })
    //保存不需要校验数据
    $(".saveBtn").click(function () {
        $(this).attr('disabled', true);
        $("[name='issubmit']").val("save");
        submitBtn();
    })
    //提交需要校验数据

    //初始化按钮js
    $(function() {
        $('.submitBtn').click(function() {
            $("[name='issubmit']").val("submit");
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
    });

    /*function selectReviewReport(reportId){
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
    }*/

    function selectReview(){
        var value = $('#isReview').val();
        if(value == 1){
            $('.reviewReportClass').removeClass('hidden');
        }else{
            $('.reviewReportClass').addClass('hidden');
        }
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
        $.get(createLink('outwarddelivery', 'ajaxGetReview', 'project=' + project), function(data)
        {
            $('#reviewReport_chosen').remove();
            $('#reviewReport').replaceWith(data);
            $('#reviewReport').chosen();
            $('.reviewClass').addClass('hidden');
        });
    };

    //需求条目选择触发需求任务
    function selectDemand(){
        var demandIds = $(".demandIdClass").val();
        var requirementIds = $(".requirementClass").val();
        if(demandIds == null || demandIds == '' || (demandIds.length == 1 && demandIds[0] == '')){
            $('#requirementId').parent().removeClass('required');
        }else{
            $('#requirementId').parent().addClass('required');
        }
        $.get(createLink('outwarddelivery', 'ajaxGetOpinionByDemand', "demandIds=" + (demandIds?demandIds:'')), function(data){
                $('#requirementId').nextAll().remove();
                $('#requirementId').replaceWith(data)
                $('#requirementId').chosen()
                // $('.requirementClass').val(requirementIds).trigger("chosen:updated");
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
        $("[name='checkList']").val('')
    }

    //产品名称下拉框是否多选
    function productMultiple(isMultiple){
        isMul = isMultiple;
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
        $.get(createLink('outwarddelivery', 'ajaxDemandSelect', "isNewModifycncc=" + modify), function(data){
            $('#demandId').next().remove();
            $('#demandId').replaceWith(data);
            $('#demandId').chosen();
        });

        if(isModifycncc == true){
            isSelectAbnormalList(true)
            modifycnccModuleShow(true);
            productMultiple(false);
            appMultiple(false);
            var level = $('#level').val();
            reviwerNodeShow(level);
            if(!$('.productenrollCheckbox').prop('checked') && !$('.testingrequestCheckbox').prop('checked'))
            {
                $('#defect').addClass('hidden');
            }
            // $(".isMakeAmendsTr").removeClass('hidden');
        }else{
            isSelectAbnormalList(false)
            modifycnccModuleShow(false);
            // $(".isMakeAmendsTr").addClass('hidden');
            if(!$('.productenrollCheckbox').prop('checked') && !$('.modifycnccCheckbox').prop('checked') && $('.testingrequestCheckbox').prop('checked')){
                productMultiple(true);
                appMultiple(true);
                reviwerNodeShow(98);
            }else if($('.productenrollCheckbox').prop('checked')){
                reviwerNodeShow(99);
            }
            $('#defect').removeClass('hidden');
        }
        if(!$('.productenrollCheckbox').prop('checked') && $('.modifycnccCheckbox').prop('checked')&& !$('.testingrequestCheckbox').prop('checked')){
            $('.productTd1').removeClass('required');
            $('.code1').removeClass('required');
        }else{
            $('.productTd1').addClass('required');
            $('.code1').addClass('required');
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
            getDefectList();
            selectabnormalCode()
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
            getDefectList();
        });
    }

    //关联产品下拉框选择事件
    function selectProductId(productId){
        /*$.get(createLink('outwarddelivery', 'ajaxGetProduct', "productId=" + productId), function(data){
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
        });*/
        /*getDefectList();*/
    }

    var lastProductId = '';
    //关联产品多选下拉框选择事件
    function selectProductMultId(){
        /*var productIds = $(".outwarddeliveryProduct2").val();
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
        });*/
        /*getDefectList();*/
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
    var iscpcc = false

    function addPartition(obj)
    {
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

    var relateIndex = 0

    function addRelate(obj)
    {
        var originIndex = $(obj).attr('data-id');
        relateIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addRelateItem' + originIndex).attr({'data-id': relateIndex, 'id':'addRelateItem' + relateIndex});

        $currentRow.find('#relate' + originIndex + '_chosen').remove();
        $currentRow.find('#relate' + originIndex).attr({'id':'relate' + relateIndex});

        // $currentRow.find('#relateNum' + originIndex + '_chosen').remove();
        if (originIndex < 1){
            $currentRow.find('.picker').remove();
        }else{
            $currentRow.find('#relateNum' + originIndex + '_chosen').remove();
        }
        $currentRow.find('#relateNum' + originIndex).attr({'id':'relateNum' + relateIndex});
        // $currentRow.find('#relateNum' + originIndex).val('').trigger("chosen:updated");
        // $currentRow.find('.picker').css('display','none');


        $(obj).parent().parent().parent().after($currentRow);

        $('#relate' + relateIndex).attr('class','form-control chosen');
        $('#relate' + relateIndex).chosen();

        $('#relateNum' + relateIndex).attr('class','form-control chosen');
        $('#relateNum' + relateIndex).chosen();

        $('#relate' + relateIndex).change();
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
            // $('#partition' + index).replaceWith(current);
            // $('#partition' + index).chosen();
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

    $(function() {
        $('#app0').change();
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
