<?php include '../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->problem->import;?></h2>
  </div>
  <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport));?></p>
  <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary');?></p>
</div>
<script>
$(function()
{
    $('#maxImport').keyup(function()
    {
        if(parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
    });
    $('#import').click(function(){location.href = createLink('outsideplan', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->problem->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-60px'><?php echo $lang->problem->id?></th>
          <th class='w-120px'><?php echo $lang->problem->abstract?></th>
          <th class='w-300px'><?php echo $lang->problem->source?></th>
          <th class='w-150px'><?php echo $lang->problem->severity?></th>
          <th class='w-200px'><?php echo $lang->problem->app;?></th>
          <th class='w-120px'><?php echo $lang->problem->pri;?></th>
          <th class='w-120px'><?php echo $lang->problem->occurDate;?></th>
          <th class='w-120px'><?php echo $lang->problem->consumed;?></th>
          <th class='w-250px'><?php echo $lang->problem->PO;?></th>
          <th class='w-120px'><?php echo $lang->problem->desc;?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($problemData as $key => $problem):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($problem->id))
            {
                echo $problem->id . html::hidden("id[$key]", $problem->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->problem->new}</sub>";
            }
            ?>
          </td>
          <td><?php echo html::input("abstract[$key]", $problem->abstract, "class='form-control'")?></td>
          <td><?php echo html::select("source[$key]", $lang->problem->sourceList,$problem->source, "class='form-control chosen'")?></td>
          <td><?php echo html::select("severity[$key]", $lang->problem->severityList,  $problem->severity, "class='form-control chosen'")?></td>
          <td><?php echo html::select("app[$key][]", $apps, $problem->app , "class='form-control chosen' multiple")?></td>
          <td><?php echo html::select("pri[$key]", $lang->problem->priList, $problem->pri , "class='form-control chosen'")?></td>
          <td><?php echo html::input("occurDate[$key]", $problem->occurDate, "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::input("consumed[$key]", $problem->consumed, "class='form-control'")?></td>
          <td><?php echo html::select("dealUser[$key]", $users, $problem->dealUser , "class='form-control chosen'")?></td>
          <td><?php echo html::input("desc[$key]", $problem->desc, "class='form-control'")?></td>

<!--          <td>--><?php //echo html::select("line[$key][]", $lines, !empty($outsideplan->line) ? $outsideplan->line : ((!empty($outsideplan->id) and isset($outsideplans[$outsideplan->id])) ? $outsideplans[$outsideplan->id]->line : ''), "class='form-control chosen' multiple")?><!--</td>-->
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='16' class='text-center form-actions'>
            <?php
            $submitText = $isEndPage ? $this->lang->save : $this->lang->file->saveAndNext;
            if(!$insert and $dataInsert === '')
            {
                echo "<button type='button' data-toggle='modal' data-target='#importNoticeModal' class='btn btn-primary btn-wide'>{$submitText}</button>";
            }
            else
            {
                echo html::submitButton($submitText);
                if($dataInsert !== '') echo html::hidden('insert', $dataInsert);
            }
            echo html::hidden('isEndPage', $isEndPage ? 1 : 0);
            echo html::hidden('pagerID', $pagerID);
            echo ' &nbsp; ' . html::backButton();
            echo ' &nbsp; ' . sprintf($lang->file->importPager, $allCount, $pagerID, $allPager);
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
    <?php if(!$insert and $dataInsert === '') include '../../common/view/noticeimport.html.php';?>
  </form>
</div>
<?php endif;?>
<script>
$(function()
{
    $.fixedTableHead('#showData');
});
</script>
<?php include '../../common/view/footer.html.php';?>
