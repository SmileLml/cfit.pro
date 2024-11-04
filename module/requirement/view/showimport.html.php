<?php include '../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->requirement->import;?></h2>
  </div>
  <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport));?></p>
  <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary');?></p>
</div>
<script>
$(function()
{
    $('#maxImport').keyup(function()
    {
        var re = /^\d+$/;
        if(!re.test($('#maxImport').val())){
            alert('只能输入整数');
            $('#maxImport').val('');
        }
        if(parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
    });
    $('#import').click(function(){location.href = createLink('requirement', 'showImport', "pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->requirement->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-60px'><?php echo $lang->requirement->id?></th>
          <th class='w-200px required'><?php echo $lang->requirement->name?></th>
          <th class='w-200px required'><?php echo $lang->requirement->opinionID?></th>
          <!-- <th class='w-150px required'><?php echo $lang->requirement->deadLine?></th> -->
          <th class='w-200px required'><?php echo $lang->requirement->desc?></th>
          <th class='w-120px required'><?php echo $lang->requirement->app;?></th>
          <th class='w-120px required'><?php echo $lang->requirement->status;?></th>
          <th class='w-120px'><?php echo $lang->requirement->onlineTimeByDemand;?></th>
          <th class='w-100px required'><?php echo $lang->requirement->createdBy?></th>
          <th class='w-100px required'><?php echo $lang->requirement->projectManager?></th>
          <th class='w-150px'><?php echo $lang->requirement->dealUser?></th>
          <th class='w-200px'><?php echo $lang->requirement->comment;?></th>
          <th class='w-140px required'><?php echo $lang->requirement->consumed;?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($requirementData as $key => $requirement):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($requirement->id))
            {
                echo $requirement->id . html::hidden("id[$key]", $requirement->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->requirement->new}</sub>";
            }
            ?>
          </td>
          <td><?php echo html::textarea("name[$key]", $requirement->name, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::select("opinionID[$key]", $opinions, !empty($requirement->opinionID) ? $requirement->opinionID: ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->opinionID: ''), "class='form-control chosen'")?></td>
          <!-- <td><?php echo html::input("deadLineDate[$key]", !empty($requirement->deadLineDate) ? $requirement->deadLineDate : ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->deadLineDate : ''), "class='form-control form-date' autocomplete='off'")?></td> -->
          <td><?php echo html::textarea("desc[$key]", $requirement->desc, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::select("app[$key]", $apps,!empty($requirement->app) ? $requirement->app : ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->app : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("status[$key]", $statusList,!empty($requirement->status) ? $requirement->status : ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->status : ''), "class='form-control'")?></td>
          <td><?php echo html::input("onlineTimeByDemand[$key]", !empty($requirement->onlineTimeByDemand) ? $requirement->onlineTimeByDemand : ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->onlineTimeByDemand : ''), "class='form-control form-date' autocomplete='off'")?></td>
          <td><?php echo html::select("createdBy[$key]", $users, !empty($requirement->createdBy) ? $requirement->createdBy: ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->createdBy : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("projectManager[$key]", $users, !empty($requirement->projectManager) ? $requirement->projectManager: ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->projectManager : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("dealUser[$key]", $users, !empty($requirement->dealUser) ? $requirement->dealUser: ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->dealUser : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::textarea("comment[$key]", $requirement->comment, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::input("consumed[$key]", !empty($requirement->consumed) ? $requirement->consumed : ((!empty($requirement->id) and isset($requirements[$requirement->id])) ? $requirements[$requirement->id]->consumed : ''), "class='form-control'")?></td>
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
            echo ' &nbsp; ' . "<a id='back' class='btn btn-back btn-wide' >{$lang->goback}</a>";
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
    $('#back').click(function(){location.href = createLink('requirement', 'browse')});
});
</script>
<?php include '../../common/view/footer.html.php';?>
