<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->auditplan->{$auditplan->objectType} . ':' . $object . '<i class="icon-angle-right"></i> ' . $lang->auditplan->check;?></h2>
    </div>
    <?php if(!empty($checkList)):?>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <?php echo html::hidden('hasDraft', !empty($draftResults));?>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <th class='text-left'><?php echo $lang->auditplan->content;?></th>
            <th class='w-250px text-left'><?php echo $lang->auditplan->result;?></th>
            <th class='w-200px text-left'><?php echo $lang->auditplan->comment;?></th>
          </tr>
          <?php foreach($checkList as $list):?>
          <tr>
            <td><?php echo $list->title;?></td>
            <td><?php echo html::radio("result[$list->id]", $lang->auditplan->resultList, isset($draftResults[$list->id]->result) ? $draftResults[$list->id]->result : 'pass');?></td>
            <td id='remark'><?php echo html::textarea("comment[$list->id]", isset($draftResults[$list->id]->comment) ? $draftResults[$list->id]->comment : '', "class='form-control' rows=1");?></td>
          </tr>
          <?php endforeach;?>
          <tr>
            <td colspan='3' class='form-actions text-center'>
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
        $("#remark").addClass('required');
    }
    else
    {
        $("#remark").removeClass('required');
    }
})
</script>
<?php include '../../common/view/footer.html.php';?>
