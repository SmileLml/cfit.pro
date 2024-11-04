<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.input-group-addon{min-width: 150px;}
.input-group{margin-bottom: 2px;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->copyrightqz->docDownload;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                    <tr>
                        <th>
                            <?php echo '模块';?>
                        </th>
                        <td>
                            <?php echo html::input('module', 'copyrightqz', "class='form-control'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->docDownload;?>
                        </th>
                        <td colspan="3" class='required'>
                            <?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->copyrightqz->filelist;?></th>
                        <td>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if($docDownload){
                                        echo $this->fetch('file', 'printFiles', array('files' => $docDownload, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                    }else{
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>

</script>