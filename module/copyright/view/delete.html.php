<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->copyright->delete;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform-submit'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->copyright->comment ;?></th>
                    <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control' rows='5'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='4'>
                        <?php echo html::submitButton($lang->copyright->save);?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<?php include '../../common/view/footer.html.php';?>
