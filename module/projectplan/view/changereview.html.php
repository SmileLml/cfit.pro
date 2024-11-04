<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    #change{
        text-overflow: ellipsis;
        overflow: hidden;
        white-space:  normal;
    }
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplan->planChange . ' - ' . $title?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
<!--                部门负责人页面-->
                <?php if ($plan->changeStage == 1): ?>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                    <td colspan='3'>
                        <?php echo html::radio('result', $lang->projectplan->reviewResultList, ""); ?>
                    </td>
                </tr>
<!--                平台架构部接口人-->
                <?php elseif($plan->changeStage == 2):?>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                    <td >
                        <?php echo html::radio('result', $lang->projectplan->changeRadioList, ""); ?>
                    </td>
                    <th id="jgstitle" style="display:none;" class='w-110px'><?php echo $lang->projectplan->platformarchitect; ?></th>
                    <td id="jgsuser" style="display:none;" class="required">
                        <?php echo html::select('architect[]', $users,$architectUser, "class='form-control chosen' multiple"); ?>
                    </td>
                </tr>
                <?php elseif ($plan->changeStage == 3): ?>
                    <tr>
                        <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                        <td colspan='3'>
                            <?php echo html::radio('result', $lang->projectplan->reviewResultList, ""); ?>
                        </td>
                    </tr>
                <?php elseif($plan->changeStage == 4):?>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                    <td colspan='3'>
                        <?php echo html::radio('result', $lang->projectplan->reviewResultList, ""); ?>
                    </td>
                </tr>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->isNeedLeader; ?></th>
                    <td colspan='3'>
                        <?php echo html::checkbox('leader', $lang->projectplan->changeCheckList, ""); ?>
                    </td>
                </tr>
                <?php elseif($plan->changeStage == 5):?>
                    <tr>
                        <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                        <td colspan='3'>
                            <?php echo html::radio('result', $lang->projectplan->reviewResultList, ""); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-110px'><?php echo $lang->projectplan->isNeedLeader; ?></th>
                        <td colspan='3'>
                            <?php unset($lang->projectplan->changeCheckList['isCTO']);?>
                            <?php echo html::checkbox('leader', $lang->projectplan->changeCheckList, $leaderApproval); ?>
                        </td>
                    </tr>
                <?php else:?>
                    <tr>
                        <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                        <td colspan='3'>
                            <?php echo html::radio('result', $lang->projectplan->reviewResultList, ""); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th><?php echo $lang->projectplan->mailto; ?></th>
                    <td colspan='3'><?php echo html::select('mailto[]', $users, $plan->changeMailto, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->reviewComment; ?></th>
                    <td colspan='3' id="commentWrap">
                        <?php echo html::textarea('comment', '', "class='form-control'"); ?>
                    </td>
                </tr>
                <tr>
                    <th class='w-200px'><?php echo "本次变更内容"; ?></th>
                    <td colspan='3'>
                    <?php echo html::textarea('planRemark', $planRemark, "class='form-control'"); ?>
                    </td>
                </tr>
                <tr>

                    <td colspan='4' class="text-red text-center strong">
                        <?php echo $lang->projectplan->changereviewPromptInfo; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 30px;padding-bottom: 30px;" class='form-actions text-center' colspan='4'>
                        <?php echo html::submitButton(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>
    $(document).ready(function(){
        window.editor['planRemark'].readonly(true);

        $("input[type=radio][name=result]").change(function (){


            if(this.value == 'reject'){
                $("#commentWrap").addClass('required');
            }else{
                $("#commentWrap").removeClass('required');
            }

            if(this.value == 'report'){
                $("#jgstitle").show();
                $("#jgsuser").show();
            }else{
                $("#jgstitle").hide();
                $("#jgsuser").hide();
            }
        });
    })
</script>