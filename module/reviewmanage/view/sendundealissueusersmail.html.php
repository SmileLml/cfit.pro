<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
    if(!$checkRes['result']):
        $minHeight = 200;
    ?>
<?php
    else:
        $minHeight = 380;
    ?>
<?php endif;?>
<div id='mainContent' class='main-content fade in  scrollbar-hover'style="min-height:<?php echo $minHeight;?>px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $review->id;?></span>
                <span><?php echo $review->title;?></span>

                <small><?php echo $lang->arrow . $lang->review->sendUnDealIssueUsersMail;?></small>
            </h2>
        </div>
        <?php if(!$checkRes['result']):?>
            <div class="tipMsg">
                <span><?php echo $checkRes['message']; ?></span>
            </div>
        <?php else:
            $unDealIssueRaiseByUsers = $checkRes['data'];
            ?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class='table table-form'>
                    <tr>
                        <th class='w-120px'><?php echo $lang->review->unDealIssueRaiseByUsers;?></th>
                        <td colspan='2'>
                            <?php echo html::select('unDealIssueRaiseByUsers[]', $users, $unDealIssueRaiseByUsers, "class='form-control chosen' multiple required");?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td class='text-center' colspan='3'>
                            <input type="hidden" name = "version" value="<?php echo $review->version; ?>">
                            <?php echo html::submitButton();?>
                        </td>
                    </tr>
                </table>

            </form>

        <?php endif;?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>

