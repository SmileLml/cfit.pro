
<?php include '../../common/view/headerkanban.lite.html.php';?>
<style>
    .btn-wide{border-color:#d8dbde !important;}
    .chosen-auto-max-width{width: 100% !important;}
    .chosen-container .chosen-results{max-height:150px;}
    th{width:120px !important;}
</style>
<div id='mainContent' class='main-content importModal'>
    <div class='center-block'>
        <div class='main-header'>
            <h2><?php echo $lang->modify->associaitonOrder;?></h2>
        </div>
    </div>
    <form class='form-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform' style="width: 430px;margin: 0 auto;margin-top:20px">
        <table class="table table-form" style="padding: 10px 0">
            <tbody>
            <tr>
                <th><?php echo $lang->modify->associaitonOrder;?></th>
                <td><?php echo html::select('abnormalCode', $abnormalList,[], "class='form-control chosen ' required  data-drop_direction='down'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->modify->comment;?></th>
                <td><?php echo html::textarea('comment', '', "class='form-control'");?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php echo html::submitButton('', 'style="display:block;margin:0 auto"', 'btn btn-wide btn-primary ');?>
                </td>
            </tr>
            </tbody>
        </table>

    </form>
</div>
<style>#product_chosen {width: 45% !important}</style>
<?php include '../../common/view/footer.lite.html.php';?>

