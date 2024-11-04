<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>

</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->edit;?><span style='opacity: 0.5;font-size: 12px;font-weight: normal;'><?php echo empty($requirement->entriesCode)?'':$lang->requirementinside->subTitle;?></span></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
           <tr>
            <th class='w-180px'><?php echo $lang->requirementinside->opinionID;?></th>
            <td colspan="2"><?php echo html::select('opinion',$opinions, $requirement->opinion, "class='form-control chosen'");?></td>
            <td class='hidden'><?php echo html::select('opinion', $opinions, $requirement->opinion, "class='form-control chosen'");?></td>
          </tr>

           <tr>
               <!--期望实现日期 -->
               <th class='w-180px'><?php echo $lang->requirementinside->deadLine;?></th>
               <td class="required"><?php echo html::input('deadLine',  $requirement->deadLine, "class='form-control form-date'");?></td>
               <!--计划完成日期 -->
               <td class="required">
                   <div class='input-group'>
                       <span class='input-group-addon'><?php echo $lang->requirementinside->planEnd;?></span>
                       <!--内部自建 状态为已发布的允许变更计划完成时间 -->
                       <?php if($requirement->createdBy != 'guestcn' && in_array($requirement->status,['published'])):?>
                           <?php echo html::input('planEnd', $requirement->planEnd, "class='form-control form-date'");?>
                       <?php else:?>
                           <?php echo html::input('planEnd', $requirement->planEnd, "class='form-control' readonly");?>
                       <?php endif;?>
                   </div>
               </td>
               <td>
               </td>
           </tr>

          <tr>
            <th class='w-110px'><?php echo $lang->requirementinside->name;?></th>
            <td colspan='2'><?php echo html::input('name', $requirement->name, 'class="form-control"');?></td>
              <td>
              </td>
          </tr>

           <tr>
               <th class='w-110px'><?php echo $lang->requirementinside->app;?></th>
               <td colspan='2'>
                   <?php echo html::select('app[]', $apps, $requirement->app, "class='form-control chosen'multiple"); ?>
               </td>
           </tr>
          <tr>
            <th class='w-110px'><?php echo $lang->requirementinside->desc;?></th>
            <td colspan='2' class="desc_edit"><?php echo html::textarea('desc', $requirement->desc, "class='form-control'");?>
            </td>
              <td>
              </td>
          </tr>
           <tr>
               <!--备注 -->
               <th><?php echo $lang->requirementinside->comment;?></th>
               <td colspan='2'><?php echo html::textarea('comment', $requirement->comment, "class='form-control'"); ?></td>
           </tr>
           <tr>
               <th><?php echo $lang->requirementinside->filelist;?></th>

               <td colspan="2">
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

               <td>
               </td>
           </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
              <td>
              </td>
          </tr>

           <tr>
               <th class='w-140px'><?php echo $lang->requirementinside->dealUser;?></th>
               <?php
                if ($requirement->status == 'topublish'){
                    $dealUser = $requirement->nextDealuser;
                }else{
                    $dealUser = isset($requirement->dealuser) ? $requirement->dealuser : '';
                }
               ?>
               <td><?php echo html::select('dealUser[]', $users, $dealUser, "class='form-control chosen' multiple");?></td>
               <td>
                   <div class='input-group'>
                   </div>
               </td>
               <td>
               </td>
           </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($this->lang->requirementinside->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitEditing', json_encode($config->requirementinside->prohibitEditing));?>
<?php js::set('entriesCode', empty($requirement->entriesCode) ? 0 : 1);?>
<script>
    $('#opinion').change(function()
    {
        var opinionID = $(this).val();
        $.get(createLink('demandinside', 'ajaxGetOpinion', "opinionID=" + opinionID), function(data)
        {
            var data = eval('('+data+')');
            $('#deadLine').val(data.deadline);
            console.log(data.overview)
            if(data.overview)
            {
                KindEditor.instances[0].focus()
                KindEditor.html('#desc', data.overview)
                KindEditor.instances[0].blur()
            }
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
