<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>

<div id="mainContent" class="main-row">
    <div class="main-col">

        <?php if ($planDemandList):    ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->projectplan->demandAllList;?></div>
                    <div class="detail-content article-content">
                        <table class="table ops">
                            <tr>
                                <th class="w-100px text-center" ><?php echo $lang->projectplan->projectDemand;?></th>

                            </tr>
                            <?php foreach ($planDemandList as $key => $list): ?>
                                <tr>
                                    <td class="w-100px" ><?php echo $list->code; ?></td>

                                </tr>

                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="cell">
                <div class="detail">
                    <form class="main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                        <div  class="text-center">该需求意向下没有需求任务可以删除！</div>
                        <br />
                        <?php  echo html::input('requirementID', $requirementID, "class='form-control ' "); ?>
                        <?php  echo html::input('planID', $planID, "class='form-control ' "); ?>
                        <div  class="text-center"><button type='submit' id='submit' class="btn btn-primary"><?php echo $lang->projectplan->onlydelete;?></button></div>
                    </form>
                </div>
            </div>
        <?php endif; ?>








    </div>

</div>
<script>

</script>
<?php include '../../common/view/footer.html.php'; ?>
