<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
js::set('abnormalTips', $this->lang->credit->abnormalTips);
js::set('reviewNodeCodeList', $this->lang->credit->reviewNodeCodeList);
js::set('reviewNodeCodeListGroupLevel', $this->lang->credit->reviewNodeCodeListGroupLevel);
js::set('productIds', !empty($creditInfo->productIds)? explode(',', $creditInfo->productIds):[]);
js::set('implementationForm', $creditInfo->implementationForm);
js::set('projectPlanId', $creditInfo->projectPlanId);
js::set('problemIds', !empty($creditInfo->problemIds) ?  explode(',', $creditInfo->problemIds):[]);
js::set('demandIds', !empty($creditInfo->demandIds) ?  explode(',', $creditInfo->demandIds):[]);
js::set('secondorderIds', !empty($creditInfo->secondorderIds) ?  explode(',', $creditInfo->secondorderIds):[]);
js::set('level', $creditInfo->level);
?>
<style>
    .input-group-addon{min-width: 150px;}
    .input-group{margin-bottom: 6px;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo  $lang->credit->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div>
                <!-- 基本信息  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->credit->baseinfo;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!--                        调整表格格式，勿删-->
                            <tr>
                                <th class='w-120px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-120px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>


                            <tr>
                                <!--所属系统 -->
                                <th><?php echo $lang->credit->appIds;?></th>
                                <td colspan='3'>
                                    <?php
                                    echo html::select('appIds[]', $appList, $creditInfo->appIds, "id='appIds' class='form-control chosen ' onchange='getProductName()' multiple");
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <!--产品名称 -->
                                <th><?php echo $lang->credit->productIds;?><i title="<?php echo $lang->credit->productIdsHelp;?>" class="icon icon-help"></i></th>
                                <td class="productTd " colspan='3'><?php echo html::select('productIds[]', [], $creditInfo->productIds, "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");?></td>
                            </tr>

                            <tr>
                                <!--实现方式 -->
                                <th><?php echo $lang->credit->implementationForm;?></th>
                                <td><?php echo html::select('implementationForm', $lang->credit->implementationFormList, $creditInfo->implementationForm, "class='form-control chosen' onchange='changeFixType(this.value)'");?></td>
                                <!--所属项目 -->
                                <th><?php echo $lang->credit->projectPlanId;?><i title="<?php echo $lang->credit->projectHelp;?>" class="icon icon-help"></i></th>
                                <td><?php echo html::select('projectPlanId', [], $creditInfo->projectPlanId, "class='form-control chosen'");?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->secondorderIds;?></th>
                                <td><?php echo html::select('secondorderIds[]', $secondorderList, $creditInfo->secondorderIds, "class='form-control chosen'  multiple");?></td>
                                <!--关联问题 -->
                                <th><?php echo $lang->credit->problemIds;?></th>
                                <td><?php echo html::select('problemIds[]', $problemList, $creditInfo->problemIds, "class='form-control chosen' multiple");?></td>

                            </tr>
                            <tr>
                                <!--关联需求 -->
                                <th><?php echo $lang->credit->demandIds;?></th>
                                <td><?php echo html::select('demandIds[]', $demandList, $creditInfo->demandIds, "class='form-control chosen' multiple ");?></td>
                                <!--关联异常变更单-->
                                <th><?php echo $lang->credit->abnormalId;?><i title="<?php echo $lang->credit->abnormalHelp;?>" class="icon icon-help"></i></th>
                                <td><?php echo html::select('abnormalId', $abnormalList, $creditInfo->abnormalId, "class='form-control chosen '  onchange='selectabnormalCode()' ");?></td>
                            </tr>
                            <tr>
                                <!--是否后补流程 -->
                                <th class='w-100px'><?php echo $lang->modify->isMakeAmends;?></th>
                                <td class="required"><?php echo html::select('isMakeAmends', $lang->modify->isMakeAmendsList, $creditInfo->isMakeAmends, "class='form-control chosen' onchange='selectIsMakeAmends()'");?></td>
                                <!--实际交付时间 -->
                                <th class='w-100px'><?php echo $lang->modify->actualDeliveryTime;?></th>
                                <td class="<?php if($creditInfo->isMakeAmends == 'yes'){echo 'required';}?>"><?php echo html::input('actualDeliveryTime', $creditInfo->actualDeliveryTime, "class='form-control form-datetime'")?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 变更参数 -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->credit->subTitle->params;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--变更级别 -->
                                <th class='w-120px'><?php echo $lang->credit->level ;?><i title="<?php echo $lang->credit->levelHelp;?>" class="icon icon-help"></i></th>
                                <td><?php echo html::select('level', $lang->credit->levelList, $creditInfo->level, "class='form-control chosen' onchange='changeLevel(this.value)'");?></td>
                                <!--变更节点 -->
                                <th class='w-120px'><?php echo $lang->credit->changeNode;?></th>
                                <td><?php echo html::select('changeNode[]', $lang->credit->changeNodeList, $creditInfo->changeNode, "class='form-control chosen' multiple");?></td>
                            </tr>
                            <tr>
                                <!--变更类型 -->
                                <th><?php echo $lang->credit->mode;?></th>
                                <td><?php echo html::select('mode', $lang->credit->modeList, $creditInfo->mode, "class='form-control chosen'");?></td>
                                <!--变更分类 -->
                                <th><?php echo $lang->credit->type;?></th>
                                <td><?php echo html::select('type[]', $lang->credit->typeList, $creditInfo->type, "class='form-control chosen' multiple");?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->changeSource;?></th>
                                <td>
                                    <?php
                                    echo html::select('changeSource[]', $lang->credit->changeSourceList, $creditInfo->changeSource, "class='form-control chosen' multiple");
                                    ?>
                                </td>
                                <!--实施方式 -->
                                <th><?php echo $lang->credit->executeMode;?></th>
                                <td><?php echo html::select('executeMode[]', $lang->credit->executeModeList, $creditInfo->executeMode, "class='form-control chosen' multiple");?></td>
                            </tr>


                            <tr>
                                <!--变更紧急程度 -->
                                <th><?php echo $lang->credit->emergencyType;?>
                                    <!--
                                    <i title="<?php echo $lang->credit->emergencyTypeHelp;?>" class="icon icon-help"></i>
                                    -->
                                </th>
                                <td ><?php echo html::select('emergencyType', $lang->credit->emergencyTypeList, $creditInfo->emergencyType, "class='form-control chosen'");?></td>
                                <!--实施期间是否有业务影响 -->
                                <th><?php echo $lang->credit->isBusinessAffect;?></th>
                                <td><?php echo html::select('isBusinessAffect', $lang->credit->isBusinessAffectList, $creditInfo->isBusinessAffect, "class='form-control chosen'");?></td>
                            </tr>

                            <tr>
                                <!--预计开始时间 -->
                                <th><?php echo $lang->credit->planBeginTime;?>
                                    <!--
                                    <i title="<?php echo $lang->credit->planBeginTimeHelp;?>" class="icon icon-help"></i>
                                    -->
                                </th>
                                <td><?php echo html::input('planBeginTime', $creditInfo->planBeginTime == '0000-00-00 00:00:00'? '':$creditInfo->planBeginTime, "class='form-control form-datetime'");?></td>
                                <!--预计结束时间 -->
                                <th><?php echo $lang->credit->planEndTime;?></th>
                                <td><?php echo html::input('planEndTime', $creditInfo->planEndTime  == '0000-00-00 00:00:00'? '':$creditInfo->planEndTime, "class='form-control form-datetime'");?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 变更内容  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->credit->subTitle->content;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!-- 调整表格格式，勿删-->
                            <tr>
                                <th class='w-120px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-120px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>
                            <tr>
                                <!--变更摘要 -->
                                <th><?php echo $lang->credit->summary;?></th>
                                <td colspan="3"><?php echo html::input('summary', $creditInfo->summary, "class='form-control' maxlength='200'");?></td>
                            </tr>
                            <tr>
                                <!--变更描述 -->
                                <th><?php echo $lang->credit->desc;?></th>
                                <td><?php echo html::textarea('desc', $creditInfo->desc, "placeholder='{$lang->credit->descPlaceholder}' rows='5' class='form-control' maxlength='1000'");?></td>
                                <!--技术验证 -->
                                <th><?php echo $lang->credit->techniqueCheck;?></th>
                                <td><?php echo html::textarea('techniqueCheck', $creditInfo->techniqueCheck, "placeholder='{$lang->credit->techniqueCheckPlaceholder}' rows='5' class='form-control'  maxlength='1000'");?></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 变更可行性分析  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->credit->feasibilityAnalysis;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <!--  调整表格格式，勿删-->
                            <tr>
                                <th class='w-120px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                                <th class='w-120px' style="height: 0;padding:0"></th>
                                <td style="height: 0;padding:0"></td>
                            </tr>
                            <tr>
                                <!--变更摘要 -->
                                <th><?php echo $lang->credit->feasibilityAnalysis;?></th>
                                <td colspan="3"><?php echo html::textarea('feasibilityAnalysis', $creditInfo->feasibilityAnalysis, "rows='3' class='form-control' ");?></td>
                            </tr>


                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 风险分析与应急处置  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->credit->riskAnalysisEmergencyHandle;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form table-bordered">
                            <thead>
                            <tr>
                                <th class="w-40px">NO</th>
                                <th class="required"><?php echo $lang->credit->riskAnalysis;?></th>
                                <th class="required"><?php echo $lang->credit->emergencyBackWay;?></th>
                                <th class="w-120px"><?php echo $lang->actions;?></th>
                            </tr>
                            </thead>
                            <tbody id="aid">
                            <?php
                                $riskAnalysisEmergencyHandle = json_decode($creditInfo->riskAnalysisEmergencyHandle);
                                foreach ($riskAnalysisEmergencyHandle as $key => $val):
                                    $indexKey = $key + 1;
                            ?>
                            <tr id="risk_<?php echo $indexKey; ?>">
                                <td><?php echo $indexKey; ?></td>
                                <td><?php echo html::textarea('riskAnalysis[]', $val->riskAnalysis, "placeholder='没有则填无' id='riskAnalysis_{$indexKey}' class='form-control'");?></td>
                                <td><?php echo html::textarea('emergencyBackWay[]', $val->emergencyBackWay, "placeholder='没有则填无' id='emergencyBackWay_{$indexKey}' class='form-control'");?></td>
                                <td>
                                    <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteLine(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 变更影响  -->
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->credit->subTitle->effect;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <!--给生产系统带来的影响变化 -->
                                <th class="w-100px"><?php echo $lang->credit->productAffect;?></th>
                                <td><?php echo html::textarea('productAffect', $creditInfo->productAffect, "placeholder='{$lang->credit->productAffectPlaceholder}' rows='5' class='form-control' maxlength='200'");?></td>
                                <!--给业务功能带来的影响 -->
                                <th><?php echo $lang->credit->businessAffect;?></th>
                                <td><?php echo html::textarea('businessAffect', $creditInfo->businessAffect, "placeholder='{$lang->credit->businessAffectPlaceholder}' rows='5' class='form-control' maxlength='200'");?></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <table class="table table-form">
                    <tbody>
                    <!--     调整表格格式，勿删-->
                    <tr>
                        <th class='w-120px' style="height: 0;padding:0"></th>
                        <td style="height: 0;padding:0"></td>
                        <th class='w-80px' style="height: 0;padding:0"></th>
                        <td style="height: 0;padding:0"></td>
                    </tr>
                    <tr class="nodes">
                        <th class='w-160px'>
                            <!--评审人员 -->
                            <?php echo $lang->credit->reviewNodes;?>
                            <!--
                        <i title="<?php echo $lang->credit->reviewNodesHelp;?>" class="icon icon-help"></i>
                        -->
                        </th>
                        <td colspan="2" id="reviewerInfo">
                            <?php
                            foreach($lang->credit->reviewNodeNameList as $nodeCode => $nodeName):
                                $currentNodeUserList = zget($reviewNodeUserList, $nodeCode, []);
                                $currentUsers =  zget($reviewerUsers, $nodeCode,'');
                                ?>
                                <div class='input-group node-item node<?php echo $nodeCode;?>' id="<?php echo $nodeCode;?>" style='width:80%'>
                                    <span class='input-group-addon'><?php echo $nodeName;?></span>
                                    <?php echo html::select("reviewerInfo[$nodeCode][]", $currentNodeUserList, $currentUsers, "class='form-control chosen' required multiple"); ?>
                                </div>
                            <?php endforeach;?>
                        </td>
                    </tr>

                    <tr>
                        <input type="hidden" name="issubmit" value="save">
                        <input type="hidden" id="creditId" value="0">
                        <td class='form-actions text-center' colspan='4'>
                            <?php
                            echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') .
                                html::commonButton($lang->credit->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') . html::backButton();
                            ?></td>
                    </tr>
                    </tbody>
                </table>

            </div>
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
<?php include '../../common/view/footer.html.php';?>
<script>
    selectIsMakeAmends()
</script>
