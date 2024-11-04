<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::set('scm',  $problem->ifReturn)?>
<?php js::set('ifultimate',  $problem->IfultimateSolution)?>
<?php js::set('ifsolutionFeedback',  $problem->SolutionFeedback)?>
<?php js::set('standardVerifyItemNo',  html::select('standardVerify', $lang->problem->standardVerifyList, 'no', " class='form-control chosen' disabled"));?>
<?php js::set('standardVerifyItem',  html::select('standardVerify', $lang->problem->standardVerifyList, $problem->standardVerify, " class='form-control chosen'"));?>
<style>
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
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $title; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <?php if($problem->createdBy != 'guestjx'): ?>
                    <input type='hidden' value="0"  name="ifReturn">
                <tr>
                    <th><?php echo $lang->problem->problemGrade; ?>
                        <i class="icon icon-help tooltip-diy"><span class="tooltip-text"><?php echo $lang->problem->problemGradePrompt;?></span></i>
                    </th>
                    <td class='required'><?php echo html::select('problemGrade', $lang->problem->problemGradeList, $problem->problemGrade, " class='form-control chosen' "); ?></td>
                    <th><?php echo $lang->problem->SolutionFeedback; ?></th>
                    <td class='required'><?php echo html::select('SolutionFeedback', $lang->problem->solutionFeedbackList, $problem->SolutionFeedback, "onchange='solutionFeedbackChange(this.value)' class='form-control chosen' "); ?></td>
                </tr>
                    <tr>
                        <!--是否为最终方案-下拉选择框-->
                        <th><?php echo $lang->problem->IfultimateSolution; ?></th>
                        <td class='required' id='IfultimateSolutionTd'>
                            <?php
                            $IfultimateSolutionDisable = $problem->SolutionFeedback == 5 ? "disabled" :"";
                            echo html::select('IfultimateSolution', $lang->problem->ifultimateSolutionList, $problem->IfultimateSolution, "onchange='ifultimateChanged(this.value)' class='form-control chosen'  $IfultimateSolutionDisable"); ?>
                        </td>
                        <th id ='standardVerifyId' class="hidden"><?php echo $lang->problem->standardVerify; ?></th>
                        <td class=""><?php echo html::select('standardVerify', $lang->problem->standardVerifyList, $problem->standardVerify, " class='form-control chosen' "); ?></td>
                    </tr>
                    <!--初步反馈-富文本-->
                    <tr class="toHide">
                        <th><?php echo $lang->problem->Tier1Feedback; ?></th>
                        <td colspan='3' class='required'><?php echo html::textarea('Tier1Feedback', $problem->Tier1Feedback, "class='form-control' placeholder='  ' maxlength='500' rows ='3'"); ?></td>
                    </tr>

                    <!--最终解决方案-富文本-->
                    <tr class="notUltimate">
                        <th><?php echo $lang->problem->ultimateSolution; ?></th>
                        <td colspan='3' class='required' id="ultimateSolutionTd"><?php echo html::textarea('solution', $problem->solution, "class='form-control' placeholder='当“解决方式”为“非应用问题”时，请在此描述原因，请“是否最终解决方案”选择“是”' 
                    maxlength='2000' rows ='3'"); ?></td>
                    </tr>
                    <!--发生原因-富文本-->
                    <tr class="notUltimate toHide">
                        <th><?php echo $lang->problem->reason; ?></th>
                        <td colspan='3' class='required' id="ultimateSolutionTd"><?php echo html::textarea('reason', $problem->reason, "class='form-control' maxlength='2000' rows ='3'"); ?></td>
                    </tr>
                    <!--影响范围-富文本-->
                    <tr class="toHide">
                        <th><?php echo $lang->problem->EditorImpactscope; ?></th>
                        <td colspan='3' class='required'><?php echo html::textarea('EditorImpactscope', $problem->EditorImpactscope, "class='form-control' placeholder='' rows ='3'"); ?></td>
                    </tr>
                    <!--解决该问题的变更-富文本-->
                    <tr class="solvingTheIssue toHide">
                        <th><?php echo $lang->problem->ChangeSolvingTheIssue; ?></th>
                        <td colspan='3'><?php echo html::textarea('ChangeSolvingTheIssue', $problem->ChangeSolvingTheIssue, "class='form-control'  maxlength='200' rows ='3'"); ?></td>
                    </tr>
                    <tr>
                        <!--处理人联系方式-输入框-->
                        <th><?php echo $lang->problem->TeleOfIssueHandler; ?></th>
                        <td class='required'><?php echo html::input('TeleOfIssueHandler', $problem->TeleOfIssueHandler, "class='form-control' maxlength='20'"); ?></td>
                        <!--计划解决(变更)时间-日历-->
                        <th class="toHide"><?php echo $lang->problem->PlannedTimeOfChange; ?></th>
                        <td class='required toHide'><?php echo html::input('PlannedTimeOfChange', $problem->PlannedTimeOfChange, "class='form-control form-datetime'"); ?></td>
                    </tr>
                    <tr class="toHide">
                        <!--计划提交变更日期-日历-->
                        <th ><?php echo $lang->problem->PlannedDateOfChangeReport; ?></th>
                        <td class='required'><?php echo html::input('PlannedDateOfChangeReport', $problem->PlannedDateOfChangeReport, "class='form-control form-date'"); ?></td>
                        <!--计划变更日期-日历-->
                        <th ><?php echo $lang->problem->PlannedDateOfChange; ?></th>
                        <td class='required'><?php echo html::input('PlannedDateOfChange', $problem->PlannedDateOfChange, "class='form-control form-date'"); ?></td>
                    </tr>
                    <!--对应产品-文本框-->
                    <tr class="toHide">
                        <th><?php echo $lang->problem->CorresProduct;?></th>
                        <td colspan='3' class='required'><?php echo html::input('CorresProduct', $problem->CorresProduct, "class='form-control'");?></td>
                    </tr>
                    <!--问题退回原因-富文本-->
                    <tr class="hide">
                        <th><?php echo $lang->problem->ReasonOfIssueRejecting; ?></th>
                        <td colspan='3' class='required'><?php echo html::textarea('ReasonOfIssueRejecting', $problem->ReasonOfIssueRejecting, "class='form-control' placeholder='' rows ='3'"); ?></td>
                    </tr>
                    <!--修订记录-富文本-->
                <?php if($problem->ReviewStatus == 'externalsendback'): ?>
                    <tr class="toHide">
                        <th><?php echo $lang->problem->revisionRecord; ?></th>
                        <td colspan='3'><?php echo html::input('revisionRecord', $problem->revisionRecord, "class='form-control'"); ?></td>
                    </tr>
                <?php endif; ?>
                    <!--上传附件-文件-->
                    <tr>
                        <th><?php echo $lang->files; ?></th>
                        <td colspan='3'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th class='w-140px'><?php echo $lang->problem->ifRecive ?></th>
                        <td><?php echo html::radio('ifReturn', array_filter($lang->problem->ifReturnList), $problem->ifReturn, "onchange='returnChanged(this.value)' class='text-center'"); ?></td>
                        <input type='hidden' value="1"  name="IfultimateSolution">
                    </tr>
                    <tr class="notReturn">
                        <th><?php echo $lang->problem->SolutionFeedback; ?></th>
                        <td class='required'><?php echo html::select('SolutionFeedback', $lang->problem->newsolutionFeedbackList, $problem->SolutionFeedback, "onchange='solutionFeedbackChange(this.value)' class='form-control chosen' "); ?></td>
                        <!--计划解决(变更)时间-日历-->
                        <th><?php echo $lang->problem->PlannedTimeOfChange; ?></th>
                        <td class='required'><?php echo html::input('PlannedTimeOfChange', $problem->PlannedTimeOfChange, "class='form-control form-datetime'"); ?></td>
                    </tr>
                    <!--解决方案-富文本-->
                    <tr class="notUltimate notReturn">
                        <th>
                            <?php echo $lang->problem->solution;?>
                        </th>
                        <td colspan='3' class='required' id="ultimateSolutionTd"><?php echo html::textarea('solution', $problem->solution, "class='form-control' 
                            maxlength='2000' rows ='3'"); ?></td>
                    </tr>

                    <!--发生原因-富文本-->
                    <tr class="notUltimate notReturn">
                        <th><?php echo $lang->problem->reason; ?></th>
                        <td colspan='3' class='required' id="ultimateSolutionTd"><?php echo html::textarea('reason', $problem->reason, "class='form-control' maxlength='2000' rows ='3'"); ?></td>
                    </tr>
                    <!--修订记录-->
                <?php if($problem->ReviewStatus == 'externalsendback'): ?>
                    <tr class="notReturn">
                        <th><?php echo $lang->problem->revisionRecord; ?></th>
                        <td colspan='3'><?php echo html::input('revisionRecord', $problem->revisionRecord, "class='form-control'"); ?></td>
                    </tr>
                <?php endif; ?>
                    <!--问题退回原因-富文本-->
                    <tr class="return">
                        <th><?php echo $lang->problem->ReasonOfIssueRejecting; ?></th>
                        <td colspan='3' class='required'><?php echo html::textarea('ReasonOfIssueRejecting', $problem->ReasonOfIssueRejecting, "class='form-control' placeholder='' rows ='3'"); ?></td>
                    </tr>
                    <!--本次操作备注-->
                    <tr>
                        <th><?php echo $lang->problem->onlyComment ?></th>
                        <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                    </tr>
                    <!--上传附件-文件-->
                    <tr class="notReturn">
                        <th><?php echo $lang->files; ?></th>
                        <td colspan='3'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                    </tr>
                    <tr>
                        <!--处理人联系方式-输入框-->
                        <th><?php echo $lang->problem->TeleOfIssueHandler; ?></th>
                        <td class='required' colspan='3'><?php echo html::input('TeleOfIssueHandler', $problem->TeleOfIssueHandler, "class='form-control' maxlength='20'"); ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th><?php echo $lang->problem->feedbackDeptReview;?></th>
                    <td class="required" colspan='3'><?php echo html::select('feedbackToHandle[]', $managerUser, $problem->feedbackToHandle, "class='form-control chosen' multiple");?>
                </tr>

                <tr>
                    <td class='form-actions text-center' colspan='4'><?php echo html::submitButton() . html::backButton(); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
