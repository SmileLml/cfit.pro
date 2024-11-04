<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2>批量审批年度计划<?php echo  ' - ' . $title?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->reviewResult; ?></th>
                    <td colspan='3'>
                        <?php echo html::radio('result', $lang->projectplan->reviewResultList, "","onchange='getResult(this)'"); ?>
                    </td>
                </tr>
                <?php if ($plan->reviewStage == 4): ?>
                <tr id="leaderOrOther">
                    <th class='w-110px'><?php echo $lang->projectplan->leaderOther; ?></th>
                    <td colspan='3'>
                        <?php echo html::checkbox("leader", $cto, 'hetielin', "onClick='return false' style='cursor:not-allowed;'"); ?>
                        <?php echo html::checkbox("leaderOther", $leader, ''); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($plan->reviewStage == 2): ?>
                <tr>
                    <th><?php echo '指派架构师'; ?></th>
                    <td colspan='3' class="required"><?php echo html::select('reviewer[]', $users, $architect, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th><?php echo $lang->projectplan->mailto; ?></th>
                    <td colspan='3'><?php echo html::select('mailto[]', $users, $plan->mailto, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th class='w-110px'><?php echo $lang->projectplan->reviewComment; ?></th>
                    <td colspan='3'  id="commentWrap">
                        <?php echo html::textarea('comment', '', "class='form-control'"); ?>
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
    $(function(){
        $("input[type=radio][name=result]").change(function (){


            if(this.value == 'reject'){
                $("#commentWrap").addClass('required');
            }else{
                $("#commentWrap").removeClass('required');
            }
        });
    })
</script>
<script>

    function getResult(res)
    {
        var result = res.value;
        if(result == 'reject'){
            $("#leaderOrOther").addClass('hidden');
        }else{
            $("#leaderOrOther").removeClass('hidden');
        }
    }
</script>
