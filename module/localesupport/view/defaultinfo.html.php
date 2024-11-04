<!--
<table class="hidden" id="owndeptDemo">
    <tbody id="owndeptLineDemo">
    <tr id="owndeptAndSjInfo_0">
        <td>
            <?php echo html::select('tempAppId[]',  $appList,  '', "id='tempAppId0' data-index='0' class='form-control chosen' disabled");?>
            <input type="hidden" name="appId[]" id="appId0" value="">
        </td>

        <td>
            <?php echo html::select('owndept[]',  $lang->application->teamList,  '', "id='owndept0' data-index='0' class='form-control chosen'");?>
        </td>

        <td>
            <?php echo html::select('sj[]',  $lang->application->fromUnitList, '' , "id='sj0' data-index='0' class='form-control chosen'");?>
        </td>

    </tr>
    </tbody>
</table>
-->


<table class="hidden" id="workReportInfo">
    <tbody id="workReportLineDemo">
    <tr id="supportUserInfo_1">
        <td>1</td>
        <td class="supportUserTd" id="supportUserTd0">
            <div>
                <?php echo html::select('supportUser[]',  $supportUsersList,  '', " id='supportUser0' data-index='0' class='form-control chosen supportUserSelect'");?>
            </div>
        </td>
        <td>
            <?php echo html::input('supportDate[]',  '', "class='form-control  form-date supportDate'  data-id = '' readonly");?>
        </td>
        <td><?php echo html::input('consumed[]',  '' , "class='form-control'");?></td>

        <td>
            <div class="input-group">
                <input type="hidden" name="workReportId[]" value="">
                <a href="javascript:void(0)" onclick="addWork(this)" class="btn btn-link"  id='addWorkItem0'  data-id='0'><i class="icon-plus"></i></a>
                <a href="javascript:void(0)" onclick="delWork(this)" class="btn btn-link"><i class="icon-close"></i></a>
            </div>
        </td>
    </tr>
    </tbody>
</table>
