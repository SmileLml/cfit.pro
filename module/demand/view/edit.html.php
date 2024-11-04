<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->demand->edit; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th></th>
                    <td>
                        <div class="demandtip"><span> <?php echo $lang->demand->projectrelatedemand ?></span></div>
                    </td>
                </tr>
                <br/>
                <tr>
                    <th class='w-180px'><?php echo $lang->demand->opinionID; ?></th>
                    <td>
                        <?php echo html::select('opinionID', $opinions, $demand->opinionID, "class='form-control chosen'"); ?>
                    </td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->demand->requirementID; ?></span>
                            <?php echo html::select('requirementID', $opinions, $demand->requirementID, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->title; ?></th>
                    <td><?php echo html::input('title', $demand->title, "class='form-control'"); ?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->demand->endDate; ?></span>
                            <?php echo html::input('endDate', $demand->endDate, "class='form-control form-date'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->demand->app; ?></th>
                    <td><?php echo html::select('app', $apps, $demand->app, "class='form-control chosen'"); ?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->demand->fixType; ?></span>
                            <?php echo html::select('fixType', $lang->demand->fixTypeList, $demand->fixType, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->demand->product; ?></th>
                    <td>
                        <?php echo html::select('product', $opinions, $demand->product, "class='form-control chosen'"); ?>
                    </td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->demand->productPlan; ?></span>
                            <?php echo html::select('productPlan', $opinions, $demand->fixType, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->acceptUser; ?></th>
                    <td colspan='2'><?php echo html::select('acceptUser', $users, $demand->acceptUser, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->desc; ?></th>
                    <td colspan='2'><?php echo html::textarea('desc', $demand->desc, "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->comment; ?></th>
                    <td colspan='2'><?php echo html::textarea('comment', $demand->comment, "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->demand->filelist; ?></th>

                    <td>
                        <div class='detail'>
                            <div class='detail-content article-content'>
                                <?php
                                if ($demand->files) {
                                    echo $this->fetch('file', 'printFiles', array('files' => $demand->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                } else {
                                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->files; ?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildform'); ?></td>
                </tr>
                <tr>
                    <!--下一节处理人 -->
                    <th class='w-140px'><?php echo $lang->demand->PO; ?></th>
                    <td><?php echo html::select('dealUser', $users, $demand->dealUser, "class='form-control chosen'"); ?></td>
                    <!--工作量 -->
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->demand->consumed; ?></span>
                            <?php echo html::input('consumed', $demand->consumed, "class='form-control'"); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='form-actions text-center'
                        colspan='3'><?php echo html::submitButton($this->lang->demand->submitBtn) . html::backButton(); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php js::set('requirementID', $demand->requirement); ?>
<?php include '../../common/view/footer.html.php'; ?>
