<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->demand->reviewNodeStatusLableList[$demand->delayStatus];?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->demand->originalResolutionDate; ?></th>
                    <td><?php echo html::input('originalResolutionDate', date('Y-m-d', strtotime($demand->originalResolutionDate)), "class='form-control form-date' disabled");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->delayResolutionDate; ?></th>
                    <td><?php echo html::input('delayResolutionDate', date('Y-m-d', strtotime($demand->delayResolutionDate)), "class='form-control form-date' disabled");?></td>
                </tr>
                <!--<tr>
                    <th><?php /*echo $lang->demand->unitAgree; */?></th>
                    <td><?php /*echo html::select('unitAgree', $lang->demand->unitAgreeList, $demand->unitAgree, "class='form-control chosen' disabled");*/?></td>
                </tr>-->
                <tr>
                    <th><?php echo $lang->demand->delayReason; ?></th>
                    <td colspan='2'><div style="max-height: 180px;overflow-y: auto;word-break:break-all;word-wrap:break-word;"><?php echo $demand->delayReason; ?></div></td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->reviewResult;?></th>
                    <td><?php echo html::select('result', $lang->demand->reviewList, '', "class='form-control chosen' required");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->suggest;?></th>
                    <td colspan='2'><?php echo html::textarea('suggest', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <!--保存初始审核节点-->
                        <input type="hidden" name = "delayVersion" value="<?php echo $demand->delayVersion; ?>">
                        <input type="hidden" name = "delayStage" value="<?php echo $demand->delayStage; ?>">
                        <input type="hidden" name = "delayStatus" value="<?php echo $demand->delayStatus; ?>">

                        <?php echo html::submitButton('提交') . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
