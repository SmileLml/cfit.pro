<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->putproduction->statusList[$putproduction->status];?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->putproduction->dealResult;?></th>
                    <td><?php echo html::select('dealResult', $lang->putproduction->dealResultList , '', "class='form-control chosen' required onchange='changeDealResult()'");?></td>
                </tr>
                <?php if($putproduction->status == 'waitcm'): ?>
                    <?php if($putproduction->stage == '1'): ?>
                        <?php if(empty($putproduction->sftpPath)):?>
                            <tr id = 'sftpPathTr' class="hidden sftpPathTrClass">
                                <th><?php echo $lang->putproduction->sftpPath;?></th>
                                <td colspan='3' id="sftpPathTd"><?php echo html::input('sftpPath[]', '', "class='form-control' required");?></td>
                                <td>
                                    <a href="javascript:void(0)" onclick="addSftpPath(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteSftpPath(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                </td>
                            </tr>
                        <?php else:?>
                                <?php foreach (json_decode($putproduction->sftpPath) as $sftpPath):?>
                                <tr id = 'sftpPathTr' class="hidden sftpPathTrClass">
                                    <th><?php echo $lang->putproduction->sftpPath;?></th>
                                    <td colspan='3' id="sftpPathTd"><?php echo html::input('sftpPath[]', $sftpPath, "class='form-control' required");?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="addSftpPath(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                        <a href="javascript:void(0)" onclick="deleteSftpPath(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                        <?php endif;?>
                    <?php else: ?>
                            <tr id = 'sftpPathTr' class="hidden sftpPathTrClass">
                                <th><?php echo $lang->putproduction->releaseId ;?></th>
                                <td colspan='3'><?php echo html::select('releaseId[]', $releases, $putproduction->releaseId, "class='form-control chosen' required multiple");?></td>
                            </tr>
                    <?php endif;?>
                <?php endif; ?>
                <tr>
                    <th><?php echo $lang->putproduction->dealMessage;?></th>
                    <td colspan='3' id="suggestTd"><?php echo html::textarea('dealMessage', '', "class='form-control' style='height:150px'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                </tr>
                <tr><td></td></tr> <tr><td></td></tr> <tr><td></td></tr> <tr><td></td></tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
    <table class="hidden">
        <tbody id="lineDemo">
        <tr id = 'sftpPathTr' class="sftpPathTrClass">
            <th><?php echo $lang->putproduction->sftpPath;?></th>
            <td colspan='3' id="sftpPathTd"><?php echo html::input('sftpPath[]', '', "class='form-control' required");?></td>
            <td>
                <a href="javascript:void(0)" onclick="addSftpPath(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                <a href="javascript:void(0)" onclick="deleteSftpPath(this)" class="btn btn-link"><i class="icon-close"></i></a>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="hidden">
<script>
    //增加媒体介质
    function addSftpPath(obj)
    {
        $(obj).parent().parent().after($('#lineDemo').children(':first-child').clone());
    }

    //删除媒体介质
    function deleteSftpPath(obj)
    {
        var moveDivs = document.getElementsByClassName("sftpPathTrClass");
        if(moveDivs.length>2){
            $(obj).parent().parent().remove();
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>