<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
    .task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
    .task-toggle .icon{display: inline-block; transform: rotate(90deg);}
    .more-tips{display: none;}
    .close-tips{display: none}
    .tooltip-diy{
        position: relative;
        display: inline-block;
    }
    .tooltip-text{
        width: 800px;
        visibility: hidden;
        background-color: #f6f4f4;
        text-align: left;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        top: -0px;
        left: 50%;
        transform: translateX(-10%);
        opacity: 0;
        transition: opacity 0.1s;
        line-height: 20px;
    }
    .tooltip-diy:hover .tooltip-text{
        visibility: visible;
        opacity: 1;
    }
</style>

<div id="mainContent" class="main-content fade" style="<?php if($problem->status == 'confirmed') echo "height:450px"?>">
    <?php if(($problem->createdBy != 'guestcn' && $problem->createdBy != 'guestjx') || ($problem->execution > 0 || $problem->ReviewStatus != "tofeedback")):?>
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->problem->deal; ?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <!--迭代26去掉处理人-->
                    <!--<tr>
                    <th class='w-140px'><?php /*echo $lang->problem->handler;*/ ?></th>
                    <td><?php /*echo html::select('user', $users, $this->app->user->account, "class='form-control chosen'");*/ ?></td>
                </tr>-->
                    <!--20221009 去掉工作量、 相关部门配合人员工作量-->
                    <!-- <tr>
            <th class='w-140px'><?php /*echo $lang->problem->consumed;*/ ?></th>
            <td><?php /*echo html::input('consumed', '', "class='form-control'");*/ ?></td>
          </tr>
          <tr id='relevantDept1'>
            <th class='w-110px'><?php /*echo $lang->problem->relevantDept;*/ ?></th>
            <td>
              <div class='table-row'>
                <div class='table-col'>
                  <?php /*echo html::select('relevantUser[]', $users, '', "class='form-control chosen'");*/ ?>
                </div>
                <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php /*echo $lang->problem->workload;*/ ?></span>
                    <?php /*echo html::input('workload[]', '', "class='form-control'");*/ ?>
                  </div>
                </div>
              </div>
            </td>
            <td class="c-actions">
              <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
            </td>
          </tr>-->
                    <tr>
                        <th class='w-110px'><?php echo $lang->problem->dealStatus; ?></th>
                        <td><?php echo html::select('status', $statusList, $status, "class='form-control chosen' onchange='selectStatus(this.value)'"); ?></td>
                    </tr>
                    <!--20221009 去掉说明-->
                    <!--  <tr>
                        <th class='w-110px'></th>
                        <td colspan='2'>
                          <p>*二线专员：处理后状态为【待分析】,下一节点处理人请选择【分析人员】</p>
                          <p>*分析人员：处理后状态为【待开发】,下一节点处理人请选择【开发人员】</p>
                          <p>*开发人员：处理后状态为【待制版】,下一节点处理人请选择【质量部CM】</p>
                          <p><a class="task-toggle" id='moreTips'>点击展开更多<i class="icon icon-angle-right"></i></a></p>
                          <p class='more-tips'>*配置管理：处理后状态为【待测试】,下一节点处理人请选择【测试人员】</p>
                          <p class='more-tips'>*测试人员：处理后状态为【测试已通过】,下一节点处理人请选择【质量部CM】</p>
                          <p class='more-tips'>****说明：若处理状态为【测试未通过】,下一节点处理人请选择【开发人员】</p>
                          <p class='more-tips'>*配置管理：处理后状态为【验版已通过】,下一节点处理人请选择【验证人员】</p>
                          <p class='more-tips'>****说明：若处理状态为【验版未通过】,下一节点处理人请选择【测试人员】</p>
                          <p class='more-tips'>*验证人员：处理后状态为【验证已通过】,下一节点处理人请选择【质量部CM】</p>
                          <p class='more-tips'>****说明：若处理状态为【验证未通过】,下一节点处理人请选择【开发人员】</p>
                          <p class='more-tips'>*二线专员：处理后状态为【待上线】,下一节点处理人选择【创建人/二线专员】</p>
                          <p class='more-tips'>*创建人/二线专员：处理后状态为【上线成功】或【上线失败】,下一节点处理人选择【二线专员】</p>
                          <p><a class="close-tips " id='closeTips'>点击收起<i class="icon icon-angle-left"></i></a></p>
                        </td>
                      </tr>-->

                    <?php if ($problem->IssueId && $problem->status == 'confirmed') { ?>
                        <tr>
                            <th class='w-110px'><?php echo $lang->problem->feedbackExpireTime; ?></th>
                            <td><?php echo html::input('feedbackExpireTime', $problem->feedbackExpireTime, "class='form-control form-datetime' "); ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="<?php if ($problem->status != 'assigned') {
                        echo 'hidden';
                    } ?> dev">
                        <th><?php echo $lang->problem->type; ?></th>
                        <td class='required'><?php echo html::select('type', $lang->problem->typeList, $problem->type, "class='form-control picker-select' onchange='selectType(this.value,$problem->id)'"); ?></td>
                    </tr>
                    <tr class="hidden">
                        <th><?php echo $lang->problem->problemCause; ?></th>
                        <td class='required'><?php echo html::select('problemCause', $lang->problem->problemCauseList, $problem->problemCause, "class='form-control picker-select' "); ?></td>
                    </tr>
                    <tr class="hidden ">
                        <th><?php echo $lang->problem->repeatProblem; ?></th>
                        <td class='required'
                            colspan="2"><?php echo html::select('repeatProblem[]', $repeatProblem, $problem->repeatProblem, "class='form-control picker-select' multiple "); ?></td>
                    </tr>
                    <tr id="dealuser">
                        <th class='w-110px'><?php echo $lang->problem->nextUser;
                            echo "<br>" . $lang->problem->nextStatus[$problem->status]; ?></th>
                        <td><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'"); ?></td>
                    </tr>
                    <!--                配合人员-->
                    <tr>
                        <th class='w-110px'><?php echo $lang->problem->mailto; ?></th>
                        <td colspan="2"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple"); ?></td>
                    </tr>
                    <tr class = "<?php if($problem->status != 'assigned') echo 'hidden'; ?>">
                        <th><?php echo $lang->problem->PlannedTimeOfChange; ?></th>
                        <?php $problem->PlannedTimeOfChange = strpos($problem->PlannedTimeOfChange, '0000') === false ? $problem->PlannedTimeOfChange: ''; ?>
                        <td colspan="2" class="required"><?php echo html::input('PlannedTimeOfChange', $problem->PlannedTimeOfChange, "class='form-control form-datetime'"); ?></td>
                    </tr>
                    <tr class="hidden dev3">
                        <th><?php echo $lang->problem->systemverify; ?></th>
                        <td class='required notrequired'><?php echo html::radio('systemverify', $lang->problem->needOptions, $problem->systemverify, 'onchange ="systemveif()"'); ?></td>
                        <td class="hidden dev3 te test">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->problem->verifyperson; ?></span>
                                <?php echo html::select('verifyperson', $users, $problem->verifyperson, "class='form-control chosen'"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden dev3">
                        <th><?php echo $lang->problem->laboratorytest; ?></th>
                        <td class='required notrequired yz'
                            colspan="2"><?php echo html::select('laboratorytest', $users, $problem->laboratorytest, "class='form-control chosen'"); ?></td>
                    </tr>

                    <tr class="<?php if ($problem->IssueId && $problem->status != 'confirmed') {
                        echo 'hidden';
                    } ?> dev dev2">
                        <th><?php echo $lang->problem->app; ?></th>
                        <td class='required notrequired'
                            colspan="2"><?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'"); ?></td>
                        <input type="hidden" name="application" id="application" value="">
                    </tr>
                    <!--暂时不显示-->
                    <tr class="hidden">
                        <th><?php echo $lang->problem->isPayment; ?></th>
                        <td colspan="2"><?php
                            foreach ($lang->application->isPaymentList as $k => $v) {
                                if (empty($k)) continue;
                                echo '<span id="isPayment_' . $k . '" class="isPayment_box hidden">' . $v . ',</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <!--20220310 根据迭代6需求 145 修改-->
                    <?php $count = array_filter(explode(',', $problem->product)); if (count($count) == 0): ?>
                        <tr class="<?php if ($problem->IssueId && $problem->status != 'confirmed') { echo 'hidden';} ?> dev dev2 " id="productTab1">
                            <th class='w-110px'><?php echo $lang->problem->product; ?></th>
                            <td id="productZone">
                                <div class='table-row' style="width:400px">
                                    <div class='table-col product-th' data-id='1'>
                                        <?php echo html::select('product[]', $productList, "", "class='form-control chosen productSelect' data-id = '1' onchange='productChange(this)'"); ?>
                                    </div>
                                    <div class='table-col ' style="width:160px">
                                        <div class='input-group required  '>
                                            <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()"> <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct ?></span></span>
                                            <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan; ?></span>
                                            <?php echo html::select('productPlan[]', $productplan, "", "class='form-control chosen w-100px productPlanSelect' id='p-1'"); ?>
                                            <span class="input-group-btn addProductPlan" data-id='0' onclick="createPlan(this)"> <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                                            <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='1'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                            <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                                            <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips ?>"></i></a></span>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($details as $key => $deatil): $indexKey = $key + 1; ?>
                            <tr class="<?php if ($problem->IssueId && $problem->status != 'confirmed') {
                                echo 'hidden';
                            } ?> dev dev2 " id="productTab<?php echo $indexKey; ?>">
                                <th class='w-110px'><?php echo $lang->problem->product; ?></th>
                                <td id="productZone">
                                    <div class='table-row' style="width:400px">
                                        <div class='table-col product-th' data-id='<?php echo $indexKey ?>'>
                                            <?php echo html::select('product[]', $productList, $deatil->product, "class='form-control chosen productSelect' data-id = '$indexKey'  id ='product$indexKey' onchange='productChange(this)'"); ?>
                                        </div>
                                        <div class='table-col ' style="width:160px">
                                            <div class='input-group required  '>
                                                <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()"> <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct ?></span></span>
                                                <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan; ?></span>
                                                <?php echo html::select('productPlan[]', $deatil->productPlan, $deatil->plan, "class='form-control chosen w-100px productPlanSelect' id='p-$indexKey'"); ?>
                                                <span class="input-group-btn addProductPlan" data-id='<?php echo $deatil->product ?>' onclick="createPlan(this)"> <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                                                <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='<?php echo $indexKey; ?>'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                                <?php if ($indexKey > 1): ?>
                                                    <span class="input-group-btn addStage " onclick="delProductItem(this)" data-id='<?php echo $indexKey; ?>' id='codeClose0'> <span class="btn addItem"><i class="icon-close" title=""></i></span></span>
                                                    <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem " style="width:30px"><i class="icon-refresh"></i></a></span>

                                                <?php else: ?>
                                                    <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                                                    <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips ?>"></i></a></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    <tr class="hidden dev ">
                        <th class='w-110px'><?php echo $lang->problem->fixType; ?></th>
                        <td class='' id="productZone">
                            <div class='table-row' style="width:475px">
                                <div class='table-col'>
                                    <?php echo html::select('fixType', $lang->problem->fixTypeList, $problem->fixType, "class='form-control chosen' onchange='selectfix()'"); ?>
                                </div>
                                <div class='table-col'>
                                    <div class='input-group required  notrequired' style="width:440px">
                                        <span class='input-group-addon fix-border'><?php echo $lang->problem->projectPlan; ?></span>
                                        <?php $where = '';
                                        $where = "onchange='loadProductExecutions( this.value,\"$problem->fixType\",\"$problem->app\")'"; ?>
                                        <?php echo html::select('projectPlan', $plans, $problem->projectPlan, "class='form-control chosen ' $where"); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                   <!-- <tr class="hidden  ">
                       <th><?php /*echo $lang->problem->stage;*/?></th>
                       <td colspan='2'><?php /*echo html::select('execution', $executions, $problem->execution,"class='form-control'");*/?></td>
                       <input type="hidden" name="flag" id="flag" value="">
                       <input type="hidden" name="executionid" id="executionid" value="">
                   </tr>-->
                    <tr class="<?php if ($problem->status != 'assigned') { echo 'hidden';} ?> dev ">
                        <th><?php echo $lang->problem->reasonbr; ?></th>
                        <td colspan='2'><?php echo html::textarea('reason', $problem->reason, "class='form-control'"); ?></td>
                    </tr>
                    <tr class="<?php if ($problem->status != 'assigned') {echo 'hidden';} ?> dev">
                        <th><?php echo $lang->problem->solution; ?></th>
                        <td colspan='2'><?php echo html::textarea('solution', $problem->solution, "class='form-control'"); ?>
                            <span colspan='3' style="color: red;" id="tasktip"></span>
                        </td>
                    </tr>
                    <tr class="hidden dev ">
                        <th><?php echo $lang->problem->plateMakAp; ?></th>
                        <td class='required notrequired' colspan='2'><?php echo html::textarea('plateMakAp', $problem->plateMakAp, "class='form-control' placeholder=$lang->tips1"); ?></td>
                    </tr>

                    <tr class="hidden dev dev5">
                        <th><?php echo $lang->problem->plateMakInfo; ?></th>
                        <td class='required notrequired' colspan='2'><?php echo html::textarea('plateMakInfo', $problem->plateMakInfo, "class='form-control' placeholder=$lang->tips2"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php elseif ($problem->createdBy == 'guestcn') :?>
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $problem->status == 'assigned' ? $lang->problem->deal.'&'.$this->lang->problem->createfeedback :$lang->problem->deal;?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form"><tbody>
                    <tr>
                        <th class='w-110px'><?php echo $lang->problem->dealStatus;?></th>
                        <td colspan="5"><?php echo html::select('status', $statusList, $status, "class='form-control chosen' onchange='selectStatus(this.value)'");?></td>
                    </tr>
                    <?php if($problem->IssueId && $problem->status == 'confirmed'){ ?>
                        <tr>
                            <th class='w-110px'><?php echo $lang->problem->feedbackExpireTime;?></th>
                            <td colspan="5"><?php echo html::input('feedbackExpireTime', $problem->feedbackExpireTime, "class='form-control form-datetime' ");?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th class="<?php if($problem->status != 'assigned'){ echo 'hidden';}?> dev"><?php echo $lang->problem->type;?></th>
                        <td class='<?php if($problem->status != 'assigned'){ echo 'hidden';}?> dev required' colspan="2"><?php echo html::select('type', $lang->problem->typeList, $problem->type, "class='form-control picker-select' onchange='selectType(this.value,$problem->id)'");?></td>

                        <th class="problemCauseClass hidden"><?php echo $lang->problem->problemCause;?></th>
                        <td class='problemCauseClass hidden required' colspan="2"><?php echo html::select('problemCause', $lang->problem->problemCauseList, $problem->problemCause, "class='form-control picker-select' ");?></td>
                    </tr>
                    <tr>
                        <?php  if($problem->status == 'assigned') :?>
                            <input type='hidden' value="0"  name="ifReturn">
                            <th><?php echo $lang->problem->problemGrade; ?>
                                <i class="icon icon-help tooltip-diy"><span class="tooltip-text"><?php echo $lang->problem->problemGradePrompt;?></span></i>
                            </th>
                            <td class ="devback" colspan="2">
                                <?php echo html::select('problemGrade', $lang->problem->problemGradeList, $problem->problemGrade, " class='form-control chosen' "); ?>
                            </td>
                        <?php endif; ?>

                        <th class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2"><?php echo $lang->problem->app;?></th>
                        <td class='<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2 required notrequired' colspan="2"><?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'");?></td>
                        <input type="hidden" name="application" id="application" value="">
                    </tr>
                    <?php  if($problem->status == 'assigned') :?>
                    <tr>
                        <th><?php echo $lang->problem->SolutionFeedback;?></th>
                        <td class="required  notrequired" colspan="2"><?php echo html::select('SolutionFeedback', $lang->problem->solutionFeedbackList, $problem->SolutionFeedback, "onchange='solutionFeedbackChange(this.value)' class='form-control chosen' "); ?></td>

                        <!--是否为最终方案-下拉选择框-->
                        <th class='devback w-110px'><?php echo $lang->problem->IfultimateSolution;?></th>
                        <td class='devback required' colspan="2" class='table-col required' id='IfultimateSolutionTd'>
                            <?php
                            $IfultimateSolutionDisable = $problem->SolutionFeedback == 5 ? "disabled" :"";
                            echo html::select('IfultimateSolution', $lang->problem->ifultimateSolutionList, $problem->IfultimateSolution, "onchange='ifultimateChanged(this.value)' class='form-control chosen'  $IfultimateSolutionDisable"); ?>
                        </td>
                    </tr>
                    <tr class="hidden'" id ='standardVerifyId'>
                        <th class='w-110px'><?php echo $lang->problem->standardVerify;?></th>
                        <td colspan="2" class="required">
                            <?php echo html::select('standardVerify', $lang->problem->standardVerifyList, $problem->standardVerify, " class='form-control chosen' "); ?>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if($problem->status == 'confirmed'):?>
                    <tr id="dealuser">
                        <th class='w-110px'><?php echo $lang->problem->nextUser; echo "<br>".$lang->problem->nextStatus[$problem->status];?></th>
                        <td colspan="5"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'");?></td>
                    </tr>
                    <tr>
                        <th class='w-110px'><?php echo $lang->problem->mailto;?></th>
                        <td colspan="5"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple");?></td>
                    </tr>
                    <?php endif;?>
                    <tr class="hidden ">
                        <th><?php echo $lang->problem->repeatProblem;?></th>
                        <td class='required' colspan="5"><?php echo html::select('repeatProblem[]', $repeatProblem, $problem->repeatProblem, "class='form-control picker-select' multiple ");?></td>
                    </tr>
                    <tr class="hidden dev3">
                        <th><?php echo $lang->problem->systemverify;?></th>
                        <td class='required notrequired'><?php echo html::radio('systemverify', $lang->problem->needOptions, $problem->systemverify,'onchange ="systemveif()"');?></td>
                        <td class="hidden dev3 te test">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->problem->verifyperson;?></span>
                                <?php echo html::select('verifyperson', $users, $problem->verifyperson, "class='form-control chosen'");?>
                            </div>
                        </td>
                    </tr>

                    <tr class="hidden dev3">
                        <th><?php echo $lang->problem->laboratorytest;?></th>
                        <td class='required notrequired yz' colspan="2"><?php echo html::select('laboratorytest', $users, $problem->laboratorytest, "class='form-control chosen'");?></td>
                    </tr>
                    <!--暂时不显示-->
                    <tr class="hidden">
                        <th><?php echo $lang->problem->isPayment;?></th>
                        <td colspan="2"><?php
                            foreach ($lang->application->isPaymentList as $k => $v) {
                                if(empty($k)) continue;
                                echo '<span id="isPayment_'.$k.'" class="isPayment_box hidden">'.$v.',</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <!--20220310 根据迭代6需求 145 修改-->
                    <?php $count = array_filter(explode(',',$problem->product));
                    if(count($count)  == 0 ):?>
                        <tr class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2 toHide" id="productTab1">
                            <th class='w-110px'><?php echo $lang->problem->product;?></th>
                            <td id="productZone">
                                <div class='table-row' style="width:400px">
                                    <div class='table-col product-th' data-id = '1'>
                                        <?php echo html::select('product[]', $productList, "", "class='form-control chosen productSelect' data-id = '1' onchange='productChange(this)'");?>
                                    </div>
                                    <div class='table-col ' style="width:160px">
                                        <div class='input-group required'>
                                            <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct?></span></span>
                                            <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan;?></span>
                                            <?php echo html::select('productPlan[]', $productplan, "", "class='form-control chosen w-100px productPlanSelect' id='p-1'");?>
                                            <span class="input-group-btn addProductPlan" data-id='0' onclick="createPlan(this)" > <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                                            <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='1'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                            <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                                            <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips?>"></i></a></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else:?>
                        <?php foreach($details as $key => $deatil):
                            $indexKey = $key+1; ?>
                            <tr class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2 toHide" id="productTab<?php echo $indexKey;?>">
                                <th class='w-110px'><?php echo $lang->problem->product;?></th>
                                <td id= "productZone">
                                    <div class='table-row' style="width:400px">
                                        <div class='table-col product-th' data-id = '<?php echo $indexKey?>'>
                                            <?php echo html::select('product[]', $productList, $deatil->product, "class='form-control chosen productSelect' data-id = '$indexKey'  id ='product$indexKey' onchange='productChange(this)'");?>
                                        </div>
                                        <div class='table-col' style="width:160px">
                                            <div class='input-group required'>
                                                <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct?></span></span>
                                                <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan;?></span>
                                                <?php echo html::select('productPlan[]',  $deatil->productPlan, $deatil->plan, "class='form-control chosen w-100px productPlanSelect' id='p-$indexKey'");?>
                                                <span class="input-group-btn addProductPlan" data-id='<?php echo $deatil->product?>' onclick="createPlan(this)" > <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                                                <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='<?php echo $indexKey;?>'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                                <?php if($indexKey > 1):?>
                                                    <span class="input-group-btn addStage " onclick="delProductItem(this)" data-id='<?php echo $indexKey;?>' id='codeClose0'> <span class="btn addItem"><i class="icon-close" title=""></i></span></span>
                                                    <span class="input-group-btn fix-border" ><a href="javascript:;" onclick="proandver(this)" class="btn addItem " style="width:30px"><i class="icon-refresh"></i></a></span>
                                                <?php else:?>
                                                    <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                                                    <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips?>"></i></a></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    <tr class="hidden dev ">
                        <th class='w-110px'><?php echo $lang->problem->fixType;?></th>
                        <td class='' id="productZone">
                            <div class='table-row' style="width:475px">
                                <div class='table-col'>
                                    <?php echo html::select('fixType', $lang->problem->fixTypeList, $problem->fixType, "class='form-control chosen' onchange='selectfix()'");?>
                                </div>
                                <div class='table-col'>
                                    <div class='input-group  required  notrequired' style="width:440px">
                                        <span class='input-group-addon fix-border'><?php echo $lang->problem->projectPlan;?></span>
                                        <?php $where = ''; $where = "onchange='loadProductExecutions( this.value,\"$problem->fixType\",\"$problem->app\")'";?>
                                        <?php echo html::select('projectPlan', $plans, $problem->projectPlan, "class='form-control chosen ' $where");?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                   <!-- <tr class="hidden  ">
                       <th><?php /*echo $lang->problem->stage;*/?></th>
                       <td colspan='2'><?php /*echo html::select('execution', $executions, $problem->execution,"class='form-control'");*/?></td>
                       <input type="hidden" name="flag" id="flag" value="">
                       <input type="hidden" name="executionid" id="executionid" value="">
                   </tr>-->
                    <?php  if($problem->status == 'assigned') :?>
                        <!--初步反馈-富文本-->
                        <tr class="toHide devback">
                            <th><?php echo $lang->problem->Tier1Feedback; ?></th>
                            <td colspan='5' class='required'><?php echo html::textarea('Tier1Feedback', $problem->Tier1Feedback, "class='form-control' placeholder='  ' maxlength='500' rows ='3'"); ?></td>
                        </tr>
                        <!--发生原因-富文本-->
                        <tr class="notUltimate toHide devback">
                            <th><?php echo $lang->problem->reason; ?></th>
                            <td colspan='5' class='required' id="ultimateSolutionTd"><?php echo html::textarea('reason', $problem->reason, "class='form-control' maxlength='2000' rows ='3'"); ?></td>
                        </tr>
                        <!--最终解决方案-富文本-->
                        <tr class="notUltimate devback">
                            <th><?php echo $lang->problem->ultimateSolution; ?></th>
                            <td colspan='5' class='required' id="ultimateSolutionTd"><?php echo html::textarea('solution', $problem->solution, "class='form-control' placeholder='当“解决方式”为“非应用问题”时，请在此描述原因，请“是否最终解决方案”选择“是”' 
                    maxlength='2000' rows ='3'"); ?></td>
                        </tr>
                        <!--影响范围-富文本-->
                        <tr class="toHide devback">
                            <th><?php echo $lang->problem->EditorImpactscope; ?></th>
                            <td colspan='5' class='required'><?php echo html::textarea('EditorImpactscope', $problem->EditorImpactscope, "class='form-control' placeholder='' rows ='3'"); ?></td>
                        </tr>
                        <!--解决该问题的变更-富文本-->
                        <tr class="solvingTheIssue toHide devback">
                            <th><?php echo $lang->problem->ChangeSolvingTheIssue; ?></th>
                            <td colspan='5'><?php echo html::textarea('ChangeSolvingTheIssue', $problem->ChangeSolvingTheIssue, "class='form-control'  maxlength='200' rows ='3'"); ?></td>
                        </tr>
                        <tr class ="devback">
                            <!--处理人联系方式-输入框-->
                            <!--计划解决(变更)时间-日历-->
                            <th class='w-110px'><?php echo $lang->problem->TeleOfIssueHandler;?></th>
                            <td >
                                <div class='table-row' style="width:475px">
                                    <div class='table-col required' >
                                        <?php echo html::input('TeleOfIssueHandler', $problem->TeleOfIssueHandler, "class='form-control' maxlength='20'"); ?>
                                    </div>
                                    <div class='toHide'>
                                        <div class='input-group required'  style="width:440px">
                                            <span class='input-group-addon fix-border'><?php echo $lang->problem->PlannedTimeOfChange;?></span>
                                            <?php echo html::input('PlannedTimeOfChange', $problem->PlannedTimeOfChange, "class='form-control form-datetime'"); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="toHide devback">
                            <!--计划提交变更日期-日历-->
                            <th ><?php echo $lang->problem->PlannedDateOfChangeReport; ?></th>
                            <!--计划变更日期-日历-->
                            <td >
                                <div class='table-row' style="width:475px">
                                    <div class='table-col required' >
                                        <?php echo html::input('PlannedDateOfChangeReport', $problem->PlannedDateOfChangeReport, "class='form-control form-date'"); ?>
                                    </div>
                                    <div class='table-col'>
                                        <div class='input-group  required  ' id ='PlannedDateOfChange' style="width:440px">
                                            <span class='input-group-addon fix-border'><?php echo $lang->problem->PlannedDateOfChange;?></span>
                                            <?php echo html::input('PlannedDateOfChange', $problem->PlannedDateOfChange, "class='form-control form-date'"); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!--问题退回原因-富文本-->
                        <tr class="hide devback">
                            <th><?php echo $lang->problem->ReasonOfIssueRejecting; ?></th>
                            <td colspan='5' class='required'><?php echo html::textarea('ReasonOfIssueRejecting', $problem->ReasonOfIssueRejecting, "class='form-control' placeholder='' rows ='3'"); ?></td>
                        </tr>
                        <!--修订记录-富文本-->
                        <?php if($problem->ReviewStatus == 'externalsendback'): ?>
                        <tr class="toHide devback" >
                            <th><?php echo $lang->problem->revisionRecord; ?></th>
                            <td colspan='5'><?php echo html::input('revisionRecord', $problem->revisionRecord, "class='form-control'"); ?></td>
                        </tr>
                        <?php endif; ?>
                        <!--上传附件-文件-->
                        <tr class ="devback">
                            <th><?php echo $lang->files; ?></th>
                            <td colspan='5'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                        </tr>

                    <tr class ="devback">
                        <th class='w-110px'><?php echo $lang->problem->feedbackDeptReview;?></th>
                        <td colspan="5" class='required'><?php echo html::select('feedbackToHandle[]', $managerUser, $problem->feedbackToHandle, "class='form-control chosen' multiple");?></td>
                    </tr>
                    <tr id="dealuser" class ="devback">
                        <th class='w-110px'><?php echo $lang->problem->nextUser; echo "<br>".$lang->problem->nextStatus[$problem->status];?></th>
                        <td colspan="5"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'");?></td>
                    </tr>

                    <tr >
                        <th class='w-110px'><?php echo $lang->problem->mailto;?></th>
                        <td colspan="5"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple");?>
                            <span colspan='3' style="color: red;" id="tasktip"></span>
                        </td>
                    </tr>
                    <?php endif;?>
                    <tr class="hidden dev ">
                        <th><?php echo $lang->problem->plateMakAp;?></th>
                        <td class='required notrequired' colspan='2'><?php echo html::textarea('plateMakAp', $problem->plateMakAp, "class='form-control' placeholder=$lang->tips1");?></td>
                    </tr>

                    <tr class="hidden dev dev5">
                        <th><?php echo $lang->problem->plateMakInfo;?></th>
                        <td class='required notrequired' colspan='2'><?php echo html::textarea('plateMakInfo', $problem->plateMakInfo, "class='form-control' placeholder=$lang->tips2");?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='5'><?php echo html::submitButton() . html::backButton();?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php elseif ($problem->createdBy == 'guestjx') :?>
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $problem->status == 'assigned' ? $lang->problem->deal. '&'.$this->lang->problem->jxcreatefeedback : $lang->problem->deal;?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th class='w-110px'><?php echo $lang->problem->dealStatus;?></th>
                        <td colspan="4"><?php echo html::select('status', $statusList, $status, "class='form-control chosen' onchange='selectStatus(this.value)'");?></td>
                    </tr>
                    <?php if($problem->IssueId && $problem->status == 'confirmed'){ ?>
                        <tr>
                            <th class='w-110px'><?php echo $lang->problem->feedbackExpireTime;?></th>
                            <td colspan="4"><?php echo html::input('feedbackExpireTime', $problem->feedbackExpireTime, "class='form-control form-datetime' ");?></td>
                        </tr>
                    <?php } ?>
                    <tr class="<?php if($problem->status != 'assigned'){ echo 'hidden';}?> dev">
                        <th><?php echo $lang->problem->type;?></th>
                        <td class='required' colspan="4"><?php echo html::select('type', $lang->problem->typeList, $problem->type, "class='form-control picker-select' onchange='selectType(this.value,$problem->id)'");?></td>
                    </tr>
                    <tr class="hidden">
                        <th><?php echo $lang->problem->problemCause;?></th>
                        <td class='required' colspan="4"><?php echo html::select('problemCause', $lang->problem->problemCauseList, $problem->problemCause, "class='form-control picker-select' ");?></td>
                    </tr>
                    <tr class="hidden ">
                        <th><?php echo $lang->problem->repeatProblem;?></th>
                        <td class='required' colspan="4"><?php echo html::select('repeatProblem[]', $repeatProblem, $problem->repeatProblem, "class='form-control picker-select' multiple ");?></td>
                    </tr>
                    <?php if($problem->status == 'confirmed'):?>
                        <tr id="dealuser">
                            <th class='w-110px'><?php echo $lang->problem->nextUser; echo "<br>".$lang->problem->nextStatus[$problem->status];?></th>
                            <td colspan="4"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'");?></td>
                        </tr>

                        <tr>
                            <th class='w-110px'><?php echo $lang->problem->mailto;?></th>
                            <td colspan="4"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple");?></td>
                        </tr>
                    <?php endif;?>
                    <tr class="hidden dev3">
                        <th><?php echo $lang->problem->systemverify;?></th>
                        <td class='required notrequired'><?php echo html::radio('systemverify', $lang->problem->needOptions, $problem->systemverify,'onchange ="systemveif()"');?></td>
                        <td class="hidden dev3 te test">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->problem->verifyperson;?></span>
                                <?php echo html::select('verifyperson', $users, $problem->verifyperson, "class='form-control chosen'");?>
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden dev3">
                        <th><?php echo $lang->problem->laboratorytest;?></th>
                        <td class='required notrequired yz' colspan="2"><?php echo html::select('laboratorytest', $users, $problem->laboratorytest, "class='form-control chosen'");?></td>
                    </tr>

                    <tr class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2">
                        <th><?php echo $lang->problem->app;?></th>
                        <td class='required notrequired' colspan="4"><?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'");?></td>
                        <input type="hidden" name="application" id="application" value="">
                    </tr>
                    <!--暂时不显示-->
                    <tr class="hidden">
                        <th><?php echo $lang->problem->isPayment;?></th>
                        <td colspan="2"><?php
                            foreach ($lang->application->isPaymentList as $k => $v)
                            {
                                if(empty($k)) continue;
                                echo '<span id="isPayment_'.$k.'" class="isPayment_box hidden">'.$v.',</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <!--20220310 根据迭代6需求 145 修改-->
                    <?php $count = array_filter(explode(',',$problem->product));
                    if(count($count)  == 0 ):
                        ?>
                        <tr class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2 " id="productTab1">
                            <th class='w-110px'><?php echo $lang->problem->product;?></th>
                            <td id="productZone">
                                <div class='table-row' style="width:400px">
                                    <div class='table-col product-th' data-id = '1'>
                                        <?php echo html::select('product[]', $productList, "", "class='form-control chosen productSelect' data-id = '1' onchange='productChange(this)'");?>
                                    </div>
                                    <div class='table-col' style="width:160px">
                                        <div class='input-group'>
                                            <!--                              <span class="input-group-btn fix-border"><?php /*echo html::a($this->createLink('product','create'),'<i class="icon-plus"></i>'.$lang->problem->newproduct,'','class="btn btn-info" onclick="return createpro()" target="_blank"')*/?></span>
-->                        <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct?></span></span>

                                            <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan;?></span>
                                            <?php echo html::select('productPlan[]', $productplan, "", "class='form-control chosen w-100px productPlanSelect' id='p-1'");?>
                                            <span class="input-group-btn addProductPlan" data-id='0' onclick="createPlan(this)" > <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                                            <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='1'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                            <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                                            <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips?>"></i></a></span>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else:?>
                        <?php
                        foreach($details as $key => $deatil):
                            $indexKey = $key+1;
                            ?>
                            <tr class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2 " id="productTab<?php echo $indexKey;?>">
                                <th class='w-110px'><?php echo $lang->problem->product;?></th>
                                <td id= "productZone">
                                    <div class='table-row' style="width:400px">
                                        <div class='table-col product-th' data-id = '<?php echo $indexKey?>'>
                                            <?php echo html::select('product[]', $productList, $deatil->product, "class='form-control chosen productSelect' data-id = '$indexKey'  id ='product$indexKey' onchange='productChange(this)'");?>
                                        </div>
                                        <div class='table-col' style="width:160px">
                                            <div class='input-group'>
                                                <!--                              <span class="input-group-btn fix-border"><?php /*echo html::a($this->createLink('product','create'),'<i class="icon-plus"></i>'.$lang->problem->newproduct,'','class="btn btn-info" onclick="return createpro()" target="_blank"')*/?></span>
-->                        <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct?></span></span>

                                                <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan;?></span>
                                                <?php echo html::select('productPlan[]',  $deatil->productPlan, $deatil->plan, "class='form-control chosen w-100px productPlanSelect' id='p-$indexKey'");?>
                                                <span class="input-group-btn addProductPlan" data-id='<?php echo $deatil->product?>' onclick="createPlan(this)" > <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                                                <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='<?php echo $indexKey;?>'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                                                <?php if($indexKey > 1):?>
                                                    <span class="input-group-btn addStage " onclick="delProductItem(this)" data-id='<?php echo $indexKey;?>' id='codeClose0'> <span class="btn addItem"><i class="icon-close" title=""></i></span></span>
                                                    <span class="input-group-btn fix-border" ><a href="javascript:;" onclick="proandver(this)" class="btn addItem " style="width:30px"><i class="icon-refresh"></i></a></span>

                                                <?php else:?>
                                                    <span class="input-group-btn fix-border"><a href="javascript:;" onclick="proandver(this)" class="btn addItem" style="width:30px"><i class="icon-refresh"></i></a></span>
                                                    <span class="input-group-btn"><a href="javascript:;" class="btn addItem" style="width:35px"><i class="icon-help" title="<?php echo $lang->problem->createPlanTips?>"></i></a></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    <tr class="hidden dev ">
                        <th class='w-110px'><?php echo $lang->problem->fixType;?></th>
                        <td class='' id="productZone">
                            <div class='table-row' style="width:475px">
                                <div class='table-col'>
                                    <?php echo html::select('fixType', $lang->problem->fixTypeList, $problem->fixType, "class='form-control chosen' onchange='selectfix()'");?>
                                </div>
                                <div class='table-col'>
                                    <div class='input-group required  notrequired' style="width:440px">
                                        <span class='input-group-addon fix-border'><?php echo $lang->problem->projectPlan;?></span>
                                        <?php $where = ''; /*$where = "onchange='loadProductExecutions( this.value,\"$problem->fixType\",\"$problem->app\")'"*/;?>
                                        <?php echo html::select('projectPlan', $plans, $problem->projectPlan, "class='form-control chosen ' $where");?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                  <!--  <tr class="hidden  ">
                        <th><?php /*echo $lang->problem->stage;*/?></th>
                        <td colspan='4'><?php /*echo html::select('execution', $executions, $problem->execution,"class='form-control'");*/?></td>
                        <input type="hidden" name="flag" id="flag" value="">
                        <input type="hidden" name="executionid" id="executionid" value="">
                    </tr>-->
                     <?php if($problem->status == 'assigned'):?>
                        <tr class ="devback">
                            <th class='w-140px'><?php echo $lang->problem->ifRecive ?></th>
                            <td><?php echo html::radio('ifReturn', array_filter($lang->problem->ifReturnList), $problem->ifReturn, "onchange='returnChanged(this.value)' class='text-center'"); ?></td>
                            <input type='hidden' value="1"  name="IfultimateSolution">
                        </tr>
                        <tr class="notReturn devback">
                            <th><?php echo $lang->problem->SolutionFeedback; ?></th>
                            <!--计划解决(变更)时间-日历-->
                            <td >
                                <div class='table-row' style="width:475px">
                                    <div class='table-col required' >
                                        <?php echo html::select('SolutionFeedback', $lang->problem->newsolutionFeedbackList, $problem->SolutionFeedback, "onchange='solutionFeedbackChange(this.value)' class='form-control chosen' "); ?>
                                    </div>
                                    <div class='table-col'>
                                        <div class='input-group  required  ' id ='PlannedDateOfChange' style="width:440px">
                                            <span class='input-group-addon fix-border'><?php echo $lang->problem->PlannedTimeOfChange;?></span>
                                            <?php echo html::input('PlannedTimeOfChange', $problem->PlannedTimeOfChange, "class='form-control form-datetime'"); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!--解决方案-富文本-->
                        <tr class="notUltimate notReturn devback">
                            <th>
                                <?php echo $lang->problem->solution;?>
                            </th>
                            <td colspan='4' class='required' id="ultimateSolutionTd"><?php echo html::textarea('solution', $problem->solution, "class='form-control' 
                            maxlength='2000' rows ='3'"); ?></td>
                        </tr>

                        <!--发生原因-富文本-->
                        <tr class="notUltimate notReturn devback">
                            <th><?php echo $lang->problem->reason; ?></th>
                            <td colspan='4' class='required' id="ultimateSolutionTd"><?php echo html::textarea('reason', $problem->reason, "class='form-control' maxlength='2000' rows ='3'"); ?></td>
                        </tr>
                        <!--修订记录-->
                    <?php if($problem->ReviewStatus == 'externalsendback'): ?>
                        <tr class="toHide notReturn devback">
                            <th><?php echo $lang->problem->revisionRecord; ?></th>
                            <td colspan='4'><?php echo html::input('revisionRecord', $problem->revisionRecord, "class='form-control'"); ?></td>
                        </tr>
                    <?php endif; ?>
                        <!--问题退回原因-富文本-->
                        <tr class="return devback">
                            <th><?php echo $lang->problem->ReasonOfIssueRejecting; ?></th>
                            <td colspan='4' class='required'><?php echo html::textarea('ReasonOfIssueRejecting', $problem->ReasonOfIssueRejecting, "class='form-control' placeholder='' rows ='3'"); ?></td>
                        </tr>
                        <!--本次操作备注-->
                        <tr class ="devback">
                            <th><?php echo $lang->problem->onlyComment ?></th>
                            <td colspan='4'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                        </tr>
                        <!--上传附件-文件-->
                        <tr class="notReturn devback">
                            <th><?php echo $lang->files; ?></th>
                            <td colspan='4'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                        </tr>
                        <tr class ="devback">
                            <!--处理人联系方式-输入框-->
                            <th><?php echo $lang->problem->TeleOfIssueHandler; ?></th>
                            <td colspan='4' class='required'><?php echo html::input('TeleOfIssueHandler', $problem->TeleOfIssueHandler, "class='form-control' maxlength='20'"); ?></td>

                        </tr>
                         <tr class ="devback">
                             <th><?php echo $lang->problem->feedbackDeptReview;?></th>
                             <td colspan="4" class='required'><?php echo html::select('feedbackToHandle[]', $managerUser, $problem->feedbackToHandle, "class='form-control chosen' multiple");?></td>
                         </tr>
                         <tr id="dealuser" class ="devback">
                             <th class='w-110px'><?php echo $lang->problem->nextUser; echo "<br>".$lang->problem->nextStatus[$problem->status];?></th>
                             <td colspan="4"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'");?></td>
                         </tr>

                         <tr >
                             <th class='w-110px'><?php echo $lang->problem->mailto;?></th>
                             <td colspan="4"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple");?>
                                 <span colspan='3' style="color: red;" id="tasktip"></span>
                             </td>
                         </tr>
                    <?php endif;?>
                   <tr class="hidden dev ">
                        <th><?php echo $lang->problem->plateMakAp;?></th>
                        <td class='required notrequired' colspan='2'><?php echo html::textarea('plateMakAp', $problem->plateMakAp, "class='form-control' placeholder=$lang->tips1");?></td>
                    </tr>

                    <tr class="hidden dev dev5">
                        <th><?php echo $lang->problem->plateMakInfo;?></th>
                        <td class='required notrequired' colspan='2'><?php echo html::textarea('plateMakInfo', $problem->plateMakInfo, "class='form-control' placeholder=$lang->tips2");?></td>
                    </tr>

                    <tr>
                        <td class='form-actions text-center' colspan='5'><?php echo html::submitButton() . html::backButton();?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php endif;?>
</div>
<table class='hidden'>
    <tbody id="relevantDeptTable">
    <tr id='relevantDept0'>
      <th class='w-110px'><?php echo $lang->problem->relevantDept;?></th>
      <td >
        <div class='table-row'>
          <div class='table-col'>
            <?php echo html::select('relevantUser[]', $users, '', "class='form-control' id='relevantUser0'");?>
          </div>
          <div class='table-col'>
            <div class='input-group'>
              <span class='input-group-addon fix-border'><?php echo $lang->problem->workload;?></span>
              <?php echo html::input('workload[]', '', "class='form-control'");?>
            </div>
          </div>
        </div>
      </td>
      <td class="c-actions">
        <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
        <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
      </td>
    </tr>
    </tbody>
</table>

<table class="hidden">
    <tbody id="productTable">
    <tr id='productTab0' class="<?php if($problem->IssueId && $problem->status != 'confirmed'){ echo 'hidden';}?> dev dev2 ">
        <th class='w-110px'><?php echo $lang->problem->product;?></th>
        <td id="productZone">
            <div class='table-row' style="width:400px">
                <div class='table-col product-th' data-id = '1'>
                    <?php echo html::select('product[]', $productList, "", "class='form-control productSelect' data-id = '' id= 'product0' onchange='productChange(this)'");?>
                </div>
                <div class='table-col' style="width:160px">
                    <div class='input-group'>
<!--                        <span class="input-group-btn fix-border" ><?php /*echo html::a($this->createLink('product','create'),'<i class="icon-plus"></i>'.$lang->problem->newproduct,'_blank','class="btn btn-info" onclick="return createpro()"')*/?></span>-->
                        <span class="input-group-btn addProductPlan" data-id='0' onclick="createpro()" > <span class="btn btn-info "><i class="icon-plus" title=""></i><?php echo $lang->problem->newproduct?></span></span>

                        <span class='input-group-addon fix-border'><?php echo $lang->problem->productPlan;?></span>
                        <?php echo html::select('productPlan[]', $productplan, "", "class='form-control w-100px productPlanSelect '  id=''");?>
                        <span class="input-group-btn addProductPlan" data-id='0' onclick="createPlan(this)" > <span class="btn btn-info "><i class="icon-plus" title=""></i>版本</span></span>
                        <span class="input-group-btn addStage " onclick="addProductItem(this)" data-id='0' id='codePlus0'> <span class="btn addItem"><i class="icon-plus" title=""></i></span></span>
                        <span class="input-group-btn addStage " onclick="delProductItem(this)" data-id='0' id='codeClose0'> <span class="btn addItem"><i class="icon-close" title=""></i></span></span>
                        <span class="input-group-btn fix-border" ><a href="javascript:;" onclick="proandver(this)" class="btn addItem " style="width:30px"><i class="icon-refresh"></i></a></span>

                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<?php
echo js::set('productPlan', $problem->productPlan);
echo js::set('product', $problem->product);
echo js::set('execution',$problem->execution ? $problem->execution : '' );
echo js::set('status',$problem->status  );
echo js::set('reviewStatus',$problem->ReviewStatus  );
echo js::set('project',$problem->projectPlan ?  $problem->projectPlan : '' );
echo js::set('type',$problem->type ?  $problem->type : '' );
echo js::set('id',$problem->id ?  $problem->id : '' );
echo js::set('fixtype',$problem->fixType  );
echo js::set('details',count($details));
echo js::set('scm',  $problem->ifReturn);
echo js::set('ifultimate',  $problem->IfultimateSolution);
echo js::set('ifsolutionFeedback',  $problem->SolutionFeedback);
echo js::set('createdBy',  $problem->createdBy);
echo js::set('standardVerifyItemNo',  html::select('standardVerify', $lang->problem->standardVerifyList, 'no', " class='form-control chosen' disabled"));
echo js::set('standardVerifyItem',  html::select('standardVerify', $lang->problem->standardVerifyList, 'no', " class='form-control chosen'"));
echo js::set('problemGradeItemNo',  html::select('problemGrade', $lang->problem->problemGradeList, 'notSerious', " class='form-control chosen' disabled"));
echo js::set('problemGradeItem',  html::select('problemGrade', $lang->problem->problemGradeList, 'notSerious', " class='form-control chosen' "));
echo js::set('solutionFeedbackItemNo',  html::select('SolutionFeedback', $lang->problem->solutionFeedbackList, 5, "class='form-control chosen' disabled"));
echo js::set('solutionFeedbackItem',  html::select('SolutionFeedback', $lang->problem->solutionFeedbackList, 5, "onchange='solutionFeedbackChange(this.value)' class='form-control chosen'"));
echo js::set('fixTypeSelect', html::select('fixType', $lang->problem->fixTypeList, $problem->fixType, "class='form-control chosen' onchange='selectfix()'"));
?>
<script>
    $(function()
    {
       if(status === 'assigned'){
           $('#status').parent().parent().addClass('hidden');
           $('#status').val('feedbacked');
           selectStatus('feedbacked');
           loadProductExecutions(project);//联动重置阶段
           $('#tasktip').text('<?php echo $this->lang->problem->saveSuccessTip;?>');
           /*只有第一次分析且反馈单状态是待反馈 时显示反馈单 表单*/
           if(execution > 0 || reviewStatus != "tofeedback"){
               $('.devback').addClass('hidden');
           }
       }else if(status == 'toclose'){
           $('#dealuser').addClass('hidden');
           selectStatus('toclose');
        }else if(status == 'confirmed'){
           $('#productZone').parent().addClass('hidden');
       }
        if(type == 'noproblem' || type == 'repeat' ) {
            $(window).load(function(){
                selectType(type,id);
            })
        }

        if(fixtype == 'second' || status === 'confirmed'){
            $('#execution').parent().parent().addClass('hidden');
        }else if(fixtype == 'project'){
            $('#execution').parent().parent().removeClass('hidden');
        }
        if(type != 'noproblem' && type != 'repeat' && status === 'assigned') {
            if(createdBy == 'guestcn' && reviewStatus == 'tofeedback'){
                $('.problemCauseClass').removeClass('hidden');
            }else {
                $('#problemCause').parent().parent().removeClass('hidden');
            }
        }

    })
    function selectfix(){
        var fixType = $('#fixType').val();
        var app = $('#app').val();
        $.get(createLink('problem', 'ajaxGetSecondLine', "fixType=" + fixType + "&app="+app), function(data)
        {
            $('#projectPlan_chosen').remove();
            $('#projectPlan').replaceWith(data);
            $('#projectPlan').chosen();
            loadProductExecutions('0');//联动重置阶段
        });
        if(fixType == ''){
            loadProductExecutions('0');//联动重置阶段
        }
        if(fixType == 'second'){
            $('#execution').parent().parent().addClass('hidden');
        }else{
            $('#execution').parent().parent().removeClass('hidden');
        }
        //loadProductExecutions(project);//联动重置阶段
    }


function selectStatus(status){
    var systemverify = $("input[type='radio']:checked").val();
    if(status === 'feedbacked' || status === 'solved')
    {
        $('.dev').removeClass('hidden');
        $('.notrequired').addClass('required')
	$('#plateMakAp').parent().parent().addClass('hidden');
    }
    else if(status === 'assigned')
    {
        $('.dev2').removeClass('hidden');
        $('.notrequired').removeClass('required');
        $('#productZone').parent().addClass('hidden');
    }
    else
    {
        $('.dev').addClass('hidden');
    }

        if(status === 'feedbacked'){
            $('.dev3').removeClass('hidden');
            $('.dev3 .notrequired').removeClass('required');
            if(systemverify === '1'){
                $('.test').addClass('required');
            }else{
                $('.test').addClass('hidden');
            }
        }
        else if(status === 'solved'){
            $('.dev3').removeClass('hidden');
            if(systemverify === '1'){
                $('.test').addClass('required');
            }else{
                $('.test').addClass('hidden');
                $('.yz').removeClass('required');
            }
        }
        //2022.4.27 tangfei
        if(status === 'solved')
        {
            $('.dev4').removeClass('hidden');
        }else{
            $('.dev4').addClass('hidden');
        }

        if(status === 'build')
        {
            $('.dev5').removeClass('hidden');
        }else{
            $('.dev5').addClass('hidden');
        }

        //20220314 新增 流程处理中，对下一节点处理人进行回写
        var problemid = "<?php echo $problem->id;?>";
        if(status === 'build'){
            //当前状态待制版   处理后：已通过  下一节点处理人：回显实验室测试人员  处理后状态：待测试
            getnextuser(problemid,status);
        }else if(status === 'waitverify'){
            //当前状态待测试   处理后：已通过  下一节点处理人：回显待制版流程处理人 处理后状态：待验版
            getnextuser(problemid,status);
        }else if(status === 'testfailed'){
            //当前状态待测试   处理后：未通过  下一节点处理人：回显待开发流程处理人 处理后状态：测试未通过
            getnextuser(problemid,status);
        }else if(status === 'testsuccess'){
            //当前状态待验版   处理后：已通过（系统部验证）  下一节点处理人：回显验证人员姓名 处理后状态：待验证
            getnextuser(problemid,status);
        }else if(status === 'verifysuccess'){
            //当前状态待验版   处理后：已通过（不需要系统部验证）  下一节点处理人：回显待制版处理人 处理后状态：待发布
            //当前状态待验证   处理后：已通过                   下一节点处理人：回显待制版处理人 处理后状态：待发布
            getnextuser(problemid,status);
        }else if(status === 'versionfailed'){
            //当前状态待验版   处理后：未通过  下一节点处理人：回显待开发流程处理人  处理后状态： 验版未通过
            getnextuser(problemid,status);
        }else if(status === 'verifyfailed'){
            //当前状态待验证   处理后：未通过  下一节点处理人：回显待开发流程处理人  处理后状态： 验证未通过
            getnextuser(problemid,status);
        }

        $('input[name=systemverify]').parent().parent().parent().addClass('hidden');
        $('#laboratorytest').parent().parent().addClass('hidden');
        var fixtype = $('#fixtype').val();
        if(status === 'feedbacked' && fixtype != 'noproblem'){
            $('#execution').parent().parent().removeClass('hidden');
            $('#execution').parent().addClass('required');
        }else{
            $('#execution').parent().parent().addClass('hidden');
            $('#execution').parent().removeClass('required');
        }

    }

    //20220311 新增 系统部选择
    function systemveif(){
        var systemverify = $("input[type='radio']:checked").val();
        var status = $('#status').val();
        if(status === 'feedbacked'){
            $('.dev3 .notrequired').removeClass('required');
            if(systemverify === '1'){
                $('.te').removeClass('hidden');
            }else{
                $('.te').addClass('hidden');
            }
        }else if(status === 'solved'){
            if(systemverify === '1'){
                $('.test').addClass('required');
                $('.te').removeClass('hidden');
                $('.yz').addClass('required');
            }else{
                $('.te').addClass('hidden');
                $('.yz').removeClass('required');
            }
        }
    }
    //根据状态，设置下一节点处理人
    function getnextuser(problemid,status){
        var link  = createLink('problem', 'ajaxGetNextUser', 'problemid=' + problemid +"&status="+status);
        $.post(link, function(data)
        {
            $('#dealUser').val(data);
            $('#dealUser').trigger('chosen:updated');
        })
    }


    $('#moreTips').bind('click', function()
    {
        $('.more-tips').attr('class', 'mo');
        $('#closeTips').removeClass('hidden');
        $('#closeTips').attr('class','task-toggle');
        $('#moreTips').addClass('hidden');

    });
    //20220311 新增收起
    $('#closeTips').bind('click', function()
    {
        $('.mo').attr('class', 'more-tips');
        $('#moreTips').removeClass('hidden');
        $('#closeTips').addClass('hidden');
    });

    var relevantIndex = 1;
    function addRelevantItem(obj)
    {
        var relevantObj  = $('#relevantDeptTable');
        var relevantHtml = relevantObj.clone();
        relevantIndex++;

        relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
        relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});

        relevantHtml.find('#relevantUser0').attr({'id':'relevantUser' + relevantIndex});
        relevantHtml.find('#relevantDept0').attr({'id':'relevantDept' + relevantIndex});

        var objIndex = $(obj).attr('data-id');
        $('#relevantDept' + objIndex).after(relevantHtml.html());

        $('#relevantUser' + relevantIndex).attr('class','form-control chosen');
        $('#relevantUser' + relevantIndex).chosen();

    }

function delRelevantItem(obj)
{
    var objIndex = $(obj).attr('data-id');
    $('#relevantDept' + objIndex).remove();
}



/*$(document).ready(function(){
    getproduct();
   var projectPlan = $('#projectPlan').val();
    loadProductExecutions(projectPlan);
});
function getproduct(){
    $.get(createLink('problem', 'ajaxGetProduct'), function(productlist)
    {
        $('#product_chosen').remove();
        $('#product').replaceWith(productlist);
        $('#product').val(product);
        $('#product').chosen();
        $('#product').change();
    });
}*/
//$('#product').change(function()
/*$('#product').live('change',function()
{
    var productID = $(this).val();
    $.get(createLink('problem', 'ajaxGetProductPlan', "productID=" + productID), function(planList)
    {
        $('#productPlan_chosen').remove();
        $('#productPlan').replaceWith(planList);
         if(productID == '99999') {
             $('#productPlan').val('1');
             $('#productPlan').chosen();
         }else{
             $('#productPlan').val(productPlan);
             $('#productPlan').chosen();
         }

    });
});
//产品选择后，给版本链接传值
function selectproduct(value){
    if(value === '0'){
        var href=$("#createp").attr('href').slice(0,-19).concat(value+".html?onlybody=yes");
    }else{
        var href=$("#createp").attr('href');
        /!* var href = href.slice(8,28);*!/
        if(href.indexOf('cfitpmp') !== -1){
            var href=$("#createp").attr('href').slice(0,28).concat(value+".html?onlybody=yes");
        }else{
            var href=$("#createp").attr('href').slice(0,20).concat(value+".html?onlybody=yes");
        }
    }
    var alink = '<a href= "'+href+'" class="btn btn-info" data-app="product" id="createp" onclick="return createPlan()" target="_blank" rel="noopener noreferrer" style="border-radius: 0px 2px 2px 0px; border-left-color: transparent;"><i class="icon-plus" title=""></i>版本</a>;'
    $('#createp').replaceWith(alink);

}*/

//问题类型下拉框选择事件
function selectType(type,id){

    if(type === 'noproblem' || type === 'repeat'){
        $.get(createLink('problem', 'ajaxGetProblem', "id=" + id), function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'assigned'){
              if(obj.IssueId === null ){
                    $('#dealUser').val(obj.createdBy).trigger("chosen:updated");
                }else{
                  if(createdBy == 'guestcn'){
                      $('#dealUser').val(obj.qzCloseUser).trigger("chosen:updated");
                  }else{
                      $('#dealUser').val(obj.jxCloseUser).trigger("chosen:updated");
                  }
                   // $('#dealUser').val(obj.apiUser).trigger("chosen:updated");

                }
                $('#dealuser').addClass('hidden');
                $('.dev2').addClass('hidden');
                $('.notrequired').addClass('required');
                $('#laboratorytest').parent().parent().addClass('hidden');
                if((obj.createdBy != 'guestjx' && obj.createdBy != 'guestcn') || reviewStatus != "tofeedback"){
                     $('#reason').parent().parent().addClass('hidden');
                     $('#solution').parent().parent().addClass('hidden');
                }

                $('#fixType').parent().parent().parent().parent().addClass('hidden');
                $('input[name=systemverify]').parent().parent().parent().addClass('hidden');
                $('#execution').parent().parent().addClass('hidden');
                var user = $('#dealUser').val();
                if(type === 'repeat'){
                    $('#repeatProblem').parent().parent().removeClass('hidden');
                }else{
                    $('#repeatProblem').parent().parent().addClass('hidden');
                }
                $.get(createLink('problem', 'ajaxGetDealUser'), function(data)
                {
                    $('#dealUser_chosen').remove();
                    $('#dealUser').replaceWith(data);
                    $('#dealUser').val(user);
                    $('#dealUser').chosen();
                });
                $('#tasktip').text('');
            }
        });
        if(createdBy == 'guestcn' && reviewStatus == 'tofeedback'){
            $('.problemCauseClass').addClass('hidden');
        }else {
            $('#problemCause').parent().parent().addClass('hidden');
        }
        $('#fixType_chosen').remove();
        $('#fixType').replaceWith(fixTypeSelect);
        $('#fixType').val('');
        $('#fixType').chosen();
        selectfix();
    }else{
        $('#dealuser').removeClass('hidden');
        $('#dealUser').val('').trigger("chosen:updated");
        $('.dev2').removeClass('hidden');
       // $('.notrequired').removeClass('required');
        $('#laboratorytest').parent().parent().addClass('hidden');
        $('#reason').parent().parent().removeClass('hidden');
        $('#solution').parent().parent().removeClass('hidden');
        $('#fixType').parent().parent().parent().parent().removeClass('hidden');
        //$('#product').parent().parent().parent().parent().addClass('required');
        $('input[name=systemverify]').parent().parent().parent().addClass('hidden');
        $('#execution').parent().parent().removeClass('hidden');
        $('#repeatProblem').parent().parent().addClass('hidden');
        $.get(createLink('problem', 'ajaxGetDealUser','type=other'), function(data)
        {
            $('#dealUser_chosen').remove();
            $('#dealUser').replaceWith(data);
            $('#dealUser').chosen();
        });
       if(status === 'assigned'){
            $('#tasktip').text('<?php echo $this->lang->problem->saveSuccessTip?>');
           if(createdBy == 'guestcn' && reviewStatus == 'tofeedback'){
               $('.problemCauseClass').removeClass('hidden');
           }else {
               $('#problemCause').parent().parent().removeClass('hidden');
           }
       }
        $('.devback').removeClass('hidden');
    }

    if(createdBy == 'guestcn'){
        if(type === 'noproblem'){
            noproblem(true);
            solutionFeedbackChange(5);
        }else {
            noproblem(false);
            solutionFeedbackChange($('#SolutionFeedback').val());
        }
    }
}
//刷新操作
/*$("#proandver").click(function(){
        var productID = $('#product').val();
        $.get(createLink('problem', 'ajaxGetProductPlan', "productID=" + productID), function(planList)
        {
            $('#productPlan_chosen').remove();
            $('#productPlan').replaceWith(planList);
            $('#productPlan').val(productPlan);
            $('#productPlan').chosen();
        });
        if(productID == 0){
            $.get(createLink('problem', 'ajaxGetProduct'), function(productlist)
            {
                $('#product_chosen').remove();
                $('#product').replaceWith(productlist);
                $('#product').val(productlist);
                $('#product').chosen();
            });
        }
    }
)*/
//验证产品版本
/* $("form").submit(function(){
     var product = $('#product').val();
     var plan = $('#productPlan').val();
     if(product == '99999' && plan != '1'){
         js:alert('所属产品选无,产品版本只能选择无!');
         return false;
     }
 })*/
</script>
<?php include '../../../common/view/footer.html.php';?>
