<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $reviewName->title;?></span>
                <?php echo isonlybody() ? ("<span title='$issue->review'>" . $issue->title . '</span>') : html::a($this->createLink('reviewissue', 'view', "project=$projectId&issueID=$issue->id"), $lang->reviewissue->title);?>
                <?php if(!isonlybody()):?>
                    <small><?php echo $lang->reviewissue->title;?></small>
                <?php endif;?>
            </h2>
        </div>
        <form method='post' target='hiddenwin'>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->reviewissue->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('delDesc', '', "rows='8' class='form-control'");?></td>
                </tr>
                <tr>
                    <td colspan='3' class='text-center form-actions'>
                        <?php echo html::submitButton();?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
            </table>
        </form>
        <hr class='small' />
        <div class='main'><?php include '../../common/view/action.html.php';?></div>
    </div>
</div>

<?php include '../../common/view/footer.html.php';?>
