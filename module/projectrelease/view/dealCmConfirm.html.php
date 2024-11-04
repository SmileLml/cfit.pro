<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='changereview'>
    <table class="table table-form">
        <tbody>
        <tr>
            <th><?php echo $lang->projectrelease->alreadyMergeCode;?></th>
            <td>
                <input type="radio" name="alreadyMergeCode" value="1" <?php if($release->alreadyMergeCode == '1'){echo 'checked';}?>>&nbsp;是 &nbsp;&nbsp;
                <input type="radio" name="alreadyMergeCode" value="2" <?php if($release->alreadyMergeCode == '2'){echo 'checked';}?>>&nbsp;否
            </td>
            <th><?php echo $lang->projectrelease->alreadyBaseLine;?></th>
            <td>
                <input type="radio" name="alreadyBaseLine" value="1" <?php if($release->alreadyBaseLine == '1'){echo 'checked';}?>>&nbsp;是 &nbsp;&nbsp;
                <input type="radio" name="alreadyBaseLine" value="2" <?php if($release->alreadyBaseLine == '2'){echo 'checked';}?>>&nbsp;否
            </td>
        </tr>
        <?php if ($release->baseLinePath == ''):?>
        <tr id='baselineTable1' class="baselineRecord <?php if($release->alreadyBaseLine != '1'){echo 'hidden';}?>">
            <th>
                <?php echo $lang->projectrelease->baseLineInfo;?>
            </th>
            <td colspan='3'>
                <input type="text" name="tagPath[]" id="tagPath1" value="" class="form-control tagPath" placeholder="<?php echo htmlspecialchars($lang->projectrelease->tagPathTip)?>" autocomplete="off">
            </td>
            <td class="c-actions">
                <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link addBaseLineBtn"><i class="icon-plus"></i></a>
            </td>
        </tr>
        <?php else:?>
            <?php
                $baseLines = explode(',',$release->baseLinePath);
                foreach ($baseLines as $bk=>$bv) {
                    $bk++;
            ?>
                    <tr id='baselineTable<?php echo $bk?>' class="baselineRecord <?php if($release->alreadyBaseLine != '1'){echo 'hidden';}?>">
                        <th>
                            <?php echo $lang->projectrelease->baseLineInfo;?>
                        </th>
                        <td colspan='3'>
                            <input type="text" name="tagPath[]" id="tagPath<?php echo $bk?>" value="<?php echo $bv?>" class="form-control tagPath" placeholder="<?php echo htmlspecialchars($lang->projectrelease->tagPathTip)?>" autocomplete="off">
                        </td>
                        <td class="c-actions">
                            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='<?php echo $bk?>' class="btn btn-link addBaseLineBtn"><i class="icon-plus"></i></a>
                            <?php if($bk > 1):?>
                            <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='<?php echo $bk?>' id='codeClose0' class="btn btn-link delBaseLineBtn"><i class="icon-close"></i></a>
                            <?php endif?>
                        </td>
                    </tr>
             <?php   }
            ?>
        <?php endif;?>
        <tr>
            <th>
                <?php echo $lang->projectrelease->cmConfirm;?>
            </th>
            <td colspan='3'>
                <?php echo html::select('result', $lang->projectrelease->dealResultList, '', "class='form-control chosen' required");?>
            </td>

        </tr>

        <tr>
            <th><?php echo $lang->projectrelease->comment;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
        </tr>

        <tr>
            <td class='form-actions text-center' colspan='4'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $release->version; ?>">
                <input type="hidden" name = "status" value="<?php echo $release->status; ?>">
                <?php echo html::submitButton() . html::backButton();?>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<table class='hidden'>
    <tbody id="baselineTableInfo">
    <tr id='baselineTable'>
        <th><?php echo $lang->projectrelease->baseLineInfo;?></th>
        <td colspan='3'>
            <input type="text" name="tagPath[]" id="tagPath0" value="" class="form-control tagPath" placeholder="<?php echo htmlspecialchars($lang->projectrelease->tagPathTip)?>" autocomplete="off">
        </td>
        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link addBaseLineBtn"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link delBaseLineBtn"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>

<script>
    $("[name='alreadyBaseLine']").change(function () {
        if ($(this).val() == 1){
            $(".baselineRecord").removeClass('hidden')
        }else{
            $(".baselineRecord").addClass('hidden')
        }
    })
    //添加
    var relevantIndex = 1;
    function addRelevantItem(obj) {
        var relevantObj  = $('#baselineTableInfo');
        var relevantHtml = relevantObj.clone();
        relevantIndex++;
        relevantHtml.find('#baselineTable').attr({'class':'baselineRecord'});
        relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
        relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});
        relevantHtml.find('#tagPath0').attr({'id':'tagPath' + relevantIndex});
        relevantHtml.find('#baselineTable').attr({'id':'baselineTable' + relevantIndex});

        var objIndex = $(obj).attr('data-id');
        $('#baselineTable' + objIndex).after(relevantHtml.html());
        sortBaseLineItem();
    }

    //刪除
    function delRelevantItem(obj) {
        var objIndex = $(obj).attr('data-id');
        $('#baselineTable' + objIndex).remove();
        sortBaseLineItem();
    }

    /**
     * 重新排序
     */
    function sortBaseLineItem() {
        var sortKey = 0;
        $('.baselineRecord').each(function () {
            sortKey++;
            $(this).find('.addBaseLineBtn').attr({'id':'codePlus' + sortKey, 'data-id': sortKey});
            $(this).find('.delBaseLineBtn').attr({'id':'codeClose' + sortKey, 'data-id': sortKey});
            $(this).find('.tagPath').attr({'id':'tagPath' + sortKey});
            $(this).attr({'id':'baselineTable' + sortKey});
        });
    }

</script>