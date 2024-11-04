<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <?php
            if($problem->changeConsumed[0]->createdDate<$this->lang->problem->oldVersionTime){
                $this->lang->problem->reviewNodeStatusLableList['toManager']='分管领导处理';
            }
            ?>
            <h2><?php echo $lang->problem->reviewNodeStatusLableList[$problem->changeStatus];?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class="w-130px"><?php echo $lang->problem->changeOriginalResolutionDate; ?></th>
                    <td colspan='2'><?php echo html::input('changeOriginalResolutionDate', strpos($problem->changeOriginalResolutionDate,'0000-00-00') !== false ? '':date('Y-m-d H:i:s',   strtotime($problem->changeOriginalResolutionDate)), "class='form-control form-date' disabled");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->problem->changeResolutionDate; ?></th>
                    <td colspan='2'><?php echo html::input('changeResolutionDate', date('Y-m-d H:i:s', strtotime($problem->changeResolutionDate)), "class='form-control form-date' disabled");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->problem->changeReason; ?></th>
                    <td colspan='2'><?php echo $problem->changeReason; ?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->problem->changeCommunicate; ?></th>
                    <td colspan='2' ><?php echo $problem->changeCommunicate; ?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->problem->reviewResult;?></th>
                    <td colspan='2'><?php echo html::select('result', $problem->changeStatus=='toProductManager'?array_merge($lang->problem->reviewList,['pass' => '通过（不上报）','report'=>'通过（上报）']):$lang->problem->reviewList, '', "class='form-control chosen' required onchange=changeResult(this.value)");?></td>
                </tr>
                <?php if ($problem->changeStatus=='toProductManager'):?>
                    <tr style="display: none" id="toManagerId">
                        <th><?php echo $lang->problem->toManager; ?></th>
                        <td colspan='2'><?php echo html::select('toManager', $toManager, '', "class='form-control chosen' required");?></td>

                    </tr>
                <?php endif;?>
                <tr>
                    <th><?php echo $lang->problem->suggest;?></th>
                    <td colspan='2'><?php echo html::textarea('suggest', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <!--保存初始审核节点-->
                        <input type="hidden" name = "changeVersion" value="<?php echo $problem->changeVersion; ?>">
                        <input type="hidden" name = "changeStage" value="<?php echo $problem->changeStage; ?>">
                        <input type="hidden" name = "changeStatus" value="<?php echo $problem->changeStatus; ?>">

                        <?php echo html::submitButton('提交') . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
<script>
    function changeResult(status)
    {
        if(status == 'reject'){
            $('#toManagerId').hide()
            $('#suggest').parent().addClass('required');
        }
        else if(status == 'report'){
            $('#toManagerId').show()
        }
        else {
            $('#suggest').parent().removeClass('required');
            $('#toManagerId').hide()

        }
    }
</script>
