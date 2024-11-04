<?php include '../../common/view/header.html.php';?>
<style>
    form {
        overflow-x: scroll;

    }
    select[readonly] {
        background-color: #eee;
        cursor: no-drop;
    }
    select[readonly] option{
        display: none;
    }
</style>
<div id="mainContent" class="main-content">
    <div class="main-header clearfix">
        <h2><?php echo $lang->residentsupport->importTitle;?></h2>
    </div>
    <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
        <table class='table  table-form' id='showData'>
            <thead>
            <tr>
                <th class='w-120px'><?php echo $lang->residentsupport->exportFileds->dutyDate?></th>
                <th class='w-120px'><?php echo $lang->residentsupport->exportFileds->postType?></th>
                <th class='w-130px'><?php echo $lang->residentsupport->exportFileds->dutyUserDept?></th>
                <th class='w-120px'><?php echo $lang->residentsupport->exportFileds->timeType?></th>
                <th class='w-160px'><?php echo $lang->residentsupport->exportFileds->dutyDuration?></th>
                <th class='w-160px'><?php echo $lang->residentsupport->exportFileds->requireInfo?></th>
                <th class='w-160px'><?php echo $lang->residentsupport->exportFileds->type?></th>
                <th class='w-160px'><?php echo $lang->residentsupport->exportFileds->subType?></th>
                <th class='w-160px'><?php echo $lang->residentsupport->exportFileds->dutyGroupLeader?></th>
                <th class='w-160px'><?php echo $lang->residentsupport->exportFileds->dutyUser?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data as $key => $app):?>
                <tr class='text-top'>
                    <td><?php echo html::input("dutyDate[$key]", $app->dutyDate, "class='form-control dutyDate form-date' ")?></td>
                    <td><?php echo html::select("postType[$key]", $lang->residentsupport->postType, $app->postType,"class='form-control' ")?></td>
                    <td><?php echo html::select("dutyUserDept[$key]", $dept, $app->dutyUserDept, "class='form-control' ")?></td>
                    <td><?php echo html::select("timeType[$key]", $lang->residentsupport->durationTypeList, $app->timeType, "class='form-control'  autocomplete='off'")?></td>
                    <td><?php echo html::input("dutyDuration[$key]", $app->dutyDuration, "class='form-control' autocomplete='off' ")?></td>
                    <td><?php echo html::input("requireInfo[$key]", $app->requireInfo, "class='form-control' autocomplete='off' ")?></td>
                    <td><?php echo html::select("type[$key]", $lang->residentsupport->typeList, $app->type, "class='form-control type'  autocomplete='off'")?></td>
                    <td><?php echo html::select("subType[$key]", $lang->residentsupport->subTypeList, $app->subType, "class='form-control subType'  autocomplete='off'")?></td>
                    <td><?php echo html::select("dutyGroupLeader[$key]", $users, $app->dutyGroupLeader, "class='form-control chosen' autocomplete='off'")?></td>
                    <td><?php echo html::select("dutyUser[$key]", $users, $app->dutyUser, "class='form-control chosen' autocomplete='off'")?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan='10' class='text-center form-actions'>
                    <?php
                    echo html::commonButton('保存', '', 'btn btn-wide btn-primary checkEnable');
                    echo ' &nbsp; ' . html::backButton('取消', 'onclick="return returnBack()"');
                    ?>
                    <input type="submit" value="保存" id="submit" style="display: none;">
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<?php js::set('backUrl',  $this->createLink('residentsupport', 'calendar'));?>
<?php js::set('checkurl',  $this->createLink('residentsupport', 'ajaxcheckIsDayEnable'));?>
<script>
    /**
     * 返回
     */
    function returnBack() {
        window.location.href = backUrl;
    }

    $(".checkEnable").click(function () {
        var dutyDate = [];
        var type = [];
        var subType = [];

        $(".dutyDate").each(function (i) {
            dutyDate[i] = $(this).val();
        });
        $(".type").each(function (j) {
            type[j] = $(this).val();
        });
        $(".subType").each(function (m) {
            subType[m] = $(this).val();
        });
        var form = document.getElementsByTagName("form");
        $.post(checkurl,{dutyDate:dutyDate,type:type,subType:subType},function (res) {
            if (res != '') {
                // bootbox.confirm(res, function (result) {
                //     if (result) {
                //         $('button[data-bb-handler="cancel"]').click();
                //         form.submit();
                //     }
                // });
                if(confirm(res) == true){
                    $("#submit").click();
                }else{
                    return false;
                }
            }else{
                $("#submit").click();
            }
        })
    })
    $(function()
    {
        $.fixedTableHead('#showData');
    });
    setInterval(function () {
        $("#submit").removeAttr("disabled");
    },5000)
</script>
<?php include '../../common/view/footer.html.php';?>
