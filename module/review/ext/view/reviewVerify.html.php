    <tr>
        <th class='w-150px'><?php echo $lang->review->appointOther;?></th>
        <td class='w-p45-f'>
            <?php echo html::radio('appointOther', $lang->review->appointOtherList, '2', "onclick='setIsAppointOther(this.value);'");?>
        </td>
        <td></td>
    </tr>
    <tr class="appointVerify hidden">
        <th class='w-150px'><?php echo $lang->review->appointUser;?></th>
        <td>
            <?php echo html::select('appointUser', $users, '', "class='form-control chosen' required onchange='checkFieldVal(this.id,this.value)'");?>
            <div id="appointUserLabel" class="text-danger help-text"></div>
        </td>
        <td></td>
    </tr>

    <tr class="appointVerify hidden">
        <th class='w-150px'><?php echo $lang->review->verifyDeadline;?></th>
        <td>
            <?php echo html::input('verifyDeadline', $review->verifyDeadline, "class='form-control form-date' disabled required onblur='checkFieldVal(this.id,this.value)'");?>
            <div id="verifyDeadlineLabel" class="text-danger help-text"></div>
        </td>
        <td></td>
    </tr>

     <tr class="verifying">
          <th class='w-150px'><?php echo $lang->review->result;?></th>
          <td>
             <?php echo html::select('result', $lang->review->reviewConclusionTempList, '', "class='form-control chosen' required onchange='checkFieldVal(this.id,this.value)'");?>
              <div id="resultLabel" class="text-danger help-text"></div>
          </td>
          <td></td>
      </tr>

      <tr class="hidden">
          <th><?php echo $lang->review->reviewedDate;?></th>
          <td>
              <?php echo html::input('reviewedDate', helper::now(), "class='form-control form-date' required onblur='checkFieldVal(this.id,this.value)'");?>
              <div id="reviewedDateLabel" class="text-danger help-text"></div>
          </td>
          <td></td>
      </tr>

  <!--<tr>
      <th><?php /*echo $lang->review->consumed;*/?></th>
      <td>
          <?php
/*          echo html::input('consumed', '', "class='form-control' required onblur='checkFieldVal(this.id,this.value)'");*/?>
          <div id="consumedLabel" class="text-danger help-text"></div>
      </td>
      <td></td>
  </tr>-->

  <tr>
      <th class='w-150px'><?php echo $lang->review->mailto;?></th>
      <td colspan="2">
          <?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple");?>
      </td>
  </tr>

  <tr>
      <th><?php echo $lang->comment ;?></th>
      <td colspan='2'>
          <?php echo html::textarea('comment', '', "rows='6' class='form-control' placeholder=' ".htmlspecialchars($lang->review->commenttip)."'");?>
      </td>
  </tr>

  <tr>
      <td class='text-center' colspan='3'>
          <?php echo html::submitButton('', '', 'btn btn-wide btn-primary reviewVerify');?>
      </td>
  </tr>

<?php
js::set('reviewId', $review->id);
js::set('lastVerify', $lastVerifyer);
?>
<script>

    /**
     * 检查字段值
     *
     * @param tagLabel
     * @param fieldVal
     */
    function checkFieldVal(fieldId, fieldVal){
        var tipMsg = '该字段不能为空';
        if(fieldId == 'appointUser'){
            tipMsg = '委托验证人员不能为空';
        }else if(fieldId == 'verifyDeadline'){
            tipMsg = '验证截至日期不能为空';
        } else if(fieldId == 'result'){
            tipMsg = '评审结果不能为空';
        } else if(fieldId == 'reviewedDate'){
            tipMsg = '评审日期不能为空';
        }/*else if(fieldId == 'consumed'){
            tipMsg = '工作量不能为空';
        }*/
        var fieldLabel = fieldId + 'Label';
        var checkRes = true;
        if(!fieldVal){
            $('#'+fieldLabel).html(tipMsg);
            checkRes = false;
        } else {
            $('#'+fieldLabel).html('');
        }
        return checkRes;
    }

    //验证，非委派时检查评审问题数量
    $('.reviewVerify').click(function (){
        var appointOther = $('input:radio[name="appointOther"]:checked').val();
        var checkRes = false;
        if(appointOther == 1){ //验证委派
            var appointUser    = $('#appointUser').val();
            var verifyDeadline = $('#verifyDeadline').val();
            //验证指派人
            checkRes = checkFieldVal('appointUser', appointUser);
            if(!checkRes){
                return false;
            }
        } else { //验证
            var result       = $('#result').val();
            var reviewedDate = $('#reviewedDate').val();
            //验证评审结果
            console.log(result);
            checkRes = checkFieldVal('result', result);
            if(!checkRes){
                return false;
            }
            //验证评审日期
            checkRes = checkFieldVal('reviewedDate', reviewedDate);
            if(!checkRes){
                return false;
            }
        }
        //验证工作量
       /* var consumed = $('#consumed').val();
        checkRes = checkFieldVal('consumed', consumed);
        if(!checkRes){
            return false;
        }*/

        //是否提交
        var isSubmit = true;
        if(appointOther == 2){//评审

            var result       = $('#result').val();
            var statusStr = 'create,active,part';
            $.ajaxSettings.async = false;
            if(result == 'passNoNeedEdit'){
            $.get(createLink('reviewissue', 'ajaxGetReviewIssueCount', "reviewID=" + reviewId+ "&statusArray=" +  statusStr), function(reviewIssueCount) {
                if(reviewIssueCount > 0 && lastVerify == 1){
                    if(!alert('该评审有'+reviewIssueCount+'个问题验证未通过，不能选择验证通过')){
                        isSubmit = false;
                    }
                }else if(reviewIssueCount > 0){
                    if(confirm("该评审存在部分问题验证未通过，请确认是否选择验证通过？",'确认窗口')){
                        isSubmit = true;
                    }else{
                        isSubmit = false;
                    }
                }
            });
            }
            $.ajaxSettings.async = true;
        }

        return isSubmit;

        
    });
</script>

