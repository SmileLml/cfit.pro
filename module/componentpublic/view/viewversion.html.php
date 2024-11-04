<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentpublic->viewversion; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentpublic->version; ?></th>
                        <td><?php echo $version->version; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->updatedDate; ?></th>
                        <td><?php echo $version->updatedDate ;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->desc; ?></th>
                        <td><?php echo $version->desc ;?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
    </form>
</div>
</div>
<script>

</script>


<?php include '../../common/view/footer.html.php'; ?>
