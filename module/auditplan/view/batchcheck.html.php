<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->auditplan->batchCheck;?></h2>
    </div>
    <?php if(!empty($checkList)):?>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <th class='text-left'><?php echo $lang->idAB;?></th>
            <th class='text-left'><?php echo $lang->auditplan->objectID;?></th>
            <th class='text-left'><?php echo $lang->auditplan->objectType;?></th>
            <th class='text-left'><?php echo $lang->auditplan->content;?></th>
            <th class='w-250px text-left'><?php echo $lang->auditplan->result;?></th>
            <th class='w-200px text-left'><?php echo $lang->auditplan->comment;?></th>
          </tr>
          <?php foreach($checkList as $auditplanID => $lists):?>
          <?php foreach($lists as $listID => $list):?>
          <tr>
            <?php echo html::hidden("hasDraft[$auditplanID]", !empty($draftResults[$auditplanID]));?>
            <td><?php echo $auditplanID;?></td>
            <td><?php echo $list->objectType == 'output' ? zget($outputs, $list->objectID) : zget($activities, $list->objectID);?></td>
            <td><?php echo $lang->auditplan->{$list->objectType};?></td>
            <td><?php echo $list->title;?></td>
            <td><?php echo html::radio("result[$auditplanID][$list->id]", $lang->auditplan->resultList, isset($draftResults[$auditplanID][$list->id]->result) ? $draftResults[$auditplanID][$list->id]->result : 'pass');?></td>
            <td id="remark[<?php echo $auditplanID;?>][<?php echo $list->id;?>]"><?php echo html::textarea("comment[$auditplanID][$list->id]", isset($draftResults[$auditplanID][$list->id]->comment) ? $draftResults[$auditplanID][$list->id]->comment : '', "class='form-control' rows=1");?></td>
          </tr>
          <?php endforeach;?>
          <?php endforeach;?>
          <tr>
            <td colspan='6' class='form-actions text-center'>
            <?php echo html::hidden('mode', 'normal');?>
            <?php echo html::submitButton();?>
            <?php echo html::submitButton($lang->auditplan->saveDraft, '', "btn btn-wide btn-secondary draft");?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
    <?php else:?>
    <div class='table-empty-tip'><?php echo $lang->auditplan->noCheckList;?></div>
    <?php endif;?>
  </div>
</div>
<script>
$('.draft').click(function()
{
    $('#mode').val('draft');
})

$("input[id^=result]").change(function()
{
    if(this.value == 'fail')
    {
        $(this).closest('tr').find('[id^=remark]').addClass('required');
    }
    else
    {
        $(this).closest('tr').find('[id^=remark]').removeClass('required');
    }
})
</script>
<?php include '../../common/view/footer.html.php';?>
