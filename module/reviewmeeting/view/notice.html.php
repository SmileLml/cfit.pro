<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
    .task-toggle .icon{display: inline-block; transform: rotate(90deg);}
    .more-tips{display: none;}
    .close-tips{display: none}
    .remarkshow{display: none}
    body{background-color: #fff}
    .main-content{box-shadow: none}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->reviewmeeting->notice;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->reviewmeeting->mailTitle;?></th>
                    <td>
                        <?php echo html::input('mailtitle', $mailTitle, "class='form-control' readonly");?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewmeeting->address;?></th>
                    <td>
                        <?php echo html::select('reviewer[]', $users, $reviewer, "class='form-control chosen' multiple required");?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewmeeting->mailto;?></th>
                    <td>
                        <?php echo html::select('mailto[]', $users, $created, "class='form-control chosen' multiple ");?>
                    </td>
                </tr>
                <tr >
                    <th><?php echo $lang->reviewmeeting->mailContent;?></th>
                    <td colspan='2'><?php echo html::textarea('mailContent', $mailContent, "class='form-control textarea'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
