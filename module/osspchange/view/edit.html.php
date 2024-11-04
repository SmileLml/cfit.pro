<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->osspchange->edit;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->proposer;?></th>
                    <td colspan='4'><?php echo html::select('proposer', $users, $osspchange->proposer, 'class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->osspchange->title;?></th>
                    <td colspan='4'><?php echo html::textarea('title', $osspchange->title, 'class="form-control" required style="height:130px"');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->background;?></th>
                    <td colspan='4'><?php echo html::textarea('background', $osspchange->background, 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->content;?></th>
                    <td colspan='4'><?php echo html::textarea('content', $osspchange->content, 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->osspchange->filelist;?></th>
                    <td colspan='4'>
                        <div class='detail'>
                            <div class='detail-content article-content'>
                                <?php
                                if($osspchange->files){
                                    echo $this->fetch('file', 'printFiles', array('files' => $osspchange->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                }else{
                                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->files;?></th>
                    <td colspan='4' class="required"><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                </tr>
                <tr>
                    <th class="w-120px"></th>
                    <td class='form-actions text-center' colspan='4'>
                        <input type="hidden" name="type" value="save">
                        <button type="button" class="btn btn-wide btn-primary saveBtn">保存</button>
                        <button type="button" class="btn btn-wide btn-primary submitBtn">提交</button>
                        <button type="button" class="btn btn-wide btn-back backBtn">返回</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(".saveBtn").click(function () {
        $("[name='type']").val('save');
        $("#dataform").submit()
    })
    $(".submitBtn").click(function () {
        $("[name='type']").val('submit')
        $("#dataform").submit()
    })
    $(".backBtn").click(function () {
        window.history.go(-1);
    })
</script>
<?php include '../../common/view/footer.html.php'?>
