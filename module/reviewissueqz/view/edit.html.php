<?php include '../../common/view/header.html.php' ?>
<?php include '../../common/view/kindeditor.html.php' ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->reviewissueqz->edit; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class="w-180px"><?php echo $lang->reviewissueqz->review; ?></th>
                    <td><?php echo html::select('reviewId', $reviewList, $issue->reviewId, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>

                <tr>
                    <th><?php echo $lang->reviewissueqz->raiseBy; ?></th>
                    <td><?php echo html::select('raiseBy', $users, $issue->raiseBy, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>

                <tr>
                    <th><?php echo $lang->reviewissueqz->raiseDate; ?></th>
                    <td><?php echo html::input('raiseDate', $issue->raiseDate, "class='form-datetime form-control' ");?></td>
                    <td></td>
                </tr>



                <tr>
                    <th><?php echo $lang->reviewissueqz->title; ?></th>
                    <td colspan='2'><?php echo html::input('title', $issue->title, 'class="form-control"'); ?></td>
                </tr>

                <tr>
                    <th class="w-120px"><?php echo $lang->reviewissueqz->desc; ?></th>
                    <td colspan='2'><?php echo html::textarea('desc', $issue->desc, 'class="form-control"'); ?></td>
                </tr>
                <tr>
                    <th class="w-120px"></th>
                    <td colspan="2" class="form-actions">
                        <?php echo html::submitButton(); ?>
                        <?php echo html::backButton(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<?php include '../../common/view/footer.html.php' ?>
