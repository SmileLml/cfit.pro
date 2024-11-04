<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>

<div id="mainContent" class="main-row">
    <div class="main-col">

        <?php if ($ChangeList):    ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->projectplan->planChangeInfo;?></div>
                    <div class="detail-content article-content">
                        <table class="table ops">
                            <tr>
                                <th class="w-100px text-center" ><?php echo $lang->projectplan->changeHistoryTime;?></th>
                                <th class="text-center"><?php echo $lang->projectplan->changeContent;?></th>
                                <th class="w-120px text-center"><?php echo $lang->projectplan->planChangeInfo;?></th>
                                <th class="w-120px text-center" ><?php echo $lang->projectplan->auditResults;?></th>
                            </tr>
                            <?php foreach ($ChangeList as $key => $list): ?>
                                <tr>
                                    <td class="w-100px" ><?php echo $list->createdDate; ?></td>
                                    <td ><?php echo $list->planRemark; ?></td>
                                    <td class="text-center"><button href="<?php echo $this->createLink('projectplan', 'ajaxshowdiffchange', "changeID=$list->id");?>" onclick="showajaxdiff(this)" class="btn" data-app="platform">查看</button></td>
                                    <td class="text-center"><?php echo $lang->projectplan->changeStatus[$list->status]; ?>
                                        <?php
                                        if($list->isreview == 2){
                                            echo '('.$lang->projectplan->noChangeplanReview.')';
                                        }

                                        ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title text-center"><?php echo $lang->projectplan->noData;?></div>

                </div>
            </div>
        <?php endif; ?>
        <div>

            <div  class="text-center"><a id="guanbiiframe" class="btn"><?php echo $lang->projectplan->close;?></a></div>
        </div>








    </div>

</div>
<script>
    function showajaxdiff(val){

        $.zui.modalTrigger.show({iframe:$(val).attr('href'),scrollInside:true,size:"fullscreen"});
    }

    function closeajaxdiff(){
        $.zui.modalTrigger.close();
    }
    $(document).ready(function(){
        $("#guanbiiframe").click(function(){

            window.parent.closeajaxdiff();
        })

    })
</script>
<?php include '../../common/view/footer.html.php'; ?>
