<?php include '../../../common/view/header.lite.html.php';?>
<script>
    function setDownloading()
    {
        if(navigator.userAgent.toLowerCase().indexOf("opera") > -1) return true;   // Opera don't support, omit it.

        $.cookie('downloading', 0);
        time = setInterval("closeWindow()", 300);
        return true;
    }

    function closeWindow()
    {
        if($.cookie('downloading') == 1)
        {
            parent.$.closeModal();
            $.cookie('downloading', null);
            clearInterval(time);
        }
    }
</script>
<main id="main">
    <div class="container">
        <div id="mainContent" class='main-content load-indicator'>
            <div class='main-header'>
                <h2><?php echo $lang->export;?></h2>
            </div>
            <form action='<?php echo $this->createLink('report', 'exportbugtester', array('browseType' => 'export'));?>' method='post' target='hiddenwin' onsubmit='setDownloading();' style='margin:20px 0px;'>
                <table class='table table-form' style='padding:30px'>
                    <tr>
                        <th class='w-150px'><?php echo $lang->setFileName;?></th>
                        <td><?php echo html::input('fileName', $fileName, "class='form-control'");?></td>
                        <td>
                            <?php
                            unset($lang->exportFileTypeList['csv']);
                            unset($lang->exportFileTypeList['xml']);
                            echo html::select('fileType',   $lang->exportFileTypeList, '', 'class="form-control"');
                            ?>
                        </td>
                        <td><?php echo html::submitButton('', '', 'btn btn-primary');?></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</main>
<?php include '../../../common/view/footer.lite.html.php';?>
