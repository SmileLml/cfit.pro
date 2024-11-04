<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
    <div id="mainContent" class="main-content fade" style="min-height: 450px; max-height:  480px;">
        <div class="center-block">
            <div class="main-header">
                <h2>
                    <span class='label label-id'><?php echo $info->code;?></span>
                    <small><?php echo $lang->arrow . $lang->localesupport->reportWork;?></small>
                </h2>
            </div>

            <?php if(!$checkRes['result']):?>
                <div class="tipMsg text-danger help-text red">
                    <span ><?php echo $lang->localesupport->reportWorkMsgTip; ?>:<br/></span>
                    <span>
                        <?php if(is_array($checkRes['message'])): ?>
                            <?php foreach ($checkRes['message'] as $val):?>
                                <?php echo $val . '<br/>'; ?>
                            <?php endforeach;?>
                        <?php else: ?>
                            <?php echo $checkRes['message']; ?>
                        <?php endif;?>
                  </span>
                </div>
            <?php else: ?>
            <div class="detail">
                <div class="detail-content article-content">
                    <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
                        <table class="table ops">
                            <thead>
                            <tr>
                                <th class="w-50px"><?php echo $lang->localesupport->rowNum;?></th>
                                <th class="w-150px"><?php echo $lang->localesupport->supportUsers;?></th>
                                <th class="w-200px"><?php echo $lang->localesupport->supportDate;?></th>
                                <th class="w-150px"><?php echo $lang->localesupport->consumed;?></th>
                                <th class="w-200px"><?php echo $lang->actions;?></th>
                            </tr>
                            </thead>
                            <?php
                            if($info->supportUsers):
                                ?>
                                <tbody id="supportUserTBody">
                                <?php
                                $supportUsers = explode(',', $info->supportUsers);
                                foreach ($supportUsers as  $supportUser):
                                    $readonly = '';
                                    if((!$isAllReportWork) && ($supportUser != $app->user->account)){
                                        $readonly = 'readonly';
                                    }

                                    $currentSupportUsersList = zget($workReportData, $supportUser, []);
                                    $count = count($currentSupportUsersList);
                                    if($count == 0):
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <span id="supportUserInfo"><?php echo zget($users, $supportUser);?></span>
                                            </td>
                                            <td>
                                                <?php if($readonly):?>
                                                    <?php echo html::input('supportDate[]', '' , "class='form-control' data-id = '' readonly");?>
                                                <?php else:?>
                                                    <?php echo html::input('supportDate[]',  '', "class='form-control  form-date'  data-id = '' readonly");?>
                                                <?php endif;?>
                                            </td>
                                            <td><?php echo html::input('consumed[]',  '' , "class='form-control' $readonly");?></td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                                <div>
                                                    <input type="hidden" name="supportUser[]" value="<?php echo $supportUser;?>" $readonly>
                                                    <input type="hidden" name="workReportId[]" value="" $readonly>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    else:
                                        foreach ($currentSupportUsersList as $key => $workReportInfo):
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td><span id="supportUserInfo"><?php echo zget($users, $supportUser);?></span></td>
                                                <td>
                                                    <?php if($readonly):?>
                                                        <?php echo html::input('supportDate[]',  $workReportInfo->supportDate , "class='form-control' readonly");?>
                                                    <?php else:?>
                                                        <?php echo html::input('supportDate[]',  $workReportInfo->supportDate , "class='form-control  form-date' readonly");?>
                                                    <?php endif;?>

                                                </td>
                                                <td><?php echo html::input('consumed[]',  $workReportInfo->consumed, "class='form-control' $readonly");?></td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                                                    <?php if($key > 0):?>
                                                    <a href="javascript:void(0)" onclick="deleteLine(this)" class="btn btn-link"><i class="icon-close"></i>
                                                        <?php endif;?>
                                                        <div>
                                                            <input type="hidden" name="supportUser[]" value="<?php echo $supportUser;?>" $readonly>
                                                            <input type="hidden" name="workReportId[]" value="<?php echo $workReportInfo->id;?>" $readonly>
                                                        </div>
                                                </td>
                                            </tr>

                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                <?php
                                endforeach;
                                ?>

                                </tbody>

                                <tr id="tipInfo" style="border-top:0px solid !important; ">
                                    <td colspan="5" style="border-top:0px solid !important; ">
                                        <div class="text-left" style="color: red"><span> <?php echo $lang->localesupport->workReportTipMessage; ?></span></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class='form-actions text-center' colspan='5'>
                                        <input type="hidden" name="localesupportId" value="<?php echo $info->id;?>">
                                        <?php echo html::submitButton() . html::backButton();?>
                                    </td>
                                </tr>
                            <?php else:?>
                                <tr>
                                    <td colspan="5"><?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?></td>
                                </tr>
                            <?php endif;?>


                        </table>
                    </form>
                </div>
            </div>
            <?php endif;?>

        </div>
    </div>

    <table class="hidden">
        <tbody id="lineDemo">
        <tr>
            <td></td>
            <td><span id="supportUserInfo"></span></td>
            <td><?php echo html::input('supportDate[]',  '' , "class='form-control form-date' readonly");?></td>
            <td><?php echo html::input('consumed[]',  '', "class='form-control'");?></td>
            <td>
                <a href="javascript:void(0)" onclick="addLine(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                <a href="javascript:void(0)" onclick="deleteLine(this)" class="btn btn-link"><i class="icon-close"></i>
                    <div>
                        <input type="hidden" name="supportUser[]" value="">
                        <input type="hidden" name="workReportId[]" value="">
                    </div>
            </td>
        </tr>
        </tbody>
    </table>

<?php
    js::set('supportUsersList', $supportUsersList);
    js::set('isAllReportWork', $isAllReportWork);
    js::set('currentUser', $app->user->account);
    js::set('start', $info->startDate != '0000-00-00 00:00:00' ? $info->startDate: $minStartDate);
    js::set('end', $info->endDate);
?>

<?php include '../../common/view/footer.html.php';?>