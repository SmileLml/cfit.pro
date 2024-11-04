<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/datepicker.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<style>
    .task-toggle {
        line-height: 28px;
        color: #0c60e1;
        cursor: pointer;
    }

    .task-toggle .icon {
        display: inline-block;
        transform: rotate(90deg);
    }

    .more-tips {
        display: none;
    }

    .close-tips {
        display: none
    }

    .tooltip-diy {
        position: relative;
        display: inline-block;
    }

    .tooltip-text {
        width: 800px;
        visibility: hidden;
        background-color: #f6f4f4;
        text-align: left;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        top: -0px;
        left: 50%;
        transform: translateX(-10%);
        opacity: 0;
        transition: opacity 0.1s;
        line-height: 20px;
    }

    .tooltip-diy:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

<div id="mainContent" class="main-content fade"
     style="<?php if ($problem->status == 'confirmed') echo "height:450px" ?>">
    <?php if ($problem->createdBy != 'guestcn' && $problem->createdBy != 'guestjx'): ?>
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->problem->deal; ?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th class='w-140px'><?php echo $lang->problem->ifRecive ?></th>
                        <td><?php echo html::radio('status', $lang->problem->ifConfirmedList, 'assigned', "onchange='ifReturnChanged(this.value)' class='text-center'"); ?></td>
                    </tr>
                    <tr id="dealuser" class="notReturn">
                        <th class='w-110px'><?php echo $lang->problem->nextUser;
                            echo "<br>" . $lang->problem->nextStatus[$problem->status]; ?></th>
                        <td colspan="2"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'"); ?></td>
                    </tr>
                    <tr class="notReturn">
                        <th class='w-110px'><?php echo $lang->problem->mailto; ?></th>
                        <td colspan="2"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple"); ?></td>
                    </tr>
                    <tr class="notReturn">
                        <th><?php echo $lang->problem->app; ?></th>
                        <td class='required notrequired'
                            colspan="2"><?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'"); ?></td>
                        <input type="hidden" name="application" id="application" value="">
                    </tr>
                    <tr class="return devback hide">
                        <th><?php echo $lang->problem->ReasonOfIssueRejecting; ?></th>
                        <td colspan='4'
                            class='required'><?php echo html::textarea('ReasonOfIssueRejecting', $problem->ReasonOfIssueRejecting, "class='form-control' placeholder='' rows ='3'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center'
                            colspan='3'><?php echo html::submitButton() . html::backButton(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php elseif ($problem->createdBy == 'guestcn') : ?>
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->problem->deal; ?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='confirmform'>
                <table class="table table-form">
                    <tbody>
                    <tr id="feedbackExpireTimeID">
                       <!-- <input type="hidden" name="status" id="status" value="assigned">-->
                        <th class='w-110px'><?php echo $lang->problem->feedbackExpireTime; ?></th>
                        <td colspan="5"><?php echo html::input('feedbackExpireTime', $problem->feedbackExpireTime, "class='form-control form-datetime' "); ?></td>
                    </tr>
                    <tr>
                        <th class="problemCauseClass hidden"><?php echo $lang->problem->problemCause; ?></th>
                        <td class='problemCauseClass hidden required' colspan="2"><?php echo html::select('problemCause', $lang->problem->problemCauseList, $problem->problemCause, "class='form-control picker-select' "); ?></td>
                    </tr>
                    <tr id="dealuser">
                        <th class='w-110px'><?php echo $lang->problem->nextUser;
                            echo "<br>" . $lang->problem->nextStatus[$problem->status]; ?></th>
                        <td colspan="5"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'"); ?></td>
                    </tr>
                    <tr>
                        <th class='w-110px'><?php echo $lang->problem->mailto; ?></th>
                        <td colspan="5"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple"); ?></td>
                    </tr>
                    <tr>
                        <th class="dev dev2"><?php echo $lang->problem->app; ?></th>
                        <td class='dev dev2 required notrequired' colspan="5">
                            <?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'"); ?>
                        </td>
                        <input type="hidden" name="application" id="application" value="">
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='5'><?php echo html::submitButton() . html::backButton(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php elseif ($problem->createdBy == 'guestjx') : ?>
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->problem->deal; ?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th class='w-140px'><?php echo $lang->problem->ifRecive ?></th>
                        <td><?php echo html::radio('status', $lang->problem->ifConfirmedList, 'assigned', "onchange='ifReturnChanged(this.value)' class='text-center'"); ?></td>
                    </tr>
                    <tr  class="notReturn">
                        <th class='w-110px'><?php echo $lang->problem->feedbackExpireTime; ?></th>
                        <td colspan="4"><?php echo html::input('feedbackExpireTime', $problem->feedbackExpireTime, "class='form-control form-datetime' "); ?></td>
                    </tr>
                    <tr id="dealuser" class="notReturn">
                        <th class='w-110px'><?php echo $lang->problem->nextUser;
                            echo "<br>" . $lang->problem->nextStatus[$problem->status]; ?></th>
                        <td colspan="4"><?php echo html::select('dealUser', $users, '', "class='form-control chosen dealUserClass'"); ?></td>
                    </tr>
                    <tr class="notReturn">
                        <th class='w-110px'><?php echo $lang->problem->mailto; ?></th>
                        <td colspan="4"><?php echo html::select('mailto[]', $users, '', "class='form-control picker-select' multiple"); ?></td>
                    </tr>
                    <tr class="notReturn">
                        <th class="dev dev2"><?php echo $lang->problem->app; ?></th>
                        <td class='dev dev2 required notrequired' colspan="4">
                            <?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'"); ?>
                        </td>
                        <input type="hidden" name="application" id="application" value="">
                    </tr>
                    <tr class="return devback hide">
                        <th><?php echo $lang->problem->ReasonOfIssueRejecting; ?></th>
                        <td colspan='4'
                            class='required'><?php echo html::textarea('ReasonOfIssueRejecting', $problem->ReasonOfIssueRejecting, "class='form-control' placeholder='' rows ='3'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center'
                            colspan='5'><?php echo html::submitButton() . html::backButton(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php
echo js::set('productPlan', $problem->productPlan);
echo js::set('product', $problem->product);
echo js::set('execution', $problem->execution ? $problem->execution : '');
echo js::set('status', $problem->status);
?>
<script>
   /* $(function () {

        if (status == 'confirmed') {
            $('#productZone').parent().addClass('hidden');
        }


        if (fixtype == 'second' || status === 'confirmed') {
            $('#execution').parent().parent().addClass('hidden');
        } else if (fixtype == 'project') {
            $('#execution').parent().parent().removeClass('hidden');
        }

    })*/

    //根据状态，设置下一节点处理人
    function getnextuser(problemid, status) {
        var link = createLink('problem', 'ajaxGetNextUser', 'problemid=' + problemid + "&status=" + status);
        $.post(link, function (data) {
            $('#dealUser').val(data);
            $('#dealUser').trigger('chosen:updated');
        })
    }

    function ifReturnChanged(scm) {
        if (scm == 'assigned') {
            $('.notReturn').removeClass('hide');
            $('.return').addClass('hide');
            $('#ReasonOfIssueRejecting').val('');
        } else {
            $('.return').removeClass('hide');
            $('.notReturn').addClass('hide');
        }
    }
   document.getElementById('confirmform').onsubmit = function (ev) {
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'status';
        hiddenInput.id = 'status';
        hiddenInput.value = 'assigned';
        var tr = document.getElementById('feedbackExpireTimeID');
        tr.appendChild(hiddenInput);
   }
</script>
<?php include '../../../common/view/footer.html.php'; ?>
