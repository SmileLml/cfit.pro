<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <?php if(isset($canDelay) && !$canDelay):?>
        <h2 style="color:block;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $lang->demand->delayTip;?></h2>
    <?php else:?>
    <div class="center-block">
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $demand->code; ?></span>
                <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demand->delay . '</span>') : html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->name); ?>
                <?php if (!isonlybody()): ?>
                    <small><?php echo $lang->arrow . $lang->demand->delay; ?></small>
                <?php endif; ?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->demand->originalResolutionDate; ?><i class="icon icon-help" title="<?php $this->app->loadLang('problem'); echo $lang->problem->originalResolutionDateHelp; ?>"></i></th>
                    <td colspan='1'><?php echo html::input('originalResolutionDate', $demand->end, "class='form-control form-date' disabled");?></td>
                    <td class="hidden"><?php echo html::input('originalResolutionDate', $demand->end, "class='form-control form-date'");?></td>
                <tr>
                    <th><?php echo $lang->demand->delayResolutionDate; ?></th>
                    <td colspan='1'><?php echo html::input('delayResolutionDate', '', "class='form-control form-date'");?></td>
                </tr>
               <!-- <tr>
                    <th><?php /*echo $lang->demand->unitAgree; */?></th>
                    <td colspan='1'><?php /*echo html::select('unitAgree', $lang->demand->unitAgreeList, '', "class='form-control chosen'");*/?></td>
                </tr>-->
                <tr>
                    <th><?php echo $lang->demand->delayReason; ?></th>
                    <td colspan='2'><?php echo html::textarea('delayReason', '', "rows='6' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->comment; ?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center'
                        colspan='3'><?php echo html::submitButton('提交') . html::backButton(); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php'; ?>
