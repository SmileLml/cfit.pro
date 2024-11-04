<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>

</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirement->edit;?><span style='opacity: 0.5;font-size: 12px;font-weight: normal;'><?php echo empty($requirement->entriesCode)?'':$lang->requirement->subTitle;?></span></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
           <tr>
                <th class='w-180px'><?php echo $lang->requirement->opinionID;?></th>
                <td colspan="2"><?php echo html::select('opinion',$opinions, $requirement->opinion, "class='form-control chosen' disabled");?></td>
                <td class='hidden'><?php echo html::select('opinion', $opinions, $requirement->opinion, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-110px'><?php echo $lang->requirement->name;?></th>
            <td colspan='2'>
                <?php echo html::input('name', $requirement->name, 'class="form-control"');?>
            </td>
              <td></td>
          </tr>

           <tr>
               <th class='w-110px'><?php echo $lang->requirement->deadLine;?></th>
               <td>
                   <!--内部自建 状态为变更中和已拆分不允许编辑 任务主题、概述、期望完成时间、附件字段 -->
                   <?php if($requirement->createdBy != 'guestcn' && in_array($requirement->status,['underchange','splited'])):?>
                       <?php echo html::input('deadLine', $requirement->deadLine, "class='form-control' readonly");?>
                   <?php else:?>
                       <?php echo html::input('deadLine', $requirement->deadLine, "class='form-control form-date'");?>
                   <?php endif;?>
               </td>
               <td>
                   <?php if($requirement->createdBy != 'guestcn'):?>
                       <div class='input-group'>
                           <span class='input-group-addon'><?php echo $lang->requirement->planEnd;?></span>
                           <!--内部自建 状态为已发布的允许变更计划完成时间 -->
                           <?php if($requirement->createdBy != 'guestcn' && in_array($requirement->status,['published'])):?>
                               <?php echo html::input('planEnd', $requirement->planEnd, "class='form-control form-date'");?>
                           <?php else:?>
                               <?php echo html::input('planEnd', $requirement->planEnd, "class='form-control' readonly");?>
                           <?php endif;?>
                       </div>
                   <?php endif;?>
               </td>

               <td></td>
           </tr>

           <tr>
               <th class='w-110px'><?php echo $lang->requirement->app;?></th>
               <td colspan='2'>
                   <?php echo html::select('app[]', $apps, $requirement->app, "class='form-control chosen'multiple"); ?>
               </td>
               <td></td>
           </tr>

          <tr>
            <th class='w-110px'><?php echo $lang->requirement->desc;?></th>
            <td colspan='2' class="desc_edit">
                <?php echo html::textarea('desc', $requirement->desc, "class='form-control'");?>
            </td>
              <td></td>
          </tr>

           <tr>
               <!--备注 -->
               <th><?php echo $lang->requirement->comment;?></th>
               <td colspan='2'><?php echo html::textarea('comment', $requirement->comment, "class='form-control'"); ?></td>
               <td></td>
           </tr>

           <tr>
               <th><?php echo $lang->requirement->filelist;?></th>
               <td colspan='2'>
                   <div class='detail'>
                       <div class='detail-content article-content'>
                           <?php
                           if($requirement->files){
                               echo $this->fetch('file', 'printFiles', array('files' => $requirement->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                           }else{
                               echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                           }
                           ?>
                       </div>
                   </div>
               </td>
               <td></td>
           </tr>

           <?php if($requirement->createdBy != 'guestcn' && !in_array($requirement->status,['underchange','splited'])):?>
           <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
               <td></td>
          </tr>

           <?php elseif($requirement->createdBy == 'guestcn'):?>
               <tr>
                   <th><?php echo $lang->files;?></th>
                   <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                   <td></td>
               </tr>
           <?php endif;?>

           <!--           清总创建且待反馈状态，增加反馈单待处理人 字段-->
           <?php if($requirement->createdBy == 'guestcn' && $requirement->feedbackStatus == 'tofeedback'):?>
               <tr>
                   <th class='w-180px'><?php echo $lang->requirement->dealUser;?></th>
                   <td><?php echo html::select('dealUser[]', $users, $requirement->dealUser, "class='form-control chosen' multiple");?></td>
                   <td class="required">
                       <div class='input-group'>
                           <span class='input-group-addon'><?php echo $lang->requirement->feedbackDealuser;?></span>
                           <?php echo html::select('feedbackDealUser', $users, $requirement->feedbackDealUser, "class='form-control chosen'");?>
                       </div>
                   </td>
                   <td></td>
               </tr>
           <?php else:?>
               <tr>
                   <th class='w-140px'><?php echo $lang->requirement->dealUser;?></th>
                   <td><?php echo html::select('dealUser[]', $users, $requirement->dealUser, "class='form-control chosen' multiple");?></td>
                   <td colspan="">
                       <div class='input-group'>
                       </div>
                   </td>
                   <td></td>
               </tr>
           <?php endif;?>
             <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($this->lang->requirement->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitEditing', json_encode($config->requirement->prohibitEditing));?>
<?php js::set('entriesCode', empty($requirement->entriesCode) ? 0 : 1);?>
<?php js::set('fieldReadonly', $readonly);?>
<script>
    //内部自建 需求概述默认只读处理
    if(fieldReadonly)
    {
        $(function()
        {
            $('#name').attr('readonly','readonly');
            var $desc = window.editor['desc'];
            $desc.readonly(true);
            var desc_iframeDom = $('.desc_edit iframe')[0].contentWindow.document.getElementsByClassName('article-content')[0]
            desc_iframeDom.style.background = '#f5f5f5';
            desc_iframeDom.style.cursor = 'not-allowed';


        });
    }

    $('#opinion').change(function()
    {
        var opinionID = $(this).val();
        $.get(createLink('demand', 'ajaxGetOpinion', "opinionID=" + opinionID), function(data)
        {
            var data = eval('('+data+')');
            if(data.overview)
            {
                KindEditor.instances[0].focus()
                KindEditor.html('#desc', data.overview)
                KindEditor.instances[0].blur()
            }
            $('#deadLine').val(data.deadline);

            //var databaseOpinionID = <?php //echo $requirement->opinion;?>//;
            //var databaseDeadline = "<?php //echo $requirement->deadLine;?>//";
            //var editChangeLockTip = "<?php //echo $this->lang->requirement->editChangeLockTip;?>//";
            //变更锁相关
            //if(opinionID != databaseOpinionID && data.changeLock == 2){
            //    alert(editChangeLockTip)
            //    $('#opinion').val(databaseOpinionID);
            //    $('#opinion').trigger("chosen:updated");
            //    KindEditor.instances[0].focus()
            //    KindEditor.html('#desc', '<?php //echo $requirement->desc;?>//')
            //    KindEditor.instances[0].blur()
            //    $('#deadLine').val(databaseDeadline);
            //}else{
            //    if(data.overview)
            //    {
            //        KindEditor.instances[0].focus()
            //        KindEditor.html('#desc', data.overview)
            //        KindEditor.instances[0].blur()
            //    }
            //    $('#deadLine').val(data.deadline);
            //}



        });
    });

if(entriesCode)
{
    var prohibitEditing = eval('(' + prohibitEditing + ')');
    for(var i in prohibitEditing)
    {
        $('#' + prohibitEditing[i]).attr('disabled', 'disabled');
    }

    $('#opinion').prop('disabled', true).trigger("chosen:updated");

    $(function()
    {
        var $desc = window.editor['desc'];
        $desc.readonly(true);
        var desc_iframeDom = $('.desc_edit iframe')[0].contentWindow.document.getElementsByClassName('article-content')[0]
        desc_iframeDom.style.background = '#f5f5f5';
        desc_iframeDom.style.cursor = 'not-allowed';
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>
