<style>
    td p {margin-bottom: 0;}
    .table-fixed td{
        white-space: unset!important;
    }
</style>
<style class="dialog"></style>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php echo html::a(inlink('browse'), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $info->id?></span>
            <span class="text" title="">&nbsp;</span>
        </div>
    </div>

</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->cronconfig->command;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->command) ? $info->command : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->cronconfig->remark;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->remark) ? $info->remark : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>


        </div>




        <div class="cell">
            <?php include '../../common/view/action.html.php';?>
        </div>
        <div class='main-actions' style="/*position: fixed;*/bottom: 10px; width: 1120px;">
            <div class="btn-toolbar">
                <?php common::printBack(inlink('browse'));?>
                <div class='divider'></div>
                <?php
                common::printIcon('cronconfig', 'edit', "cronID=$info->id", $info, 'button', '', '', 'iframe', true);
                common::printIcon('cronconfig', 'delete', "cronID=$info->id", $info, 'button', 'trash', '', 'iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->cronconfig->basicInfo;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-150px'><?php echo $lang->cronconfig->status;?></th>
                            <td><?php echo zget($lang->cronconfig->statusList, $info->status, '');?></td>
                        </tr>


                        <tr>
                            <th><?php echo $lang->cronconfig->createBy;?></th>
                            <td><?php echo zget($users, $info->createBy, '');?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->cronconfig->createTime;?></th>
                            <td><?php echo $info->createTime;?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->cronconfig->editBy;?></th>
                            <td><?php echo zget($users, $info->editBy, '');?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->cronconfig->editTime;?></th>
                            <td><?php echo $info->editTime == '0000-00-00 00:00:00' ? '': $info->editTime; ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<?php include '../../common/view/footer.html.php';?>
