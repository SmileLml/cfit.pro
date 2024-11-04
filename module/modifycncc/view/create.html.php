<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
js::set('codeIndex', 1);
?>
<style>
    .input-group-addon{min-width: 150px;} .input-group{margin-bottom: 6px;}
    .top-table{border-top: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .middle-table{border-left: 0px solid; border-right: 0px solid;}
    .tail-table{border-bottom: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
    .input-group-btn{padding: 4px;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->modifycncc->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                    <tr>
                        <th class='w-140px'><?php echo $lang->modifycncc->level;?></th>
                        <td><?php echo html::select('level', $lang->modifycncc->levelList, '', "class='form-control chosen' onchange='selectLevel(this.value)'");?></td>
                    </tr>
                    <tr class="nodes hidden">
                        <th class='w-140px'>
                          <?php echo $lang->modifycncc->reviewNodes;?>
                            <i title="<?php echo $lang->modifycncc->reviewNodesTip;?>" class="icon icon-help"></i>
                        </th>
                        <td>
                          <?php
                          foreach($lang->modifycncc->reviewerList as $key => $nodeName):
                            $currentAccounts = '';
                            if(in_array($key, $defChosenReviewNodes) && isset($reviewerAccounts[$key])):
                              $currentAccounts = implode(',', $reviewerAccounts[$key]);
                            endif;
                            ?>
                              <div class='input-group node-item node<?php echo $key;?>'>
                                  <span class='input-group-addon'><?php echo $nodeName;?></span>
                                <?php echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");?>
                              </div>
                          <?php endforeach;?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->params;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class='w-140px'><?php echo $lang->modifycncc->productRegistrationCode;?></th>
                            <td><?php echo html::input('productRegistrationCode', '', "class='form-control' placeholder='如无产品登记号，则填“无”'");?></td>
                            <th class='w-140px'><?php echo $lang->modifycncc->node;?></th>
                            <td><?php echo html::select('node[]', $lang->modifycncc->nodeList, '', "class='form-control chosen' multiple onchange='selectNode(this.value)'");?></td>
                        </tr>
                        <tr style="display: none">
                            <th class='w-140px'><?php echo $lang->modifycncc->operationType;?></th>
                            <td><?php echo html::select('operationType',$lang->modifycncc->operationTypeList, '1', "class='form-control chosen'");?></td>
                        </tr>
                        <tr class="app-partition hidden">
                            <th class='w-140px'><?php echo $lang->modifycncc->app;?></th>
                            <td colspan="3" class="required">
                                <div class="table-row app-partitions">
                                    <div class="table-col w-500px">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo $lang->modifycncc->applicationName;?></span>
                                            <?php echo html::select('app[0]', $apps, '', "id='app0' data-index='0' class='form-control chosen' onchange='selectApp(this.value,this.id)'");?>
                                        </div>
                                    </div>
                                    <div class="table-col">
                                        <div class="input-group">
                                            <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->partitionName;?></span>
                                            <?php echo html::select('partition[0][]', [], ' ', "id='partition0' class='form-control chosen' multiple");?>
                                            <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                            <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="app-only">
                            <th><?php echo $lang->modifycncc->app;?></th>
                            <td colspan="3" class="required">
                              <?php echo html::select('appOnly[]', $apps, '', "id='appOnly' class='form-control chosen' multiple");?>
                            </td>
                        </tr>
                        <tr>
                            <th class='w-140px'><?php echo $lang->modifycncc->mode;?></th>
                            <td><?php echo html::select('mode', $lang->modifycncc->modeList, '', "class='form-control chosen'");?></td>
                            <th class='w-140px'><?php echo $lang->modifycncc->classify;?></th>
                            <td><?php echo html::select('classify', $lang->modifycncc->classifyList, '', "class='form-control chosen'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->changeSource;?></th>
                            <td><?php echo html::select('changeSource',$lang->modifycncc->changeSourceList, '', "class='form-control chosen' onchange='selectChangeSource(this.value)'");?></td>
                            <th><?php echo $lang->modifycncc->changeStage;?></th>
                            <td><?php echo html::select('changeStage', $lang->modifycncc->changeStageList, '', "class='form-control chosen'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->implementModality;?></th>
                            <td><?php echo html::select('implementModality',$lang->modifycncc->implementModalityList, '', "class='form-control chosen'");?></td>
                            <th class='w-140px'><?php echo $lang->modifycncc->type;?></th>
                            <td><?php echo html::select('type', $lang->modifycncc->typeList, '2', "class='form-control chosen'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->isBusinessCooperate;?></th>
                            <td><?php echo html::select('isBusinessCooperate',$lang->modifycncc->isBusinessCooperateList, '1', "class='form-control chosen' onchange='selectIsBusinessCooperate(this.value)'");?></td>
                            <th><?php echo $lang->modifycncc->isBusinessJudge;?></th>
                            <td><?php echo html::select('isBusinessJudge',$lang->modifycncc->isBusinessJudgeList, '1', "class='form-control chosen' onchange='selectIsBusinessJudge(this.value)'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->isBusinessAffect;?></th>
                            <td><?php echo html::select('isBusinessAffect',$lang->modifycncc->isBusinessAffectList, '1', "class='form-control chosen' onchange='selectIsBusinessAffect(this.value)'");?></td>
                            <th class='w-140px'><?php echo $lang->modifycncc->property;?></th>
                            <td><?php echo html::select('property', $lang->modifycncc->propertyList, '2', "class='form-control chosen' onchange='selectProperty(this.value)'");?></td>
                        </tr>
                        <tr class="backspaceExpectedTime hidden">
                            <th><?php echo $lang->modifycncc->backspaceExpectedStartTime;?></th>
                            <td class="required"><?php echo html::input('backspaceExpectedStartTime', '', "class='form-control form-datetime'");?></td>
                            <th><?php echo $lang->modifycncc->backspaceExpectedEndTime;?></th>
                            <td class="required"><?php echo html::input('backspaceExpectedEndTime', '', "class='form-control form-datetime'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->fixType;?></th>
                            <td><?php echo html::select('fixType', $lang->modifycncc->fixTypeList, '', "class='form-control chosen' onchange='selectFixType(this.value)'");?></td>
                            <th><?php echo $lang->modifycncc->isAppend;?></th>
                            <td>
                                <div class='checkbox-primary'>
                                    <input id='isAppend' name='isAppend' value='2' type='checkbox' class='no-margin' />
                                    <label for='isAppend'><?php echo $lang->modifycncc->append;?></label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->content;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <!--                        调整表格格式，勿删-->
                        <tr>
                            <th class='w-140px' style="height: 0;padding:0"></th>
                            <td style="height: 0;padding:0"></td>
                            <th class='w-140px' style="height: 0;padding:0"></th>
                            <td style="height: 0;padding:0"></td>
                        </tr>
                        <tr>
                            <th class='w-140px'><?php echo $lang->modifycncc->desc;?></th>
                            <td colspan="3"><?php echo html::input('desc', '', "class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->planBegin;?></th>
                            <td ><?php echo html::input('planBegin', '', "class='form-control form-datetime'");?></td>
                            <th><?php echo $lang->modifycncc->planEnd;?></th>
                            <td ><?php echo html::input('planEnd', '', "class='form-control form-datetime'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->target;?></th>
                            <td colspan="3"><?php echo html::textarea('target', '', "placeholder=' ' class='form-control' style='height:100px'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->reason;?></th>
                            <td colspan="3"><?php echo html::textarea('reason', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->changeContentAndMethod;?></th>
                            <td colspan="3"><?php echo html::textarea('changeContentAndMethod', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->step;?></th>
                            <td colspan="3"><?php echo html::textarea('step', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->techniqueCheck;?></th>
                            <td colspan="3"><?php echo html::textarea('techniqueCheck', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->test;?></th>
                            <td colspan="3"><?php echo html::textarea('test', '', "placeholder=' ' class='form-control'");?></td>
                        </tr
                        <tr>
                            <th><?php echo $lang->modifycncc->checkList;?></th>
                            <td colspan="3"><?php echo html::textarea('checkList', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel modifycnccBusinessCooperate hidden">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->BusinessCooperate;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->cooperateDepNameList;?></th>
                            <td class="required"><?php echo html::select('cooperateDepNameList',$lang->modifycncc->cooperateDepNameListList, '', "class='form-control chosen'");?></td>
                        </tr>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->businessCooperateContent;?></th>
                            <td class="required"><?php echo html::textarea('businessCooperateContent', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel modifycnccBusinessJudge hidden">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->BusinessJudge;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->judgeDep;?></th>
                            <td class="required"><?php echo html::select('judgeDep',$lang->modifycncc->judgeDepList, '', "class='form-control chosen'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->judgePlan;?></th>
                            <td class="required"><?php echo html::textarea('judgePlan', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel changeSource hidden">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->Controltable;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->controlTableFile;?></th>
                            <td class="required"><?php echo html::input('controlTableFile', '', "placeholder='请填写正确的名称，比如“2021年数据管理CBP项目上线控制表”' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->controlTableSteps;?></th>
                            <td class="required"><?php echo html::input('controlTableSteps', '', "placeholder='多个之间以英文逗号分隔，比如“BJ101,BJ102,BJ102”' class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->Project;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class='w-140px'><?php echo $lang->modifycncc->project;?></th>
                            <td colspan='3'><?php echo html::select('project[]', $projects, '', "class='form-control chosen' multiple");?></td>
                        </tr>
                        <tr>
                            <th class='w-140px'><?php echo $lang->modifycncc->problem;?></th>
                            <td colspan='3'><?php echo html::select('problem[]', $problems, '', "class='form-control chosen' multiple data-id='0'");?></td>
                        </tr>
                        <tr>
                            <th class='w-140px'><?php echo $lang->modifycncc->demand;?></th>
                            <td colspan='3'><?php echo html::select('demand[]', $demands, '', "class='form-control chosen' multiple");?></td>
                        </tr>
                        <tr class='top-table code-list-1'>
                            <th><?php echo $lang->modifycncc->productCode;?></th>
                            <td class='required'><?php echo html::input('productCode[]', '', "class='form-control' readonly id='productCode1'");?></td>
                            <td class="c-actions">
                                <a href="javascript:void(0)" onclick="addProductItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
                                <a href="javascript:void(0)" onclick="delProductItem(this)" data-id='1' id='codeClose1' class="btn btn-link"><i class="icon-close"></i></a>
                            </td>
                        </tr>
                        <tr class='middle-table code-list-1'>
                            <th><?php echo $lang->modifycncc->assignProduct;?></th>
                            <td class='required'><?php echo html::select('assignProduct[]', $products, '', "class='form-control chosen' id='assignProduct1' data-id='1' onchange='setProductField(this)'");?></td>
                        </tr>
                        <tr class='middle-table code-list-1'>
                            <th><?php echo $lang->modifycncc->versionNumber;?></th>
                            <td class='required'><?php echo html::input('versionNumber[]', '', "class='form-control' oninput='setVersionField(this)' id='versionNumber1' data-id='1'");?></td>
                            <td><?php echo $lang->modifycncc->versionNumberTips;?></td>
                        </tr>
                        <tr class='middle-table code-list-1'>
                            <th><?php echo $lang->modifycncc->supportPlatform;?></th>
                            <td class='required'><?php echo html::input('supportPlatform[]', '', "class='form-control' oninput='setSupportField(this)' id='supportPlatform1' data-id='1'");?></td>
                            <td><?php echo $lang->modifycncc->supportPlatformTips;?></td>
                        </tr>
                        <tr class='tail-table code-list-1' id='codeFooter1'>
                            <th><?php echo $lang->modifycncc->hardwarePlatform;?></th>
                            <td><?php echo html::input('hardwarePlatform[]', '', "class='form-control' oninput='setHardwareField(this)' id='hardwarePlatform1' data-id='1'");?></td>
                            <td><?php echo $lang->modifycncc->hardwarePlatformTips;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->relatedDemandNum;?></th>
                            <td><?php echo html::select('relatedDemandNum[]', $requirement, '', "class='form-control chosen' multiple");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->CNCCprojectIdUnique;?></th>
                            <td><?php echo html::select('CNCCprojectIdUnique[]', $cbpproject,'', "class='form-control chosen' multiple");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->changeRelation;?></th>
                            <td colspan="3">
                            <div class="table-row">
                                <div class="table-col w-400px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->modifycncc->tableTitle->relate;?></span>
                                        <?php echo html::select('relate[]', $lang->modifycncc->relateTypeList, '', "id='relate0' data-index='0' class='form-control chosen'");?>
                                    </div>
                                </div>
                                <div class="table-col">
                                    <div class="input-group">
                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->modifycncc->tableTitle->relateNum ;?></span>
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
            </div>
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->FeasibilityAnalysis;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->feasibilityAnalysis;?></th>
                            <td><?php echo html::select('feasibilityAnalysis[]',$lang->modifycncc->feasibilityAnalysisList, '', "class='form-control chosen' multiple");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->risk;?></th>
                            <td><?php echo html::textarea('risk', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->RiskAnalysis;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form table-bordered">
                        <thead>
                        <tr>
                            <th class="w-40px">NO</th>
                            <th>风险分析</th>
                            <th>应急回退方式</th>
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
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->Effect;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->effect;?></th>
                            <td><?php echo html::textarea('effect', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->businessFunctionAffect;?></th>
                            <td><?php echo html::textarea('businessFunctionAffect', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->backupDataCenterChangeSyncDesc;?></th>
                            <td><?php echo html::textarea('backupDataCenterChangeSyncDesc', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->modifycncc->emergencyManageAffect;?></th>
                            <td><?php echo html::textarea('emergencyManageAffect', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        <tr class="businessAffect hidden">
                            <th><?php echo $lang->modifycncc->businessAffect;?></th>
                            <td class="required"><?php echo html::textarea('businessAffect', '', "placeholder=' ' class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                  <?php echo $lang->modifycncc->subTitle->benchmarkVerification;?>
                </div>
                <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th class="w-140px"><?php echo $lang->modifycncc->benchmarkVerificationType;?></th>
                            <td><?php echo html::select('benchmarkVerificationType',$lang->modifycncc->benchmarkVerificationTypeList, '', "class='form-control chosen'");?></td>
                            <th class="w-140px"><?php echo $lang->modifycncc->verificationResults;?></th>
                            <td><?php echo html::input('verificationResults', '', "class='form-control'");?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-140px'><?php echo $lang->modifycncc->applyUsercontact;?></th>
                    <td><?php echo html::input('applyUsercontact', '', "class='form-control'");?></td>
                    <th class="w-140px"><?php echo $lang->modifycncc->consumed;?></th>
                    <td><?php echo html::input('consumed', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='4'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<table class='hidden'>
    <tbody id='productCodeContent'>
    <tr class='top-table'>
        <th class="w-140px"><?php echo $lang->modifycncc->productCode;?></th>
        <td class='required'><?php echo html::input('productCode[]', '', "class='form-control' readonly id='productCode0'");?></td>
        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addProductItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delProductItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    <tr class='middle-table'>
        <th><?php echo $lang->modifycncc->assignProduct;?></th>
        <td class='required'><?php echo html::select('assignProduct[]', $products, '', "class='form-control' id='assignProduct0' data-id='0' onchange='setProductField(this)'");?></td>
    </tr>
    <tr class='middle-table'>
        <th><?php echo $lang->modifycncc->versionNumber;?></th>
        <td class='required'><?php echo html::input('versionNumber[]', '', "class='form-control' oninput='setVersionField(this)' id='versionNumber0' data-id='0'");?></td>
        <td><?php echo $lang->modifycncc->versionNumberTips;?></td>
    </tr>
    <tr class='middle-table'>
        <th><?php echo $lang->modifycncc->supportPlatform;?></th>
        <td class='required'><?php echo html::input('supportPlatform[]', '', "class='form-control' oninput='setSupportField(this)' id='supportPlatform0' data-id='0'");?></td>
        <td><?php echo $lang->modifycncc->supportPlatformTips;?></td>
    </tr>
    <tr class='tail-table' id='codeFooter0'>
        <th><?php echo $lang->modifycncc->hardwarePlatform;?></th>
        <td><?php echo html::input('hardwarePlatform[]', '', "class='form-control' oninput='setHardwareField(this)' id='hardwarePlatform0' data-id='0'");?></td>
        <td><?php echo $lang->modifycncc->hardwarePlatformTips;?></td>
    </tr>
    </tbody>
</table>
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
<script>
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
        }else{
            $('.app-partition').addClass('hidden')
            $('.app-only').removeClass('hidden')
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

    function selectLevel(level)
    {
        $('.nodes').removeClass('hidden');
        $('.node-item').removeClass('hidden');
        if(level == 1)
        {
            $('.node-item').removeClass('hidden');
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
    }

    function selectChangeSource(changeSource)
    {
        if(changeSource == 1)
        {
            $('.changeSource').removeClass('hidden');
        }else
        {
            $('.changeSource').addClass('hidden');
        }

    }

    function selectIsBusinessCooperate(isBusinessCooperate)
    {
        if(isBusinessCooperate == 2)
        {
            $('.modifycnccBusinessCooperate').removeClass('hidden');
        }else
        {
            $('.modifycnccBusinessCooperate').addClass('hidden');
        }

    }

    function selectIsBusinessJudge(isBusinessJudge)
    {
        if(isBusinessJudge == 2)
        {
            $('.modifycnccBusinessJudge').removeClass('hidden');
        }else
        {
            $('.modifycnccBusinessJudge').addClass('hidden');
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

    function selectFixType(obj){
        $.get(createLink('modifycncc', 'ajaxGetSecondLine', "fixType=" + obj), function(data){
            $('#project_chosen').remove();
            $('#project').replaceWith(data);
            $('#project').chosen();
        });
    }

    function setProductField(obj)
    {
        var $dataID = $(obj).attr('data-id');
        getCodeFields(obj, $dataID);
    }

    function setVersionField(obj)
    {
        var $dataID = $(obj).attr('data-id');
        getCodeFields(obj, $dataID);
    }

    function setSupportField(obj)
    {
        var $dataID = $(obj).attr('data-id');
        getCodeFields(obj, $dataID);
    }

    function setHardwareField(obj)
    {
        var $dataID = $(obj).attr('data-id');
        getCodeFields(obj, $dataID);
    }

    function getCodeFields(obj, $dataID)
    {
        var $productID  = $('#assignProduct' + $dataID).val();
        if(!isEmpty($productID))
        {
            $.get(createLink('product', 'ajaxGetProductCode', 'productID=' + $productID), function(data)
            {
                var $product = data;
                var $version  = $('#versionNumber' + $dataID).val();
                var $support  = $('#supportPlatform' + $dataID).val();
                var $hardware = $('#hardwarePlatform' + $dataID).val();
                if(!isEmpty($version))  $version  = '-' + $version + '-for';
                if(!isEmpty($support))  $support  = '-' + $support;
                if(!isEmpty($hardware)) $hardware = '-' + $hardware;

                var $fieldValue = $product + $version + $support + $hardware;
                $('#productCode' + $dataID).attr('value', $fieldValue);
            })
        }
        else
        {
            var $version  = $('#versionNumber' + $dataID).val();
            var $support  = $('#supportPlatform' + $dataID).val();
            var $hardware = $('#hardwarePlatform' + $dataID).val();
            if(!isEmpty($version))  $version  = '-' + $version + '-for';
            if(!isEmpty($support))  $support  = '-' + $support;
            if(!isEmpty($hardware)) $hardware = '-' + $hardware;

            var $fieldValue = $version + $support + $hardware;
            $('#productCode' + $dataID).attr('value', $fieldValue);
        }
    }

    function isEmpty(obj)
    {
        if(typeof obj == "undefined" || obj == null || obj == "")
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function addProductItem(obj)
    {
        var originHtml      = $('#productCodeContent');
        var productCodeHtml = originHtml.clone();
        codeIndex ++;

        productCodeHtml.find('#codePlus0').attr({'id':'codePlus' + codeIndex, 'data-id': codeIndex});
        productCodeHtml.find('#codeClose0').attr({'id':'codeClose' + codeIndex, 'data-id': codeIndex});

        productCodeHtml.find('#productCode0').attr({'id':'productCode' + codeIndex, 'data-id': codeIndex});
        productCodeHtml.find('#assignProduct0').attr({'id':'assignProduct' + codeIndex, 'data-id': codeIndex});
        productCodeHtml.find('#versionNumber0').attr({'id':'versionNumber' + codeIndex, 'data-id': codeIndex});
        productCodeHtml.find('#supportPlatform0').attr({'id':'supportPlatform' + codeIndex, 'data-id': codeIndex});
        productCodeHtml.find('#hardwarePlatform0').attr({'id':'hardwarePlatform' + codeIndex, 'data-id': codeIndex});
        productCodeHtml.find('#codeFooter0').attr({'id':'codeFooter' + codeIndex});


        productCodeHtml.find('.top-table').attr({'class':'top-table code-list-' + codeIndex});
        productCodeHtml.find('.middle-table').attr({'class':'middle-table code-list-' + codeIndex});
        productCodeHtml.find('.tail-table').attr({'class':'tail-table code-list-' + codeIndex});

        var objIndex = $(obj).attr('data-id');
        $('#codeFooter' + objIndex).after(productCodeHtml.html());

        $('#assignProduct' + codeIndex).attr('class','form-control chosen');
        $('#assignProduct' + codeIndex).chosen();
    }

    function delProductItem(obj)
    {
        var objIndex = $(obj).attr('data-id');
        $('.code-list-' + objIndex).remove();
    }

    function selectApp(app,id)
    {
        var index = id.split('app')[1]
        var title = $('#'+id).children('[value='+app+']')[0].title
        var code = title.split('_')[0]
        if(code){
            $.get(createLink('modifycncc', 'ajaxGetPartitionByCode', 'applicationcode=' + code), function(data)
            {
                var current = $(data)
                current.attr({'id':'partition' + index,'name':'partition['+index+'][]'})
                $('#partition' + index + '_chosen').remove();
                $('#partition' + index).replaceWith(current)
                $('#partition' + index).chosen()
            })
        }
    }

    $(function() {
        $('#app0').change()
    });


    var partitionIndex = 0

    function addPartition(obj)
    {
        var originIndex = $(obj).attr('data-id');
        partitionIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionIndex, 'id':'addItem' + partitionIndex});

        $currentRow.find('#app' + originIndex + '_chosen').remove();
        $currentRow.find('#app' + originIndex).attr({'id':'app' + partitionIndex,'name':'app['+partitionIndex+']'});

        $currentRow.find('#partition' + originIndex + '_chosen').remove();
        $currentRow.find('#partition' + originIndex).attr({'id':'partition' + partitionIndex,'name':'partition['+partitionIndex+'][]'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#app' + partitionIndex).attr('class','form-control chosen');
        $('#app' + partitionIndex).chosen();

        $('#partition' + partitionIndex).attr('class','form-control chosen');
        $('#partition' + partitionIndex).chosen();
        $('#app'+partitionIndex).change();
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
    }

    function delRelate(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($("select[name*='relate[]']").length > 1)
        {
            $currentRow.remove();
        }

    }
</script>
<?php include '../../common/view/footer.html.php';?>
